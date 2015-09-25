<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	$data;
	if (isset($_POST)){
		$fhref = 'supplier.php';
		$fcurrpage = getCurrentPageData($mysqli, $fhref);
		if ($_SESSION['access'][$fcurrpage['id']]['read'] == 1){
			$data = array(
				'tabledata' => array(),
				'totalpage' => 0,
			);
			$frecord = 10;
			$fpage = intval($mysqli->real_escape_string($_POST['search']['currentpage'])) - 1;
			$fsearch = $mysqli->real_escape_string($_POST['search']['text']);
			
			$flimit = "LIMIT ".($frecord*$fpage).", $frecord";
			$fcondition = "(s.supplier_id LIKE '%$fsearch%' OR
											s.supplier_name LIKE '%$fsearch%' OR
											s.supplier_address LIKE '%$fsearch%' OR
											s.supplier_city LIKE '%$fsearch%' OR
											s.supplier_phone LIKE '%$fsearch%' OR
											s.supplier_description LIKE '%$fsearch%') AND
											s.supplier_deletedate IS NULL 
										";

			/* Table Data Query */
			$query = "SELECT s.* 
								FROM tsupplier s
								WHERE $fcondition
								ORDER BY s.supplier_id ASC
								$flimit
							 ";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					while ($row = $result->fetch_assoc()){
						$newRow = array(
							'supplier_id' => $row['supplier_id'],
							'supplier_name' => $row['supplier_name'],
							'supplier_address' => $row['supplier_address'],
							'supplier_city' => $row['supplier_city'],
							'supplier_phone' => $row['supplier_phone'],
							'supplier_description' => $row['supplier_description'],
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
			$query = "SELECT COUNT(s.supplier_id) as recordcount
								FROM tsupplier s
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