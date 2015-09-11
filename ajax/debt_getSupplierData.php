<?php
	include '../config.php';

	$data = array();
	if (isset($_GET)){
		$fsearch = $_GET['q'];
		$query = "SELECT s.supplier_id, s.supplier_name 
							FROM tsupplier s 
							WHERE (s.supplier_id LIKE '%$fsearch%' OR
										 s.supplier_name LIKE '%$fsearch%') AND
									   s.supplier_deletedate IS NULL
						 ";
		if ($result = $mysqli->query($query)){			
			if ($result->num_rows > 0){
				// $data = $result->fetch_all();
				while ($row = $result->fetch_assoc()){
					$data[] = array(
						'id' => $row['supplier_id'],
						'text' => '['.$row['supplier_id'].'] '.$row['supplier_name'],
					);
				}
				$result->free();
			}
			else{
				printf("Errormessage: %s\n", $mysqli->error);
			}
		}
	}
	else{
		printf("Errormessage: %s\n", $mysqli->error);
	}
	echo json_encode($data);
?>