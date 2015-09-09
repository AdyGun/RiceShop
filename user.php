<?php include('header.php'); ?>

	<script type="text/javascript">		
		function submitAjaxForm(){
			$.ajax({                                      
				url: 'ajax/user_submitForm.php',                  
				type: 'post',
				data: $('#box_input_form form').serialize(),
				dataType: 'json',         
				beforeSend: function(){
					helper.showBoxLoading('#box_input_form');
				},
				success: function(result){
					helper.removeBoxLoading('#box_input_form');
					helper.showAlertMessage(result.alert);
					if (result.type){
						doCommand('cancel');
						refreshDataTable($('#hidsearch').val(), $('#hidcurrentpage').val());
					}
				}
			});			
		}
		function doCommand(command){
			if (command == 'cancel'){
				validator.message.removeAll($('#box_input_form form'));
				$('#box_input_form form').clearForm();
				$('#box_input_form form input:not([disabled])').eq(0).focus();
				$('.box-footer input[type="hidden"]').val('');
				$('#btncreate').removeClass('hide');
				$('#btnupdate').addClass('hide');
				$('#btndelete').addClass('hide');
				$('#hidcommand').val('create');
			}
			else{				
				if (command == 'delete'){
					if (!confirm('Apakah anda yakin untuk menghapus data ini?'))
						return false;
				}
				else{
					if (!validator.validCheck($('#box_input_form form')))
						return false;
				}
				$('#hidcommand').val(command);
				submitAjaxForm();
			}
		}
		function refreshDataTable(contSearch, contPage){
			$.ajax({
				url: 'ajax/user_getTableData.php',
				type: 'post',
				data: {
					page: contPage,
					search: contSearch
				},
				dataType: 'json',
				beforeSend: function(){
					helper.showBoxLoading('#box_table_list');
				},
				success: function(result){
					helper.removeBoxLoading('#box_table_list');
					$('#hidsearch').val(contSearch);
					$('#hidcurrentpage').val(contPage);
					$('#table_data_list tbody tr').remove();
					if (!result.type){
						helper.showAlertMessage(result.alert);
						$('#table_data_list tbody').append('<tr><td colspan="4">User tidak ditemukan</td></tr>');
					}
					else{
						/* Table Data */
						var tabledata = result.data.tabledata;
						for (var i=0; i<result.data.tabledata.length; i++){
							var tabContent = '<tr data-mx-id="'+tabledata[i].user_id+'">';
							tabContent += '<td>'+tabledata[i].user_name+'</td>';
							tabContent += '<td>'+tabledata[i].user_completename+'</td>';
							tabContent += '<td>'+tabledata[i].level_name+'</td>';
							tabContent += '<td>';
							tabContent += 	'<div class="btn-group">';
							tabContent += 		'<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">';
							tabContent += 			'<span class="caret"></span>';
							tabContent += 		'</button>';
							tabContent += 		'<ul class="dropdown-menu">';
							tabContent += 			'<li class="bg-success"><a href="javascript:void(0)" data-mx-command="update">Ubah</a></li>';
							tabContent += 			'<li class="bg-danger"><a href="javascript:void(0)" data-mx-command="delete">Hapus</a></li>';
							tabContent += 		'</ul>';
							tabContent += 	'</div>';
							tabContent += '</td>';
							tabContent += '</tr>';
							$('#table_data_list tbody').append(tabContent);
						}
						$('#table_data_list tbody a[data-mx-command]').click(function(){
							fillTextField($(this).parents('tr').attr('data-mx-id'),$(this).attr('data-mx-command'));
						});
						/* Table Paging */
						var totalpage = result.data.totalpage;
						$('#table_data_paging').html(helper.createPaginationBar(contPage, totalpage));
						$('#table_data_paging .pagination li:not(.active,.disabled,[data-mx-disabled]) a').click(function(e){
							e.preventDefault();
							refreshDataTable($('#input_search').val(), $(this).attr('data-mx-page'));
						});
					}
				}
			});
		}
		function fillTextField(content,command){
			$.ajax({
				url: 'ajax/user_fillFormData.php',
				type: 'post',
				data: {
					id: content,
				},        
				dataType: 'json',         
				beforeSend: function(){
					helper.showBoxLoading('#box_input_form');
				},
				success: function(result){
					helper.removeBoxLoading('#box_input_form');
					if (!result.type){
						helper.showAlertMessage(result.alert);
					}
					else{
						$('#btncreate').addClass('hide');
						validator.message.removeAll($('#box_input_form form'));
						$('#hidcommand').val(command);
						$('#hidid').val(result.data[0]);
						$('#input_name').val(result.data[1]);
						$('#hidname').val(result.data[1]);
						$('#input_completename').val(result.data[2]);
						$('#dd_level').val(result.data[3]);
						if ($('#dd_level').val() != result.data[3]){
							$('#dd_level').val($('#dd_level option').eq(0).val());
						}
						if (command == 'update'){
							$('#btnupdate').removeClass('hide');
							$('#btndelete').addClass('hide');
							$('#input_name, #input_completename').blur();
						}
						else if (command == 'delete'){
							$('#btndelete').removeClass('hide');
							$('#btnupdate').addClass('hide');
						}
					}		
				}
			});
		}
		$(document).ready(function(){
			refreshDataTable($('#input_search').val(), 1);
			$('#box_input_form .box-footer button[data-mx-command]').click(function(e){
				e.preventDefault();
				doCommand($(this).attr('data-mx-command'));
			});
			$('#input_search').keypress(function(e){
				if(e.which == 13) {
					e.preventDefault();
					refreshDataTable($(this).val(), 1);
				}
			});
			$('#btnsearch').click(function(e){
				e.preventDefault();
				refreshDataTable($(this).val(), 1);
			});
			/* Form Validator */
			$('#input_name').blur(function(){
				if ($('#hidcommand').val() == 'create')
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Nama Login', 4, 'tuser', 'user_name', '', 'user_deletedate');
				else
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Nama Login', 4, 'tuser', 'user_name', $('#hidname').val(), 'user_deletedate');
			});
			$('#input_completename').blur(function(){
				validator.check.minLength($(this).parents('.form-group'), 'Nama Lengkap', 4);
			});
		});
	</script>
	

		<!---------------------- Main content ---------------------->
		<section class="content">
			<div id="content_alert">
				
			</div>
			<!-- CREATE NEW FORM -->
			<div class="box box-primary" id="box_input_form">
				<div class="box-header with-border">
					<h3 class="box-title">Form User</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<form class="form-horizontal" role="form">
					<div class="box-body">
						<div class="col-md-12">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_name">Nama Login :</label>
								<div class="col-sm-4">
									<input type="text" autofocus class="form-control" id="input_name" name="input[name]" maxlength="30">
								</div>
								<span class="help-block"></span>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_completename">Nama Lengkap :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input_completename" name="input[completename]" maxlength="40">
								</div>
								<span class="help-block"></span>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Level Akses :</label>
								<div class="col-sm-4">
									<select class="form-control" id="dd_level" name="input[level]">
										<?php
											$query = "SELECT level_id, level_name FROM tuser_level WHERE level_deletedate IS NULL ORDER BY level_name ASC";
											if ($result = $mysqli->query($query)){
												if ($result->num_rows > 0){
													while ($row = $result->fetch_assoc()){
														echo '<option value="'.$row['level_id'].'">'.$row['level_name'].'</option>';
													}
													$result->free();
												}
												else{
													echo '<option value="0">-- Data kosong--</option>';
												}
											}
										?>
									</select>
								</div>
							</div>
						</div>
					</div><!-- /.box-body -->
					<div class="box-footer">
						<?php 
							if ($_SESSION['access'][$pagedata['id']]['create'] == 1) {
								echo '<button type="submit" data-mx-command="create" id="btncreate" class="btn btn-primary">Tambah Baru</button>';
							} 
							if ($_SESSION['access'][$pagedata['id']]['update'] == 1) { 
								echo '<button type="submit" data-mx-command="update" id="btnupdate" class="btn btn-success hide">Ubah</button>';
							} 
							if ($_SESSION['access'][$pagedata['id']]['delete'] == 1) { 
								echo '<button type="submit" data-mx-command="delete" id="btndelete" class="btn btn-danger hide">Hapus</button>';
							} 
						?>
						<button type="clear" data-mx-command="cancel" id="btncancel" class="btn btn-default">Batal</button>
						<input type="hidden" id="hidcommand" name="hidden[command]">
						<input type="hidden" id="hidid" name="hidden[id]">
						<input type="hidden" id="hidname" name="hidden[name]">
					</div>
				</form>
			</div><!-- /.create-new-form -->
			
			<?php if ($_SESSION['access'][$pagedata['id']]['read'] == 1) { ?>
			<!-- TABLE DATA LIST -->
			<div class="box box-info box-solid" id="box_table_list">
				<div class="box-header with-border">
					<h3 class="box-title">Daftar User</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label">Cari :</label>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" id="input_search" class="form-control" />
									<input type="hidden" id="hidsearch">
									<input type="hidden" id="hidcurrentpage">
									<span class="input-group-btn">
										<span class="input-group-btn">
                      <button class="btn btn-info" type="button" id="btnsearch"><i class="fa fa-search"></i></button>
                    </span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<table id="table_data_list" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Nama Login</th>
								<th>Nama Lengkap</th>
								<th>Level User</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<!-- Table List -->
						</tbody>
					</table>
					<div id="table_data_paging" class="text-center">
						<!-- Pagination Bar -->
					</div>
				</div>
			</div><!-- /.table-data-list -->
			<?php } ?>
		</section><!--- /.main-content -->
	</div><!-- /.content-wrapper -->
	
<?php include('footer.php'); ?>