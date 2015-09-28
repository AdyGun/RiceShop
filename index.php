<?php include('header.php'); ?>

		<!---------------------- Main content ---------------------->
		<section class="content">
			<div id="content_alert">
				<?php if (isset($_GET['err']) && $_GET['err'] == '1'){ ?>
					<div class="callout callout-danger">
						<h4><i class="fa fa-exclamation"></i> Perhatian:</h4>
						Anda tidak berhak untuk mengakses halaman ini!
					</div>
				<?php } ?>
			</div>
			
			Selamat Datang!
		</section><!--- /.main-content -->
	</div><!-- /.content-wrapper -->
	
<?php include('footer.php'); ?>