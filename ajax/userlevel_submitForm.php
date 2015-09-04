<?php
	include '../config.php';
	include '../function.php';

	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$go = true;
		$flogin = $_SESSION['login'];
		$fcurrpage = $_SESSION['module'];
		$fid = $mysqli->real_escape_string(strtoupper($_POST['hidden']['id']));
		$fdate = date('Y-m-d H:i:s');
		if ($_POST['hidden']['command']=="create" || $_POST['hidden']['command']=="update"){
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
				if ($_POST['hidden']['command']=='update'){
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
				if ($_POST['hidden']['command']=='create'){
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
				else if ($_POST['hidden']['command']=='update'){
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
					$mQuery = "INSERT INTO tlevel_access VALUES ('$fid','$value');";
					if (!$mResult = $mysqli->query($mQuery)){
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
		if ($_POST['hidden']['command']=='delete'){
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