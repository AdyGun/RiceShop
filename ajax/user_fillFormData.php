<?php
	include '../config.php';

	$alert = array();
	$isSuccess = true;
	$data;
	if (isset($_POST))
	{
		$fid = $mysqli->real_escape_string(strtoupper($_POST['id']));
		$query = "SELECT u.user_id, u.user_name, u.user_completename, l.level_name
							FROM tuser u
							LEFT JOIN tuser_level l ON l.level_id = u.level_id
							WHERE u.user_id = '$fid'
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