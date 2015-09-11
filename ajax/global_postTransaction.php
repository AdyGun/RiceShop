<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$currURL = $mysqli->real_escape_string($_POST['href']);
		$fhref = getPageName($currURL);
		$fcurrpage = getCurrentPageData($mysqli, $fhref);
		$fcommand = $mysqli->real_escape_string($_POST['command']);
		/* Checking Access Rights */
		if (($fcommand == 'create' && $_SESSION['access'][$fcurrpage['id']]['create'] == 1) 
		|| ($fcommand == 'update' && $_SESSION['access'][$fcurrpage['id']]['update'] == 1)){
			$go = true;
			$fid = $mysqli->real_escape_string(strtoupper($_POST['id']));
			$ftable = $mysqli->real_escape_string($_POST['table']);
			$fidfield = $mysqli->real_escape_string($_POST['id_field']);
			$fstatusfield = $mysqli->real_escape_string($_POST['status_field']);
			$fdeletefield = $mysqli->real_escape_string($_POST['delete_field']);
			$fdate = date('Y-m-d H:i:s');
			
			$query = "UPDATE $ftable SET 
									$fstatusfield='posted'
								WHERE $fidfield='$fid' AND $fdeletefield IS NULL";
			if ($result = $mysqli->query($query)){
				$alert[] = array(
					'type' => 'success',
					'message' => 'Transaksi berhasil di Posting.',
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