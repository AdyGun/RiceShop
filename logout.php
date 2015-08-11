<?php
    include 'config.php';
    include 'function.php';

    if (isset($_SESSION['login'])){
			$fuser = $mysqli->real_escape_string($_SESSION['login']['user_id']);
			$fdate = date("Y-m-d H:i:s");
			$query = "UPDATE tuser SET user_status = 'Offline', user_accessdate = '$fdate' where user_id='$fuser'";
			if ($result = $mysqli->query($query)){
				//INSERT ACTIVITY LOG
				addLog($mysqli,$fuser,'','','Logout');
				unset($_SESSION['login']);
			}
			else{
				printf("Errormessage: %s\n", $mysqli->error);
			}
    }
    header("Location: login.php");
?>