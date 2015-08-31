<?php include('header.php'); ?>

	<script type="text/javascript">		
		function submitAjaxForm(){
			$.ajax({                                      
				url: 'ajax/module_submitForm.php',                  
				type: 'post',
				data: $('#box_module_form form').serialize(),
				dataType: 'json',         
				beforeSend: function(){
					helper.showBoxLoading('#box_module_form');
				},
				success: function(result){
					helper.removeBoxLoading('#box_module_form');
					helper.showAlertMessage(result.alert);
					if (result.type){
						doCommand('cancel');
						refreshModuleListTable($('#hidsearch').val(), $('#hidcurrentpage').val());
					}
				}
			});			
		}
		function doCommand(command){
			if (command == 'cancel'){
				validator.message.removeAll($('#box_module_form form'));
				$('#box_module_form form').clearForm();
				$('#box_module_form form #module_issub_no').prop('checked', true);
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
					if (!validator.validCheck($('#box_module_form form')))
						return false;
				}
				$('#hidcommand').val(command);
				submitAjaxForm();
			}
		}
		function refreshModuleListTable(contSearch, contPage){
			$.ajax({
				url: 'ajax/module_getModuleListTable.php',
				type: 'post',
				data: {
					page: contPage,
					search: contSearch
				},
				dataType: 'json',
				beforeSend: function(){
					helper.showBoxLoading('#box_module_list');
				},
				success: function(result)      
				{
					helper.removeBoxLoading('#box_module_list');
					$('#hidsearch').val(contSearch);
					$('#hidcurrentpage').val(contPage);
					$('#module_list_table tbody tr').remove();
					if (!result.type){
						helper.showAlertMessage(result.alert);
						$('#module_list_table tbody').append('<tr><td colspan="7">Module tidak ditemukan</td></tr>');
					}
					else{
						/* Table Data */
						var tabledata = result.data.tabledata;
						for (var i=0; i<result.data.tabledata.length; i++){
							var tabContent = '<tr data-mx-id="'+tabledata[i].module_id+'">';
							tabContent += '<td>'+tabledata[i].module_id+'</td>';
							tabContent += '<td>'+tabledata[i].module_name+'</td>';
							tabContent += '<td>'+tabledata[i].category+'</td>';
							tabContent += '<td>'+tabledata[i].description+'</td>';
							tabContent += '<td>'+tabledata[i].pageurl+'</td>';
							tabContent += '<td>'+tabledata[i].issub+'</td>';
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
							$('#module_list_table tbody').append(tabContent);
						}
						$('#module_list_table tbody a[data-mx-command]').click(function(){
							fillTextField($(this).parents('tr').attr('data-mx-id'),$(this).attr('data-mx-command'));
						});
						/* Table Paging */
						var totalpage = result.data.totalpage;
						$('#module_list_paging').html(helper.createPaginationBar(contPage, totalpage));
						$('#module_list_paging .pagination li:not(.active,.disabled,[data-mx-disabled]) a').click(function(e){
							e.preventDefault();
							refreshModuleListTable($('#module_search').val(), $(this).attr('data-mx-page'));
						});
					}
				}
			});
		}
		function fillTextField(content,command){
			$.ajax({
				url: 'ajax/global_getTableData.php',
				type: 'post',
				data: {
					id: content,
					table: 'tmodule',
					field: 'module_id'
				},        
				dataType: 'json',         
				beforeSend: function(){
					helper.showBoxLoading('#box_module_form');
				},
				success: function(result){
					helper.removeBoxLoading('#box_module_form');
					if (!result.type){
						helper.showAlertMessage(result.alert);
					}
					else
					{
						$('#btncreate').addClass('hide');
						validator.message.removeAll($('#box_module_form form'));
						$('#hidcommand').val(command);
						$('#hidid').val(result.data[0]);
						$('#module_name').val(result.data[1]);
						$('#hidname').val(result.data[1]);
						$('#module_category').val(result.data[2]);
						$('#module_description').val(result.data[3]);
						$('#module_pageurl').val(result.data[4]);
						$('#hidpageurl').val(result.data[4]);
						if (result.data[5] == '1')
							$('#module_issub_yes').prop('checked', true);
						else
							$('#module_issub_no').prop('checked', true);
						
						if (command == 'update'){
							$('#btnupdate').removeClass('hide');
							$('#btndelete').addClass('hide');
							$('#module_name, #module_category, #module_pageurl').blur();
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
			refreshModuleListTable($('#module_search').val(), 1);
			$('#box_module_form .box-footer button[data-mx-command]').click(function(e){
				e.preventDefault();
				doCommand($(this).attr('data-mx-command'));
			});
			$('#module_search').keypress(function(e){
				if(e.which == 13) {
					e.preventDefault();
					refreshModuleListTable($(this).val(), 1);
				}
			});
			$('#module_search_btn').click(function(e){
				e.preventDefault();
				refreshModuleListTable($(this).val(), 1);
			});
			/* Form Validator */
			$('#module_name').blur(function(){
				if ($('#hidcommand').val() == 'create')
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Module Name', 4, 'tmodule', 'module_name', '', 'no');
				else
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Module Name', 4, 'tmodule', 'module_name', $('#hidname').val(), 'no');
			});
			$('#module_pageurl').blur(function(){
				if ($('#hidcommand').val() == 'create')
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Module PageURL', 6, 'tmodule', 'module_pageurl', '', 'no');
				else
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Module PageURL', 6, 'tmodule', 'module_pageurl', $('#hidpageurl').val(), 'no');
			});
			$('#module_category').blur(function(){
					validator.check.minLength($(this).parents('.form-group'), 'Module Category', 4);
			});
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
			<div id="content_alert">
				
			</div>
			<!-- CREATE NEW MODULE -->
			<div class="box box-primary" id="box_module_form">
				<div class="box-header with-border">
					<h3 class="box-title">Tambah Module Baru</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<form class="form-horizontal" role="form">
					<div class="box-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group" id="fgname">
									<label for="module_name" class="col-sm-4 control-label">Module Name :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control input-xs" id="module_name" name="module[name]" maxlength="30">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="fgcategory">
									<label for="module_category" class="col-sm-4 control-label">Module Category :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="module_category" name="module[category]" maxlength="30">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group" id="fgpageurl">
									<label for="module_pageurl" class="col-sm-4 control-label">Module PageURL :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="module_pageurl" name="module[pageurl]" maxlength="40">
										<span class="help-block"></span>
									</div>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Module Description :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="3" id="module_description" name="module[description]"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Is sub?</label>
									<div class="col-sm-8">
										<div class="radio">
											<label>
												<input type="radio" name="module[issub]" id="module_issub_no" value="0" checked />
												No
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" name="module[issub]" id="module_issub_yes" value="1" />
												Yes
											</label>
										</div>
									</div>
								</div>
							</div>
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
						<input type="hidden" id="hidpageurl" name="hidden[pageurl]">
					</div>
				</form>
			</div><!-- /.create-new-module -->
			<!-- MODULE LIST -->
			<div class="box box-info box-solid" id="box_module_list">
				<div class="box-header with-border">
					<h3 class="box-title">Daftar Module</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
					<div class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label">Search</label>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" id="module_search" class="form-control" />
									<input type="hidden" id="hidsearch">
									<input type="hidden" id="hidcurrentpage">
									<span class="input-group-btn">
										<span class="input-group-btn">
                      <button class="btn btn-info" type="button" id="module_search_btn"><i class="fa fa-search"></i></button>
                    </span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<table id="module_list_table" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Module ID</th>
								<th>Module Name</th>
								<th>Category</th>
								<th>Description</th>
								<th>PageURL</th>
								<th>Is Sub?</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<!-- Table List -->
						</tbody>
					</table>
					<div id="module_list_paging" class="text-center">
						<!-- Pagination Bar -->
					</div>
				</div>
			</div><!-- /.create-new-module -->
		</section><!--- /.main-content -->
	</div><!-- /.content-wrapper -->
	
<?php include('footer.php'); ?>