<?php
	if(!isset($_SESSION)) session_start();
	
	//SERVER CONNECTION
  $websitename = "http://localhost/riceshop";
	$mysql_host = "localhost";
	$mysql_database = "riceshop";
	$mysql_user = "root";
	$mysql_password = "";

  $mysqli = mysqli_connect($mysql_host,$mysql_user,$mysql_password,$mysql_database);
	if ($mysqli->connect_errno){
		echo "Failed to connect to MySQL: " . $mysqli->connect_error;
		exit;
	}
	date_default_timezone_set('Asia/Jakarta');
	
	//DETAIL TOKO
	$store_name = "TOKO BERAS";
	$store_address = "Jln. XXX No. X,  XXXXX";
	$store_phone = "xxx-xxxxxxx";
	$store_fax = "xxx-xxxxxxx";
?>
