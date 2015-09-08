<?php
	include '../config.php';

	$alert = array();
	$isSuccess = true;
	$data;
	if (isset($_POST))
	{
		$fid = $_POST['id'];
		$query = "SELECT m.module_id, a.access_create, a.access_read, a.access_update, a.access_delete
							FROM tlevel_access a
							LEFT JOIN tmodule m ON m.module_id = a.module_id
							WHERE a.level_id = '$fid'";
		if ($result = $mysqli->query($query)){
			if ($result->num_rows > 0){
				$data = $result->fetch_all();
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