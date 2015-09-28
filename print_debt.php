<?php
	include 'config.php';
	include 'function.php';
	
	$debtData = array();
	if (isset($_GET['id'])){
		$fhref = 'debt.php';
		$fcurrpage = getCurrentPageData($mysqli, $fhref);
		if ($_SESSION['access'][$fcurrpage['id']]['read'] == 1){
			$fid = $mysqli->real_escape_string($_GET['id']);
			$query = "SELECT d.*, s.supplier_name
								FROM tdebt d 
								LEFT JOIN tsupplier s ON s.supplier_id = d.supplier_id
								WHERE d.debt_id='$fid' AND d.debt_deletedate IS NULL";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					$debtData = $result->fetch_assoc();
					$debtData['debt_date'] = date_create_from_format('Y-m-d', $debtData['debt_date']);
					$formatedDate = $debtData['debt_date']->format('d/m/Y');
					$formatedNominal = number_format($debtData['debt_nominal'], 0, ',' ,'.');
					$result->free();
				}
				else{
					header("Location: index.php?err=1");
				}
			}
			else{
				echo "Errormessage: ".$mysqli->error;
			}
		}
	}
	else{
		header("Location: index.php?err=1");
	}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | Invoice</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- FontAwesome 4.4.0 -->
    <link href="plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">

		<!-- jQuery 2.1.4 -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
		
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <!--<body onload="window.print();">-->
  <body>
    <div class="wrapper">
      <!-- Main content -->
      <section class="invoice">
        <!-- title row -->
        <div class="row">
          <div class="col-xs-12">
            <h2 class="page-header">
							<img src="images/logo.jpg" height="50"> UD Agung
							<span class="pull-right" style="line-height: 2;">Faktur Utang</span>
            </h2>
          </div><!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
					<div class="col-sm-7 invoice-col">
            <b>No. #<?php echo $debtData['debt_id'];?></b><br>
            <b>Tanggal:</b> <?php echo $formatedDate;?><br>
            <b>Nama:</b> <?php echo $debtData['supplier_name'];?><br>
            <h3><b>Jumlah Utang:</b> Rp <?php echo $formatedNominal;?></h3><br>
          </div><!-- /.col -->
					<div class="col-sm-1 invoice-col">
            
          </div><!-- /.col -->
          <div class="col-sm-4 invoice-col">
            <img src="data:image/png;base64,<?php echo $debtData['debt_imageblob'];?>" width="100%">
          </div><!-- /.col -->
        </div><!-- /.row -->
				
      </section><!-- /.content -->
    </div><!-- ./wrapper -->

		<!-- jQuery UI 1.11.4 -->
    <script src="plugins/jQueryUI/jquery-ui.min.js" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/app.min.js"></script>
  </body>
</html>
