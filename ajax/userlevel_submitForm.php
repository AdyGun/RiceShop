<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$fhref = $mysqli->real_escape_string($_POST['hidden']['href']);
		$fcurrpage = getCurrentPageData($mysqli, $fhref);
		$fcommand = $mysqli->real_escape_string($_POST['hidden']['command']);
		/* Checking Access Rights */
		if (($fcommand == 'create' && $_SESSION['access'][$fcurrpage['id']]['create'] == 1) 
		|| ($fcommand == 'update' && $_SESSION['access'][$fcurrpage['id']]['update'] == 1)
		|| ($fcommand == 'delete' && $_SESSION['access'][$fcurrpage['id']]['delete'] == 1)){
			$go = true;
			$flogin = $_SESSION['login'];
			$fid = $mysqli->real_escape_string(strtoupper($_POST['hidden']['id']));
			$fdate = date('Y-m-d H:i:s');
			if ($fcommand == 'create' || $fcommand == 'update'){
				$fname = $mysqli->real_escape_string($_POST['input']['name']);
				$fdesc = $mysqli->real_escape_string($_POST['input']['description']);
				$fmodule = array();
				if (isset($_POST['input']['module'])){ $fmodule = $_POST['input']['module']; }
				/* Checking Validation */
				if (strlen($fname)<4){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Panjang Nama Level User minimal 4 karakter!',
					);
					$go = false;
				}
				else{
					$query = "SELECT level_name FROM tuser_level WHERE level_name='$fname'";				
					if ($fcommand == 'update'){
						$fhidname = $mysqli->real_escape_string($_POST['hidden']['name']);
						$query .= " AND level_name<>'$fhidname'";
					}				
					if ($result = $mysqli->query($query)){
						if ($result->num_rows>0){
							$alert[] = array(
								'type' => 'danger',
								'message' => 'Nama Level User sudah pernah didaftarkan!',
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
					if ($fcommand == 'create'){
						// Insert User Level
						$fid = autoGenerateID($mysqli, "LEV", "tuser_level", "level_id", 4);
						$query = "INSERT INTO tuser_level VALUES ('$fid','$fname','$fdesc',NULL);";
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Level User baru telah ditambahkan.',
							);
							// Insert Activity Log
							addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],$fid,'Tambah');
						}
						else{
							$alert[] = array(
								'type' => 'danger',
								'message' => "Errormessage: ".$mysqli->error,
							);
							$isSuccess = false;
						}
					}
					else if ($fcommand == 'update'){
						// Update User Level
						$query = "UPDATE tuser_level SET 
												level_name='$fname',						
												level_description='$fdesc'
											WHERE level_id='$fid'";
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Data Level User berhasil diubah.',
							);
							// Insert Activity Log
							addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],$fid,'Ubah');
							// Delete Level Access
							$dQuery = "DELETE FROM tlevel_access WHERE level_id = '$fid'";
							if (!$dResult = $mysqli->query($dQuery)){
								$alert[] = array(
									'type' => 'danger',
									'message' => "Errormessage: ".$mysqli->error,
								);
								$isSuccess = false;
							}
						}
						else{
							$alert[] = array(
								'type' => 'danger',
								'message' => "Errormessage: ".$mysqli->error,
							);
							$isSuccess = false;
						}
					}
					// Insert Level Access
					foreach ($fmodule as $value){
						$mAC = isset($_POST['input'][$value]['c']) ? 1 : 0;
						$mAR = isset($_POST['input'][$value]['r']) ? 1 : 0;
						$mAU = isset($_POST['input'][$value]['u']) ? 1 : 0;
						$mAD = isset($_POST['input'][$value]['d']) ? 1 : 0;
						$mQuery = "INSERT INTO tlevel_access VALUES ('$fid','$value',$mAC,$mAR,$mAU,$mAD);";
						if (!$mResult = $mysqli->query($mQuery)){
							$alert[] = array(
								'type' => 'danger',
								'message' => "Errormessage: ".$mysqli->error,
							);
							$isSuccess = false;
						}
					}
					//Add Access Rights Session
					setAccessSession($mysqli, $fid);
				}
				else{
					$isSuccess = false;
				}
			}
			if ($fcommand == 'delete'){
				$query = "UPDATE tuser_level SET level_deletedate='$fdate' WHERE level_id='$fid'";
				if ($result = $mysqli->query($query)){
					$alert[] = array(
						'type' => 'success',
						'message' => 'Data Level User telah berhasil dihapus.',
					);
					// Insert Activity Log
					addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],$fid,'Hapus');
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