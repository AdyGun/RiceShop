<?php include('header.php'); ?>

	<script type="text/javascript">
		$(document).ready(function(){
			$("#example1").DataTable();
		});
	</script>

	<!---------------------- Content Wrapper. Contains page content ---------------------->
	<div class="content-wrapper">
		<!---------------------- Content Header (Page header) ---------------------->
		<section class="content-header">
			<h1>
				Utility
				<small>Module</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Utility</li>
				<li class="active">Module</li>
			</ol>
		</section><!-- /.content-header -->
		
		<!---------------------- Main content ---------------------->
		<section class="content">
			<!-- CREATE NEW MODULE -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Tambah Module Baru</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<form role="form">
					<div class="box-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="module_name">Module Name</label>
									<input type="text" class="form-control" id="module_name" name="module[name]">
								</div>
								<div class="form-group">
									<label for="module_category">Module Category</label>
									<input type="text" class="form-control" id="module_category" name="module[category]">
								</div>
								<div class="form-group">
									<label for="module_pageurl">Module PageURL</label>
									<input type="text" class="form-control" id="module_pageurl" name="module[pageurl]">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label>Module Description</label>
									<textarea class="form-control" rows="3" id="module_description" name="module[description]"></textarea>
								</div>
								<div class="form-group">
									<label>Is sub?</label>
									<div class="radio">
										<label>
											<input type="radio" name="module[issub]" id="module_issub_no" value="no" checked />
											No
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="module[issub]" id="module_issub_yes" value="yes" />
											Yes
										</label>
									</div>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
					<div class="box-footer">
						<button type="submit" class="btn btn-primary">Tambah Baru</button>
						<button type="clear" class="btn btn-default">Batal</button>
					</div>
				</form>
			</div><!-- /.create-new-module -->
			<!-- MODULE LIST -->
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Daftar Module</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Rendering engine</th>
								<th>Browser</th>
								<th>Platform(s)</th>
								<th>Engine version</th>
								<th>CSS grade</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Trident</td>
								<td>Internet
									Explorer 4.0</td>
								<td>Win 95+</td>
								<td> 4</td>
								<td>X</td>
							</tr>
							<tr>
								<td>Trident</td>
								<td>Internet
									Explorer 5.0</td>
								<td>Win 95+</td>
								<td>5</td>
								<td>C</td>
							</tr>
							<tr>
								<td>Trident</td>
								<td>Internet
									Explorer 5.5</td>
								<td>Win 95+</td>
								<td>5.5</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Trident</td>
								<td>Internet
									Explorer 6</td>
								<td>Win 98+</td>
								<td>6</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Trident</td>
								<td>Internet Explorer 7</td>
								<td>Win XP SP2+</td>
								<td>7</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Trident</td>
								<td>AOL browser (AOL desktop)</td>
								<td>Win XP</td>
								<td>6</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Firefox 1.0</td>
								<td>Win 98+ / OSX.2+</td>
								<td>1.7</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Firefox 1.5</td>
								<td>Win 98+ / OSX.2+</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Firefox 2.0</td>
								<td>Win 98+ / OSX.2+</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Firefox 3.0</td>
								<td>Win 2k+ / OSX.3+</td>
								<td>1.9</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Camino 1.0</td>
								<td>OSX.2+</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Camino 1.5</td>
								<td>OSX.3+</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Netscape 7.2</td>
								<td>Win 95+ / Mac OS 8.6-9.2</td>
								<td>1.7</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Netscape Browser 8</td>
								<td>Win 98SE+</td>
								<td>1.7</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Netscape Navigator 9</td>
								<td>Win 98+ / OSX.2+</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.0</td>
								<td>Win 95+ / OSX.1+</td>
								<td>1</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.1</td>
								<td>Win 95+ / OSX.1+</td>
								<td>1.1</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.2</td>
								<td>Win 95+ / OSX.1+</td>
								<td>1.2</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.3</td>
								<td>Win 95+ / OSX.1+</td>
								<td>1.3</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.4</td>
								<td>Win 95+ / OSX.1+</td>
								<td>1.4</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.5</td>
								<td>Win 95+ / OSX.1+</td>
								<td>1.5</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.6</td>
								<td>Win 95+ / OSX.1+</td>
								<td>1.6</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.7</td>
								<td>Win 98+ / OSX.1+</td>
								<td>1.7</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Mozilla 1.8</td>
								<td>Win 98+ / OSX.1+</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Seamonkey 1.1</td>
								<td>Win 98+ / OSX.2+</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Gecko</td>
								<td>Epiphany 2.20</td>
								<td>Gnome</td>
								<td>1.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Webkit</td>
								<td>Safari 1.2</td>
								<td>OSX.3</td>
								<td>125.5</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Webkit</td>
								<td>Safari 1.3</td>
								<td>OSX.3</td>
								<td>312.8</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Webkit</td>
								<td>Safari 2.0</td>
								<td>OSX.4+</td>
								<td>419.3</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Webkit</td>
								<td>Safari 3.0</td>
								<td>OSX.4+</td>
								<td>522.1</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Webkit</td>
								<td>OmniWeb 5.5</td>
								<td>OSX.4+</td>
								<td>420</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Webkit</td>
								<td>iPod Touch / iPhone</td>
								<td>iPod</td>
								<td>420.1</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Webkit</td>
								<td>S60</td>
								<td>S60</td>
								<td>413</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera 7.0</td>
								<td>Win 95+ / OSX.1+</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera 7.5</td>
								<td>Win 95+ / OSX.2+</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera 8.0</td>
								<td>Win 95+ / OSX.2+</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera 8.5</td>
								<td>Win 95+ / OSX.2+</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera 9.0</td>
								<td>Win 95+ / OSX.3+</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera 9.2</td>
								<td>Win 88+ / OSX.3+</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera 9.5</td>
								<td>Win 88+ / OSX.3+</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Opera for Wii</td>
								<td>Wii</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Nokia N800</td>
								<td>N800</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Presto</td>
								<td>Nintendo DS browser</td>
								<td>Nintendo DS</td>
								<td>8.5</td>
								<td>C/A<sup>1</sup></td>
							</tr>
							<tr>
								<td>KHTML</td>
								<td>Konqureror 3.1</td>
								<td>KDE 3.1</td>
								<td>3.1</td>
								<td>C</td>
							</tr>
							<tr>
								<td>KHTML</td>
								<td>Konqureror 3.3</td>
								<td>KDE 3.3</td>
								<td>3.3</td>
								<td>A</td>
							</tr>
							<tr>
								<td>KHTML</td>
								<td>Konqureror 3.5</td>
								<td>KDE 3.5</td>
								<td>3.5</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Tasman</td>
								<td>Internet Explorer 4.5</td>
								<td>Mac OS 8-9</td>
								<td>-</td>
								<td>X</td>
							</tr>
							<tr>
								<td>Tasman</td>
								<td>Internet Explorer 5.1</td>
								<td>Mac OS 7.6-9</td>
								<td>1</td>
								<td>C</td>
							</tr>
							<tr>
								<td>Tasman</td>
								<td>Internet Explorer 5.2</td>
								<td>Mac OS 8-X</td>
								<td>1</td>
								<td>C</td>
							</tr>
							<tr>
								<td>Misc</td>
								<td>NetFront 3.1</td>
								<td>Embedded devices</td>
								<td>-</td>
								<td>C</td>
							</tr>
							<tr>
								<td>Misc</td>
								<td>NetFront 3.4</td>
								<td>Embedded devices</td>
								<td>-</td>
								<td>A</td>
							</tr>
							<tr>
								<td>Misc</td>
								<td>Dillo 0.8</td>
								<td>Embedded devices</td>
								<td>-</td>
								<td>X</td>
							</tr>
							<tr>
								<td>Misc</td>
								<td>Links</td>
								<td>Text only</td>
								<td>-</td>
								<td>X</td>
							</tr>
							<tr>
								<td>Misc</td>
								<td>Lynx</td>
								<td>Text only</td>
								<td>-</td>
								<td>X</td>
							</tr>
							<tr>
								<td>Misc</td>
								<td>IE Mobile</td>
								<td>Windows Mobile 6</td>
								<td>-</td>
								<td>C</td>
							</tr>
							<tr>
								<td>Misc</td>
								<td>PSP browser</td>
								<td>PSP</td>
								<td>-</td>
								<td>C</td>
							</tr>
							<tr>
								<td>Other browsers</td>
								<td>All others</td>
								<td>-</td>
								<td>-</td>
								<td>U</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div><!-- /.create-new-module -->
		</section><!--- /.main-content -->
	</div><!-- /.content-wrapper -->
	
<?php include('footer.php'); ?>