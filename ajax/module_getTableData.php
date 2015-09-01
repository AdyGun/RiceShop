<?php
	include '../config.php';
	
	$alert = array();
	$isSuccess = true;
	$data;
	if (isset($_POST)){
		$data = array(
			'tabledata' => array(),
			'totalpage' => 0,
		);
		$frecord = 10;
		$fpage = intval($_POST['page']) - 1;
		$fsearch = $mysqli->real_escape_string(strtoupper($_POST['search']));
		
		$flimit = "LIMIT ".($frecord*$fpage).", $frecord";
		$fcondition = "UPPER(m.module_id) LIKE '%$fsearch%' OR
									 UPPER(m.module_name) LIKE '%$fsearch%' OR
									 UPPER(m.module_description) LIKE '%$fsearch%' OR
									 UPPER(m.module_category) LIKE '%$fsearch%' OR
									 m.module_pageurl LIKE '%$fsearch%'
									";

		/* Table Data Query */
		$query = "SELECT m.* 
							FROM tmodule m
							WHERE $fcondition
							ORDER BY m.module_id ASC
							$flimit
						 ";
		if ($result = $mysqli->query($query)){
			if ($result->num_rows > 0){
				while ($row = $result->fetch_assoc()){
					$fissub = '';
					if ($row['module_issub'] == 0)
						$fissub = 'No';
					else
						$fissub = 'Yes';
					$newRow = array(
						'module_id' => $row['module_id'],
						'module_name' => $row['module_name'],
						'category' => $row['module_category'],
						'description' => $row['module_description'],
						'pageurl' => $row['module_pageurl'],
						'issub' => $fissub,
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
				'message' => printf("Errormessage: %s\n", $mysqli->error),
			);
			$isSuccess = false;
		}
		/* Paging Query */
		$query = "SELECT COUNT(m.module_id) as recordcount
							FROM tmodule m
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
				'message' => printf("Errormessage: %s\n", $mysqli->error),
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