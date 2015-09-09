<?php
	include '../config.php';
	
	if (isset($_POST)){
		$fid = $mysqli->real_escape_string($_POST['id']);			
		$ftable = $mysqli->real_escape_string($_POST['table']);
		$ffield = $mysqli->real_escape_string($_POST['field']);
		$fexc = $mysqli->real_escape_string($_POST['exc']);
		$fdel = $mysqli->real_escape_string($_POST['del']);			
		if ($fdel == 'no'){
			$query = "SELECT $ffield FROM $ftable WHERE $ffield='$fid' AND $ffield<>'$fexc'";
		}
		else{
			$query = "SELECT $ffield FROM $ftable WHERE $ffield='$fid' AND $ffield<>'$fexc' AND $fdel IS NULL";
		}
		if ($result = $mysqli->query($query)){			
			if ($result->num_rows > 0){
				echo "false";
			}
			else{
				echo "true";
			}
		}
		else{
			echo "nodata";
		}
	}
	else{
		echo "nodata";
	}
?>