<?php
    include 'config.php';
    include 'function.php';
		
    if (isset($_POST["bsignin"])){
			$username = $mysqli->real_escape_string(strtoupper($_POST["fusername"]));
			$temppassword = $mysqli->real_escape_string($_POST["fpassword"]);
			$password = sha1(md5($temppassword));
			$level = "";
			$query = "SELECT u.user_id, u.user_name, u.user_completename, u.level_id, l.level_name
								FROM tuser u
								LEFT JOIN tuser_level l ON u.level_id = l.level_id
								WHERE user_name='$username' AND user_password='$password' AND user_deletedate IS NULL";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					$duser = array();
					while ($row = $result->fetch_assoc()){
						$duser = $row;
					}
					$result->free();
					//Add Login Session
					$_SESSION["login"] = $duser;
					//Add Access Rights Session
					setAccessSession($mysqli, $duser['level_id']);
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
