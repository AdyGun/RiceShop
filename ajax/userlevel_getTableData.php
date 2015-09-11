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
		$fsearch = $mysqli->real_escape_string($_POST['search']);
		
		$flimit = "LIMIT ".($frecord*$fpage).", $frecord";
		$fcondition = "(l.level_id LIKE '%$fsearch%' OR
									  l.level_name LIKE '%$fsearch%' OR
									  l.level_description LIKE '%$fsearch%') AND
										l.level_deletedate IS NULL 
									";
										// AND l.level_id <> 'LEV0000'
		/* Table Data Query */
		$query = "SELECT l.* 
							FROM tuser_level l
							WHERE $fcondition
							ORDER BY l.level_id ASC
							$flimit
						 ";
		if ($result = $mysqli->query($query)){
			if ($result->num_rows > 0){
				while ($row = $result->fetch_assoc()){
					$newRow = array(
						'level_id' => $row['level_id'],
						'level_name' => $row['level_name'],
						'description' => $row['level_description'],
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
		$query = "SELECT COUNT(l.level_id) as recordcount
							FROM tuser_level l
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