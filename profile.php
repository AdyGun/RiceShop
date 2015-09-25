<?php include('header.php'); ?>

	<script>
		function submitAjaxForm(){
			$.ajax({                                      
				url: 'ajax/profile_submitChangePassword.php',                  
				type: 'post',
				data: $('#tab_changepass form').serialize(),
				dataType: 'json',         
				beforeSend: function(){
					$('#tab_changepass button[data-mx-command="cancel"]').prop('disabled', true);
					$('#tab_changepass button[data-mx-command="submit"]').prop('disabled', true);
					$('#tab_changepass button[data-mx-command="submit"] i').removeClass('hide');
				},
				success: function(result){
					$('#tab_changepass button[data-mx-command="cancel"]').prop('disabled', false);
					$('#tab_changepass button[data-mx-command="submit"]').prop('disabled', false);
					$('#tab_changepass button[data-mx-command="submit"] i').addClass('hide');
					helper.showAlertMessage(result.alert);
					if (result.type){
						doCommand('cancel');
					}
				}
			});			
		}
		function doCommand(command){
			if (command == 'cancel'){
				validator.message.removeAll($('#tab_changepass form'));
				$('#tab_changepass form').clearForm();
				$('#hidcommand').val('submit');
			}
			else{				
				if (!validator.validCheck($('#tab_changepass form')))	return false;
				$('#hidcommand').val(command);
				submitAjaxForm();
			}
		}
		$(document).ready(function(){
			$('#tab_changepass form button[data-mx-command]').click(function(e){
				e.preventDefault();
				doCommand($(this).attr('data-mx-command'));
			});
			$('#input_confirmpassword').blur(function(){
				if ($(this).val() != $('#input_newpassword').val()){
					validator.message.add($(this).parents('.form-group'), 'error', 'Password baru dan Konfirmasi password tidak sama!');
				}
			});
		});
	</script>

		<!---------------------- Main content ---------------------->
		<section class="content">
			<div id="content_alert">
				
			</div>
			<div class="row">
				<div class="col-md-3">
					<!-- Profile Image -->
					<div class="box box-primary">
						<div class="box-body box-profile">
							<img class="profile-user-img img-responsive img-circle" src="dist/img/avatar5.png" alt="User profile picture">
							<h3 class="profile-username text-center"><?php echo $_SESSION['login']['user_completename'];?></h3>
							<p class="text-muted text-center"><?php echo $_SESSION['login']['level_name'];?></p>
						</div><!-- /.box-body -->
					</div><!-- /.box -->
				</div><!-- /.col -->
				<div class="col-md-9">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<!-- <li><a href="#timeline" data-toggle="tab">Timeline</a></li> -->
							<li class="active"><a href="#tab_changepass" data-toggle="tab">Ganti Password</a></li>
						</ul>
						<div class="tab-content">
							<div class="active tab-pane" id="tab_changepass">
								<form class="form-horizontal">
									<div class="form-group">
										<label for="input_oldpassword" class="col-sm-4 control-label">Password Lama :</label>
										<div class="col-sm-6">
											<input type="password" class="form-control" id="input_oldpassword" name="input[oldpassword]">
										</div>
									</div>
									<div class="form-group">
										<label for="input_newpassword" class="col-sm-4 control-label">Password Baru :</label>
										<div class="col-sm-6">
											<input type="password" class="form-control" id="input_newpassword" name="input[newpassword]">
										</div>
									</div>
									<div class="form-group">
										<label for="input_confirmpassword" class="col-sm-4 control-label">Konfirmasi Password Baru :</label>
										<div class="col-sm-6">
											<input type="password" class="form-control" id="input_confirmpassword" name="input[confirmpassword]">
											<span class="help-block inline"></span>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-offset-4 col-sm-10">
											<button type="submit" data-mx-command="submit" class="btn btn-info"><i class="fa fa-refresh fa-spin hide"></i> Ganti Password</button>
											<button type="clear" data-mx-command="cancel" class="btn btn-default">Batal</button>
											<input type="hidden" id="hidcommand" name="hidden[command]">
										</div>
									</div>
								</form>
							</div><!-- /.tab-pane -->
						</div><!-- /.tab-content -->
					</div><!-- /.nav-tabs-custom -->
				</div><!-- /.col -->
			</div><!-- /.row -->

		</section><!--- /.main-content -->
	</div><!-- /.content-wrapper -->
	
<?php include('footer.php'); ?>