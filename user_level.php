<?php include('header.php'); ?>

	<script type="text/javascript">		
		function submitAjaxForm(){
			$.ajax({                                      
				url: 'ajax/userlevel_submitForm.php',                  
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
				url: 'ajax/userlevel_getTableData.php',
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
						$('#table_data_list tbody').append('<tr><td colspan="4">Level User tidak ditemukan</td></tr>');
					}
					else{
						/* Table Data */
						var tabledata = result.data.tabledata;
						for (var i=0; i<result.data.tabledata.length; i++){
							var tabContent = '<tr data-mx-id="'+tabledata[i].level_id+'">';
							tabContent += '<td>'+tabledata[i].level_id+'</td>';
							tabContent += '<td>'+tabledata[i].level_name+'</td>';
							tabContent += '<td>'+tabledata[i].description+'</td>';
							tabContent += '<td>';
							tabContent += 	'<div class="btn-group">';
							tabContent += 		'<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">';
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
				url: 'ajax/global_getSingleRowData.php',
				type: 'post',
				data: {
					id: content,
					table: 'tuser_level',
					field: 'level_id'
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
						fillCheckBoxModule(content);
						$('#btncreate').addClass('hide');
						validator.message.removeAll($('#box_input_form form'));
						$('#hidcommand').val(command);
						$('#hidid').val(result.data[0]);
						$('#input_name').val(result.data[1]);
						$('#hidname').val(result.data[1]);
						$('#input_description').val(result.data[2]);
						if (command == 'update'){
							$('#btnupdate').removeClass('hide');
							$('#btndelete').addClass('hide');
							$('#input_name').blur();
						}
						else if (command == 'delete'){
							$('#btndelete').removeClass('hide');
							$('#btnupdate').addClass('hide');
						}
					}		
				}
			});
		}
		function fillCheckBoxModule(content){
			$.ajax({
				url: 'ajax/userlevel_getAccessModule.php',
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
						$('#box_input_form form input[type="checkbox"]').removeAttr('checked');
						for (var i = 0; i < result.data.length; i++){
							$('#box_input_form form input[type="checkbox"][value="'+result.data[i][0]+'"]').prop('checked', true);
							if (result.data[i][1] == 1) 
								$('#table_module_list input[type="checkbox"][name="input['+result.data[i][0]+'][c]"]').prop('checked', true);
							if (result.data[i][2] == 1) 
								$('#table_module_list input[type="checkbox"][name="input['+result.data[i][0]+'][r]"]').prop('checked', true);
							if (result.data[i][3] == 1) 
								$('#table_module_list input[type="checkbox"][name="input['+result.data[i][0]+'][u]"]').prop('checked', true);
							if (result.data[i][4] == 1) 
								$('#table_module_list input[type="checkbox"][name="input['+result.data[i][0]+'][d]"]').prop('checked', true);
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
			$('#table_module_list input[type="checkbox"][name="input[module][]"]').click(function(){
				if ($(this).prop('checked')){
					$(this).parents('tr').find('input[type="checkbox"]:not([name="input[module][]"])').prop('checked', true).prop('disabled', false);
				}
				else{
					$(this).parents('tr').find('input[type="checkbox"]:not([name="input[module][]"])').prop('checked', false).prop('disabled', true);
				}
			});
			/* Form Validator */
			$('#input_name').blur(function(){
				if ($('#hidcommand').val() == 'create')
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Nama Level User', 4, 'tuser_level', 'level_name', '', 'level_deletedate');
				else
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Nama Level User', 4, 'tuser_level', 'level_name', $('#hidname').val(), 'level_deletedate');
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
					<h3 class="box-title">Tambah Level User Baru</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<form class="form-horizontal" role="form">
					<div class="box-body">
						<div class="col-md-12">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input_name">Nama Level User :</label>
								<div class="col-sm-4">
									<input type="text" autofocus class="form-control" id="input_name" name="input[name]" maxlength="30">
								</div>
								<span class="help-block"></span>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Keterangan :</label>
								<div class="col-sm-4">
									<textarea class="form-control" rows="2" id="input_description" name="input[description]"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Hak Akses :</label>
								<div class="col-sm-10">
									<table id="table_module_list" class="table table-bordered table-striped">
										<thead>
											<tr>
												<th>#</th>
												<th>Kategori</th>
												<th>Nama</th>
												<th>Keterangan</th>
												<th>Tambah</th>
												<th>Baca</th>
												<th>Ubah</th>
												<th>Hapus</th>
											</tr>
										</thead>
										<tbody>
											<!-- Table List -->
											<?php
												$query = "SELECT * FROM tmodule ORDER BY module_category";
												 // WHERE module_id<>'MOD0000'
												if ($result = $mysqli->query($query)){
													if ($result->num_rows > 0){
														while ($row = $result->fetch_assoc()){
															echo '<tr>';
															echo '<td><input type="checkbox" name="input[module][]" value="'.$row["module_id"].'" /></td>';
															echo '<td>'.$row['module_category'].'</td>';
															echo '<td>'.$row['module_name'].'</td>';
															echo '<td>'.$row['module_description'].'</td>';
															if ($row['module_hascrud'] == 1){
																echo '<td><input type="checkbox" name="input['.$row["module_id"].'][c]" disabled /></td>';
																echo '<td><input type="checkbox" name="input['.$row["module_id"].'][r]" disabled /></td>';
																echo '<td><input type="checkbox" name="input['.$row["module_id"].'][u]" disabled /></td>';
																echo '<td><input type="checkbox" name="input['.$row["module_id"].'][d]" disabled /></td>';																
															}
															else{
																echo '<td></td><td></td><td></td><td></td>';
															}
															echo '</tr>';
														}
														$result->free();
													}
												}
											?>
										</tbody>
									</table>
								</div>
							</div>
							<!-- <div class="form-group">
								<label class="col-sm-2 control-label">Hak Akses :</label>
								<div class="col-sm-10">
									<?php
										$query = "SELECT * FROM tmodule ORDER BY module_category";
										 // WHERE module_id<>'MOD0000'
										if ($result = $mysqli->query($query)){
											if ($result->num_rows > 0){
												while ($row = $result->fetch_assoc()){
													echo '<div class="checkbox">';
													echo 	 '<label>';
													echo 	 	 '<input type="checkbox" name="input[module][]" value="'.$row["module_id"].'" />';
													echo 	 	 '['.$row['module_category'].'] <strong>'.$row['module_name'].'</strong> - '.$row['module_description'];
													echo 	 '</label>';
													echo '</div>';
												}
												$result->free();
											}
										}
									?>
								</div>
							</div> -->
						</div>
					</div><!-- /.box-body -->
					<div class="box-footer">
						<button type="submit" data-mx-command="create" id="btncreate" class="btn btn-primary">Tambah Baru</button>
						<button type="submit" data-mx-command="update" id="btnupdate" class="btn btn-success hide">Ubah</button>
						<button type="submit" data-mx-command="delete" id="btndelete" class="btn btn-danger hide">Hapus</button>
						<button type="clear" data-mx-command="cancel" id="btncancel" class="btn btn-default">Batal</button>
						<input type="hidden" id="hidcommand" name="hidden[command]">
						<input type="hidden" id="hidid" name="hidden[id]">
						<input type="hidden" id="hidname" name="hidden[name]">
					</div>
				</form>
			</div><!-- /.create-new-module -->
			<!-- TABLE DATA LIST -->
			<div class="box box-info box-solid" id="box_table_list">
				<div class="box-header with-border">
					<h3 class="box-title">Daftar Module</h3>
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
								<th>Kode Level User</th>
								<th>Nama Level User</th>
								<th>Keterangan</th>
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
			</div><!-- /.create-new-module -->
		</section><!--- /.main-content -->
	</div><!-- /.content-wrapper -->
	
<?php include('footer.php'); ?>