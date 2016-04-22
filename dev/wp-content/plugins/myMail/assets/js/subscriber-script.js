jQuery(document).ready(function($) {

	"use strict"
	
	var timeout,
		_email = $('#email'),
		_id = $('#ID'),
		_userimage = $('.avatar'),
		_form = $('form#subscriber_form'),
		wpnonce = $('#_wpnonce').val();

	//init the whole thing
	function _init(){
		
		_events();

		$('.piechart').easyPieChart({
			animate: 1000,
			rotate: 180,
			barColor: '#21759B',
			trackColor: '#dedede',
			lineWidth: 9,
			size: 75,
			lineCap: 'square',
			onStep: function(value) {
				this.$el.find('span').text(Math.round(value));
			},
			onStop: function(value) {
				this.$el.find('span').text(Math.round(value));
			}
		});

		
	}
	
	function _events(){

		$('.detail').on('click', function(){

			var _this = $(this).addClass('active'),
				_ul = _this.find('.click-to-edit'),
				_first = _ul.find('> li').first(),
				_last = _ul.find('> li').last();

			if(!_first.is(':hidden')){
				_first.hide();
				_last.show().find('input').first().focus().select();
			}

		});

		$('.show-more-info').on('click', function(){
			$('.more-info').slideToggle(100);
		});

		$('.map.zoomable').on('click', function(){
			var _this = $(this),
				_img = _this.find('img');

			if(!_img.hasClass('zoomed')){
				_img.attr('src', _img.attr('src').replace(/zoom=\d+/, 'zoom=11')).addClass('zoomed');
			}else{
				_img.attr('src', _img.attr('src').replace(/zoom=\d+/, 'zoom=5')).removeClass('zoomed');
			}
		})

		if(typeof jQuery.datepicker == 'object'){
			$('input.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				firstDay: mymailL10n.start_of_week,
				dayNames: mymailL10n.day_names,
				dayNamesMin: mymailL10n.day_names_min,
				monthNames: mymailL10n.month_names,
				prevText: mymailL10n.prev,
				nextText: mymailL10n.next,
				showAnim: 'fadeIn',
				onClose: function () {
					var date = $(this).datepicker('getDate');
					$('.deliverydate').html($(this).val());
				}
			});
		}
							
		_email
		.on('blur', function(){
			var _this = $(this),
				email = $.trim(_this.val().toLowerCase()),
				valid = _verify(email);
				
			if(!valid){
				$('.email-error').slideUp(100,function(){ $(this).remove(); });
				
				$('<p class="email-error">&#9650; '+mymailL10n.invalid_email+'</p>').hide().insertAfter(_this).slideDown(100);
				setTimeout(function(){_this.focus(),1});
				_form.prop('disabled', true);
				
			}else{
				$(this).val(email);
				
				if(_userimage.data('email') != email){
					_userimage.addClass('avatar-loading');
					_getGravatar(email,function(data){
						if(data.success)
							_userimage.data('email',email).removeClass('avatar-loading').css({'background-image': 'url('+data.url.replace(/&amp;/, '&')+')'});
					});
				}
				
			}
			if(!email || !valid)_form.prop('disabled', true);
			_this.trigger('keyup');
			
		})
		.on('keyup', function(){
			var _this = $(this);
			clearTimeout(timeout);
			timeout = setTimeout(function(){
				var email = $.trim(_this.val().toLowerCase()),
				valid = _verify(email);
				if(!valid) return false;
				
				_ajax('check_email', {email:email,id:_id.val()}, function(data){
					_form.prop('disabled', data.exists);
					$('.email-error').slideUp(100,function(){ $(this).remove(); });
					if(data.exists){
						$('<p class="email-error">&#9650; '+mymailL10n.email_exists+'</p>').hide().insertAfter(_this).slideDown(100);
						setTimeout(function(){_this.focus(),1});
					}
				});
			
			},400);
			
			_form.prop('disabled', true);
			
		});

	}
	
	
	function _verify(email){
		
		return !email || /^([\w-+]+(?:\.[\w-]+)*)\@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$|(\[?(\d{1,3}\.){3}\d{1,3}\]?)$/.test(email);
	
	}
	
	function _getGravatar(email, callback){
		_ajax('get_gravatar', {email:email}, callback);
	}
	
	function _ajax(action, data, callback, errorCallback){

		if($.isFunction(data)){
			if($.isFunction(callback)){
				errorCallback = callback;
			}
			callback = data;
			data = {};
		}
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: $.extend({action: 'mymail_'+action, _wpnonce:wpnonce}, data),
			success: function(data, textStatus, jqXHR){
					callback && callback.call(this, data, textStatus, jqXHR);
				},
			error: function(jqXHR, textStatus, errorThrown){
					if(textStatus == 'error' && !errorThrown) return;
					if(console) console.error($.trim(jqXHR.responseText));
					errorCallback && errorCallback.call(this, jqXHR, textStatus, errorThrown);
				},
			dataType: "JSON"
		});
	}
	
	_init();
	
});
