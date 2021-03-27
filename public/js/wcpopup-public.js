
jQuery(document).ready(function() {
	var timervalue = jQuery("#wcpopup_variable").data('timer');
	var countvalue = jQuery("#wcpopup_variable").data('count');
	var countervalue = jQuery("#wcpopup_variable").data('counter');
	var startvalue = jQuery("#wcpopup_variable").data('start');	
	
	var fancy = jQuery('#wcpopup_modal').fancybox({
		beforeClose: function() {
			jQuery.fn.wcpopupclose();
		}
	}); 	
	
	setTimeout(function(){
		if (startvalue == 0){
			fancy.trigger('click');
			startvalue = 1;
		}
	},timervalue) 
	
	
	jQuery('.single_add_to_cart_button').bind('click',function(){
		if (countervalue < countvalue){
			fancy.trigger('click'); 
			countervalue++;
		}
	}) 
	
	jQuery('.add_to_cart_button').bind('click',function(){
		if (countervalue < countvalue){
			fancy.trigger('click'); 
			countervalue++;
		}
	}) 	
	 
	
	jQuery('#wcpopup_modal  #send').bind('click',function(){
		var data = {
			'action': 'wcpopupsenddata',
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
					startvalue = 1;
					countervalue = countvalue;
					jQuery.fancybox.close();
					//location.reload();
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
		jQuery.fancybox.close();
		return false;
	})	
});
jQuery.fn.wcpopupclose = function(){
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
		}
	});
}

jQuery.fn.logic = function(){
	var element = this;
	var weight = 0;
	weight = parseFloat(element.find('.jweight').data('weight'));
	
	var quantity = 0;
	quantity = element.find('.product_quantity').data('quantity');

	var price = 0;		
	price = parseFloat(element.find('.jprice').data('price'));
	
	var sum_weight;
	var all_weight = 0;
	var all_amount = 0;
	
	sum_weight = quantity*weight
	element.find('.product_weight').html(sum_weight.toFixed(3) +' kg');
	all_amount = (price*quantity) + all_amount;
	
	var array = {'weight':parseFloat(sum_weight),'price':parseFloat(all_amount)};
	return array;
}

jQuery.fn.progresbar = function(shipid,shipname,all_amount,all_weight,country_weight,country_poshlina){
	var element = this;
	var proc_weight = 100/country_weight*all_weight;
	var bar_color = 'bg-success';
	if (all_weight > country_weight){
		bar_color = 'bg-danger';
	}
	var remain_weight = jQuery.fn.language('weight_last')+' '+parseFloat(country_weight-all_weight).toFixed(3);
	if (parseFloat(country_weight-all_weight).toFixed(3)<0){
		remain_weight = jQuery.fn.language('limit_exceeded_weight');
	}			
	var progres_weight= jQuery.fn.language('total_weight')+' '+shipname+'<div class="container">'+
	'<div class="row"><div class="col-9">'+
	'<div class="progress" title="'+jQuery.fn.language('hint_weight')+'">'+
	'<div class="progress-bar '+bar_color+'" role="progressbar" style="width: '+proc_weight+'%" aria-valuenow="'+proc_weight+'" aria-valuemin="0" aria-valuemax="100"></div>'+
	'<span class="justify-content-center d-flex position-absolute w-100 bartext">'+
	''+remain_weight+' '+jQuery.fn.language('weight')+
	'</span></div></div>'+
	'<div class="col-3">'+jQuery.fn.language('weight_max')+' '+country_weight+' '+jQuery.fn.language('weight')+'</div></div></div>';
	element.append(progres_weight);
	
	
	var proc_sum = 100/country_poshlina*all_amount;
	var bar_sum_color = 'bg-success';
	if (all_amount > country_poshlina){
		bar_sum_color = 'bg-danger';
	}	
	var remain_poshlina = jQuery.fn.language('duty_free_last')+' '+parseFloat(country_poshlina-all_amount).toFixed(2);
	if (parseFloat(country_poshlina-all_amount).toFixed(2)<0){
		remain_poshlina = jQuery.fn.language('limit_exceeded_duty_free');
	}
	var progres_poshlina = jQuery.fn.language('duty_free_limit')+' '+shipname+'<div class="container">'+
	'<div class="row"><div class="col-9">'+
	'<div class="progress" title="'+jQuery.fn.language('hint_duty_free')+'">'+
	'<div class="progress-bar '+bar_sum_color+'" role="progressbar" style="width: '+proc_sum+'%" aria-valuenow="'+proc_sum+'" aria-valuemin="0" aria-valuemax="100"></div>'+
	'<span class="justify-content-center d-flex position-absolute w-100 bartext">'+
	''+remain_poshlina+' '+jQuery.fn.language('currency')+
	'</span></div></div>'+
	'<div class="col-3">'+jQuery.fn.language('duty_free_max')+' '+country_poshlina+' '+jQuery.fn.language('currency')+'</div></div></div>';		
	element.append(progres_poshlina);
}
