<?php include('header.php'); ?>

	<script type="text/javascript">		
		function doPostTransaction(content, command){
			$.ajax({
				url: 'ajax/global_postTransaction.php',
				type: 'post',
				data: {
					table: 'tdebt',
					id: content,
					id_field: 'debt_id',
					status_field: 'debt_status',
					delete_field: 'debt_deletedate',
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
					refreshDataTable($('#hidsearch').val(), $('#hidcurrentpage').val(), $('#hidpostedpending').val());
				}
			});
		}
		function submitAjaxForm(){
			$('#hidnominal').val($('#input_nominal').autoNumeric('get'));
			$('#hiddate').val(moment($('#input_date').datepicker('getDate')).format('YYYY-MM-DD'));
			$('#hidphoto').val($('#input_photo').val());
			$.ajax({                                      
				url: 'ajax/debt_submitForm.php',                  
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
						if (confirm('Apakah anda ingin langsung posting?')){
							doPostTransaction(result.id, $('#hidcommand').val());
						}
						else{
							refreshDataTable($('#hidsearch').val(), $('#hidcurrentpage').val(), $('#hidpostedpending').val());
						}
					}
				}
			});			
		}
		function doCommand(command){
			if (command == 'cancel'){
				validator.message.removeAll($('#box_input_form form'));
				$('#box_input_form form').clearForm();
				$('#box_input_form form input:not([disabled])').eq(0).focus();
				$('#hidid').val('');
				$('#hidname').val('');
				$('#btncreate').removeClass('hide');
				$('#btnupdate').addClass('hide');
				$('#btndelete').addClass('hide');
				$('#hidcommand').val('create');
				$('#hidphoto').val('');
				$('#input_date').datepicker('setDate', Date());
				$('#input_supplier').val(0).trigger('change');
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
		function refreshDataTable(contSearch, contPage, contStatus){
			$.ajax({
				url: 'ajax/debt_getTableData.php',
				type: 'post',
				data: {
					page: contPage,
					search: contSearch,
					status: contStatus,
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
						$('#table_data_list tbody').append('<tr><td colspan="7">Utang tidak ditemukan</td></tr>');
						$('#table_data_paging').html('');
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
							tabContent += '<td>Rp <span class="auto-numeric">'+tabledata[i].debt_nominal+'</span></td>';
							tabContent += '<td>';
							if (contStatus == 'pending'){
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
						$('#table_data_paging').html(helper.createPaginationBar(contPage, totalpage));
						$('#table_data_paging .pagination li:not(.active,.disabled,[data-mx-disabled]) a').click(function(e){
							e.preventDefault();
							refreshDataTable($('#input_search').val(), $(this).attr('data-mx-page'), $('#hidpostedpending').val());
						});
					}
					$('.auto-numeric').autoNumeric('init', {aSep: '.', aDec: ' ', vMax: '99999999999999999999', mDec: '99', aPad: false, lZero: 'deny'});
				}
			});
		}
		function fillTextField(content,command){
			$.ajax({
				url: 'ajax/debt_fillFormData.php',
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
						$('#input_id').val(result.data[0]);
						$('#input_date').datepicker('setDate', moment(result.data[1], 'YYYY-MM-DD').toDate());
						$('#input_description').val(result.data[2]);
						$('#input_nominal').autoNumeric('set', result.data[3]);
						if ($('#input_supplier option[value="'+result.data[4]+'"]').length == 0){
							$('#input_supplier').append('<option value="'+result.data[4]+'">['+result.data[4]+'] '+result.data[5]+'</option>')
						}
						$('#input_supplier').val(result.data[4]).trigger('change');
						$('#input_user').val(result.data[7]);
						$('#input_photo').val(result.data[8]);
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
		/* Webcam Function */
		function webcam_onReady(cameraNames,camera,microphoneNames,microphone,volume) {
			$.each(cameraNames, function(index, text) {
					$('#webcam_camlist').append( $('<option></option>').val(index).html(text) )
			});
			$('#webcam_camlist').val(camera);
		}
		function webcam_onError(errorId,errorMsg) {
			alert(errorMsg);
		}
		$(document).ready(function(){
			moment.locale('id');
			refreshDataTable($('#input_search').val(), 1, $('#hidpostedpending').val());
			$('#box_input_form .box-footer button[data-mx-command]').click(function(e){
				e.preventDefault();
				doCommand($(this).attr('data-mx-command'));
			});
			$('#input_search').keypress(function(e){
				if(e.which == 13) {
					e.preventDefault();
					refreshDataTable($(this).val(), 1, $('#hidpostedpending').val());
				}
			});
			$('#btnsearch').click(function(e){
				e.preventDefault();
				refreshDataTable($(this).val(), 1, $('#hidpostedpending').val());
			});
			$('#btn_photo').click(function(e){
				e.preventDefault();
				if ($('#input_photo').val() != '')
					$('#webcam_snapimg').attr("src", "data:image/png;base64," + $('#input_photo').val());
				else
					$('#webcam_snapimg').removeAttr('src');
				$('#modal_webcam').modal('show');
			});
			$('#cb_postedpending').change(function(e){
				if ($(this).prop('checked'))
					$('#hidpostedpending').val('posted');
				else
					$('#hidpostedpending').val('pending');
				refreshDataTable($('#input_search').val(), $('#hidcurrentpage').val(), $('#hidpostedpending').val());
			});
			/* Form Cosmetics */
			$('#input_supplier').select2({
				ajax: {
					url: 'ajax/debt_getSupplierData.php',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term, // search term
							page: params.page
						};
					},
					processResults: function (data, page) {
						return {
							results: data
						};
					},
					cache: true
				},
				// placeholder: 'Pilih supplier..',
				minimumInputLength: 1,
				language: 'id',
			});
			$('#input_date').datepicker({
					format: 'mm/dd/yyyy',
					autoclose: true,
					language: 'id',
					format: 'DD, d MM yyyy',
					endDate: '1d',
			}).datepicker('setDate', Date());
			/* Form Validator */
			$('#input_supplier').on('select2:close', function(){
				if ($(this).val() == 0){
					validator.message.add($(this).parents('.form-group'), 'error', 'Supplier belum dipilih!');
				}
				else{
					validator.message.add($(this).parents('.form-group'), 'success', 'Ok.');
				}
			});
			$('#input_date').blur(function(){
				var date_today = new Date(new Date().toDateString()).getTime();
				var date_input = $('#input_date').datepicker('getDate').getTime();
				if (date_input > date_today){
					validator.message.add($(this).parents('.form-group'), 'error', 'Tanggal utang lebih dari hari ini!');
				}
				else{
					validator.message.add($(this).parents('.form-group'), 'success', 'Ok.');
				}
			});
			$('#input_nominal').blur(function(){
				if ($(this).autoNumeric < 1){
					validator.message.add($(this).parents('.form-group'), 'error', 'Nominal utang harus lebih dari 0!');
				}
				else{
					validator.message.add($(this).parents('.form-group'), 'success', 'Ok.');
				}
			});
			/* Webcam */
			$('#webcam_viewer').scriptcam({
				width: 400,
				height: 300,
				useMicrophone: false,
				showMicrophoneErrors: false,
				onError: webcam_onError,
				disableHardwareAcceleration: 1,
				cornerRadius: 20,
				cornerColor: 'e3e5e2',
				onWebcamReady: webcam_onReady,
			});
			$('#webcam_camlist').change(function(){
				$.scriptcam.changeCamera($(this).val());
			});
			$('#webcam_snapshot').click(function(){
				$('#webcam_snapimg').attr("src","data:image/png;base64,"+$.scriptcam.getFrameAsBase64());
				$('#tempphoto').val($.scriptcam.getFrameAsBase64());
			});
			$('#webcam_save').click(function(){
				$('#input_photo').val($('#tempphoto').val());
				$('#modal_webcam').modal('hide');
			});
			$('#modal_webcam').on('hidden.bs.modal', function (e) {
				if ($('#input_photo').val() == ''){
					$('#webcam_snapimg').removeAttr('src');
					validator.message.add($('#input_photo').parents('.form-group'), 'error', 'Foto masih kosong!');
				}
				else{
					validator.message.add($('#input_photo').parents('.form-group'), 'success', 'Ok.');
				}
			})
		});
	</script>

		<!---------------------- Main content ---------------------->
		<section class="content">
			<div id="content_alert">
				
			</div>
			<div class="modal fade" id="modal_webcam">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Ambil foto penghutang</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-6">
									<div id="webcam_viewer" style="width: 100%; height: 300px;"></div>
								</div>
								<div class="col-md-6">
									<img id="webcam_snapimg" style="width: 100%; height: 300px;"/>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="input-group">
										<select class="form-control" id="webcam_camlist" size="1"></select>
										<div class="input-group-btn">
											<button class="btn btn-info" id="webcam_snapshot">Ambil gambar</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
							<button type="button" class="btn btn-primary" id="webcam_save">Simpan</button>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<!-- CREATE NEW FORM -->
			<div class="box box-primary" id="box_input_form">
				<div class="box-header with-border">
					<h3 class="box-title">Form Transaksi Utang</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<form class="form-horizontal" role="form">
					<div class="box-body">
						<div class="col-lg-6">
							<div class="form-group">
								<label for="input_id" class="col-sm-4 control-label">Kode Utang :</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="input_id" name="input[id]" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="input_date" class="col-sm-4 control-label">Tanggal :</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" class="form-control" id="input_date" name="input[date]" maxlength="30">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="hidden" id="hiddate" name="hidden[date]" maxlength="30">
									</div>
									<span class="help-block inline"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Supplier :</label>
								<div class="col-sm-8">
									<select autofocus class="form-control select2" id="input_supplier" name="input[supplier]">
										<option value="0">Pilih supplier..</option>
									</select>
									<span class="help-block inline"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Foto :</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" id="input_photo" disabled >
										<input type="hidden" id="hidphoto" name="hidden[photo]">
										<input type="hidden" id="tempphoto">
										<div class="input-group-btn">
											<button class="btn btn-info" id="btn_photo">Ambil foto</button>
										</div>
									</div>
									<span class="help-block inline"></span>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="input_user" class="col-sm-4 control-label">Operator :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="input_user" name="input[user]" value="<?php echo $_SESSION['login']['user_name'] ?>" disabled>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Keterangan :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" id="input_description" name="input[description]"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="input_nominal" class="col-sm-4 control-label">Nominal :</label>
								<div class="col-sm-5">
									<div class="input-group">
										<span class="input-group-addon">Rp</span>
										<input type="text" class="form-control auto-numeric" id="input_nominal" name="input[nominal]" maxlength="20">
										<input type="hidden" id="hidnominal" name="hidden[nominal]">
									</div>
									<span class="help-block inline"></span>
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
						<input type="hidden" id="hidhref" name="hidden[href]" value="<?php echo $pagename; ?>">
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
					<h3 class="box-title">Daftar Utang</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
					<div class="form-horizontal">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Cari :</label>
									<div class="col-sm-8">
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
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">Filter :</label>
									<div class="col-sm-8">
										<input type="checkbox" id="cb_postedpending" checked data-toggle="toggle" data-on="Posting" data-off="Pending" data-onstyle="success" data-offstyle="warning" >
										<input type="hidden" id="hidpostedpending" value="posted">
									</div>
								</div>
							</div>
						</div>
					</div>
					<table id="table_data_list" class="table table-bordered table-striped">
						<thead>
							<tr class="info">
								<th>Kode</th>
								<th>Tanggal</th>
								<th>Supplier</th>
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