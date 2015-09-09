<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$fcurrpage = getCurrentPageData($mysqli, $_SERVER['HTTP_REFERER']);
		$fcommand = $_POST['hidden']['command'];
		/* Checking Access Rights */
		if (($fcommand == 'create' && $_SESSION['access'][$fcurrpage['id']]['create'] == 1) 
		|| ($fcommand == 'update' && $_SESSION['access'][$fcurrpage['id']]['update'] == 1)
		|| ($fcommand == 'delete' && $_SESSION['access'][$fcurrpage['id']]['delete'] == 1)){
			$go = true;
			$flogin = $_SESSION['login'];
			$fid = $mysqli->real_escape_string(strtoupper($_POST['hidden']['id']));
			$fdate = date('Y-m-d H:i:s');
			if ($fcommand =='create' || $fcommand == 'update'){
				$fname = $mysqli->real_escape_string($_POST['input']['name']);
				$fcompletename = $mysqli->real_escape_string($_POST['input']['completename']);
				$flevel = $mysqli->real_escape_string(strtoupper($_POST['input']['level']));
				/* Checking Validation */
				if (strlen($fname)<4){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Panjang Nama Login minimal 4 karakter!',
					);
					$go = false;
				}
				else{
					$query = "SELECT user_name FROM tuser WHERE user_name='$fname'";
					if ($fcommand=='update'){
						$fhidname = $mysqli->real_escape_string($_POST['hidden']['name']);
						$query .= " AND user_name<>'$fhidname'";
					}				
					if ($result = $mysqli->query($query)){
						if ($result->num_rows>0){
							$alert[] = array(
								'type' => 'danger',
								'message' => 'Nama Login sudah pernah didaftarkan!',
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
				if (strlen($fcompletename)<4){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Panjang Nama Lengkap minimal 4 karakter!',
					);
					$go = false;
				}
				if ($flevel == '0'){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Isi data Level User terlebih dahulu!',
					);
					$go = false;
				}
				/* Validation Success */
				if ($go == true){
					if ($fcommand=='create'){
						// Insert User
						$fid = autoGenerateID($mysqli, "USER", "tuser", "user_id", 4);
						$fpass = sha1(md5(123456));
						$query = "INSERT INTO tuser 
											VALUES ('$fid',
															'$fname',
															'$fpass',
															'$fcompletename',
															'$flevel',
															'Offline',
															NULL,
															NULL,
															NULL)
										 ";
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'User baru telah ditambahkan.',
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
					else if ($fcommand=='update'){
						// Update User Level
						$query = "UPDATE tuser SET 
												user_name='$fname',
												user_completename='$fcompletename',
												level_id='$flevel'
											WHERE user_id='$fid'";
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Data User berhasil diubah.',
							);
							// Insert Activity Log
							addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],$fid,'Ubah');
						}
						else{
							$alert[] = array(
								'type' => 'danger',
								'message' => "Errormessage: ".$mysqli->error,
							);
							$isSuccess = false;
						}
					}
					//Add Access Rights Session
					setAccessSession($mysqli, $flevel);
				}
				else{
					$isSuccess = false;
				}
			}
			if ($fcommand=='delete'){
				$query = "UPDATE tuser SET user_deletedate='$fdate' WHERE user_id='$fid'";
				if ($result = $mysqli->query($query)){
					$alert[] = array(
						'type' => 'success',
						'message' => 'Data User telah berhasil dihapus.',
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