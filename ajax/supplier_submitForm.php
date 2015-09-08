<?php
	include '../config.php';
	include '../function.php';
	
	$alert = array();
	$isSuccess = true;
	if (isset($_POST)){
		$go = true;
		$fcurrpage = getCurrentPageData($mysqli);
		$fid = $mysqli->real_escape_string(strtoupper($_POST['hidden']['id']));
		if ($_POST['hidden']['command']=="create" || $_POST['hidden']['command']=="update"){
			$fname = $mysqli->real_escape_string($_POST['input']['name']);
			$faddress = $mysqli->real_escape_string($_POST['input']['address']);
			$fcity = $mysqli->real_escape_string($_POST['input']['city']);
			$fphone = $mysqli->real_escape_string($_POST['input']['phone']);
			$fdescription = $mysqli->real_escape_string($_POST['input']['description']);
			/* Checking Validation */
			if (strlen($fname)<4){
				$alert[] = array(
					'type' => 'danger',
					'message' => 'Panjang Nama Supplier minimal 4 karakter!',
				);
				$go = false;
			}
			else{
				$query = "SELECT supplier_name FROM tsupplier WHERE supplier_name='$fname'";				
				if ($_POST['hidden']['command']=='update'){			
					$fhidname = $mysqli->real_escape_string($_POST['hidden']['name']);
					$query .= " AND supplier_name<>'$fhidname'";
				}				
				if ($result = $mysqli->query($query)){
					if ($result->num_rows>0){
						$alert[] = array(
							'type' => 'danger',
							'message' => 'Nama Supplier sudah pernah didaftarkan!',
						);
						$go = false;
					}
				}
				else{
					$alert[] = array(
						'type' => 'danger',
						'message' => "Errormessage: ".$mysqli->error,
					);
					$go = false;
				}
			}
			if (strlen($faddress)<6){
				$alert[] = array(
					'type' => 'danger',
					'message' => 'Panjang Alamat minimal 6 karakter!',
				);
				$go = false;
			}
			if (strlen($fcity)<3){
				$alert[] = array(
					'type' => 'danger',
					'message' => 'Panjang Kota minimal 3 karakter!',
				);
				$go = false;
			}
			/* Validation Success */
			if ($go == true){
				if ($_POST['hidden']['command']=='create'){
					$fid = autoGenerateID($mysqli, "SUP", "tsupplier", "supplier_id", 4);
					$query = "INSERT INTO tsupplier 
										VALUES ('$fid',
														'$fname',
														'$faddress',
														'$fcity',
														'$fphone',
														'$fdescription',
														NULL)
									 ";	
					if ($result = $mysqli->query($query)){
						$alert[] = array(
							'type' => 'success',
							'message' => 'Supplier baru telah ditambahkan.',
						);
						// Insert Activity Log
						addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],$fid,'Tambah');
					}
					else{
						$alert[] = array(
							'type' => 'danger',
							'message' => "Errormessage: ".$mysqli->error,
						);
						$isSuccess = false;
					}
				}
				else if ($_POST['hidden']['command']=='update'){
					$query = "UPDATE tsupplier SET 
											supplier_name='$fname',						
											supplier_address='$faddress',
											supplier_city='$fcity',
											supplier_phone='$fphone',
											supplier_description='$fdescription'
										WHERE supplier_id='$fid'";
					if ($result = $mysqli->query($query)){
						$alert[] = array(
							'type' => 'success',
							'message' => 'Data Supplier berhasil diubah.',
						);
						// Insert Activity Log
						addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],$fid,'Ubah');
					}
					else{
						$alert[] = array(
							'type' => 'danger',
							'message' => "Errormessage: ".$mysqli->error,
						);
						$isSuccess = false;
					}
				}
			}
			else{
				$isSuccess = false;
			}
		}
		if ($_POST['hidden']['command']=='delete'){
			$query = "DELETE FROM tsupplier WHERE supplier_id='$fid'";
			if ($result = $mysqli->query($query)){
				$alert[] = array(
					'type' => 'success',
					'message' => 'Data Supplier telah berhasil dihapus.',
				);
				// Insert Activity Log
				addLog($mysqli,$flogin['user_id'],$fcurrpage['category'].' '.$fcurrpage['name'],$fid,'Hapus');
			}
			else{
				$alert[] = array(
					'type' => 'danger',
					'message' => "Errormessage: ".$mysqli->error,
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
	);
	echo json_encode($ajaxResult);
?>