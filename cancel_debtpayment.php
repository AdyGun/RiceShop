<?php include('header.php'); ?>

	<script type="text/javascript">		
		function doValidation(type){
			$('#input_id, #input_description').blur();
		}
		function submitAjaxForm(){
			$.ajax({                                      
				url: 'ajax/postingCancel_debtpayment.php',
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
					}
				}
			});			
		}
		function doCommand(command){
			if (command == 'cancel'){
				validator.message.removeAll($('#box_input_form form'));
				$('#box_input_form form').clearForm();
				$('#box_input_form form input:not([disabled])').eq(0).focus();
				$('#hidcommand').val('submit');
			}
			else{
				doValidation();
				if (!validator.validCheck($('#box_input_form form')))
					return false;
				$('#hidcommand').val(command);
				submitAjaxForm();
			}
		}
		
		
		$(document).ready(function(){
			$('#box_input_form .box-footer button[data-mx-command]').click(function(e){
				e.preventDefault();
				doCommand($(this).attr('data-mx-command'));
			});
			/* Form Validator */
			$('#input_id').blur(function(){
				validator.check.minLength($(this).parents('.form-group'), 'Kode Pembayaran Utang', 10);
			});
			$('#input_description').blur(function(){
				validator.check.minLength($(this).parents('.form-group'), 'Keterangan', 4);
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
					<h3 class="box-title">Batal Posting Utang</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<form class="form-horizontal" role="form">
					<div class="box-body">
						<div class="col-md-12">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="input_id">Kode Pembayaran Utang :</label>
								<div class="col-sm-3">
									<input type="text" autofocus class="form-control" id="input_id" name="input[id]" maxlength="20">
								</div>
								<span class="help-block inline"></span>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Keterangan :</label>
								<div class="col-sm-4">
									<textarea class="form-control" rows="2" id="input_description" name="input[description]"></textarea>
								</div>
								<span class="help-block inline"></span>
							</div>
						</div>
					</div><!-- /.box-body -->
					<div class="box-footer">
						<button type="submit" data-mx-command="submit" id="btnsubmit" class="btn btn-primary">Proses</button>
						<button type="clear" data-mx-command="cancel" id="btncancel" class="btn btn-default">Batal</button>
						<input type="hidden" id="hidcommand" name="hidden[command]">
					</div>
				</form>
			</div><!-- /.create-new-form -->
			
		</section><!--- /.main-content -->
	</div><!-- /.content-wrapper -->
	
<?php include('footer.php'); ?>