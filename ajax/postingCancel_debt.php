<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$fhref = 'cancel_debt.php';
		$flogin = $_SESSION['login'];
		$privCheck = privilegeCheck($mysqli, $fhref, $flogin['user_id']);
		if ($privCheck['result'] == 'success'){
			$go = true;
			$currpagedata = $privCheck['data'];
			$fid = $mysqli->real_escape_string(strtoupper($_POST['input']['id']));
			$fdescription = $mysqli->real_escape_string($_POST['input']['description']);
			
			// Checking Validation
			if (strlen($fid)<10){
				$alert[] = array(
					'type' => 'danger',
					'message' => 'Panjang Kode Utang minimal 10 karakter!',
				);
				$go = false;
			}
			if (strlen($fdescription)<4){
				$alert[] = array(
					'type' => 'danger',
					'message' => 'Panjang Keterangan minimal 4 karakter!',
				);
				$go = false;
			}
			if ($go == true){
				$sQuery = "SELECT debt_id FROM tdebt WHERE debt_id='$fid' AND debt_status = 'posted'";
				if ($sResult = $mysqli->query($sQuery)){
					if ($sResult->num_rows > 0){
						// Update Debt Status
						$uQuery = "UPDATE tdebt SET debt_status = 'pending' WHERE debt_id = '$fid' AND debt_status = 'posted'";
						if (!$uResult = $mysqli->query($uQuery)){
							$alert[] = array(
								'type' => 'danger',
								'message' => "Errormessage: ".$mysqli->error,
							);
							$isSuccess = false;
						}
						// Insert Activity Log
						addLog($mysqli,$flogin['user_id'],$currpagedata['category'].' '.$currpagedata['name'],$fid,'Batal Posting',$fdescription);
						$alert[] = array(
							'type' => 'success',
							'message' => 'Batal posting berhasil.',
						);
					}
					else{
						$alert[] = array(
							'type' => 'danger',
							'message' => 'Kode Utang tidak ditemukan!',
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
			else{
				$isSuccess = false;
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