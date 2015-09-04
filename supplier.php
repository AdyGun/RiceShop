<?php include('header.php'); ?>

	<script type="text/javascript">		
		function submitAjaxForm(){
			$.ajax({                                      
				url: 'ajax/supplier_submitForm.php',                  
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
				url: 'ajax/supplier_getTableData.php',
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
						$('#table_data_list tbody').append('<tr><td colspan="7">Supplier tidak ditemukan</td></tr>');
					}
					else{
						/* Table Data */
						var tabledata = result.data.tabledata;
						for (var i=0; i<result.data.tabledata.length; i++){
							var tabContent = '<tr data-mx-id="'+tabledata[i].supplier_id+'">';
							tabContent += '<td>'+tabledata[i].supplier_id+'</td>';
							tabContent += '<td>'+tabledata[i].supplier_name+'</td>';
							tabContent += '<td>'+tabledata[i].supplier_address+'</td>';
							tabContent += '<td>'+tabledata[i].supplier_city+'</td>';
							tabContent += '<td class="auto-numeric">'+tabledata[i].supplier_phone+'</td>';
							tabContent += '<td>'+tabledata[i].supplier_description+'</td>';
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
					$('.auto-numeric').autoNumeric('init', {aSep: '', aDec: ' ', vMax: '99999999999999999999', mDec: '99', aPad: false, lZero: 'keep'});
				}
			});
		}
		function fillTextField(content,command){
			$.ajax({
				url: 'ajax/global_getSingleRowData.php',
				type: 'post',
				data: {
					id: content,
					table: 'tsupplier',
					field: 'supplier_id'
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
						$('#input_id').val(result.data[0]);
						$('#input_name').val(result.data[1]);
						$('#hidname').val(result.data[1]);
						$('#input_address').val(result.data[2]);
						$('#input_city').val(result.data[3]);
						$('#input_phone').val(result.data[4]);
						$('#input_phone').autoNumeric('set', result.data[4]);
						$('#input_description').val(result.data[5]);
						if (command == 'update'){
							$('#btnupdate').removeClass('hide');
							$('#btndelete').addClass('hide');
							$('#input_name, #input_address, #input_city').blur();
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
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Nama Supplier', 4, 'tsupplier', 'supplier_name', '', 'supplier_deletedate');
				else
					validator.check.duplicatedValue($(this).parents('.form-group'), 'Nama Supplier', 4, 'tsupplier', 'supplier_name', $('#hidname').val(), 'supplier_deletedate');
			});
			$('#input_address').blur(function(){
				validator.check.minLength($(this).parents('.form-group'), 'Alamat', 6);
			});
			$('#input_city').blur(function(){
				validator.check.minLength($(this).parents('.form-group'), 'Alamat', 3);
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
					<h3 class="box-title">Tambah User Baru</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<form class="form-horizontal" role="form">
					<div class="box-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="input_id" class="col-sm-4 control-label">Kode Supplier :</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="input_id" name="input[id]" maxlength="10" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="input_name" class="col-sm-4 control-label">Nama Supplier :</label>
									<div class="col-sm-6">
										<input type="text" autofocus class="form-control" id="input_name" name="input[name]" maxlength="30">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Alamat :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="2" id="input_address" name="input[address]"></textarea>
										<span class="help-block"></span>
									</div>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="input_city" class="col-sm-4 control-label">Kota :</label>
									<div class="col-sm-7">
										<input type="text" class="form-control" id="input_city" name="input[city]" maxlength="30">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label for="input_phone" class="col-sm-4 control-label">Telpon :</label>
									<div class="col-sm-5">
										<input type="text" class="form-control auto-numeric" id="input_phone" name="input[phone]" maxlength="30">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Keterangan :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="2" id="input_description" name="input[description]"></textarea>
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
					</div>
				</form>
			</div><!-- /.create-new-module -->
			<!-- TABLE DATA LIST -->
			<div class="box box-info box-solid" id="box_table_list">
				<div class="box-header with-border">
					<h3 class="box-title">Daftar Supplier</h3>
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
								<th>Kode</th>
								<th>Nama</th>
								<th>Alamat</th>
								<th>Kota</th>
								<th>Telpon</th>
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