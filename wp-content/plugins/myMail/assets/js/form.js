jQuery(document).ready(function(jQuery) {

	"use strict"

	jQuery('body')

	.on('submit.mymail','form.mymail-ajax-form', function(event){

		event.preventDefault();

		var form = jQuery(this),
			data = form.serialize(),
			info = jQuery('<div class="mymail-form-info"></div>'), c;

			if(jQuery.isFunction(window.mymail_pre_submit)){
				c = window.mymail_pre_submit.call(this, data);
				if(c === false) return false;
				if(typeof c !== 'undefined') data = c;
			}

			form.addClass('loading').find('.submit-button').prop('disabled', true);

			jQuery.post(form.attr('action'), data, function(response){

				form.removeClass('loading has-errors').find('div.mymail-wrapper').removeClass('error');

				form.find('.mymail-form-info').remove();

				if(jQuery.isFunction(window.mymail_post_submit)){
					c = window.mymail_post_submit.call(form[0], response);
					if(c === false) return false;
					if(typeof c !== 'undefined') response = c;
				}

				form.find('.submit-button').prop('disabled', false);

				info.html(response.html).prependTo(form);

				if(response.success){

					if(!form.is('.is-profile')) form
						.find('.mymail-form-fields').slideUp(100)
						.find('.mymail-wrapper').find(':input').prop('disabled', true).filter('.input').val('');

					(response.redirect)
						? location.href = response.redirect
						: info.show().addClass('success');

				}else{

					if(response.fields)
						jQuery.each(response.fields, function(field){

							form.addClass('has-errors').find('.mymail-'+field+'-wrapper').addClass('error');

						})
					info.show().addClass('error');
				}

			}, 'JSON');


	});


});
