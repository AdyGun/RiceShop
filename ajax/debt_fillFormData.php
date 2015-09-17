<?php
	include '../config.php';

	$alert = array();
	$isSuccess = true;
	$data = array();
	if (isset($_POST)){
		$fid = $mysqli->real_escape_string($_POST['id']);
		$query = "SELECT d.debt_id, d.debt_date, d.debt_description, d.debt_nominal, s.supplier_id, s.supplier_name, u.user_id, u.user_name, d.debt_imageblob
							FROM tdebt d
							LEFT JOIN tsupplier s ON s.supplier_id = d.supplier_id
							LEFT JOIN tuser u ON u.user_id = d.user_id
							WHERE d.debt_id = '$fid' AND d.debt_status = 'pending' AND d.debt_deletedate IS NULL
						 ";
		if ($result = $mysqli->query($query)){
			if ($result->num_rows > 0){
				$row = $result->fetch_row();
				$data = $row;
				$result->free();
			}
			else{
				$alert[] = array(
					'type' => 'danger',
					'message' => 'No Result!',
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
		$alert[] = array(
			'type' => 'warning',
			'message' => 'Something bad happen!!!',
		);
		$isSuccess = false;
	}
	$ajaxResult = array(
		'type' => $isSuccess,
		'alert' => $alert,
		'data' => $data,
	);
	echo json_encode($ajaxResult);
?>