
jQuery(document).ready(function() {
	var timervalue = jQuery("#wcpopup_variable").data('timer');
	setTimeout(function(){
		jQuery('#wcpopup_modal').fancybox().trigger('click'); 
	},timervalue) 
	
	jQuery('#wcpopup_modal  #send').bind('click',function(){
		var data = {
			'action': 'wcpopupsetcountry',
			'country': jQuery("#wcpopup_modal #country").val(),
			'username': jQuery("#wcpopup_modal #username").val(),
			'userphone': jQuery("#wcpopup_modal #userphone").val(),
			'usermail': jQuery("#wcpopup_modal #usermail").val(),
		};
		var ajax_url = "/wp-admin/admin-ajax.php";		
		
		jQuery.ajax({
			type        : "POST",
			cache       : false,
			url         : ajax_url,
			data        : data,
			dataType: 'json',
			success: function(data) {
				console.log(data);
				if (data.status == 1){
					location.reload();
				}
				if (data.status == 2){
					jQuery('#wcpopup_modal #result').html();
					jQuery('#wcpopup_modal input[required]').each(function(i,j){
						if (jQuery.inArray(jQuery(this).attr('id'), data.id) != -1){ 
							jQuery(this).removeClass('areasuccess');
							jQuery(this).addClass('areaerror');
						}else{ 
							jQuery(this).removeClass('areaerror');
							jQuery(this).addClass('areasuccess');
						}
					} )
				}
			}
		});

		return false;
	})
	jQuery('#wcpopup_modal  #later').bind('click',function(){
		var data = {
			'action': 'wcppopupclose',
		};
		var ajax_url = "/wp-admin/admin-ajax.php";		
		
		jQuery.ajax({
			type        : "POST",
			cache       : false,
			url         : ajax_url,
			data        : data,
			dataType: 'json',
			success: function(data) {
				console.log(data);
				if (data.status == 1){
					 jQuery.fancybox.close();
				}
			}
		});

		return false;
	})	
});

