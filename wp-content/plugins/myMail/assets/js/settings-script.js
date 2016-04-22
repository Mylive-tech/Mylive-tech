jQuery(document).ready(function($) {

	"use strict"

	var nav = $('.mainnav'),
		deliverynav = $('#deliverynav'),
		tabs = $('.tab'),
		deliverytabs = $('#tab-delivery').find('.subtab'),
		wpnonce = $('#mymail_nonce').val(),
		reservedtags = $('#reserved-tags').data('tags');

	$('a.external').on('click', function(){
		window.open(this.href);
		return false;
	});

	$('form').on('submit', function(){return false});

	$(window).load(function(){
		if($('#settingsloaded').length){
			$('.submit-form').prop('disabled', false);
			$('form').off('submit');
		}else{
			alert('error loading settings page');
		}
	});

	deliverynav.on('click', 'a.nav-tab', function(){
		deliverynav.find('a').removeClass('nav-tab-active');
		deliverytabs.hide();
		var hash = $(this).addClass('nav-tab-active').attr('href');
		$('#deliverymethod').val(hash.substr(1));
		$('#subtab-'+hash.substr(1)).show();
		return false;
	});


	nav.on('click', 'a', function(){
		nav.find('li').removeClass('active');
		tabs.hide();
		var hash = $(this).parent().addClass('active').find('a').attr('href');
		$('#tab-'+hash.substr(1)).show();
		location.hash = hash;
		$('form').attr('action','options.php'+hash);
		if(hash == '#system_info'){
			var textarea = $('#system_info_content');
			if($.trim(textarea.val())) return;
			textarea.val(mymailL10n.loading+'...');
			_ajax('get_system_info', function(response){

				if(response.log && console)
					console.log(response.log);
				textarea.val(response.msg);
			});
		}
		return false;
	});


	$('.settings-tabs').find('a[href^="#"]').on('click', function(){
		nav.find('a[href="'+$(this).attr('href')+'"]').trigger('click');
	});

	if(location.hash == '#forms' && confirm('The forms are no longer on the settings page. Go there?')){
		location.href = 'edit.php?post_type=newsletter&page=mymail_forms';
	}

	(location.hash && nav.find('a[href="'+location.hash+'"]').length)
		? nav.find('a[href="'+location.hash+'"]').trigger('click')
		: nav.find('a').eq(0).trigger('click');

	$('.system_mail').on('change', function(){
		$('.system_mail_template').prop('disabled', $(this).val() == 0);
	});


	$('#mymail_geoip').on('change', function(){
		($(this).is(':checked'))
			? $('#mymail_geoipcity').prop('disabled', false)
			: $('#mymail_geoipcity').prop('disabled', true).prop('checked', false);
	});
	$('#mymail_geoip').on('change', function(){
		($(this).is(':checked'))
			? $('#load_country_db').prop('disabled', false)
			: $('#load_country_db').prop('disabled', true).prop('checked', false);
	});
	$('#mymail_geoipcity').on('change', function(){
		($(this).is(':checked'))
			? $('#load_city_db').prop('disabled', false)
			: $('#load_city_db').prop('disabled', true).prop('checked', false);
	});

	$('#load_country_db, #load_city_db').on('click', function(){
		var $this = $(this),
			loader = $('.geo-ajax-loading').css({'visibility':'visible'}),
			type = $this.data('type');

		$('button').prop('disabled', true);

		_ajax('load_geo_data', {
			type: type

		}, function(response){

			$('button').prop('disabled', false);
			loader.css({'visibility':'hidden'});
			$this.prop('disabled', false).html(response.buttontext);
			var msg = $('<div class="'+((!response.success) ? 'error' : 'updated')+'"><p>'+response.msg+'</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200,0).delay(200).slideUp(200,function(){msg.remove();});

			if(response.success){
				if(response.path){
					$('#'+type+'_db_path').val(response.path);
				}
			}

		}, function(jqXHR, textStatus, errorThrown){

			$('button').prop('disabled', false);
			loader.css({'visibility':'hidden'});
			$this.prop('disabled', false);
			var msg = $('<div class="error"><p>'+textStatus+' '+jqXHR.status+': '+errorThrown+'</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200,0).delay(200).slideUp(200,function(){msg.remove();});

		});

		return false;
	});

	$('#upload_country_db_btn').on('click', function(){
		$('#upload_country_db').removeClass('hidden');
		return false;
	});

	$('#upload_city_db_btn').on('click', function(){
		$('#upload_city_db').removeClass('hidden');
		return false;
	});

	$('.has-archive-check').on('change', function(){
		($(this).is(':checked'))
			? $('.archive-slug').slideDown(200)
			: $('.archive-slug').slideUp(200);
	});

	$('.edit-slug').on('click', function(){
		$(this).parent().parent().find('span').hide().filter('.edit-slug-area').show().find('input').focus().select();
	});

	$('.users-register').on('change', function(){
		($(this).is(':checked'))
			? $('#'+$(this).data('section')).slideDown(200)
			: $('#'+$(this).data('section')).slideUp(200);
	});
	$('#mymail_dkim').on('change', function(){
		($(this).is(':checked'))
			? $('.dkim-info').slideDown(200)
			: $('.dkim-info').slideUp(200);
	});
	$('#mymail_spf').on('change', function(){
		($(this).is(':checked'))
			? $('.spf-info').slideDown(200)
			: $('.spf-info').slideUp(200);
	});

	$('#bounce_active').on('change', function(){
		($(this).is(':checked'))
			? $('#bounce-options').slideDown(200)
			: $('#bounce-options').slideUp(200);
	});


	$('#mymail_generate_dkim_keys').on('click', function(){
		return ($('#dkim_keys_active').length && confirm(mymailL10n.create_new_keys));
		return false;
	});

	$('input.smtp.secure').on('change', function(){
		$('#mymail_smtp_port').val($(this).data('port'));
	});

	$('#capabilities-table')
		.on('mouseenter', 'label', function(){
			$('#current-cap').stop().html($(this).attr('title')).css('opacity',1).show();
		})
		.on('mouseleave', 'tbody', function(){
			$('#current-cap').fadeOut();
		})
		.on('change', 'input.selectall', function(){
			var $this = $(this);
			$('input.cap-check-'+$this.val()).prop('checked', $this.prop('checked'));
		});

	$('.mymail_sendtest').on('click', function(){
		var $this = $(this),
			loader = $('.test-ajax-loading').css({'visibility':'visible'}),
			basic = $this.data('role') == 'basic',
			to = (basic) ? $('#mymail_testmail').val() : $('#mymail_authenticationmail').val();

		$this.prop('disabled', true);

		_ajax('send_test', {
			test: true,
			basic: basic,
			to: to

		}, function(response){

			if(response.log && console)
				response.success ? console.log(response.log) : console.error(response.log);

			loader.css({'visibility':'hidden'});
			$this.prop('disabled', false);
			var msg = $('<div class="'+((!response.success) ? 'error' : 'updated')+'"><p>'+response.msg+'</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200,0).delay(200).slideUp(200,function(){msg.remove();});

		}, function(jqXHR, textStatus, errorThrown){

			loader.css({'visibility':'hidden'});
			$this.prop('disabled', false);
			var msg = $('<div class="error"><p>'+textStatus+' '+jqXHR.status+': '+errorThrown+'</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(4000).fadeTo(200,0).delay(200).slideUp(200,function(){msg.remove();});

		});
	});



	$('#bounce_ssl').on('change', function(){

		$('#bounce_port').val($(this).is(':checked') ? '995' : '110');

	});

	$('.mymail_bouncetest').on('click', function(){
		var $this = $(this),
			loader = $('.bounce-ajax-loading').css({'visibility':'visible'}),
			status = $('.bouncetest_status').show();

		$this.prop('disabled', true);

		_ajax('bounce_test', {

		}, function(response){

			bounce_test_check(response.identifier, 1, function(){
				$this.prop('disabled', false);
			});

		}, function(jqXHR, textStatus, errorThrown){

			loader.css({'visibility':'hidden'});
			$this.prop('disabled', false);
			status.html(textStatus+' '+jqXHR.status+': '+errorThrown);

		});
	});

	$( ".sortable" ).sortable({
		containment: "parent"
	});


	$('input.cron_radio').on('change', function(){
		$('.cron_opts').hide();
		$('.'+$(this).val()).show();
	});

	$('#mymail_add_tag').on('click', function () {
		var el = $('<div class="tag"><code>{<input type="text" class="tag-key">}</code> &#10152; <input type="text" class="regular-text tag-value"> <a class="tag-remove">&#10005;</a></div>').insertBefore($(this));
		el.find('.tag-key').focus();
	});

	$('.tags')
	.on('change', '.tag-key', function(){
		var _this = $(this),
			_base = _this.parent().parent(),
			val = _sanitize(_this.val());

		if(!val) _this.parent().parent().remove();

		_this.val(val);
		_base.find('.tag-value').attr('name', 'mymail_options[custom_tags]['+val+']');

	})
	.on('click', '.tag-remove', function(){
		$(this).parent().remove();
		return false;
	});

	$('#mymail_add_field').on('click', function(){

		var el = $('<div class="customfield"><a class="customfield-move-up" title="'+mymailL10n.move_up+'">&#9650;</a><a class="customfield-move-down" title="'+mymailL10n.move_down+'">&#9660;</a><div><span class="label">'+mymailL10n.fieldname+':</span><label><input type="text" class="regular-text customfield-name"></label></div><div><span class="label">'+mymailL10n.tag+':</span><span><code>{</code><input type="text" class="customfield-key code"><code>}</code></span></div><div><span class="label">'+mymailL10n.type+':</span><select class="customfield-type"><option value="textfield">'+mymailL10n.textfield+'</option><option value="textarea">'+mymailL10n.textarea+'</option><option value="dropdown">'+mymailL10n.dropdown+'</option><option value="radio">'+mymailL10n.radio+'</option><option value="checkbox">'+mymailL10n.checkbox+'</option><option value="date">'+mymailL10n.datefield+'</option></select></div><ul class="customfield-additional customfield-dropdown customfield-radio"><li><ul class="customfield-values"><li><span>&nbsp;</span> <span class="customfield-value-box"><input type="text" class="regular-text customfield-value" value=""> <label><input type="radio" value="" title="'+mymailL10n.default_selected+'" class="customfield-default" disabled> '+mymailL10n.default_field+'</label></span></li></ul><span>&nbsp;</span> <a class="customfield-value-add">'+mymailL10n.add_field+'</a></li></ul><div class="customfield-additional customfield-checkbox"><span>&nbsp;</span><label><input type="checkbox" value="1" title="'+mymailL10n.default_field+'" class="customfield-default" disabled> '+mymailL10n.default_checked+'</label></div><a class="customfield-remove">remove field</a><br></div>').appendTo($('.customfields').eq(0));
		el.find('.customfield-name').focus();
	});

	$('#sync_list_check').on('change', function(){
		$('#sync_list').slideToggle(200);
		$('.sync-button').prop('disabled', true);
	});

	$('#sync_list')
	.on('click', '#add_sync_item', function(){
		var items = $('.mymail_syncitem');

		items.eq(0).clone().insertAfter(items.last()).removeAttr('title').find('select').each(function(){
			$(this).attr('name', $(this).attr('name').replace('[synclist][0]', '[synclist]['+items.length+']'));
		});

		$('.sync-button').prop('disabled', true);

	})
	.on('click', '.remove-sync-item', function(){
		$(this).parent().remove();
		$('.sync-button').prop('disabled', true);
	})
	.on('change', 'select', function(){
		$('.sync-button').prop('disabled', true);
	})
	.on('click', '#sync_subscribers_wp', function(){
		if(event.target == this && !confirm(mymailL10n.sync_subscriber)) return false;

		var _this = $(this),
			loader = $('.sync-ajax-loading').css({'visibility':'visible'});

			$('.sync-button').prop('disabled', true);

		_ajax('sync_all_subscriber',{offset: _this.data('offset')}, function(response){

			$('.sync-button').prop('disabled', false);
			if(response.success && response.count){
				_this.data('offset', response.offset).trigger('click');
			}else{
				loader.css({'visibility':'hidden'});
				_this.data('offset', 0);
			}

		}, function(jqXHR, textStatus, errorThrown){

			loader.css({'visibility':'hidden'});
			$('.sync-button').prop('disabled', false);

		});
		return false;
	})
	.on('click', '#sync_wp_subscribers', function(){
		if(event.target == this && !confirm(mymailL10n.sync_wp_user)) return false;

		var _this = $(this),
			loader = $('.sync-ajax-loading').css({'visibility':'visible'});

			$('.sync-button').prop('disabled', true);

		_ajax('sync_all_wp_user',{offset: _this.data('offset')}, function(response){

			$('.sync-button').prop('disabled', false);
			if(response.success && response.count){
				_this.data('offset', response.offset).trigger('click');
			}else{
				loader.css({'visibility':'hidden'});
				_this.data('offset', 0);
			}

		}, function(jqXHR, textStatus, errorThrown){

			loader.css({'visibility':'hidden'});
			$('.sync-button').prop('disabled', false);

		});
		return false;
	})


	$('.customfields')
	.on('change', '.customfield-name', function(){
		var _this = $(this),
			_tagfield = _this.parent().parent().parent().find('.customfield-key');

		if(!_tagfield.val()) _tagfield.val(_this.val()).trigger('change');
	})
	.on('change', '.customfield-key', function(){
		var _this = $(this),
			_base = _this.parent().parent().parent(),
			val = _sanitize(_this.val());

		if(!val) _this.parent().parent().remove();

		_this.val(val);
		_base.find('.customfield-name').attr('name', 'mymail_options[custom_field]['+val+'][name]');
		_base.find('.customfield-type').attr('name', 'mymail_options[custom_field]['+val+'][type]');
		_base.find('.customfield-value').attr('name', 'mymail_options[custom_field]['+val+'][values][]');
		_base.find('.customfield-default').attr('name', 'mymail_options[custom_field]['+val+'][default]');

	})
	.on('click', '.customfield-remove', function(){
		$(this).parent().remove();
	})
	.on('click', '.customfield-move-up', function(){

		var _this = $(this).parent();
		_this.insertBefore(_this.prev());

	})
	.on('click', '.customfield-move-down', function(){

		var _this = $(this).parent();
		_this.insertAfter(_this.next());

	})
	.on('change', '.customfield-type', function(){

		var type = $(this).val();
		$(this).parent().parent().find('.customfield-additional').slideUp(200).find('input').prop('disabled', true);

		if(type != 'textfield'){
			$(this).parent().parent().find('.customfield-'+type).stop().slideDown(200).find('input').prop('disabled', false);
		}
	})
	.on('change', '.customfield-value', function(){

		$(this).next().find('input').val($(this).val());
	})
	.on('click', 'a.customfield-value-remove', function(){
		$(this).parent().parent().remove();
	})
	.on('click', 'a.customfield-value-add', function(){
		var field = $(this).parent().find('li').eq(0).clone();
		field.appendTo($(this).parent().find('ul')).find('input').val('').focus().select();
	});

	function bounce_test_check(identifier, count, callback){
		var $this = $(this),
			loader = $('.bounce-ajax-loading').css({'visibility':'visible'}),
			status = $('.bouncetest_status');

		_ajax('bounce_test_check',{identifier: identifier, passes: count}, function(response){

			status.html(response.msg);

			if(response.complete){
				loader.css({'visibility':'hidden'});
				callback && callback();
			}else{
				setTimeout(function(){
					bounce_test_check(identifier, ++count, callback);
				}, 1000);
			}

		}, function(jqXHR, textStatus, errorThrown){

			loader.css({'visibility':'hidden'});
			$this.prop('disabled', false);
			status.html(textStatus+' '+jqXHR.status+': '+errorThrown);

		});


	}

	function _sanitize(string){
		var tag = $.trim(string).toLowerCase().replace(/ /g,'-').replace(/[^a-z0-9_-]*/g,'');
		if($.inArray(tag, reservedtags) != -1){
			alert(sprintf(mymailL10n.reserved_tag,'"'+tag+'"' ));
			tag += '-a';
		}
		return tag;
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

	function sprintf() {
		var a = Array.prototype.slice.call(arguments), str = a.shift();
		while(a.length)	str = str.replace('%s', a.shift());
		return str;
	}

});
