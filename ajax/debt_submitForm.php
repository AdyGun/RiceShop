<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	$fid = '';
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
				$fuser = $flogin['user_id'];
				$ftempdate = date_create_from_format('Y-m-d',$mysqli->real_escape_string($_POST['hidden']['date']));
				$fsupplier = $mysqli->real_escape_string($_POST['input']['supplier']);
				$fdescription = $mysqli->real_escape_string($_POST['input']['description']);
				$fnominal = intval($mysqli->real_escape_string($_POST['hidden']['nominal']));
				$photoString = $mysqli->real_escape_string($_POST['hidden']['photo']);
				$fstatus = 'pending';
				/* Checking Validation */
				if ($ftempdate->format('Y-m-d') > date('Y-m-d')){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Tanggal utang lebih dari hari ini!',
					);
					$go = false;
				}
				if ($fsupplier == '0'){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Isi data Supplier terlebih dahulu!',
					);
					$go = false;
				}
				if ($photoString == ''){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Foto masih kosong!',
					);
					$go = false;
				}
				if ($fnominal < 1){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Nominal utang harus lebih dari 0!',
					);
					$go = false;
				}
				/* Validation Success */
				if ($go == true){
					// Convert Photo String to JPG
					$photoData = base64_decode($photoString);
					$photoSource = imagecreatefromstring($photoData);
					// Create Image Directory
					if (!file_exists('../images/debt')) {
							mkdir('../images/debt', 0777, true);
					}
					// Convert DateTime to String
					$fdebtdate = $ftempdate->format('Y-m-d');
					if ($fcommand=='create'){
						$fid = autoGenerateID($mysqli, "DB".$ftempdate->format('ym'), "tdebt", "debt_id", 6);
						$fphoto = 'images/debt/'.$fid.'.jpg';
						$query = "INSERT INTO tdebt 
											VALUES ('$fid',
															'$fdebtdate',
															'$fsupplier',
															'$fuser',
															'$fdescription',
															$fnominal,
															'$fphoto',
															'$fstatus',
															NULL)
										 ";	
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Utang baru telah ditambahkan.',
							);
							$photoSave = imagejpeg($photoSource,'../'.$fphoto,100);
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
						$fphoto = 'images/debt/'.$fid.'.jpg';
						$query = "UPDATE tdebt SET 
												debt_date='$fdebtdate',						
												supplier_id='$fsupplier',
												user_id='$fuser',
												debt_description='$fdescription',
												debt_nominal=$fnominal,
												debt_status='$fstatus'
											WHERE debt_id='$fid'";
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Data Utang berhasil diubah.',
							);
							$imageDataEncoded = base64_encode(@file_get_contents($fphoto));
							if ($imageDataEncoded != $photoString){
								$photoSave = imagejpeg($photoSource,'../'.$fphoto,100);
							}
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
					imagedestroy($photoSource);
				}
				else{
					$isSuccess = false;
				}
			}
			if ($fcommand=='delete'){
				$query = "UPDATE tdebt SET debt_deletedate='$fdate' WHERE user_id='$fid'";
				if ($result = $mysqli->query($query)){
					$alert[] = array(
						'type' => 'success',
						'message' => 'Data Utang telah berhasil dihapus.',
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
		'id' => $fid,
	);
	echo json_encode($ajaxResult);
?>