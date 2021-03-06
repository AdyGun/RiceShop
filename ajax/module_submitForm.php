<?php
	include '../config.php';
	include '../function.php';

	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$fhref = 'module.php';
		$fcurrpage = getCurrentPageData($mysqli, $fhref);
		$fcommand = $mysqli->real_escape_string($_POST['hidden']['command']);
		/* Checking Access Rights */
		if (($fcommand == 'create' && $_SESSION['access'][$fcurrpage['id']]['create'] == 1) 
		|| ($fcommand == 'update' && $_SESSION['access'][$fcurrpage['id']]['update'] == 1)
		|| ($fcommand == 'delete' && $_SESSION['access'][$fcurrpage['id']]['delete'] == 1)){
			$go = true;
			$fid = $mysqli->real_escape_string(strtoupper($_POST['hidden']['id']));
			if ($fcommand == 'create' || $fcommand == 'update'){
				$fname = $mysqli->real_escape_string(ucwords(strtolower($_POST['input']['name'])));			
				$fcategory = $mysqli->real_escape_string(ucwords(strtolower($_POST['input']['category'])));	
				$fpageurl = $mysqli->real_escape_string(strtolower($_POST['input']['pageurl']));			
				$fdesc = $mysqli->real_escape_string(ucwords(strtolower($_POST['input']['description'])));
				$fissub = $mysqli->real_escape_string($_POST['input']['issub']);
				$fhascrud = $mysqli->real_escape_string($_POST['input']['hascrud']);
				/* Checking Validation */
				if (strlen($fname)<4){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Panjang Module Name minimal 4 karakter!',
					);
					$go = false;
				}
				else{
					$query = "SELECT module_name FROM tmodule WHERE module_name='$fname'";				
					if ($fcommand=='update'){			
						$fhidname = $mysqli->real_escape_string($_POST['hidden']['name']);
						$query .= " AND module_name<>'$fhidname'";
					}				
					if ($result = $mysqli->query($query)){
						if ($result->num_rows>0){
							$alert[] = array(
								'type' => 'danger',
								'message' => 'Module Name sudah pernah didaftarkan!',
							);
							$go = false;
						}
					}
					else{
						$alert[] = array(
							'type' => 'danger',
							'message' => "Errormessage: ".$mysqli->error,
						);
						$go = false;
					}
				}
				if (strlen($fcategory)<4){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Panjang Category minimal 4 karakter!',
					);
					$go = false;
				}
				if (strlen($fpageurl)<6){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Panjang PageURL minimal 6 karakter!',
					);
					$go = false;
				}
				else{
					$query = "SELECT module_pageurl FROM tmodule WHERE module_pageurl='$fpageurl'";
					if ($fcommand=='update'){
						$fhidpageurl = $mysqli->real_escape_string($_POST['hidden']['pageurl']);
						$query .= " AND module_pageurl<>'$fhidpageurl'";
					}
					if ($result = $mysqli->query($query)){
						if ($result->num_rows>0){
							$alert[] = array(
								'type' => 'danger',
								'message' => 'PageURL sudah pernah didaftarkan!',
							);
							$go = false;
						}							
					}
					else{
						$alert[] = array(
							'type' => 'danger',
							'message' => "Errormessage: ".$mysqli->error,
						);
						$go = false;
					}
				}
				/* Validation Success */
				if ($go == true){
					if ($fcommand=='create'){
						$fid = autoGenerateID($mysqli, "MOD", "tmodule", "module_id", 4);
						$query = "INSERT INTO tmodule VALUES ('$fid','$fname','$fcategory','$fdesc','$fpageurl',$fissub,$fhascrud);";	
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Module baru telah ditambahkan.',
							);
						}
						else{
							$alert[] = array(
								'type' => 'danger',
								'message' => "Errormessage: ".$mysqli->error,
							);
							$isSuccess = false;
						}
					}
					else if ($fcommand=='update'){
						$query = "UPDATE tmodule SET 
												module_name='$fname',						
												module_category='$fcategory',
												module_description='$fdesc',
												module_pageurl='$fpageurl',
												module_issub=$fissub,
												module_hascrud=$fhascrud
											WHERE module_id='$fid'";
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Data Module berhasil diubah.',
							);
						}
						else{
							$alert[] = array(
								'type' => 'danger',
								'message' => "Errormessage: ".$mysqli->error,
							);
							$isSuccess = false;
						}
					}
				}
				else{
					$isSuccess = false;
				}
			}
			if ($fcommand=='delete'){
				$query = "DELETE FROM tmodule WHERE module_id='$fid'";
				if ($result = $mysqli->query($query)){
					$alert[] = array(
						'type' => 'success',
						'message' => 'Data Module telah berhasil dihapus.',
					);
				}
				else{
					$alert[] = array(
						'type' => 'danger',
						'message' => "Errormessage: ".$mysqli->error,
					);
					$isSuccess = false;
				}
			}
		}
		else{
			$alert[] = array(
				'type' => 'warning',
				'message' => 'You dont have right to do this!!!',
			);
			$isSuccess = false;
		}
	}
	else{
		$alert[] = array(
			'type' => 'warning',
			'message' => 'Something bad happen!!!',
		);
		$isSuccess = false;
	}
	$ajaxResult = array(
		'type' => $isSuccess,
		'alert' => $alert,
	);
	echo json_encode($ajaxResult);
?>