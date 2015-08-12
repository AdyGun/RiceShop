<?php
	function getPageName(){
		$currentFile = $_SERVER["PHP_SELF"];
		$parts = Explode('/', $currentFile);
		$realurl = Explode('?', $parts[count($parts)-1]);
		return $realurl[0];
	}
	
	function addLog($mysqli_link, $user_id, $log_name, $log_reference, $log_action){
		//INSERT ACTIVITY LOG
		$log_date = date("Y-m-d H:i:s");
		$log_query = "INSERT INTO tlog (log_name, log_reference, log_action, log_date, user_id) VALUES 
								  ('$log_name', '$log_reference', '$log_action', '$log_date','$user_id');";
		if (!$log_result = $mysqli_link->query($log_query)){
			printf("Errormessage: %s\n", $mysqli_link->error);
		}
	}
	
	function getIconName($throwed_name){
		$iconName = '';
		switch ($throwed_name) {
			case "Utama":
				$iconName = 'dashboard';
				break;
			case "Utility":
				$iconName = 'wrench';
				break;
		}
		return $iconName;
	}
?>