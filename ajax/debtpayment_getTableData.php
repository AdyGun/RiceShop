<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	$data = array();
	if (isset($_POST)){
		$fhref = 'debt_payment.php';
		$fcurrpage = getCurrentPageData($mysqli, $fhref);
		if ($_SESSION['access'][$fcurrpage['id']]['read'] == 1){
			$data = array(
				'tabledata' => array(),
				'totalpage' => 0,
			);
			$frecord = 10;
			$fpage = intval($mysqli->real_escape_string($_POST['search']['currentpage'])) - 1;
			$fsearch = $mysqli->real_escape_string($_POST['search']['text']);
			if (isset($_POST['search']['status']))
				$fstatus = $mysqli->real_escape_string($_POST['search']['status']);
			else
				$fstatus = 'posted';
			
			$flimit = "LIMIT ".($frecord*$fpage).", $frecord";
			$fcondition = "(p.debtpayment_id LIKE '%$fsearch%' OR
											DATE_FORMAT(p.debtpayment_date,'%d-%m-%Y') LIKE '%$fsearch%' OR
											d.debt_id LIKE '%$fsearch%' OR
											u.user_name LIKE '%$fsearch%' OR
											u.user_id LIKE '%$fsearch%' OR
											p.debtpayment_nominal LIKE '%$fsearch%' OR
											p.debtpayment_description LIKE '%$fsearch%') AND
											p.debtpayment_status = '$fstatus' AND
											p.debtpayment_deletedate IS NULL
										";

			/* Table Data Query */
			$query = "SELECT p.*, u.user_id, u.user_name, d.debt_id
								FROM tdebtpayment p
								LEFT JOIN tdebt d ON d.debt_id = p.debt_id
								LEFT JOIN tuser u ON u.user_id = p.user_id
								WHERE $fcondition
								ORDER BY p.debtpayment_id ASC
								$flimit
							 ";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc()){
						$newRow = array(
							'debtpayment_id' => $row['debtpayment_id'],
							'debtpayment_date' => $row['debtpayment_date'],
							'debt_id' => $row['debt_id'],
							'user_id' => $row['user_id'],
							'user_name' => $row['user_name'],
							'debtpayment_description' => $row['debtpayment_description'],
							'debtpayment_nominal' => $row['debtpayment_nominal'],
						);
						$data['tabledata'][] = $newRow;
					}
					$result->free();
				}
				else{
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
			/* Paging Query */
			$query = "SELECT COUNT(p.debtpayment_id) as recordcount
								FROM tdebtpayment p
								LEFT JOIN tdebt d ON d.debt_id = p.debt_id
								LEFT JOIN tuser u ON u.user_id = p.user_id
								WHERE $fcondition
							 ";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					$row = $result->fetch_row();
					$data['totalpage'] = ceil(intval($row[0]) / 10);
					$result->free();
				}
				else{
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
				'message' => 'You dont have right to do this!!!',
			);
			$isSuccess = false;
		}
	}
	else{
		$alert[] = array(
			'type' => 'warning',
			'message' => 'No Post!!!',
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