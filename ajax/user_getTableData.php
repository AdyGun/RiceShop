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
		$fcondition = "(u.user_name LIKE '%$fsearch%' OR
									  u.user_completename LIKE '%$fsearch%' OR
									  l.level_name LIKE '%$fsearch%') AND
										u.user_deletedate IS NULL 
									";
		/* Table Data Query */
		$query = "SELECT u.user_id, u.user_name, u.user_completename, l.level_name 
							FROM tuser u
							LEFT JOIN tuser_level l ON l.level_id = u.level_id
							WHERE $fcondition
							ORDER BY u.user_id ASC
							$flimit
						 ";
		if ($result = $mysqli->query($query)){
			if ($result->num_rows > 0){
				while ($row = $result->fetch_assoc()){
					$newRow = array(
						'user_id' => $row['user_id'],
						'user_name' => $row['user_name'],
						'user_completename' => $row['user_completename'],
						'level_name' => $row['level_name'],
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
		$query = "SELECT COUNT(u.user_id) as recordcount
							FROM tuser u
							LEFT JOIN tuser_level l ON l.level_id = u.level_id
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