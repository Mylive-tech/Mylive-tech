jQuery(document).ready(function(jQuery) {
	
	"use strict"

	jQuery('body')
	
	.on('submit.mymail','form.mymail-ajax-form', function(){
		var form = jQuery(this),
			data = form.serialize(),
			info = form.find('.mymail-form-info'),
			loader = form.find('.mymail-loader'), c;
			
			if(jQuery.isFunction(window.mymail_pre_submit)){
				c = window.mymail_pre_submit.call(this, data);
				if(c === false) return false;
				if(typeof c !== 'undefined') data = c;
			}
			
			loader.addClass('loading');
			form.addClass('loading').find('.submit-button').prop('disabled', true);
			
			jQuery.post(form.attr('action'), data, function(response){
				
				loader.removeClass('loading');
				
				form.removeClass('loading').find('div.mymail-wrapper').removeClass('error');
				
				info.removeClass('success error');
				
				if(jQuery.isFunction(window.mymail_post_submit)){
					c = window.mymail_post_submit.call(form[0], response);
					if(c === false) return false;
					if(typeof c !== 'undefined') response = c;
				}
				
				if(response.success){

					form.find('.mymail-wrapper').find(':input').prop('disabled', true).filter('.input').val('');

					(response.redirect)
						? location.href = response.redirect
						: info.addClass('success').html(response.html).slideDown(200);
					
					
				}else{
				
					form.find('.submit-button').prop('disabled', false);
					
					if(response.fields)
						jQuery.each(response.fields, function(field){
							
							form.find('.mymail-'+field+'-wrapper').addClass('error');
							
						})
					info.addClass('error').html(response.html).slideDown(200);
				}
				
			}, 'JSON');
			
		return false;
			
	});
	
	
});