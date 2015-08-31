/*! MyScript helper.js
 * ===================
 * Main JS script for this App
 * Contain helper for all page.
 *
 * @Author  AdyGun
 * @Email   <adygun91@gmail.com>
 * @version 0.0.4
 */

var helper = new function(){
	/*! Show box's loading animation */
  this.showBoxLoading = function(selector){
		var s = '<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>';
		$(selector).append(s);
  }
	/*! Remove box's loading animation */
	this.removeBoxLoading = function(selector){
		$(selector+' .overlay').remove();
	}
	this.showAlertMessage = function(alert_data){
		var s = '';
		for (var i = 0; i < alert_data.length; i++){
			s += '<div class="alert alert-'+alert_data[i].type+'">';
			s += alert_data[i].message;
			s += '<button type="button" class="close" data-dismiss="alert">×</button></div>';
		}
		$('#content_alert').append(s);
		// $('.content-wrapper .content > .callout').attr('class', 'callout callout-'+type);
		// $('.content-wrapper .content > .callout h4').removeClass('hide');
		// $('.content-wrapper .content > .callout p').removeClass('hide');
		// if (header == ''){
			// $('.content-wrapper .content > .callout h4').addClass('hide').html(header);
		// }
		// if (message == ''){
			// $('.content-wrapper .content > .callout p').addClass('hide').html(message);
		// }
	}
	this.createPaginationBar = function(currentPage, totalPage){
		var flagDisable = '', flagActive = '';
		var contentHTML = '<ul class="pagination">';
		// Counting Mid Bar Page
		var pageFrom = (currentPage == totalPage - 3) ? currentPage - 2 : currentPage - 2;
		if (pageFrom < 2) { pageFrom = 2 };
		var pageTo = (currentPage == 4) ? currentPage + 2 : currentPage + 2;
		if (pageTo > totalPage - 1) { pageTo = totalPage - 1 };
			// Left Bar
		flagDisable = (currentPage == 1) ? 'disabled' : '';
		contentHTML += '<li class="'+flagDisable+'"><a href="javascript:void(0)" data-mx-page="'+(currentPage-1)+'">&laquo;</a></li>';
		flagActive = (currentPage == 1) ? 'active' : '';
		contentHTML += '<li class="'+flagActive+'"><a href="javascript:void(0)" data-mx-page="'+1+'">1</a></li>';
		if (pageFrom - 1 == 2){
			contentHTML += '<li><a href="javascript:void(0)" data-mx-page="'+2+'">2</a></li>';
		}
		else if(pageFrom - 1 != 1){
			contentHTML += '<li data-mx-disabled><a href="javascript:void(0)">...</a></li>';
		}		
			// Mid Bar
		for (var i = pageFrom; i <= pageTo; i++){
			flagActive = (currentPage == i) ? 'active' : '';
			contentHTML += '<li class="'+flagActive+'"><a href="javascript:void(0)" data-mx-page="'+i+'">'+i+'</a></li>';
		}
			// Right Bar
		if (pageTo + 1 == totalPage - 1){
			contentHTML += '<li><a href="javascript:void(0)" data-mx-page="'+(totalPage-1)+'">'+(totalPage - 1)+'</a></li>';
		}
		else if (pageTo + 1 != totalPage){
			contentHTML += '<li data-mx-disabled><a href="javascript:void(0)">...</a></li>';
		}
		flagActive = (currentPage == totalPage) ? 'active' : '';
		if (totalPage != 1){
			contentHTML += '<li class="'+flagActive+'"><a href="javascript:void(0)" data-mx-page="'+totalPage+'">'+totalPage+'</a></li>';
		}
		flagDisable = (currentPage == totalPage) ? 'disabled' : '';
		contentHTML += '<li class="'+flagDisable+'"><a href="javascript:void(0)" data-mx-page="'+(currentPage+1)+'">&raquo;</a></li>';
		contentHTML += '</ul>';
		return contentHTML;
	}
}