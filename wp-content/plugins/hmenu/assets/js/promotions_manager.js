//PROMOTIONS

//load
jQuery(function(){
	get_promotion();
});

//get promotion
function get_promotion(){
	jQuery.ajax({
		url: ajax_url,
		type: "POST",
		data: {
			action: 'hmenu_getPromotion'
		},
		dataType: "json"
	}).done(function(data){
		if(data){
			show_promotion(data['image'],data['link']);
		}else{
			//default
			show_promotion(plugin_url +'/assets/images/admin/default_promo.png','http://www.heroplugins.com');
		}
	});
}

//show promotion
function show_promotion(img,img_link){
	if(img_link == null || img_link == ''){
		jQuery('.promo_holder').empty().html('<img id="promo_img" src="'+ img +'" />');
	}else{
		jQuery('.promo_holder').empty().html('<a href="'+ img_link +'" target="_blank"><img id="promo_img" src="'+ img +'" /></a>');
	}
	jQuery('#promo_img').bind('load', function(){
		jQuery('.promo_holder').css({
			height: '0px'
		}).fadeIn(0).animate({
			height: jQuery('#promo_img').height() +'px'
		},400);
	});
}