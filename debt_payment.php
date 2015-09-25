<?php include('header.php'); ?>

	<script type="text/javascript">
		/* Debt Table Function */
		function doValidation(){
			$('#input_date, #input_nominal').blur();
		}
		function doPostTransaction(content, command){
			$.ajax({
				url: 'ajax/global_postTransaction.php',
				type: 'post',
				data: {
					table: 'tdebtpayment',
					id: content,
					id_field: 'debtpayment_id',
					status_field: 'debtpayment_status',
					delete_field: 'debtpayment_deletedate',
					command: command,
					href: window.location.href,
				},        
				dataType: 'json',         
				beforeSend: function(){
					helper.showBoxLoading('#box_input_form');
				},
				success: function(result){
					helper.removeBoxLoading('#box_input_form');
					helper.showAlertMessage(result.alert);
					refreshDataTable();
					refreshDebtTable();
				}
			});
		}
		function submitAjaxForm(){
			$('#hidnominal').val($('#input_nominal').autoNumeric('get'));
			$('#hiddate').val(moment($('#input_date').datepicker('getDate')).format('YYYY-MM-DD'));
			$.ajax({                                      
				url: 'ajax/debtpayment_submitForm.php',                  
				type: 'post',
				data: $('#form_payment').serialize(),
				dataType: 'json',         
				beforeSend: function(){
					helper.showBoxLoading('#box_input_form');
				},
				success: function(result){
					helper.removeBoxLoading('#box_input_form');
					helper.showAlertMessage(result.alert);
					if (result.type){
						doCommand('cancel');
						if ($('#hidcommand').val() != 'delete' && confirm('Apakah anda ingin langsung posting?')){
							doPostTransaction(result.id, $('#hidcommand').val());							
						}
						else{
							refreshDataTable();
							refreshDebtTable();
						}
					}
				}
			});			
		}
		function doCommand(command){
			if (command == 'cancel'){
				$('#input_debtid').val('').focus();
				$('#lbl_debtdate').html('-');
				$('#lbl_debtsupplier').html('-');
				$('#lbl_debtdescription').html('-');
				$('#lbl_debtnominal').autoNumeric('set', 0);
				$('#lbl_debtremain').autoNumeric('set', 0);
				$('#img_debtphoto').attr('src', 'dist/img/avatar.png');
				validator.message.removeAll($('#form_payment'));
				$('#form_payment').clearForm();
				$('#form_payment input[type="text"], #form_payment textarea').prop('disabled', true);
				$('#btncreate').removeClass('hide').prop('disabled', true);
				$('#btnupdate').addClass('hide');
				$('#btndelete').addClass('hide');
				$('#hidcommand').val('create');
				$('#input_date').datepicker('setDate', Date());
				$('#input_nominal').autoNumeric('set', 0);
			}
			else{				
				if (command == 'delete'){
					if (!confirm('Apakah anda yakin untuk menghapus data ini?'))
						return false;
				}
				else{
					doValidation();
					if (!validator.validCheck($('#form_payment')))
						return false;
				}
				$('#hidcommand').val(command);
				submitAjaxForm();
			}
		}
		function fillDebtDetail(content){
			$.ajax({
				url: 'ajax/debt_fillFormData.php',
				type: 'post',
				data: {
					id: content,
					status: 'posted',
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
						validator.message.removeAll($('#box_input_form #tab_1'));
						$('#hiddebtid').val(result.data[0]);
						$('#input_debtid').val(result.data[0]);
						$('#lbl_debtdate').html(moment(result.data[1], 'YYYY-MM-DD').format('dddd, D MMMM YYYY'));
						$('#hiddebtdate').val(result.data[1]);
						$('#input_date').datepicker('setStartDate', moment(result.data[1], 'YYYY-MM-DD').toDate());
						$('#lbl_debtdescription').html(result.data[2]);
						$('#lbl_debtnominal').autoNumeric('set', result.data[3]);
						$('#lbl_debtsupplier').html(result.data[4]);
						$('#img_debtphoto').attr("src", "data:image/png;base64," + result.data[6]);
						$('#lbl_debtremain').autoNumeric('set', result.data[7]);
						$('#hiddebtremain').val(result.data[7]);
						$('#input_date').prop('disabled', false);
						$('#input_description').prop('disabled', false);
						$('#input_nominal').prop('disabled', false);
						$('#btncreate').prop('disabled', false);
						$('.nav-tabs li a[href="#tab_1"]').trigger('click');
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
					table: 'tdebtpayment',
					field: 'debtpayment_id'
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
						validator.message.removeAll($('#form_payment'));
						$('#hidcommand').val(command);
						fillDebtDetail(result.data[2]);
						$('#hidid').val(result.data[0]);
						$('#input_id').val(result.data[0]);
						$('#input_date').datepicker('setDate', moment(result.data[1], 'YYYY-MM-DD').toDate());
						$('#input_description').val(result.data[4]);
						$('#input_nominal').autoNumeric('set', result.data[5]);
						if (command == 'update'){
							$('#btnupdate').removeClass('hide');
							$('#btndelete').addClass('hide');
							doValidation();
						}
						else if (command == 'delete'){
							$('#btndelete').removeClass('hide');
							$('#btnupdate').addClass('hide');
						}
					}		
				}
			});
		}
		function refreshDebtTable(){
			$.ajax({
				url: 'ajax/debt_getTableData.php',
				type: 'post',
				data: $('#form_debt_table').serialize(),
				dataType: 'json',
				beforeSend: function(){
					helper.showBoxLoading('#box_input_form');
				},
				success: function(result){
					helper.removeBoxLoading('#box_input_form');
					$('#table_debt_list tbody tr').remove();
					if (!result.type){
						helper.showAlertMessage(result.alert);
						$('#table_debt_list tbody').append('<tr><td colspan="8">Utang tidak ditemukan</td></tr>');
						$('#table_debt_paging').html('');
					}
					else{
						/* Table Data */
						var tabledata = result.data.tabledata;
						for (var i=0; i<result.data.tabledata.length; i++){
							var tabContent = '<tr data-mx-id="'+tabledata[i].debt_id+'">';
							tabContent += '<td>'+tabledata[i].debt_id+'</td>';
							tabContent += '<td>'+moment(tabledata[i].debt_date, 'YYYY-MM-DD').format('dddd, D MMMM YYYY', 'id')+'</td>';
							tabContent += '<td>['+tabledata[i].supplier_id+'] '+tabledata[i].supplier_name+'</td>';
							tabContent += '<td>'+tabledata[i].user_name+'</td>';
							tabContent += '<td>'+tabledata[i].debt_description+'</td>';
							tabContent += '<td class="debttable-autonumeric">'+tabledata[i].debt_nominal+'</td>';
							if (tabledata[i].debtremain == 0){
								tabContent += '<td><span class="label label-success debttable-autonumeric">LUNAS</span></td>';
							}
							else{
								tabContent += '<td><span class="label label-danger debttable-autonumeric">'+tabledata[i].debtremain+'</span></td>';
							}
							tabContent += '<td><button type="button" class="btn btn-info btn-sm">Pilih</button></td>';
							tabContent += '</tr>';
							$('#table_debt_list tbody').append(tabContent);
						}
						$('#table_debt_list tbody td button').click(function(){
							fillDebtDetail($(this).parents('tr').attr('data-mx-id'));
						});
						/* Table Paging */
						var totalpage = result.data.totalpage;
						$('#table_debt_paging').html(helper.createPaginationBar($('#hiddebtcurrentpage').val(), totalpage));
						$('#table_debt_paging .pagination li:not(.active,.disabled,[data-mx-disabled]) a').click(function(e){
							e.preventDefault();
							$('#hiddebtcurrentpage').val($(this).attr('data-mx-page'));
							refreshDebtTable();
						});
					}
					$('.debttable-autonumeric').autoNumeric('init', {aSep: '.', aDec: ' ', aSign: 'Rp ', vMax: '99999999999999999999', mDec: '99', aPad: false, lZero: 'deny'});
				}
			});
		}
		function refreshDataTable(){
			$.ajax({
				url: 'ajax/debtpayment_getTableData.php',
				type: 'post',
				data: $('#box_table_list form').serialize(),
				dataType: 'json',
				beforeSend: function(){
					helper.showBoxLoading('#box_table_list');
				},
				success: function(result){
					helper.removeBoxLoading('#box_table_list');
					$('#table_data_list tbody tr').remove();
					if (!result.type){
						helper.showAlertMessage(result.alert);
						$('#table_data_list tbody').append('<tr><td colspan="7">Pembayaran utang tidak ditemukan</td></tr>');
						$('#table_data_paging').html('');
					}
					else{
						/* Table Data */
						var tabledata = result.data.tabledata;
						for (var i=0; i<result.data.tabledata.length; i++){
							var tabContent = '<tr data-mx-id="'+tabledata[i].debtpayment_id+'">';
							tabContent += '<td>'+tabledata[i].debtpayment_id+'</td>';
							tabContent += '<td>'+moment(tabledata[i].debtpayment_date, 'YYYY-MM-DD').format('dddd, D MMMM YYYY', 'id')+'</td>';
							tabContent += '<td>'+tabledata[i].debt_id+'</td>';
							tabContent += '<td>'+tabledata[i].user_name+'</td>';
							tabContent += '<td>'+tabledata[i].debtpayment_description+'</td>';
							tabContent += '<td class="table-autonumeric">'+tabledata[i].debtpayment_nominal+'</td>';
							tabContent += '<td>';
							if ($('#hidstatus').val() == 'pending'){
								tabContent += 	'<div class="btn-group">';
								tabContent += 		'<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">';
								tabContent += 			'<span class="caret"></span>';
								tabContent += 		'</button>';
								tabContent += 		'<ul class="dropdown-menu">';
								tabContent += 			'<li class="bg-success"><a href="javascript:void(0)" data-mx-command="update">Ubah</a></li>';
								tabContent += 			'<li class="bg-danger"><a href="javascript:void(0)" data-mx-command="delete">Hapus</a></li>';
								tabContent += 		'</ul>';
								tabContent += 	'</div>';
							}
							tabContent += '</td>';
							tabContent += '</tr>';
							$('#table_data_list tbody').append(tabContent);
						}
						$('#table_data_list tbody a[data-mx-command]').click(function(){
							fillTextField($(this).parents('tr').attr('data-mx-id'),$(this).attr('data-mx-command'));
						});
						/* Table Paging */
						var totalpage = result.data.totalpage;
						$('#table_data_paging').html(helper.createPaginationBar($('#hidcurrentpage').val(), totalpage));
						$('#table_data_paging .pagination li:not(.active,.disabled,[data-mx-disabled]) a').click(function(e){
							e.preventDefault();
							$('#hidcurrentpage').val($(this).attr('data-mx-page'));
							refreshDataTable();
						});
					}
					$('.table-autonumeric').autoNumeric('init', {aSep: '.', aDec: ' ', aSign: 'Rp ', vMax: '99999999999999999999', mDec: '99', aPad: false, lZero: 'deny'});
				}
			});
		}
		$(document).ready(function(){
			refreshDataTable();
			refreshDebtTable();
			$('#box_input_form .box-footer button[data-mx-command]').click(function(e){
				e.preventDefault();
				doCommand($(this).attr('data-mx-command'));
			});
			$('#input_debtid').keypress(function(e){
				if(e.which == 13) {
					e.preventDefault();
					fillDebtDetail($(this).val());
				}
			});
			$('#btndebtid').click(function(e){
				e.preventDefault();
				fillDebtDetail($('#input_debtid').val());
			});
			$('#input_search').keypress(function(e){
				if(e.which == 13) {
					e.preventDefault();
					refreshDataTable();
				}
			});
			$('#btnsearch').click(function(e){
				e.preventDefault();
				refreshDataTable();
			});
			$('#input_debtsearch').keypress(function(e){
				if(e.which == 13) {
					e.preventDefault();
					refreshDebtTable();
				}
			});
			$('#btndebtsearch').click(function(e){
				e.preventDefault();
				refreshDebtTable();
			});
			$('#cb_status').change(function(e){
				if ($(this).prop('checked'))
					$('#hidstatus').val('posted');
				else
					$('#hidstatus').val('pending');
				refreshDataTable();
			});
			/* Form Cosmetics */
			$('#input_date').datepicker({
					format: 'mm/dd/yyyy',
					autoclose: true,
					language: 'id',
					format: 'DD, d MM yyyy',
					endDate: '1d',
			}).datepicker('setDate', Date());
			$('.lbl-autonumeric').autoNumeric('init', {aSep: '.', aDec: ' ', aSign: 'Rp ', vMax: '99999999999999999999', mDec: '99', aPad: false, lZero: 'deny'});
			$('.form-autonumeric').autoNumeric('init', {aSep: '.', aDec: ' ', vMax: '99999999999999999999', mDec: '99', aPad: false, lZero: 'deny'});
			/* Form Validator */
			$('#input_date').blur(function(){
				var date_today = new Date(new Date().toDateString()).getTime();
				var date_input = $('#input_date').datepicker('getDate').getTime();
				var date_debt = moment($('#hiddebtdate').val(), 'YYYY-MM-DD').toDate().getTime();
				if (date_input > date_today){
					validator.message.add($(this).parents('.form-group'), 'error', 'Tanggal pembayaran lebih dari hari ini!');
				}
				else if (date_input < date_debt){
					validator.message.add($(this).parents('.form-group'), 'error', 'Tanggal pembayaran harus melebihi tanggal utang!');
				}
				else{
					validator.message.add($(this).parents('.form-group'), 'success', 'Ok.');
				}
			});
			$('#input_nominal').blur(function(){
				if ($(this).autoNumeric('get') < 1){
					validator.message.add($(this).parents('.form-group'), 'error', 'Nominal pembayaran harus lebih dari 0!');
				}
				// else if ($(this).autoNumeric('get') > $('#hiddebtremain').val()){
					// validator.message.add($(this).parents('.form-group'), 'error', 'Nominal pembayaran melebihi sisa utang!');
				// }
				else{
					validator.message.add($(this).parents('.form-group'), 'success', 'Ok.');
				}
			});
		});
	</script>
	
		<!---------------------- Main content ---------------------->
		<section class="content">
			<div id="content_alert">
				
			</div>
			<div class="box box-primary" id="box_input_form">
				<div class="box-header with-border">
					<h3 class="box-title">Form Pembayaran Utang</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
					<div class="row">
						<div class="col-lg-12">
							<!-- Custom Tabs -->
							<div class="nav-tabs-custom">
								<ul class="nav nav-tabs">
									<li class="active"><a href="#tab_1" data-toggle="tab">Rincian Utang</a></li>
									<li><a href="#tab_2" data-toggle="tab">Daftar Utang</a></li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="tab_1">
										<!-- DETAIL FORM -->
										<div role="form">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="input_debtid">Kode Utang :</label>
														<div class="input-group">
															<input type="text" class="form-control" id="input_debtid" name="input[debtid]">
															<span class="input-group-btn">
																<span class="input-group-btn">
																	<button class="btn btn-primary" type="button" id="btndebtid"><i class="fa fa-search"></i></button>
																</span>
															</span>
														</div>
														<span class="help-block inline"></span>
													</div>
													<div class="form-group">
														<label class="control-label">Tanggal :</label>
														<h4 id="lbl_debtdate">-</h4>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label class="control-label">Supplier :</label>
														<h4 id="lbl_debtsupplier">-</h4>
													</div>
													<div class="form-group">
														<label class="control-label">Foto :</label>
														<div class="input-group">
															<img src="dist/img/avatar.png" width="" height="100" id="img_debtphoto" />
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label class="control-label">Keterangan :</label>
														<h4 id="lbl_debtdescription">-</h4>
													</div>
													<div class="form-group">
														<label class="control-label">Nominal :</label>
														<h4 id="lbl_debtnominal" class="lbl-autonumeric">0</h4>
													</div>
													<div class="form-group">
														<label class="control-label">Sisa Utang :</label>
														<h4><span id="lbl_debtremain" class="label label-danger lbl-autonumeric">0</span></h4>
														<input type="hidden" id="hiddebtremain">
													</div>
												</div>
											</div>
										</div>
									</div><!-- /.tab-pane -->
									<div class="tab-pane" id="tab_2">
										<div class="row">
											<form class="form-horizontal" id="form_debt_table">
												<input type="hidden" name="hidden[href]" value="<?php echo $pagename; ?>">
												<div class="col-md-6">
													<div class="form-group">
														<label class="col-sm-4 control-label">Cari :</label>
														<div class="col-sm-8">
															<div class="input-group">
																<input type="text" id="input_debtsearch" class="form-control" name="search[text]"/>
																<input type="hidden" id="hiddebtsearch"  value="">
																<input type="hidden" id="hiddebtcurrentpage" name="search[currentpage]" value="1">
																<span class="input-group-btn">
																	<span class="input-group-btn">
																		<button class="btn btn-primary" type="button" id="btndebtsearch"><i class="fa fa-search"></i></button>
																	</span>
																</span>
															</div>
														</div>
													</div>
												</div>
											</form>
										</div>
										<table id="table_debt_list" class="table table-bordered table-striped">
											<thead>
												<tr class="bg-light-blue">
													<th>Kode</th>
													<th>Tanggal</th>
													<th>Supplier</th>
													<th>Operator</th>
													<th>Keterangan</th>
													<th>Nominal</th>
													<th>Sisa Utang</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<!-- Table List -->
											</tbody>
										</table>
										<div id="table_debt_paging" class="text-center">
											<!-- Pagination Bar -->
										</div>
									</div><!-- /.tab-pane -->
								</div><!-- /.tab-content -->
							</div><!-- nav-tabs-custom -->
						</div>
					</div>
					<!-- TAB CREATE NEW FORM -->
					<div class="row">
						<form class="form-horizontal" id="form_payment">
							<input type="hidden" name="hidden[href]" value="<?php echo $pagename; ?>">
							<input type="hidden" id="hidcommand" name="hidden[command]">
							<input type="hidden" id="hidid" name="hidden[id]">
							<input type="hidden" id="hiddebtid" name="hidden[debtid]">
							<input type="hidden" id="hiddebtdate" name="hidden[debtdate]">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="input_id" class="col-sm-4 control-label">Kode Pembayaran :</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="input_id" name="input[id]" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="input_date" class="col-sm-4 control-label">Tanggal :</label>
									<div class="col-sm-7">
										<div class="input-group">
											<input type="text" class="form-control" id="input_date" maxlength="30" disabled>
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<input type="hidden" id="hiddate" name="hidden[date]" maxlength="30">
										</div>
										<span class="help-block inline"></span>
									</div>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Keterangan :</label>
									<div class="col-sm-8">
										<textarea class="form-control" rows="3" id="input_description" name="input[description]" disabled></textarea>
									</div>
								</div>
								<div class="form-group">
									<label for="input_nominal" class="col-sm-4 control-label">Nominal :</label>
									<div class="col-sm-5">
										<div class="input-group">
											<span class="input-group-addon">Rp</span>
											<input type="text" class="form-control form-autonumeric" id="input_nominal" maxlength="20" value="0" disabled>
											<input type="hidden" id="hidnominal" name="hidden[nominal]">
										</div>
										<span class="help-block inline"></span>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div><!-- /.box-body -->
				<div class="box-footer">
					<?php 
						if ($_SESSION['access'][$pagedata['id']]['create'] == 1) {
							echo '<button type="submit" data-mx-command="create" id="btncreate" class="btn btn-primary" disabled>Tambah Baru</button>';
						} 
						if ($_SESSION['access'][$pagedata['id']]['update'] == 1) { 
							echo '<button type="submit" data-mx-command="update" id="btnupdate" class="btn btn-success hide">Ubah</button>';
						} 
						if ($_SESSION['access'][$pagedata['id']]['delete'] == 1) { 
							echo '<button type="submit" data-mx-command="delete" id="btndelete" class="btn btn-danger hide">Hapus</button>';
						} 
					?>
					<button type="clear" data-mx-command="cancel" id="btncancel" class="btn btn-default">Batal</button>
				</div>
			</div><!-- /.create-new-form -->
		
			<?php if ($_SESSION['access'][$pagedata['id']]['read'] == 1) { ?>
			<!-- TABLE DATA LIST -->
			<div class="box box-info box-solid" id="box_table_list">
				<div class="box-header with-border">
					<h3 class="box-title">Daftar Pembayaran Utang</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
					<form class="form-horizontal">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Cari :</label>
									<div class="col-sm-8">
										<div class="input-group">
											<input type="text" id="input_search" class="form-control" name="search[text]"/>
											<input type="hidden" id="hidsearch"  value="">
											<input type="hidden" id="hidcurrentpage" name="search[currentpage]" value="1">
											<span class="input-group-btn">
												<span class="input-group-btn">
													<button class="btn btn-info" type="button" id="btnsearch"><i class="fa fa-search"></i></button>
												</span>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Filter :</label>
									<div class="col-sm-8">
										<input type="checkbox" id="cb_status" checked data-toggle="toggle" data-on="Posting" data-off="Pending" data-onstyle="success" data-offstyle="warning" >
										<input type="hidden" id="hidstatus" name="search[status]" value="posted">
									</div>
								</div>
							</div>
						</div>
					</form>
					<table id="table_data_list" class="table table-bordered table-striped">
						<thead>
							<tr class="info">
								<th>Kode Pembayaran</th>
								<th>Tanggal</th>
								<th>Kode Utang</th>
								<th>Operator</th>
								<th>Keterangan</th>
								<th>Nominal</th>
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