<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$go = true;
		$flogin = $_SESSION['login'];
		$fcurrpage = $_SESSION['module'];
		$fid = $mysqli->real_escape_string(strtoupper($flogin['user_id']));
		$fdate = date('Y-m-d H:i:s');
		if ($_POST['hidden']['command']=="submit"){
			$foldpass = $mysqli->real_escape_string($_POST['input']['oldpassword']);
			$tempoldpass = sha1(md5($foldpass));
			$fnewpass = $mysqli->real_escape_string($_POST['input']['newpassword']);
			$fconfirmpass = $mysqli->real_escape_string($_POST['input']['confirmpassword']);
			/* Checking Validation */
			$query = "SELECT user_id FROM tuser WHERE user_id='$fid' AND user_password = '$tempoldpass'";						
			if ($result = $mysqli->query($query)){
				if ($result->num_rows == 0){
					$alert[] = array(
						'type' => 'danger',
						'message' => 'Password lama salah!',
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
			if ($fnewpass != $fconfirmpass){
				$alert[] = array(
					'type' => 'danger',
					'message' => 'Password Baru dan Konfirmasi Password tidak sama!',
				);
				$go = false;
			}
			/* Validation Success */
			if ($go == true){
				$tempnewpass = sha1(md5($fnewpass));
				// Ubah Password User
				$query = "UPDATE tuser SET user_password = '$tempnewpass' WHERE user_id = '$fid'";
				if ($result = $mysqli->query($query)){
					$alert[] = array(
						'type' => 'success',
						'message' => 'Password berhasil diganti.',
					);
					// Insert Activity Log
					addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],'','Ganti Password');
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