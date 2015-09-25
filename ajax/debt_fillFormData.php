<?php
	include '../config.php';

	$alert = array();
	$isSuccess = true;
	$data = array();
	if (isset($_POST)){
		$fid = $mysqli->real_escape_string($_POST['id']);
		$fstatus = $mysqli->real_escape_string($_POST['status']);
		$query = "SELECT d.debt_id, 
										 d.debt_date, 
										 d.debt_description, 
										 d.debt_nominal, 
										 s.supplier_id, 
										 s.supplier_name, 
										 d.debt_imageblob, 
										 (d.debt_nominal-x.totalpayment) as debtremain
							FROM tdebt d
							LEFT JOIN tsupplier s ON s.supplier_id = d.supplier_id
							LEFT JOIN (SELECT debt_id, SUM(debtpayment_nominal) as totalpayment
												 FROM tdebtpayment 
												 WHERE debtpayment_status = 'posted' AND 
												 		 debtpayment_deletedate IS NULL 
												 GROUP BY debt_id) x ON x.debt_id = d.debt_id
							WHERE d.debt_id = '$fid' AND d.debt_status = '$fstatus' AND d.debt_deletedate IS NULL
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