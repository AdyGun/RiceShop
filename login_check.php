<?php
    include 'config.php';
    include 'function.php';
		
    if (isset($_POST["bsignin"])){
			$username = $mysqli->real_escape_string(strtoupper($_POST["fusername"]));
			$temppassword = $mysqli->real_escape_string($_POST["fpassword"]);
			$password = sha1(md5($temppassword));
			$level = "";
			$query = "SELECT user_id, user_name, user_completename, level_id FROM tuser WHERE user_name='$username' AND user_password='$password' AND user_deletedate IS NULL";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					$duser = array();
					while ($row = $result->fetch_assoc()){
						$duser = $row;
					}
					$_SESSION["login"] = $duser;
					$result->free();
					//INSERT ACTIVITY LOG
					addLog($mysqli,$duser["user_id"],'','','Login');
					
					header("Location: index.php");
				}
				else{
					header("Location: login.php?err=2");
				}
			}
			else{
				printf("Errormessage: %s\n", $mysqli->error);
			}
    }
		else{
			header("Location: login.php?err=1");
		}
?>
