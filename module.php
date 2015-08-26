<?php include('header.php'); ?>

	<script type="text/javascript">		
		function submitAjaxForm(){
			console.log('Submit Function.');
			$.ajax({                                      
				url: 'ajax/module_submitForm.php',                  
				type: 'post',
				data: $('#box_module_form form').serialize(),
				dataType: 'json',         
				beforeSend: function(){
					helper.showBoxLoading('#box_module_form');
				},
				success: function(result){		
					console.log(result);
					helper.removeBoxLoading('#box_module_form');
					helper.showAlertMessage(result.alert);
					if (result.type){
						console.log('sukses coi');
						doCommand('cancel');
						refreshModuleListTable($('#hidsearch').val(), $('#hidcurrentpage').val());
					}
				}
			});			
		}
		function doCommand(command){
			console.log('Do '+command+' function.');
			if (command == 'cancel'){
				//remove all validation message
				$('#box_module_form form').clearForm();
				$('#box_module_form form #module_issub_no').prop('checked', true);
				$('.box-footer input[type="hidden"]').val('');
				$('#btncreate').removeClass('hide');
				$('#btnupdate').addClass('hide');
				$('#btndelete').addClass('hide');
			}
			else{				
				if (command == 'delete'){
					if (!confirm("Apakah anda yakin untuk menghapus data ini?")) 
						return false;
				}
				$('#hidcommand').val(command);
				submitAjaxForm();
			}
		}
		function refreshModuleListTable(contSearch, contPage){
			console.log('Refresh Module list table');
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
							var tabContent = '<tr mx-id="'+tabledata[i].module_id+'">';
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
							tabContent += 			'<li class="bg-success"><a href="javascript:void(0)" mx-command="edit">Ubah</a></li>';
							tabContent += 			'<li class="bg-danger"><a href="javascript:void(0)" mx-command="delete">Hapus</a></li>';
							tabContent += 		'</ul>';
							tabContent += 	'</div>';
							tabContent += '</td>';
							tabContent += '</tr>';
							$('#module_list_table tbody').append(tabContent);
						}
						$('#module_list_table tbody a[mx-command]').click(function(){
							fillTextField($(this).parents('tr').attr('mx-id'),$(this).attr('mx-command'));
						});
						/* Table Paging */
						var totalpage = result.data.totalpage;
						$('#module_list_paging').html(helper.createPaginationBar(contPage, totalpage));
						$('#module_list_paging .pagination li:not(.active,.disabled,[mx-disabled]) a').click(function(e){
							e.preventDefault();
							refreshModuleListTable($('#module_search').val(), $(this).attr('mx-page'));
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
						// Remove all validation message
						if (command == 'edit'){
							$('#btnupdate').removeClass('hide');
							$('#btndelete').addClass('hide');
						}
						else if (command == 'delete'){
							$('#btndelete').removeClass('hide');
							$('#btnupdate').addClass('hide');
						}
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
						// do Validation
						// cekFieldKembar("cgroupname","Module Name",4,"tmodule","module_name",data[1],"no");
						// cekFieldMin("cgroupcategory","Category",4);
						// cekFieldKembar("cgrouppageurl","PageURL",6,"tmodule","module_pageurl",data[4],"no");
					}		
				}
			});
		}
		$(document).ready(function(){
			refreshModuleListTable($('#module_search').val(), 1);
			$('#box_module_form .box-footer button[mx-command]').click(function(e){
				e.preventDefault();
				doCommand($(this).attr('mx-command'));
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
				<form class="form-horizontal">
					<div class="box-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="module_name" class="col-sm-4 control-label">Module Name :</label>
									<div class="col-sm-6">
										<input type="text" class="form-control" id="module_name" name="module[name]">
									</div>
								</div>
								<div class="form-group">
									<label for="module_category" class="col-sm-4 control-label">Module Category :</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="module_category" name="module[category]">
									</div>
								</div>
								<div class="form-group">
									<label for="module_pageurl" class="col-sm-4 control-label">Module PageURL :</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="module_pageurl" name="module[pageurl]">
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
						<button type="submit" mx-command="create" id="btncreate" class="btn btn-primary">Tambah Baru</button>
						<button type="submit" mx-command="update" id="btnupdate" class="btn btn-success hide">Ubah</button>
						<button type="submit" mx-command="delete" id="btndelete" class="btn btn-danger hide">Hapus</button>
						<button type="clear" mx-command="cancel" id="btncancel" class="btn btn-default">Batal</button>
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