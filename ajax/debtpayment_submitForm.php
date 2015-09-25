<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	$fid = '';
	if (isset($_POST)){
		$fhref = 'debt_payment.php';
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
				$ftempdebtdate = date_create_from_format('Y-m-d',$mysqli->real_escape_string($_POST['hidden']['debtdate']));
				$fdebtid = $mysqli->real_escape_string($_POST['hidden']['debtid']);
				$fdescription = $mysqli->real_escape_string($_POST['input']['description']);
				$fnominal = intval($mysqli->real_escape_string($_POST['hidden']['nominal']));
				$fstatus = 'pending';
				/* Checking Validation */
				if ($ftempdate->format('Y-m-d') > date('Y-m-d')){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Tanggal pembayaran lebih dari hari ini!',
					);
					$go = false;
				}
				if ($ftempdate->format('Y-m-d') < $ftempdebtdate->format('Y-m-d')){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Tanggal pembayaran harus melebihi tanggal utang!',
					);
					$go = false;
				}
				if ($fnominal < 1){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Nominal pembayaran harus lebih dari 0!',
					);
					$go = false;
				}
				else{
					$debtremain = -1;
					$dQuery = "SELECT d.debt_id, (d.debt_nominal - x.totalpayment) as debtremain
										 FROM tdebt d
										 LEFT JOIN (SELECT debt_id, SUM(debtpayment_nominal) as totalpayment
																FROM tdebtpayment 
																WHERE debtpayment_status = 'posted' AND 
																			debtpayment_deletedate IS NULL 
																GROUP BY debt_id) x ON x.debt_id = d.debt_id
										 WHERE d.debt_id = '$fdebtid';
										";
					if ($dResult = $mysqli->query($dQuery)){
						if ($dResult->num_rows > 0){
							$row = $dResult->fetch_row();
							$debtremain = $row[1];
							$dResult->free();
						}
						else{
							$alert[] = array(
								'type' => 'danger',
								'message' => 'Utang tidak ditemukan!',
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
					if ($fnominal > $debtremain){
						$alert[] = array(
							'type' => 'danger',
							'message' => 'Nominal pembayaran melebihi sisa utang!',
						);
						$go = false;
					}
				}
				/* Validation Success */
				if ($go == true){
					// Convert DateTime to String
					$fpaymentdate = $ftempdate->format('Y-m-d');
					if ($fcommand=='create'){
						$fid = autoGenerateID($mysqli, "PU".$ftempdate->format('ym'), "tdebtpayment", "debtpayment_id", 6);
						$query = "INSERT INTO tdebtpayment 
											VALUES ('$fid',
															'$fpaymentdate',
															'$fdebtid',
															'$fuser',
															'$fdescription',
															$fnominal,
															'$fstatus',
															NULL)
										 ";	
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Pembayaran utang baru telah ditambahkan.',
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
						$query = "UPDATE tdebtpayment SET 
												debtpayment_date='$fpaymentdate',						
												debt_id='$fdebtid',
												user_id='$fuser',
												debtpayment_description='$fdescription',
												debtpayment_nominal=$fnominal,
												debtpayment_status='$fstatus'
											WHERE debtpayment_id='$fid'";
						if ($result = $mysqli->query($query)){
							$alert[] = array(
								'type' => 'success',
								'message' => 'Data Pembayaran utang berhasil diubah.',
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
				}
				else{
					$isSuccess = false;
				}
			}
			if ($fcommand=='delete'){
				$query = "UPDATE tdebtpayment SET debtpayment_deletedate='$fdate' WHERE debtpayment_id='$fid'";
				if ($result = $mysqli->query($query)){
					$alert[] = array(
						'type' => 'success',
						'message' => 'Data Pembayaran utang telah berhasil dihapus.',
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