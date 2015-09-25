<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	$data = array();
	if (isset($_POST)){
		$fhref = 'debt.php';
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
			if (isset($_POST['search']['paid']))
				$paidcondition = 'AND debtremain <> 0';
			else
				$paidcondition = '';
			
			$flimit = "LIMIT ".($frecord*$fpage).", $frecord";
			$fcondition = "(d.debt_id LIKE '%$fsearch%' OR
											DATE_FORMAT(d.debt_date,'%d-%m-%Y') LIKE '%$fsearch%' OR
											s.supplier_name LIKE '%$fsearch%' OR
											s.supplier_id LIKE '%$fsearch%' OR
											u.user_name LIKE '%$fsearch%' OR
											d.debt_nominal LIKE '%$fsearch%' OR
											d.debt_description LIKE '%$fsearch%') AND
											d.debt_status = '$fstatus' AND
											d.debt_deletedate IS NULL
											$paidcondition
										";

			/* Table Data Query */
			$query = "SELECT d.*, u.user_id, u.user_name, s.supplier_name, s.supplier_id, (d.debt_nominal-x.totalpayment) as debtremain
								FROM tdebt d
								LEFT JOIN tsupplier s ON s.supplier_id = d.supplier_id
								LEFT JOIN tuser u ON u.user_id = d.user_id
								LEFT JOIN (SELECT debt_id, SUM(debtpayment_nominal) as totalpayment
													 FROM tdebtpayment 
													 WHERE debtpayment_status = 'posted' AND 
																 debtpayment_deletedate IS NULL 
													 GROUP BY debt_id) x ON x.debt_id = d.debt_id
								WHERE $fcondition
								ORDER BY d.debt_id ASC
								$flimit
							 ";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc()){
						$newRow = array(
							'debt_id' => $row['debt_id'],
							'debt_date' => $row['debt_date'],
							'supplier_id' => $row['supplier_id'],
							'supplier_name' => $row['supplier_name'],
							'user_id' => $row['user_id'],
							'user_name' => $row['user_name'],
							'debt_description' => $row['debt_description'],
							'debt_nominal' => $row['debt_nominal'],
							'debtremain' => $row['debtremain'],
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
			$query = "SELECT COUNT(d.debt_id) as recordcount
								FROM tdebt d
								LEFT JOIN tsupplier s ON s.supplier_id = d.supplier_id
								LEFT JOIN tuser u ON u.user_id = d.user_id
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