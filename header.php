<?php
	include 'config.php';
	include 'function.php';
	
	$pagename = strtolower(getPageName());
	if ($pagename != '404.php'){
		if (isset($_SESSION["login"])){
			/* Privilege Check */
			$login = $mysqli->real_escape_string($_SESSION['login']['user_id']);
			$query = "SELECT m.module_id
								FROM tlevel_access l
								INNER JOIN tmodule m ON l.module_id=m.module_id	
								INNER JOIN tuser u ON u.level_id=l.level_id						
								WHERE m.module_pageurl = '$pagename' AND u.user_id = '$login'
							 ";
			if ($result = $mysqli->query($query)){
				if ($result->num_rows > 0){
					$fpageid = "";
					while ($row = $result->fetch_assoc()){
						$fpageid = $row["module_id"];
					}
					$result->free();
					$flogindate = date("Y-m-d H:i:s");
					$query = "UPDATE tuser SET user_status = 'Online', module_id = '$fpageid', user_accessdate = '$flogindate' WHERE user_id='$login'";
					if (!$result = $mysqli->query($query)){
						printf("Errormessage: %s\n", $mysqli->error);
					}
				}
				else{
					// if ($_SESSION["login"]!="CREATOR"){
						header("Location: 404.php");
					// }
				}
			}
			else{
				printf("Errormessage: %s\n", $mysqli->error);
			}
		}
		else{
			header("Location: login.php?err=1");
		}
	}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>AdminLTE 2 | Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.4.0 -->
    <link href="plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 -->
    <link href="plugins/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
		<!-- DataTables -->
    <link href="plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
    <!-- Date Picker -->
    <link href="plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <!-- Daterange picker -->
    <link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    

		<!-- jQuery 2.1.4 -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
		
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
	
	<body class="skin-blue sidebar-mini">
		<div class="wrapper">

			<header class="main-header">
				<!-- Logo -->	
				<a href="index.php" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><b>R</b>S</span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg"><b>Rice</b>Shop</span>
				</a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="dist/img/avatar3.png" class="user-image" alt="User Image" />
                  <span class="hidden-xs">AdyGun</span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="dist/img/avatar3.png" class="img-circle" alt="User Image" />
                    <p>
                      Ady Gunawan - Web Developer
                      <!--<small>Member since Aug. 2015</small>-->
                    </p>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <!--<div class="pull-left">
                      <a href="#" class="btn btn-default btn-flat">Profile</a>
                    </div>-->
                    <div class="pull-right">
                      <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
			
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MENU NAVIGASI</li>
						<?php
							$login = $_SESSION["login"]['user_id'];
							$query = "SELECT l.level_id, m.*
												FROM tlevel_access l
												INNER JOIN tmodule m ON l.module_id=m.module_id
												INNER JOIN tuser u ON u.level_id=l.level_id		
												WHERE u.user_id = '$login' AND module_issub = 0
												ORDER BY m.module_category, m.module_name
											 ";
							if ($result = $mysqli->query($query)){
								if ($result->num_rows > 0){	
									$tcategory = "";
									$ctr = 0;
									while ($row = $result->fetch_assoc()){
										$active_mark = '';
										if ($pagename == $row['module_pageurl']) $active_mark = 'active';
										if ($row['module_category'] != $tcategory){
											$nav_icon = getIconName($row['module_category']);
											if ($ctr != 0){
												echo '</ul></li>';
											}
											echo '<li class="treeview '.$active_mark.'">';
											echo 	 '<a href="#">';
											echo 	 	 '<i class="fa fa-'.$nav_icon.'"></i> <span>'.$row['module_category'].'</span> <i class="fa fa-angle-left pull-right"></i>';
											echo 	 '</a>';
											echo 	 '<ul class="treeview-menu">';
											
											$tcategory = $row['module_category'];
										}
										echo '<li class="'.$active_mark.'"><a href="'.$row['module_pageurl'].'"><i class="fa fa-circle-o"></i> '.$row['module_name'].'</a></li>';
										$ctr++;
										if ($ctr == $result->num_rows){
											echo '</ul></li>';
										}
									}
									$result->free();
								}
							}
							else{
								printf("Errormessage: %s\n", $mysqli->error);
							}
						?>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
			
			