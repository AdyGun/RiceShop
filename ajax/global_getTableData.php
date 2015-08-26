<?php
	include '../config.php';

	$alert = array();
	$isSuccess = true;
	$data;
	if (isset($_POST))
	{
		$fid = $_POST['id'];
		$ftable = $_POST['table'];
		$ffield = $_POST['field'];
		$query = "SELECT * FROM $ftable WHERE $ffield = '$fid'";
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