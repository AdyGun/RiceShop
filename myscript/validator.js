/*! MyScript validator.js
 * ===================
 * Validator script
 * Contain form validator before submit to server.
 *
 * @Author  AdyGun
 * @Email   <adygun91@gmail.com>
 * @version 0.0.3
 */

var validator = new function(){
	/*! Add or Remove Validation's Message */
	this.message = new function(){
		this.add = function(el, type, message){
			if (type != '') { type = 'has-'+type; }
			el.removeClass('has-success')
				.removeClass('has-error')
				.removeClass('has-warning')
				.addClass(type);
			el.find('span.help-block').html(message);
		}
		this.remove = function(el){
			el.removeClass('has-success')
				.removeClass('has-error')
				.removeClass('has-warning')
			el.find('span.help-block').html('');
		}
		this.removeAll = function(el){
			el.find('.has-success').removeClass('has-success');
			el.find('.has-warning').removeClass('has-warning');
			el.find('.has-error').removeClass('has-error');
			el.find('span.help-block').html('');
		}
	}
	/*! Validation Check */
	this.check = new function(){
		this.required = function(el, fieldname){
			if (el.find('input').val() == ''){
				validator.message.add(el, 'error', fieldname+' harus diisi!');
			}
			else{
				validator.message.add(el, 'success', 'Ok.');
			}
		}
		this.minLength = function(el, fieldname, textlength){
			if (el.find('input, textarea').val().length >= textlength){
				validator.message.add(el, 'success', 'Ok.');
			}
			else{
				validator.message.add(el, 'error', 'Panjang '+fieldname+' minimal '+textlength+' karakter!');
			}
		}
		this.duplicatedValue = function(el, fieldname, textlength, tname, fname, except, isdel){
			var content = el.find('input').val();
			if (content.length >= textlength){
				$.ajax({
					url: 'ajax/global_checkDuplicatedValue.php',                  
					type: 'post',
					data: {
						id: content,
						table: tname,
						field: fname,
						exc: except,
						del: isdel
					},        
					dataType: 'text',
					beforeSend: function(){
						el.find('input').attr('disabled', 'disabled');
					},
					success: function(result){
						el.find('input').removeAttr('disabled');
						if (result == 'false'){
							validator.message.add(el, 'error', fieldname+' sudah pernah di daftarkan!');
						}
						else if (result == 'true'){
							validator.message.add(el, 'success', 'Anda bisa menggunakan '+fieldname+' ini.');
						}
					}
				});					
			}
			else{
				validator.message.add(el, 'error', 'Panjang '+fieldname+' minimal '+textlength+' karakter!');
			}
		}
	}
	/*! Check if the Form is passes the Validator or not */
	this.validCheck = function(el){
		// Form Submit check
		if (el.is('form')){
			if (el.find('.has-error').length > 0){
				return false;
			}
			else if (el.find('.has-warning').length > 0){
				if (confirm('Terdapat pesan peringatan pada form. Apakah Anda ingin melanjutkan?'))
					return true;
				else
					return false;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
	}
}