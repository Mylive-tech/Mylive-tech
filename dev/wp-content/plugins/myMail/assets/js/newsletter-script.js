jQuery(document).ready(function ($) {

	"use strict"

	var _iframe = $('#mymail_iframe'),
		_ibody = _iframe.contents().find('body'),
		_idoc, _container = $('#mymail_template .inside'),
		_disabled = !! $('#mymail_disabled').val(),
		_title = $('#title'),
		_subject = $('#mymail_subject'),
		_preheader = $('#mymail_preheader'),
		_content = $('#content'),
		_excerpt = $('#excerpt'),
		_plaintext = $('#plain-text-wrap'),
		_head = $('#head'),
		_obar = $('#optionbar'),
		_undo = [],
		style = '',
		campaign_id = $('#post_ID').val(),
		_currentundo = 0,
		_clickbadgestats = $('#clickmap-stats'),
		_mymaildata = $('[name^="mymail_data"]'),
		_colorpickers = $('.mymail-color'),
		wpnonce = $('#mymail_nonce').val(),
		iframeloaded = false,
		timeout, refreshtimout, modules, optionbar, charts, editbar, animateDOM = $.browser.webkit ? $('body') : $('html'),
		getSelect, selectRange, isDisabled = false,
		is_touch_device = 'ontouchstart' in document.documentElement,
		isTinyMCE = typeof tinymce == 'object';


	//init the whole thing
	function _init() {

		_disable(true);
		_time();
		
		//set the document of the iframe cross browser like
		_idoc = (_iframe[0].contentWindow || _iframe[0].contentDocument);
		if (_idoc.document) _idoc = _idoc.document;

		_events();
		
		var iframeloadinterval = setTimeout(function () {
			if (!iframeloaded) _iframe.trigger('load');
		}, 5000);
		
		_container.addClass('load');
		
		_iframe.load(function () {
		
			if (iframeloaded) return false;
			if (!_disabled) {
				if(!optionbar) optionbar = new _optionbar();
				if(!editbar) editbar = new _editbar();
				if(!modules) modules = new _modules();
			} else {}
			
			//open all links in new window
			_container.removeClass('load');

			_enable();
			iframeloaded = true;
			_refresh();
			_resize(0,0);
			_save();
			
			clearInterval(iframeloadinterval);
			
			//prevent first time autosave
			if (typeof autosaveLast != 'undefined')
				window.autosaveLast = (typeof wp != 'undefined' && wp.autosave) ? wp.autosave.getCompareString() : _title.val() + _content.val() + _excerpt.val();
			
			if (typeof wp != 'undefined'){
				if(wp.autosave && wp.autosave.server && typeof wp.autosave.server.postChanged == 'function'){
					window.mymail_autosave = _getAutosaveString();
					//overwrite autosave
					wp.autosave.server.postChanged = function(){
						return window.mymail_autosave != _getAutosaveString();
					}
				}
			}
			if(_disabled){
				_title.prop('disabled', true);
				//overwrite autosave function since we don't need it
				window.autosave = wp.autosave = function(){return true;};
				window.onbeforeunload = null;

			}else{
				
			}


		});

		$(window).on('resize.mymail', _refresh);
		window.mymail_refresh = function(){
			_refresh();
			_save();
		}
		window.mymail_hideButtons = function(){
			_container.find('.content.mymail-btn').remove();
		}
		$('#publish').on('click', _save);
		
		//switch to autoresponder if referer is right
		if(/post_status=autoresponder/.test($('#referredby').val())) $('#mymail_delivery').find('a[href="#autoresponder"]').click();

		if ($.browser.msie) $('body').addClass('ie');
		if (is_touch_device) $('body').addClass('touch');
		
		
	}


	function _events() {

		$('body').on('click', 'a.external', function(){
			window.open(this.href);
			return false;
		});
		
		if (!_disabled) {
			_title.change(function () {
				if (!_subject.val()) _subject.val($(this).val());
			});
			
			$('#use_pwd')
			.on('change', function(){
				$('#password-wrap').slideToggle(200).find('input').focus().select();
			});

			$('#post').on('submit', function(){
				if(isDisabled) return false;
				_save();
			});

			$('#local-storage-notice')
			.on('click', '.restore-backup, .undo-restore-backup', function(){
				setTimeout(function(){
					var content = _content.val();
					if(!content){
						iframeloaded = modules = false;
						_iframe[0].contentWindow.location.reload();
					}else{
						_setContent(content, false);
						_refresh();
					}
				},100);
			});
			
			$('#mymail_delivery')
				.on('change', 'input.timezone', function(){

					$('.active_wrap').toggleClass('timezone-enabled');
				})
				.on('change', 'input.autoresponder-timezone', function(){

					$('.autoresponderfield-mymail_autoresponder_timebased').toggleClass('timezone-enabled');
				});
			
			$('#editbar, #mymail_delivery')
				.on('change', '.dynamic_embed_options_taxonomy', function(){
					var $this = $(this),
						val = $this.val();
						$this.parent().find('.button').remove();
					if(val != -1){
						if($this.parent().find('select').length < $this.find('option').length-1)
							$(' <a class="button button-small add_embed_options_taxonomy">' +mymailL10n.add+ '</a>').insertAfter($this);
					}else{
						$this.parent().html('').append($this);
					}	
				
					return false;
				})
				.on('click', '.add_embed_options_taxonomy', function(){
					var $this = $(this),
						el = $this.prev().clone();
						
					el.insertBefore($this).val('-1');
					$('<span> '+mymailL10n.or+ ' </span>').insertBefore(el);
					$this.remove();
					
					return false;
				});
				
			$('#autoresponder-post_type').on('change', function(){
				var cats = $('#autoresponder-taxonomies');
				cats.find('select').prop('disabled', true);
				_ajax('get_post_term_dropdown', {
					labels:false,
					names:true,
					posttype: $(this).val()
				}, function (response) {
					if (response.success) {
						cats.html(response.html);
					}
				}, function(jqXHR, textStatus, errorThrown){
				
					loader(false);
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
		
				});
			});

			$('.default-value').on('click', function () {
				var _this = $(this);
				$('#' + _this.data('for')).val(_this.data('value'));
			});
			
			$('.category-tabs').on('click', 'a', function(){
				var _this = $(this),
					href = _this.attr('href');
				
				$('#mymail_delivery').find('.tabs-panel').hide();
				$('#mymail_delivery').find('.tabs').removeClass('tabs');
				_this.parent().addClass('tabs');
				$(href).show();
				$('#mymail_is_autoresponder').val((href == '#autoresponder') ? 1 : '');
				return false;
			});

			$('.mymail_sendtest').on('click', function () {
				var $this = $(this),
					loader = $('#delivery-ajax-loading').css({'display': 'inline'});
					$this.prop('disabled', true);
				_save();
				_ajax('send_test', {
					ID: campaign_id,
					subject: _subject.val(),
					from: $('#mymail_from').val(),
					from_name: $('#mymail_from-name').val(),
					to: $('#mymail_testmail').val(),
					reply_to: $('#mymail_reply_to').val(),
					template: $('#mymail_template_name').val(),
					preheader: $('#mymail_preheader').val(),
					embed_images: $('#mymail_data_embed_images').is(':checked'),
					issue: $('#mymail_autoresponder_issue').val(),
					content: _content.val(),
					plaintext: _excerpt.val(),
					autoplain: $('#plaintext').is(':checked'),
					head: _head.val()
					
				}, function (response) {
				
					loader.hide();
					$this.prop('disabled', false);
					var msg = $('<div class="' + ((!response.success) ? 'error' : 'updated') + '"><p>' + response.msg + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
						msg.remove();
					});
				}, function(jqXHR, textStatus, errorThrown){
			
					loader.hide();
					$this.prop('disabled', false);
					var msg = $('<div class="error"><p>'+textStatus+' '+jqXHR.status+': '+errorThrown+'</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
						msg.remove();
					});
					
				})
			});
			
			$('.mymail_spamscore').on('click', function () {
				var $this = $(this),
					loader = $('#delivery-ajax-loading').css({'display': 'inline'}),
					progress = $('#spam_score_progress').removeClass('spam-score').slideDown(200),
					progressbar = progress.find('.bar');
					$this.prop('disabled', true);
					
					$('.score').html('');
				
				_save();
				
				progressbar.css({'width': '0'}).css({'width': '20%'});
				
				_ajax('send_test', {
					ID: campaign_id,
					subject: _subject.val(),
					from: $('#mymail_from').val(),
					from_name: $('#mymail_from-name').val(),
					to: $('#mymail_testmail').val(),
					reply_to: $('#mymail_reply_to').val(),
					spamtest: true,
					template: $('#mymail_template_name').val(),
					preheader: $('#mymail_preheader').val(),
					embed_images: $('#mymail_data_embed_images').is(':checked'),
					issue: $('#mymail_autoresponder_issue').val(),
					content: _content.val(),
					plaintext: _excerpt.val(),
					autoplain: $('#plaintext').is(':checked'),
					head: _head.val()
					
				}, function (response) {
				
					if(response.success){
						
						progressbar.css({'width': '40%'});
						check(response.id,1);
						
					}else{
					
						loader.hide();
						progress.slideUp(200);
						var msg = $('<div class="error"><p>' + response.msg + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
							msg.remove();
						});
						
					}
				}, function(jqXHR, textStatus, errorThrown){
			
					loader.hide();
					$this.prop('disabled', false);
					var msg = $('<div class="error"><p>'+textStatus+' '+jqXHR.status+': '+errorThrown+'</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
						msg.remove();
					});
					
				})
				
				function check(id, round){
					
					_ajax('check_spam_score', {
						ID: id,
					}, function (response) {
					
						
						if(response.score){
							
							loader.hide();
							$this.prop('disabled', false);
							progress.addClass('spam-score');
							progressbar.css({'width': (parseFloat(response.score)*10)+'%'}, 400);
							
						$('.score').html('<strong>'+sprintf(mymailL10n.yourscore, response.score)+'</strong>:<br>'+mymailL10n.yourscores[Math.floor((response.score/10)*mymailL10n.yourscores.length)]);
							
							
						}else{
						
							if(round <= 5 && !response.abort){
							
								var percentage = (round*10)+50;
								
								progressbar.css({'width': (percentage)+'%'}, round*400+5000);
								setTimeout(function(){check(id, ++round);}, round*400);
								
							}else{
							
								loader.hide();
								$this.prop('disabled', false);
					
								progressbar.css({'width': '100%'}, 10);
								progress.slideUp(200);
								
								var msg = $('<div class="error"><p>' + response.msg + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
									msg.remove();
								});
								
							}
						
							
						}
					}, function(jqXHR, textStatus, errorThrown){
				
						loader.hide();
						$this.prop('disabled', false);
						var msg = $('<div class="error"><p>'+textStatus+' '+jqXHR.status+': '+errorThrown+'</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
							msg.remove();
						});
						
					})
				}
				
			});

			$('#mymail_data_active').on('change', function () {
				var checked = $(this).is(':checked');
				(checked) ? $('.active_wrap').addClass('disabled') : $('.active_wrap').removeClass('disabled');
				$('.deliverydate, .deliverytime').prop('disabled', !checked);

			});
			
			$('#mymail_data_autoresponder_active').on('change', function () {
				var checked = $(this).is(':checked');
				(checked) ? $('.autoresponder_active_wrap').addClass('disabled') : $('.autoresponder_active_wrap').removeClass('disabled');

			});

			$('input.color').minicolors({
				changeDelay: 50,
				control: 'brightness',
				letterCase: 'uppercase',
				position: 'left',
				swatchPosition: 'right',
				change: function () {
					$(this).trigger('change');
				}
			}).on('change', function () {
				var _this = $(this);
				var from = _this.data('value');
				_changeColor(from, _this.val(), _this, true);
			});
			
			if(typeof jQuery.datepicker == 'object'){
				$('input.datepicker').datepicker({
					dateFormat: 'yy-mm-dd',
					minDate: new Date(),
					firstDay: mymailL10n.start_of_week,
					showWeek: true,
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

				$('input.datepicker.nolimit').datepicker( "option", "minDate", null );

							
			}else{
			
				$('input.datepicker').prop('readonly', false);
				
			}
			
			$('input.datepicker').on('focus', function () {
				$(this).removeClass('inactive').trigger('click');
			}).on('blur', function () {
				$('.deliverydate').html($(this).val());
				$(this).addClass('inactive');
			}).on('change', function () {});

			$('input.deliverytime').on('blur', function () {
				$(document).unbind('.mymail_deliverytime');
			}).on('focus, click', function (event) {
				var $this = $(this),
					input = $(this)[0],
					l = $this.offset().left;
				if (event.clientX - l > 23) {
					var c = 1,
						startPos = 3,
						endPos = 5;
				} else {
					var c = 0,
						startPos = 0,
						endPos = 2;
				}

				$(document).unbind('.mymail_deliverytime').on('keypress.mymail_deliverytime', function (event) {
					if (event.keyCode == 9) {
						return (c = !c) ? !selectRange(input, 3, 5) : (event.shiftKey) ? !selectRange(input, 0, 2) : true;
					}

				}).on('keyup.mymail_deliverytime', function (event) {
					if ($this.val().length == 1) {
						$this.val($this.val() + ':00');
						selectRange(input, 1, 1);
					}
					if (document.activeElement.selectionStart == 2) {
						if ($this.val().substr(0, 2) > 23) {
							$this.trigger('change');
							return false;
						}
						selectRange(input, 3, 5);
					}
				});
				selectRange(input, startPos, endPos);

			}).on('change', function () {
				var $this = $(this),
					val = $this.val(), time;
				$this.addClass('inactive');
				if (!/^\d+:\d+$/.test(val)) {

					if (val.length == 1) {
						val = "0" + val + ":00";
					} else if (val.length == 2) {
						val = val + ":00";
					} else if (val.length == 3) {
						val = val.substr(0, 2) + ":" + val.substr(2, 3) + "0";
					} else if (val.length == 4) {
						val = val.substr(0, 2) + ":" + val.substr(2, 4);
					}
				}
				time = val.split(':');

				if (!/\d\d:\d\d$/.test(val) && val != "" || time[0] > 23 || time[1] > 59) {
					$this.val('00:00').focus();
					selectRange($this[0], 0, 2);
				} else {
					$this.val(val);
				}
			})
			
			$('#mymail_autoresponder_action').on('change', function(){
				$('#autoresponder_wrap').removeAttr('class').addClass('autoresponder-'+$(this).val());
			});
			
			$('#mymail_autoresponder_advanced_check').on('change', function(){
				$('#mymail_autoresponder_advanced').slideToggle();
			});
			
			$('#mymail_autoresponder_advanced')
			.on('click', '.add-condition', function () {
				var cond = $('.mymail_autoresponder_condition'),
					id = cond.length,
					clone = cond.last().clone();
				
				clone.hide().removeAttr('id').insertAfter(cond.last()).slideDown();
					$.each(clone.find('input, select'), function(){
						var name = $(this).val('').attr('name');
						$(this).attr('name', name.replace(/\[\d+\]/, '['+id+']'));
					});
			})
			.on('click', '.remove-condition', function () {
				$(this).parent().parent().slideUp(function(){$(this).remove()});
			});
			
			$('#autoresponder_wrap')
			.on('click', '.mymail_autoresponder_timebased-end-schedule', function () {
				var checked = $(this).is(':checked');
				(checked) ? $('.mymail_autoresponder_timebased-end-schedule-field').slideDown() : $('.mymail_autoresponder_timebased-end-schedule-field').slideUp();
			});

			$('#mymail_receivers')
			.on('change', 'input.list', function () {
				var lists = [], conditions = [],
					inputs = $('#list-checkboxes').find('input, select'),
					listinputs = $('#list-checkboxes').find('input.list'),
					extra = $('#list_extra'),
					data = {},
					total = $('#mymail_total');

				$('input.list-parent-'+$(this).val()).prop('checked', $(this).prop('checked'));
					
				$.each(listinputs, function () {
					var id = $(this).val();
					if($(this).is(':checked')) lists.push(id);
				});
				
				data.lists = lists;
				data.ignore_lists = $('#ignore_lists').is(':checked');
				
				if(extra.is(':checked')){
					$.each($('.mymail_list_condition'), function(){
						var _this = $(this),
							_select = _this.find('select'),
							_input = _this.find('input');
						
						conditions.push( {
							field: _select.eq(0).val(),
							operator: _select.eq(1).val(),
							value: _input.eq(0).val()
						} );
						
					});

					data.operator = $('#mymail_list_operator').val();
					data.conditions = conditions;

				}
				
				total.addClass('loading');
				
				_disable();

				_ajax('get_totals', data, function (response) {
					_enable();
					total.removeClass('loading').html(response.totalformated);
					
				}, function(jqXHR, textStatus, errorThrown){
					_enable();
					total.removeClass('loading').html('?');
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				});
				
			}).on('change', '#all_lists', function () {
				$('#list-checkboxes').find('input.list').prop('checked', $(this).is(':checked')).eq(0).trigger('change');
				
			}).on('change', '#ignore_lists', function () {
				var checked = $(this).is(':checked');
				$('#list-checkboxes').each(function(){
					(checked) ? $(this).slideUp(200) : $(this).slideDown(200);
				}).find('input.list').eq(0).trigger('change');
			
			}).on('change', '#mymail_list_operator', function () {
				$('#mymail_list_conditions')
				.removeClass('operator-is-OR operator-is-AND')
				.addClass('operator-is-'+$(this).val());

			});

			$('#mymail_total').on('click', function(){
				$('#list-checkboxes').find('input.list').eq(0).trigger('change');
			});


			$('#mymail_list_advanced')
				.on('click', '.add-condition', function () {
					var cond = $('.mymail_list_condition'),
						id = cond.length,
						clone = cond.last().clone();
					
					clone.hide().removeAttr('id').insertAfter(cond.last()).slideDown();
						$.each(clone.find('input, select'), function(){
							var name = $(this).prop('disabled', false).val('').attr('name');
							$(this).attr('name', name.replace(/\[\d+\]/, '['+id+']')).removeClass('hasDatepicker').removeAttr('id');
						});
				})
				.on('click', '.remove-condition', function () {
					$(this).parent().parent().slideUp(function(){
						$(this).remove();
						$('#list-checkboxes').find('input.list').eq(0).trigger('change');
					});
					
				})
				.on('change.datefields', 'select.condition-field', function () {
					var _this = $(this);
					if(typeof jQuery.datepicker != 'object') return;

					if(_this.parent().find('input').data("datepicker"))
						_this.parent().find('input').datepicker('destroy');

					if($.inArray(_this.val(), mymaildata.datefields) !== -1){

						_this.parent().find('input').datepicker({
							dateFormat: 'yy-mm-dd',
							firstDay: mymailL10n.start_of_week,
							showWeek: true,
							dayNames: mymailL10n.day_names,
							dayNamesMin: mymailL10n.day_names_min,
							monthNames: mymailL10n.month_names,
							prevText: mymailL10n.prev,
							nextText: mymailL10n.next,
							showAnim: 'fadeIn'
						});

					}
				})
				.on('change', 'select, input', function () {
					$('#list-checkboxes').find('input.list').eq(0).trigger('change');
				})
				.find('select.condition-field').trigger('change.datefields');
			
			$('#list_extra').on('change', function () {
				if($(this).is(':checked')){
					$('#mymail_list_advanced').slideDown();
				}else{
					$('#mymail_list_advanced').slideUp();
				}
				$('#list-checkboxes').find('input.list').eq(0).trigger('change');
			});
			
			$('#mymail_options').on('click', 'a.default-value', function () {
				var el = $(this).prev().find('input'),
					color = el.data('default');
				el.minicolors('value', color);
				return false;
			}).on('click', 'ul.colorschema', function () {
				var colorfields = $('#mymail_options').find('input.color'),
					li = $(this).find('li.colorschema-field');
					
				_disable();
				
				$.each(li, function (i) {
					var color = li.eq(i).data('hex');
					colorfields.eq(i).minicolors('value', color);
				});
				
				_enable();

			}).on('click', 'a.savecolorschema', function () {
				var colors = $.map($('#mymail_options').find('.color'), function (e) {
					return $(e).val();
				});
				
				var loader = $('#colorschema-ajax-loading').css({ 'display': 'inline' });

				_ajax('save_color_schema', {
					template: $('#mymail_template_name').val(),
					colors: colors
				}, function (response) {
					loader.hide();
					if (response.success) {
						$('.colorschema').last().after($(response.html).hide().fadeIn());
					}
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				})

			}).on('click', '.colorschema-delete', function(){
				
				if(confirm(mymailL10n.delete_colorschema)){
					
					var schema = $(this).parent().parent();
					var loader = $('#colorschema-ajax-loading').css({ 'display': 'inline' });
					_ajax('delete_color_schema', {
						template: $('#mymail_template_name').val(),
						hash: schema.data('hash')
					}, function (response) {
						loader.hide();
						if(response.success){
							schema.fadeOut(100,function(){schema.remove()});
						}
					}, function(jqXHR, textStatus, errorThrown){
						loader.hide();
						alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
					});
					
				}
				
				return false;
				
			}).on('click', '.colorschema-delete-all', function(){
				
				if(confirm(mymailL10n.delete_colorschema_all)){
					
					var schema = $('.colorschema.custom');
					var loader = $('#colorschema-ajax-loading').css({ 'display': 'inline' });
					_ajax('delete_color_schema_all', {
						template: $('#mymail_template_name').val(),
					}, function (response) {
						loader.hide();
						if(response.success){
							schema.fadeOut(100,function(){schema.remove()});
						}
					}, function(jqXHR, textStatus, errorThrown){
						loader.hide();
						alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
					});
					
				}
				
				return false;
				
			}).on('change', '#mymail_version', function () {
				var val = $(this).val();
				_changeElements(val);

			}).on('click', 'ul.backgrounds ul a', function () {
				$('ul.backgrounds').find('a').removeClass('active');
				var base = $(this).parent().parent().data('base'),
					val = $(this).addClass('active').data('file');
					
				if(!val) base = '';
				$('#mymail_background').val(base + val);
				_changeBG(base + val);
				
			}).on('mouseenter', 'ul.backgrounds a', function () {
				var base = $(this).parent().parent().data('base'),
					file = $(this).data('file');

				if (file) $('ul.backgrounds a').eq(0).css({
					'background-image': 'url(' + base + $(this).data('file') + ')'
				});
			}).on('mouseleave', 'ul.backgrounds a', function () {
				$('ul.backgrounds a').eq(0).css({
					'background-image': 'url(' + $.trim($('#mymail_background').val()) + ')'
				});
			}).on('change', 'input.background-link', function () {
				var _this = $(this);
				var from = _this.data('value');
				_changeBackgroundLink(from, _this.val(), _this, true);

			});

			_container.on('click', 'a.addbutton', function () {
				var data = $(this).data();

				editbar.open({
					type: 'btn',
					offset: data.offset,
					element: $('<a href="" editable label="Button"><img alt=""></a>').appendTo(data.element),
					name: data.name
				});
				return false;
			}).on('click', 'a.addrepeater', function () {
				var data = $(this).data();
				
				data.element.clone().insertAfter(data.element);
				_refresh();
	
				return false;
			}).on('click', 'a.removerepeater', function () {
				var data = $(this).data();
				
				data.element.remove();
				_refresh();

				return false;
			});
			
			$('.device-list').on('click', 'a', function(){
				var size = $(this).data('size'),
					zoom = size == '320x480' ? 0.5 : size == '480x320' ? 0.75 : 1;
					
				$('.mymail_campaign_preview').removeAttr('class').addClass('mymail_campaign_preview device-'+size);
				
				$('#mymail_campaign_preview_iframe').data('zoom', zoom).trigger('load');
				
			});
			
			$('#mymail_campaign_preview_iframe').on('load', function(){
				var body = $('#mymail_campaign_preview_iframe').contents().find('body'),
					style = body.find('style').text(),
					hasqueries = /@media/.test(style);
					
				if(!hasqueries){
					var zoom = $('#mymail_campaign_preview_iframe').data('zoom');
					body.css({
						'zoom': zoom,
						'-moz-transform': 'scale('+zoom+')',
						'-moz-transform-origin': '0 0',
						'-o-transform': 'scale('+zoom+')',
						'-o-transform-origin': '0 0',
						'transform': 'scale('+zoom+')',
						'transform-origin': '0 0',
					});
				}
				
			});

			_plaintext
			.on('click', 'a.button', function(){
				var oldval = _excerpt.val();
				_excerpt.val(mymailL10n.loading);
				_ajax('get_plaintext', {
					html: _getContent()
				}, function (response) {
					_excerpt.val(response);
				}, function(jqXHR, textStatus, errorThrown){
					_excerpt.val(oldval);
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				}, 'HTML');

			})
			.on('change', '#plaintext', function(){
				var checked = $(this).is(':checked');
				_excerpt.prop('disabled', checked)[checked ? 'addClass' : 'removeClass']('disabled');
			});
			
			if(typeof wp != 'undefined' && wp.heartbeat){
			
				$(document)
				.on('heartbeat-send', function(e, data) {

					if(data['wp_autosave']){
						data['wp_autosave']['content'] = _getContent();
						data['wp_autosave']['excerpt'] = _excerpt.val();
						data['mymaildata'] = _mymaildata.serialize();

					}

				})
			}			
			
		} else {
		
			_title.prop('disabled', true);
			
			$('#change-permalinks').remove();
			if (typeof autosavePeriodical != 'undefined') autosavePeriodical.repeat = false;
			
			$('#show_recipients').on('click', function () {
				var $this = $(this),
					list = $('#recipients-list'),
					loader = $('#recipients-ajax-loading');
				
				if(!list.is(':hidden')){
					$this.removeClass('open');
					list.slideUp(100);
					return false;
				}	
				loader.css({ 'display': 'inline' });
				
				_ajax('get_recipients', {
					id: campaign_id
				}, function (response) {
					$this.addClass('open');
					loader.hide();
					list.html(response.html).slideDown(100);
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				})
				return false;
			});
			$('#show_clicks').on('click', function () {
				var $this = $(this),
					list = $('#clicks-list'),
					loader = $('#clicks-ajax-loading');
				
				if(!list.is(':hidden')){
					$this.removeClass('open');
					list.slideUp(100);
					return false;
				}	
				loader.css({ 'display': 'inline' });
				
				_ajax('get_clicks', {
					id: campaign_id
				}, function (response) {
					$this.addClass('open');
					loader.hide();
					list.html(response.html).slideDown(100);
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				})
				return false;
			});
			$('#show_environment').on('click', function () {
				var $this = $(this),
					list = $('#environment-list'),
					loader = $('#environment-ajax-loading');
				
				if(!list.is(':hidden')){
					$this.removeClass('open');
					list.slideUp(100);
					return false;
				}	
				loader.css({ 'display': 'inline' });
				
				_ajax('get_environment', {
					id: campaign_id
				}, function (response) {
					$this.addClass('open');
					loader.hide();
					list.html(response.html).slideDown(100);
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				})
				return false;
			});
			$('#show_geolocation').on('click', function () {
				var $this = $(this),
					list = $('#geolocation-list'),
					loader = $('#geolocation-ajax-loading');
				
				if(!list.is(':hidden')){
					$this.removeClass('open');
					list.slideUp(100);
					return false;
				}	
				loader.css({ 'display': 'inline' });

				
				_ajax('get_geolocation', {
					id: campaign_id
				}, function (response) {
					$this.addClass('open');
					loader.hide();
					list.html(response.html).slideDown(100, function(){

					var data, countrydata,
						mapoptions = {
							legend: false,
							region: 'world',
							resolution: 'countries',
							datalessRegionColor: '#ffffff',
							enableRegionInteractivity: true,
							colors: ['#d7f1fc','#21759B'],
							backgroundColor: {
								fill: 'none',
								stroke: null,
								strokeWidth: 0
							}
						},
						hash;

					var geomap;
		
					google.load('visualization', '1.0', {
						packages: ['geochart', 'corechart'],
						callback: function(){

							geomap = new google.visualization.GeoChart(document.getElementById('countries_map'));
							data = countrydata = google.visualization.arrayToDataTable(response.countrydata);

							if(location.hash && (hash = location.hash.match(/region=([A-Z]{2})/))){
								regionClick(hash[1]);
							}else{
								draw();
							}
							
							google.visualization.events.addListener(geomap, 'regionClick', regionClick);
					
						}
					});
					
					$('a.zoomout').on('click', function(){
						showWorld();
						return false;
					});
			
					$('#countries_table').find('tbody').find('tr').on('click', function(){
						var code = $(this).data('code');
						(code == 'unknown' || !code)
							? showWorld()
							: regionClick(code);
							
						return false;
					});
			
					function showWorld(){
						var options = {
							'region': 'world',
							'displayMode': 'region',
							'resolution': 'countries',
							'colors': ['#d7f1fc','#21759B']
						};
						
						data = countrydata;
						draw(options);
						
						$('#countries_table').find('tr').removeClass('wp-ui-highlight');
						$('#mapinfo').hide();
						
						location.hash = '#region=';
						
					}
	
					function regionClick(event){


						var options = {},
							region = event.region ? event.region : event,
							d;
						
						if(region.match(/-/)) return false;
						
						options['region'] = region;

						(response.unknown_cities[region])
							? $('#mapinfo').show().html('+ '+response.unknown_cities[region]+' unknown locations')
							: $('#mapinfo').hide();
						
						d = response.geodata[region] ? response.geodata[region] : [];
						
						options['resolution'] = 'provinces';
						options['displayMode'] = 'markers';
						options['dataMode'] = 'markers';
						options['colors'] = ['#4397BD','#21759B'];
						
						data = new google.visualization.DataTable()
						data.addColumn('number', 'Lat');
						data.addColumn('number', 'Long');
						data.addColumn('string', 'tooltip');
						data.addColumn('number', 'Value');
						data.addColumn({type:'string', role:'tooltip'}); 
						
						data.addRows(d);

						$('#countries_table').find('tr').removeClass('wp-ui-highlight');
						$('#country-row-'+region).addClass('wp-ui-highlight');
						
						location.hash = '#region='+region
						draw(options);
						
					}
					
					
					
					function draw(options){
						options = $.extend(mapoptions, options);
						geomap.draw(data, options);
						$('a.zoomout').css({'visibility': (mapoptions['region'] != 'world' ? 'visible' : 'hidden') });
					}
					
					function regTo3dig(region){
						var regioncode = region;
						$.each(regions, function(code, regions){
							if($.inArray(region, regions) != -1) regioncode = code;
						})
						return regioncode;
					}
		



					});
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				})
				return false;
			});
			$('#show_errors').on('click', function () {
				var $this = $(this),
					list = $('#error-list'),
					loader = $('#error-ajax-loading');
				
				if(!list.is(':hidden')){
					$this.removeClass('open');
					list.slideUp(100);
					return false;
				}	
				loader.css({ 'display': 'inline' });
				
				_ajax('get_errors', {
					id: campaign_id
				}, function (response) {
					$this.addClass('open');
					loader.hide();
					list.html(response.html).slideDown(100);
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				})
				return false;
			});
			$('#show_countries').on('click', function () {
				
				$('#countries_wrap').toggle();
				return false;
			});
			
			$('#mymail_details')
			.on('click', '.load-more-receivers', function(){
				var $this = $(this),
					page = $this.data('page'),
					types = $this.data('types'),
					orderby = $this.data('orderby'),
					order = $this.data('order'),
					loader = $this.next().css({ 'display': 'inline' });
					
				_ajax('get_recipients_page', {
					id: campaign_id,
					types: types,
					page: page,
					orderby: orderby,
					order: order
				}, function (response) {
					loader.hide();
					if(response.success){
						$this.parent().parent().replaceWith(response.html);
					}
				}, function(jqXHR, textStatus, errorThrown){
					detailbox.removeClass('loading');
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				});
				
				return false;
			})
			.on('change', '.recipients-limit, select.recipients-order', function(){
				var list = $('#recipients-list'),
					loader = $('#recipients-ajax-loading'),
					types = $('input.recipients-limit:checked').map(function(){return this.value}).get(),
					orderby = $('select.recipients-order').val(),
					order = $('a.recipients-order').hasClass('asc') ? 'ASC' : 'DESC';

				loader.css({ 'display': 'inline' });
				$('input.recipients-limit').prop('disabled', true);
				
				_ajax('get_recipients', {
					id: campaign_id,
					types : types.join(','),
					orderby: orderby,
					order: order
				}, function (response) {
					loader.hide();
					$('input.recipients-limit').prop('disabled', false);
					list.html(response.html).slideDown(100);
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				})
				return false;
			})
			.on('click', 'a.recipients-order', function(){
				$(this).toggleClass('asc');
				$('select.recipients-order').trigger('change');
			})
			.on('click', '.show-receiver-detail', function(){
				var $this = $(this),
					id = $this.data('id'),
					detailbox = $('#receiver-detail-'+id).show();
					
				$this.parent().addClass('loading').parent().addClass('expanded');
				
				_ajax('get_recipient_detail', {
					id: id,
					campaignid: campaign_id
				}, function (response) {
					$this.parent().removeClass('loading');
					if(response.success){
						detailbox.find('div.receiver-detail-body').html(response.html).slideDown(100);
					}
				}, function(jqXHR, textStatus, errorThrown){
					detailbox.removeClass('loading');
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				});
				
				return false;
			});
			_container.on('mouseenter', 'a.clickbadge', function () {
				var _this = $(this),
					_position = _this.position(),
					p = _this.data('p'),
					link = _this.data('link'),
					clicks = _this.data('clicks'),
					total = _this.data('total');

				_clickbadgestats.find('.piechart').data('easyPieChart').update(p);
				_clickbadgestats.find('.link').html(link);
				_clickbadgestats.find('.clicks').html(clicks);
				_clickbadgestats.find('.total').html(total);
				_clickbadgestats.stop().fadeIn(100).css({
					top: _position.top-85,
					left: _position.left-(_clickbadgestats.width()/2-_this.width()/2)
				})
;
				
			}).on('mouseleave', 'a.clickbadge', function () {
				_clickbadgestats.stop().fadeOut(400);
			});


			$('#mymail_receivers')
			.on('click', '.create-new-list', function(){
				var $this = $(this).hide();
				$('.create-new-list-wrap').slideDown();
				$('.create-list-type').trigger('change');
				return false;
			})
			.on('click', '.create-list', function(){
				var $this = $(this),
					listtype = $('.create-list-type'),
					name = '',
					loader = $('#mymail_total');
					
				if(listtype.val() == -1) return false;
				
				if(name = prompt(mymailL10n.enter_list_name, sprintf(mymailL10n.create_list, listtype.find(':selected').text(), $('#title').val()))){
					
					loader.addClass('loading');
					
					_ajax('create_list', {
						name: name,
						listtype: listtype.val(),
						id: campaign_id
					}, function (response) {
						loader.removeClass('loading');
						var msg = $('<div class="' + ((!response.success) ? 'error' : 'updated') + '"><p>' + response.msg + '</p></div>').hide().prependTo($('.create-new-list-wrap')).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
							msg.remove();
						});
						//alert(response.msg);
					}, function(jqXHR, textStatus, errorThrown){
						loader.removeClass('loading');
						//detailbox.removeClass('loading');
						alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
					});
				}
				
				return false;
			})
			.on('change', '.create-list-type', function(){
				var listtype = $(this),
					loader = $('#mymail_total');

					//loader = $this.next().css({ 'display': 'inline' });
				if(listtype.val() == -1) return false;
				listtype.prop('disabled', true);
				loader.addClass('loading');
				
				_ajax('get_create_list_count', {
					listtype: listtype.val(),
					id: campaign_id
				}, function (response) {
					listtype.prop('disabled', false);
					loader.removeClass('loading').html(response.count);
					
				}, function(jqXHR, textStatus, errorThrown){
					listtype.prop('disabled', false);
					loader.removeClass('loading').html('');
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				});
			});
			//.trigger('change');

			$('#mymail_total').on('click', function(){
				$('.create-list-type').trigger('change');
			});

			
			$('.piechart').easyPieChart({
				animate: 2000,
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


			if(typeof wp != 'undefined' && wp.heartbeat){
			
				$(document)
				.on('heartbeat-send', function(e, data) {

					if(data['wp_autosave'])
					 	delete data['wp_autosave'];

					data['mymail'] = {
						page: 'edit',
						id: campaign_id
					};

				})
				.on( 'heartbeat-tick', function(e, data) {

					if(!data.mymail[campaign_id]) return;

					var data = data.mymail[campaign_id], 
						stats = $('#stats').find('.verybold'),
						charts = $('#stats').find('.piechart'),
						progress = $('.progress'),
						p = (data.sent/data.total*100);

					$('.hb-sent').html(data.sent_f);
					$('.hb-opens').html(data.opens_f);
					$('.hb-clicks').html(data.clicks_f);
					$('.hb-clicks_total').html(data.clicks_total_f);
					$('.hb-unsubs').html(data.unsubs_f);
					$('.hb-bounces').html(data.bounces_f);
					$('.hb-geo_location').html(data.geo_location);

					$.each(data.environment, function(type){
						$('.hb-'+type).html((this.percentage*100).toFixed(2)+'%');
					});

					charts.eq(0).data('easyPieChart').update(Math.round(data.open_rate));
					charts.eq(1).data('easyPieChart').update(Math.round(data.click_rate));
					charts.eq(2).data('easyPieChart').update(Math.round(data.unsub_rate));
					charts.eq(3).data('easyPieChart').update(Math.round(data.bounce_rate));

					progress.find('.bar').width(p+'%');
					progress.find('span').eq(1).html(data.sent_formatted);
					progress.find('var').html(Math.round(p)+'%');

					_clickBadges(data.clickbadges);

					if(data.status != $('#original_post_status').val() && !$('#mymail_status_changed_info').length){

						$('<div id="mymail_status_changed_info" class="error inline"><p>'+sprintf(mymailL10n.statuschanged, '<a href="post.php?post='+campaign_id+'&action=edit">'+mymailL10n.click_here+'</a></p></div>'))
							.hide()
							.prependTo('#postbox-container-2')
							.slideDown(200);
					}

				});

				wp.heartbeat.interval( 'fast' );
			}



		}



	}

	var _optionbar = function () {

			var codemirror;
			
			function init() {
				_obar
				.on('click', 'a.template', showFiles)
				.on('click', 'button.save-template', save)
				.on('mouseenter', 'a.save-template', focusName)
				.on('click', 'a.clear-modules', clear)
				.on('click', 'a.preview', preview)
				.on('click', 'a.undo', undo)
				.on('click', 'a.redo', redo)
				.on('click', 'a.code', codeView)
				.on('click', 'a.plaintext', plainText);
			}

			function save() {
				var name = $('#new_template_name').val();
				if (!name) return false;
				_save();
				var loader = $('#new_template-ajax-loading').css({ 'display': 'inline' }),
					modules = $('#new_template_modules').is(':checked'),
					overwrite = $('#new_template_overwrite').is(':checked'),
					content = _getContent();
					
				_ajax('create_new_template', {
					name: name,
					modules: modules,
					overwrite: overwrite,
					template: $('#mymail_template_name').val(),
					content: content,
					head: _head.val()
				}, function (response) {
					loader.hide();
					if(response.success){
						window.onbeforeunload = null;
						window.location = response.url;
					}else{
						alert(response.msg);
					}
				}, function(jqXHR, textStatus, errorThrown){
					loader.hide();
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				});
				return false;
			}

			function undo() {

				if (_currentundo) {
					_container.addClass('noeditbuttons');
					_currentundo--;
					_setContent(_undo[_currentundo], 100, false);
					_content.val(_undo[_currentundo]);
					_obar.find('a.redo').removeClass('disabled');
					if (!_currentundo) $(this).addClass('disabled');
				}

				return false;
			}

			function redo() {
				var length = _undo.length;

				if (_currentundo < length - 1) {
					_container.addClass('noeditbuttons');
					_currentundo++;
					_setContent(_undo[_currentundo], 100, false);
					_content.val(_undo[_currentundo]);
					_obar.find('a.undo').removeClass('disabled');
					if (_currentundo >= length - 1) $(this).addClass('disabled');
				}

				return false;
			}

			function clear() {
				if (confirm('remove all modules?')) {
					_container.addClass('noeditbuttons');
					var modules = _iframe.contents().find('module');
					var modulecontainer = _iframe.contents().find('modules');
					modulecontainer.slideUp(function () {
						modules.remove();
						modulecontainer.show();
						_refresh();
						_save();
					});
				}
				return false;
			}

			function preview() {

				var _this = $(this),
					content = _getContent(),
					subject = _subject.val(),
					title = _title.val();
				
				if(_obar.find('a.preview').is('.loading')) return false;

				_obar.find('a.preview').addClass('loading');
				_ajax('set_preview', {
					id: campaign_id,
					content: content,
					head: _head.val(),
					issue: $('#mymail_autoresponder_issue').val(),
					subject: subject
				}, function (response) {
					_obar.find('a.preview').removeClass('loading');
					
					$('#mymail_campaign_preview_iframe').attr('src', ajaxurl + '?action=mymail_get_preview&hash=' + response.hash + '&_wpnonce=' + response.nonce);
					tb_show((title ? sprintf(mymailL10n.preview_for, '"' + title + '"') : mymailL10n.preview), '#TB_inline?hash=' + response.hash + '&_wpnonce=' + response.nonce + '&width='+(Math.min(900, $(window).width()-250))+'&height='+($(window).height()-100)+'&inlineId=mymail_campaign_preview', null);

				}, function(jqXHR, textStatus, errorThrown){
					_obar.find('a.preview').removeClass('loading');
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				});

			}

			function hide() {
				_obar.remove();
			}
			
			function focusName() {
				$('#new_template_name')
				.on('focus', function(){
					$(document).unbind('keypress.mymail').bind('keypress.mymail', function (event) {
						if(event.keyCode == 13) {
							save();
							return false;
						}
					});
				}).select().focus()
				.on('blur', function(){
					$(document).unbind('keypress.mymail');
				});
			}

			function showFiles(name) {
				var $this = $(this);
					
				$this.parent().find('ul').eq(0).slideToggle(100);
				return false;
			}

			function codeView() {
				var isRaw = !_iframe.is(':visible');

				if (!isRaw) {
					_obar.find('a.code').addClass('loading');
					_disable();
					$.getScript( mymaildata.url + 'assets/js/codemirror.js', function(){
						_ajax('toggle_codeview', {
							content: _getContent(),
							head: _head.val(),
							_wpnonce: wpnonce
						}, function (response) {
							_obar.find('a.code').addClass('active').removeClass('loading');
							_iframe.hide();
							_content.val(response.content).show();
							_obar.find('a').not('a.redo, a.undo, a.code').addClass('disabled');
							_container.addClass('noeditbuttons');
							
							codemirror = CodeMirror.fromTextArea(_content.get(0), {
								mode: {
									name: "htmlmixed",
									scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									mode: null},
									{matches: /(text|application)\/(x-)?vb(a|script)/i,
										mode: "vbscript"}]
								},
								tabMode: "indent",
								lineNumbers: true,
								viewportMargin:Infinity,
								autofocus: true
							});
	
						}, function(jqXHR, textStatus, errorThrown){
							_obar.find('a.code').addClass('active').removeClass('loading');
							_enable();
							alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
						});
					});
				} else {
				
					_iframe.show();
					_content.val(codemirror.getValue());
					codemirror.clearHistory();
					$('.CodeMirror').remove();
					_setContent(_content.val());
					_content.hide();
					_obar.find('a.code').removeClass('active');
					_obar.find('a').not('a.redo, a.undo, a.code').removeClass('disabled');
					_container.removeClass('noeditbuttons');
					_enable();
					_refresh();
					
				}
				return false;
			}

			function plainText() {
				var isPlain = !_iframe.is(':visible');

				if (!isPlain) {

					_obar.find('a.plaintext').addClass('active');
					_iframe.slideUp();
					_excerpt.show();
					_plaintext.slideDown();
					_obar.find('a').not('a.redo, a.undo, a.plaintext, a.preview').addClass('disabled');
					_container.addClass('noeditbuttons');

				} else {
				
					_iframe.slideDown();
					_plaintext.slideUp();
					_obar.find('a.plaintext').removeClass('active');
					_obar.find('a').not('a.redo, a.undo, a.plaintext, a.preview').removeClass('disabled');
					_container.removeClass('noeditbuttons');
					_refresh();
					
				}
				return false;
			}

			init();

			return {
				hide: function () {
					hide();
				}
			}
		}



	var _editbar = function () {

			var bar = $('#editbar'),
				base, contentheights = {
					'img': 110,
					'single': 115,
					'multi': 110,
					'btn': 13,
					'auto': 15,
					'codeview': 100
				},
				imagepreview = bar.find('.imagepreview'),
				imagewidth = bar.find('.imagewidth'),
				imageheight = bar.find('.imageheight'),
				factor = bar.find('.factor'),
				highdpi = bar.find('.highdpi'),
				imagelink = bar.find('.imagelink'),
				imageurl = bar.find('.imageurl'),
				imagealt = bar.find('.imagealt'),
				singlelink = bar.find('.singlelink'),
				buttonlink = bar.find('.buttonlink'),
				buttonlabel = bar.find('.buttonlabel'),
				buttonalt = bar.find('.buttonalt'),
				buttonnav = bar.find('.button-nav'),
				buttontabs = bar.find('ul.buttons'),
				buttontype, current, currentimage, currenttext, currenttag, assetstype, assetslist, itemcount, checkForPostsTimeout, searchTimeout, checkRSSfeedInterval, rssURL = 'x', searchstring = '', codeview,
				editor = $('#wp-mymail-editor-wrap'),
				postsearch = $('#post-search'),
				imagesearch = $('#image-search'),
				mediauploader = typeof wp !== 'undefined' && typeof wp.media !== 'undefined';

			function init() {
				bar
				.on('keyup change', 'input.live', change)
				.on('keyup change', '#mymail-editor', change)
				.on('click', '.replace-image', replaceImage)
				.on('change', '.highdpi', toggleHighDPI)
				.on('click', 'button.save', save)
				.on('click', '.cancel', cancel)
				.on('click', 'a.remove', remove)
				.on('click', 'a.reload', loadPosts)
				.on('click', 'a.single-link-content', loadSingleLink)
				.on('click', 'a.add_image', openMedia)
				.on('click', 'a.add_image_url', openURL)
				.on('click', '.imagelist li', choosePic)
				.on('dblclick', '.imagelist li', save)
				.on('change', '#post_type_select input', loadPosts)
				.on('click', '.postlist li', choosePost)
				.on('click', '.load-more-posts', loadMorePosts)
				.on('click', 'a.btnsrc', changeBtn)
				.on('click', '.imagepreview', toggleImgZoom)
				.on('click', 'a.nav-tab', openTab)
				.on('change', 'select.check-for-posts', checkForPosts)
				.on('keyup change','#rss_url', loadPosts)
				.on('keyup change','#post-search', searchPost)
				.on('keyup change','#image-search', searchPost)
				.on('click', '#rss_url', function(){$(this).focus().select();})
				.on('click', '.rss_change', changeRSS)
				.on('click', '#recent_feeds a', recentFeed)

				
				.on('mouseenter', '#wp-mymail-editor-wrap, .imagelist, .postlist, .CodeMirror', disabledrag)
				.on('mouseleave', '#wp-mymail-editor-wrap, .imagelist, .postlist, .CodeMirror', enabledrag);

				_getRealDimensions(_iframe.contents().find("img").eq(0), function(w,h,f){
					var h = f >= 1.5;
					factor.val(f);
					highdpi.prop('checked', h);
					(h) ? bar.addClass('high-dpi') : bar.removeClass('high-dpi');
				});
				
				buttonnav.on('click', 'a', function(){
					$(this).parent().find('a').removeClass('nav-tab-active');
					$(this).parent().parent().find('ul.buttons').hide();
					var hash = $(this).addClass('nav-tab-active').attr('href');
					$('#tab-'+hash.substr(1)).show();
					return false;
				});
				
				imageurl.on('paste change', function(e){
					var $this = $(this);
					setTimeout(function(){
						var url = dynamicImage($this.val()),
							img = new Image();
						if(url){
							loader();
							img.onload = function(){
								imagepreview.attr('src', url);
								currentimage = {
									width: img.width,
									height: img.height,
									asp: img.width/img.height
								};
								loader(false);
							};
							img.onerror = function(){
								if(e.type != 'paste') alert(sprintf(mymailL10n.invalid_image, '"'+url+'"'));
							};
							img.src = url;
						}
					}, 1);
				});

				imagewidth.on('change', function(){
					imageheight.val(Math.round(imagewidth.val()/currentimage.asp));
				});
				imageheight.on('change', function(){
					imagewidth.val(Math.round(imageheight.val()*currentimage.asp));
				});
				
				$('#dynamic_embed_options_post_type').on('change', function(){
					
					var cats = $('#dynamic_embed_options_cats');
					cats.find('select').prop('disabled', true);
					loader();
					_ajax('get_post_term_dropdown', {
						posttype: $(this).val()
					}, function (response) {
						loader(false);
						if (response.success) {
							cats.html(response.html);
							if(currenttag && currenttag.terms){
								var taxonomies = cats.find('.dynamic_embed_options_taxonomy_wrap');
								$.each(currenttag.terms, function(i, term){
									if(!term) return;
									var term_ids = term.split(',');
									$.each(term_ids, function(j, id){
										var select = taxonomies.eq(i).find('select').eq(j);
										if(!select.length){
											var last = taxonomies.eq(i).find('select').last(),
												select = last.clone();
											select.insertAfter(last);
											$('<span> '+mymailL10n.or+ ' </span>').insertBefore(select);
										}
										select.val(id);
									});
								});
							}
				

							
						}
						checkForPosts();
					}, function(jqXHR, textStatus, errorThrown){
					
						loader(false);
						alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
			
					});
					
				});
				
				
				if(bar.draggable){
					bar.draggable({
						'distance': 20,
						'axis': 'y'
					});
				}
				
				if(isTinyMCE && tinymce.activeEditor){
					if(tinymce.majorVersion >= 4){

						tinymce.activeEditor.on('keyup', function () {
							mceUpdater(this);
						});
						tinymce.activeEditor.on('ExecCommand', function () {
							mceUpdater(this);
						});

					}else{
						tinymce.activeEditor.onKeyUp.add(function () {
							mceUpdater(this);
						});
						tinymce.activeEditor.onExecCommand.add(function () {
							mceUpdater(this);
						});
					}
				}

				if(mediauploader){
				
					var ed_id = wp.media.editor.id();
					var ed_media = wp.media.editor.get( ed_id );
						ed_media = 'undefined' != typeof( ed_media ) ? ed_media : wp.media.editor.add( ed_id );
						
					ed_media.on('close', loadPosts);
					
				}

			}

			function draggable(bool) {
				if(bar.draggable){
					if(bool !== false){
						bar.draggable( "enable" );
					}else{
						bar.draggable( "disable" );
					}
				}
			}
			
			function disabledrag() {
				draggable(false);
			}
			function enabledrag() {
				draggable(true);
			}


			function openTab(id, trigger) {
				var $this;
				if(typeof id == 'string'){
					$this = base.find('a[href="'+id+'"]');
				}else{
					$this = $(this);
					id = $this.attr('href');
				}
				
				$this.parent().find('a.nav-tab').removeClass('nav-tab-active');
				$this.addClass('nav-tab-active');
				base.find('.tab').hide();
				base.find(id).show();
				if(id == '#dynamic_embed_options' && trigger !== false) $('#dynamic_embed_options_post_type').trigger('change');
				if(id == '#image_button') buttontype = 'image';
				if(id == '#text_button') buttontype = 'text';
				
				assetslist = base.find(id).find('.postlist').eq(0);
				return false;
			}


			function replaceImage() {
				loader();
				var f = factor.val(),
					w = current.element.width(),
					h = Math.round(w/1.6),
					img = $('<img>', {
						'src': 'https://dummy.newsletter-plugin.com/'+(w*f)+'x'+(h*f)+'.jpg',
						'alt': current.content,
						'title': current.content,
						'label': current.content,
						'width': w,
						'height': h,
						'border': 0,
						'editable': current.content
					});
				
				img[0].onload = function(){
					//_refresh();
					img.attr({
						'width': w,
						'height': h,
					}).removeAttr('style');
					close();
				};
				if (current.element.parent().is('a')) current.element.unwrap();
				if (!current.element.parent().is('td')) current.element.unwrap();
				current.element.replaceWith(img);
				return false;
			}


			function toggleHighDPI(){
				
				if($(this).is(':checked')){
					factor.val(2);
					bar.addClass('high-dpi');
				}else{
					factor.val(1);
					bar.removeClass('high-dpi');
				}
			}
			
			function checkForPosts() {
				clearInterval(checkForPostsTimeout);
				loader();
				$('#dynamic_embed_options').find('h4').eq(0).html('&hellip;');
				checkForPostsTimeout = setTimeout(function(){
				
				var post_type = bar.find('#dynamic_embed_options_post_type').val(),
					relative = bar.find('#dynamic_embed_options_relative').val(),
					taxonomies = bar.find('.dynamic_embed_options_taxonomy_wrap'),
					extra = [];
					
					$.each(taxonomies, function(i){
						var selects = $(this).find('select'),
							values = [];
						$.each(selects, function(){
							var val = parseInt($(this).val(), 10);
							if(val != -1 && $.inArray(val, values) == -1) values.push(val);
						});
						values = values.join(',');
						if(values) extra[i] = values;
					});
					
				_ajax('check_for_posts', {
					post_type:post_type,
					relative:relative,
					extra:extra
				
				}, function (response) {
					loader(false);
					if (response.success) {
						$('#dynamic_embed_options').find('h4').eq(0).html(response.title);
					}
				}, function(jqXHR, textStatus, errorThrown){
				
					loader(false);
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
		
				});
				
				}, 500);
				
				return false;
			}
			
			function dynamicImage(val, w, h) {
				var m;
				if(/^\{([a-z0-9-_]+)_image:-?[0-9,;]+(\|\d+)?\}$/.test(val)){
					var f = factor.val();
					val = mymaildata.ajaxurl+'?action=mymail_image_placeholder&tag='+val.replace('{','').replace('}','')+'&w='+((w || imagewidth.val())*f)+'&h='+((h || imageheight.val())*f)+'&f='+f;
				}
				
/*
				else if(m = val.match(/(https?)(.*?)youtube\.com\/watch\?v=([a-zA-Z0-9]+)/)){
					console.log(val, m);
					val = m[1]+'://img.youtube.com/vi/'+m[3]+'/maxresdefault.jpg';
					$.getJSON(m[1]+'://gdata.youtube.com/feeds/api/videos/'+m[3]+'?v=2&alt=jsonc&callback=?', function(response){
						console.log(response);
						//imagelink.val();
						imagelink.val(response.data.player.default.replace('&feature=youtube_gdata_player','&feature=mymail'));
						imagealt.val(response.data.title);
						imagepreview.attr('src', response.data.thumbnail.hqDefault);
						imageurl.attr('src', response.data.thumbnail.hqDefault);
						
					});
				}else{
					console.log('no dynmaic');
				}
*/
				return $.trim(val);
			}
			
			function isDynamicImage(val) {
				if(-1 !== val.indexOf('?action=mymail_image_placeholder&tag=')){
					var m = val.match(/([a-z0-9-_]+)_image:-?[0-9,;]+(\|\d+)?/);
					return '{'+m[0]+'}';
				}
				return false;
			}
			
			function change(e) {
				if((e.keyCode || e.which) != 27)
					current.element.html($(this).val());
			}

			function loader(bool) {
				if(bool === false){
					$('#editbar-ajax-loading').hide();
					bar.find('.buttons').find('button').prop('disabled', false);
				}else{
					$('#editbar-ajax-loading').css({ 'display': 'inline' });
					bar.find('.buttons').find('button').prop('disabled', true);
				}
			}

			function save() {

				if (current.type == 'img') {
				
					if(imageurl.val()){
						
						currentimage = {
							id: null,
							name: '',
							src: dynamicImage(imageurl.val()),
							width: currentimage.width,
							height: currentimage.height,
							asp: currentimage.width/currentimage.height
						};
						
					}
					
					if(currentimage){
						loader();
						
						var f = factor.val() || 1;

						current.element.attr({
							'data-id': currentimage.id,
							'width': Math.round(imagewidth.val()),
							'height': Math.round(imageheight.val()),
							'alt': currentimage.name
						}).data('id', currentimage.id).addClass('mymail-loading');

						if(currentimage.src) current.element.attr('src', currentimage.src);

						_ajax('create_image', {
							id: currentimage.id,
							src: currentimage.src,
							width: imagewidth.val()*f,
							height: imageheight.val()*f
						}, function (response) {
						
							loader(false);

							if (response.success) {
								imagepreview.attr('src', response.image.url);
								
								response.image.width = (response.image.width || currentimage.width)/f;
								response.image.height = response.image.width/(currentimage.asp);
								response.image.asp = currentimage.asp;
								
								currentimage = response.image;
								currentimage.name = imagealt.val();
								
								current.element.one('load', function(){
									current.element.removeClass('mymail-loading');
								});
								current.element.removeAttr('src').attr({
									'src': currentimage.url,
									'width': Math.round(imagewidth.val()),
									'height': Math.round(imageheight.val()),
									'alt': currentimage.name
								});



							}
							imagealt.val('');

							close();

						}, function(jqXHR, textStatus, errorThrown){
						
							loader(false);
							alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
			
						});
						
					}else{
						current.element.attr({
							'alt': imagealt.val()
						});

						close();
					}

					if (current.element.parent().is('a')) current.element.unwrap();
					var link = imagelink.val();
					if (link) current.element.wrap('<a href="' + link + '"></a>');

					return false;

				} else if (current.type == 'btn') {
				
					var link = buttonlink.val();
					if(!link && !confirm(mymailL10n.remove_btn)) return false;
					
					if('image' == buttontype){
						var btnsrc = base.find('a.active').find('img').attr('src');
						var f = factor.val();
						var img = new Image();
						img.onload = function(){
						
							if(!current.element.find('img').length){
								var wrap = current.element.closest('table.textbutton');
								var element = $('<a href="" editable label="' + current.name + '"><img></a>');
								(wrap.length) ? wrap.replaceWith(element) : current.element.replaceWith(element);
								current.element = element;
							}
							current.element.find('img').attr({
								'src': btnsrc,
								'width': Math.round((img.width || current.element.width())/f),
								'height':  Math.round((img.height || current.element.height())/f),
								'alt': buttonalt.val(),
								'title': buttonalt.val()
							});
							
							(link) ? current.element.attr('href', link) : current.element.remove();
							close();
						}
						img.src = btnsrc;
					}else{
						var wrap = current.element.closest('table.textbutton'),
							label = buttonlabel.val();
						if(!wrap.length){
							current.element.replaceWith('<table class="textbutton" align="left"><tr><td align="center" width="auto"><a href="' + link + '" editable label="' + label + '">' + label + '</a></td></tr></table>')
						}else{
							current.element.text(label);
						}
						if(link){
							current.element.attr('href', link);
						}else{
							current.element.remove();
							wrap.remove();
						}
						close();
						
					}
					return false;

				} else if (current.type == 'auto') {

					var insertmethod = $('#embedoption-bar').find('.nav-tab-active').data('type');
					
					if('dynamic' == insertmethod){
					
						var post_type = bar.find('#dynamic_embed_options_post_type').val(),
							relative = bar.find('#dynamic_embed_options_relative').val(),
							contenttype = bar.find('#dynamic_embed_options_content').val(),
							taxonomies = bar.find('.dynamic_embed_options_taxonomy_wrap'),
							tag = '{'+post_type+':'+relative,
							extra = [];
							
						$.each(taxonomies, function(i){
							var selects = $(this).find('select'),
								values = [];
							$.each(selects, function(){
								var val = parseInt($(this).val(), 10);
								if(val != -1 && $.inArray(val, values) == -1) values.push(val);
							});
							values = values.join(',');
							if(values) extra[i] = values;
						});
						
						
						extra = extra.join(';');
						if(extra) extra = ';'+extra;
						
						currenttext = {
							title: '{'+post_type+'_title:'+relative+extra+'}',
							link: '{'+post_type+'_link:'+relative+extra+'}',
							content: '{'+post_type+'_content:'+relative+extra+'}',
							excerpt: '{'+post_type+'_excerpt:'+relative+extra+'}',
							image: '{'+post_type+'_image:'+relative+extra+'}'
						};
						
						current.element.attr('data-tag', '{'+post_type+':'+relative+extra+'}').data('tag', '{'+post_type+':'+relative+extra+'}');
						
						
					}else if('rss' == insertmethod) {
						
						var contenttype = $('.embed_options_content_rss:checked').val();
						current.element.removeAttr('data-tag').removeData('tag');
						
					}else{
						
						var contenttype = $('.embed_options_content:checked').val();
						current.element.removeAttr('data-tag').removeData('tag');
						
					}
					
					if (currenttext) {

						current.elements.headlines.html('');
						if (currenttext.title) current.elements.headlines.eq(0).html(currenttext.title);
						
						
						if (currenttext.link){
							
							if(current.elements.buttons.length){
								current.elements.buttons.last().attr('href', currenttext.link);
								current.elements.buttons.not(':last').remove();
							}else{
							
								current.elements.bodies.last().after('<buttons><table class="textbutton" align="left"><tr><td align="center" width="auto"><a href="'+currenttext.link+'" title="'+mymailL10n.read_more+'" editable label="'+mymailL10n.read_more+'">'+mymailL10n.read_more+'</a></td></tr></table></buttons>');
								
							}
						}
						
						if (currenttext.content && current.elements.bodies.length) {
							var contentcount = current.elements.bodies.length,
								content = ('excerpt' == contenttype && currenttext.excerpt) ? currenttext.excerpt : currenttext.content,
								contentlength = content.length,
								partlength = (insertmethod == 'static') ? Math.ceil(contentlength / contentcount) : contentlength;
								
							for (var i = 0; i < contentcount; i++) {
								current.elements.bodies.eq(i).html(content.substring(i * partlength, i * partlength + partlength));
							}

						}

						if (currenttext.image && current.elements.images.length) {
							loader();
							
							var imgelement = current.elements.images.eq(0);
							var f = factor.val();

							if ('static' == insertmethod){
								_ajax('create_image', {
									id: currenttext.image.id,
									width: current.elements.images.eq(0).width()*f,
								}, function (response) {
								
									if (response.success) {
										loader(false);
										
										imgelement.attr({
											'data-id': currenttext.image.id,
											'src': response.image.url,
											'width': Math.round(response.image.width/f),
											'height' : Math.round(response.image.height/f),
											'alt' : currenttext.title
										}).data('id', currenttext.image.id);
										
										if (imgelement.parent().is('a')) {
											imgelement.unwrap();
										}
	
										if (currenttext.link) {
											imgelement.wrap('<a>');
											imgelement.parent().attr('href', currenttext.link);
										} 
									}
									close();
								}, function(jqXHR, textStatus, errorThrown){
								
									loader(false);
									alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
				
								});
								
								return false;
								
							}else if('rss' == insertmethod) {
							
								var width = imgelement.width();
								
								imgelement.removeAttr('height').removeAttr('data-id').attr({
									'src': dynamicImage(currenttext.image.src, width),
									'width': width,
									'alt' : currenttext.title
								}).removeData('id');
								
							}else{
							
								var width = imgelement.width();
								
								imgelement.removeAttr('height').removeAttr('data-id').attr({
									'src': dynamicImage(currenttext.image, width),
									'width': width,
									'alt' : currenttext.title
								}).removeData('id');
							}
						}
						
						_iframe.contents().find("html")
						.find("img").each(function () {
							this.onload = function(){
								_refresh();
							};
						});
						
						
					}

				} else if (current.type == 'multi') {

 					if(isTinyMCE && tinymce.activeEditor && !tinymce.activeEditor.isHidden())
 						current.element.html(tinymce.activeEditor.getContent());

				} else if (current.type == 'single') {

					if (current.element.parent().is('a')) current.element.unwrap();
					var link = singlelink.val();
					if (link) current.element.wrap('<a href="' + link + '"></a>');

				} else if (current.type == 'codeview') {

					var html = codeview.getValue();
					current.element.html(html);
					current.modulebuttons.prependTo(current.element);

				}
				
				
				
				close();
				return false;
			}

			function remove() {
				if (current.element.parent().is('a')) current.element.unwrap();
				if (!current.element.parent().is('td')) current.element.unwrap();
				if('btn' == current.type){
					var wrap = current.element.closest('table.textbutton');
					if(wrap.length) wrap.remove();
				}
				current.element.remove();
				close();
				return false;
			}

			function cancel() {
				switch (current.type) {
					case 'img':
						break;
					case 'btn':
						if (!current.element.attr('href')){
							var wrap = current.element.closest('table.textbutton');
							if(wrap.length) wrap.remove();
							current.element.remove();
						}
						break;
					default:
						current.element.html(current.content);
				}
				close();
				return false;
			}

			function changeBtn() {
				var _this = $(this),
				link = _this.data('link');
				base.find('.btnsrc').removeClass('active');
				_this.addClass('active');
				
				buttonalt.val(_this.attr('title'));
				
				if(link){
					var pos;
					buttonlink.val(link);
					if((pos = (link+'').indexOf('USERNAME', 0)) != -1){
						buttonlink.focus();
						selectRange(buttonlink[0], pos, pos+8);
					};
					
				}
				return false;
			}
			
			function toggleImgZoom() {
				$(this).toggleClass('zoom');
			}
			
			function choosePic(event, el) {
				var _this = el || $(this),
					id = _this.data('id'),
					name = _this.data('name'),
					src = _this.data('src');
					
				currentimage = {
					id: id,
					name: name,
					src: src
				};
				loader();
				
				base.find('li.selected').removeClass('selected');
				_this.addClass('selected');
				
				if(current.element.data('id') == id){
					imagealt.val(current.element.attr('alt'));
				}else if(current.element.attr('alt') != name) {
					imagealt.val(name);
				}
				imageurl.val('');
				imagepreview.attr('src', '').on('load', function () {
				
					imagepreview.off('load');

					current.width = imagepreview.width();
					current.height = imagepreview.height();
					current.asp = _this.data('asp') || (current.width / current.height);
					
					currentimage.asp = current.asp;
					
					loader(false);

					imageheight.val(Math.round(imagewidth.val()/current.asp));

/*
					current.element.attr({
						'src': src,
						'width': imagewidth.val(),
						'height': Math.round(imagewidth.val()/current.asp)
					});
*/

				}).attr('src', src);

				return currentimage;
			}

			function choosePost() {
				var _this = $(this),
					id = _this.data('id'),
					name = _this.data('name'),
					link = _this.data('link'),
					thumbid = _this.data('thumbid');
					
				if (current.type == 'btn') {
				
					buttonlink.val(link);
					buttonalt.val(name);
					base.find('li.selected').removeClass('selected');
					_this.addClass('selected')

				} else if (current.type == 'single') {
				
					singlelink.val(link);
					base.find('li.selected').removeClass('selected');
					_this.addClass('selected')

				} else {
				
					loader();
					_ajax('get_post', {
						id: id
					}, function (response) {
						loader(false);
						base.find('li.selected').removeClass('selected');
						_this.addClass('selected')
						if (response.success) {
							currenttext = {
									title: response.title,
									link: response.link,
									content: response.content,
									excerpt: response.excerpt,
									image: response.image ? {
										id: response.image.id,
										src: response.image.src,
										name: response.image.name
									} : false
							};
							base.find('.editbarinfo').html(mymailL10n.curr_selected + ': <span>' + currenttext.title + '</span>');
							
						}
					}, function(jqXHR, textStatus, errorThrown){
					
						loader(false);
						base.find('li.selected').removeClass('selected');
						alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
			
					});

				}
				return false;
			}

			function open(data) {
			

				$(document).on('keypress.mymail', function(e){

					switch (e.keyCode || e.which) {
					case 27:
						cancel();
						return false;
						break;
					case 13:
						if(type != 'multi'){
							save();
							return false;
						}
						break;
					}

				});

				current = data;
				var el = data.element,
					top = (type != 'img') ? data.offset.top : 0,
					name = data.name || '',
					type = data.type,
					content = $.trim(el.html()),
					offset,
					fac = 1;

				base = bar.find('div.type.' + type);

				bar.addClass('current-' + type);
				
				current.width = el.width();
				current.height = el.height();
				current.asp = current.width / current.height;

				current.content = content;
				
				currenttag = current.element.data('tag');
				
				if(type == 'img'){
				
					factor.val(1);
					_getRealDimensions(el, function(w,h,f){
						var h = f >= 1.5;
						factor.val(f);
						highdpi.prop('checked', h);
							
						(h) ? bar.addClass('high-dpi') : bar.removeClass('high-dpi');
							
						fac = f;
					});

					
				}else if(type == 'btn'){
					
					
					if(el.find('img').length){
						
						$('#button-type-bar').find('a').eq(0).trigger('click');
						var btnsrc = el.find('img').attr('src');
					
						if(buttonnav.length){
						
							var button = bar.find("img[src='"+btnsrc+"']");
								
							if(button.length){
								bar.find('ul.buttons').hide();
								var b = button.parent().parent().parent();
								bar.find('a[href="#'+b.attr('id').substr(4)+'"]').trigger('click');
							}else{
								$.each(bar.find('.button-nav'), function(){
									$(this).find('.nav-tab').eq(0).trigger('click');
								});
							}
							
						}
						
						buttonlabel.val(el.find('img').attr('alt'));
						
						_getRealDimensions(el.find('img'), function(w,h,f){
							var h = f >= 1.5;
							factor.val(f);
							highdpi.prop('checked', h);
							(h) ? bar.addClass('high-dpi') : bar.removeClass('high-dpi');
							
							fac = f;
						});
						
					}else{
						
						$('#button-type-bar').find('a').eq(1).trigger('click');
						buttonlabel.val($.trim(el.text())).focus().select();
						bar.find('ul.buttons').hide();
					}
					
				}else if(type == 'auto'){
				
					var tab = $('#embedoption-bar').find('.nav-tab-active').data('type');
					openTab('#'+tab+'_embed_options', true);
					
					if(currenttag){
					
						var parts = currenttag.substr(1, currenttag.length - 2).split(':'),
							extra = parts[1].split(';'),
							relative = parseInt(extra.shift(), 10),
							terms = extra.length ? extra : null;
							
						currenttag = {
							'post_type' : parts[0],
							'relative' : relative,
							'terms' : terms
						};
						
						$('#dynamic_embed_options_post_type').val(currenttag.post_type).trigger('change');
						$('#dynamic_embed_options_relative').val(currenttag.relative).trigger('change');
						
					}else{
					
					}
					

				}else if(type == 'codeview'){
				
					var textarea = base.find('textarea'),
						clone = el.clone();

					current.modulebuttons = clone.find('.modulebuttons');
					
					clone.find('.modulebuttons').remove();
				
					var html = $.trim(clone.html());
					textarea.html(html);
					$.getScript( mymaildata.url + 'assets/js/codemirror.js', function(){});

				}
				
				offset = Math.min(_container.offset().top + current.offset.top - contentheights[type], (_container.offset().top+_container.height()-$(window).height()) + 100);
				_container.addClass('noeditbuttons');

				_scroll(offset, function () {

					bar.find('h2').eq(0).html(name);
					bar.find('div.type').hide();

					bar.find('div.' + type).show();
					bar.css({
						top: (self.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop)-_container.offset().top + contentheights[type]
					});
					
					loader();
					
					if (type == 'single') {
					
						var val = content.replace(/&amp;/g, '&');
						
						if(current.element.parent().is('a')){
							var href = current.element.parent().attr('href');
							singlelink.val(href != '#' ? href : '');
						
						}else if(current.element.find('a').length){
							var link = current.element.find('a');
							var href = link.attr('href');
							val = link.text();
							singlelink.val(href != '#' ? href : '');
						}
						
						base.find('input').eq(0).val($.trim(val));

					} else if (type == 'img') {

						var maxwidth = parseInt(el[0].style.maxWidth, 10) || el.parent().width() || el.width() || null;
						var maxheight = parseInt(el[0].style.maxHeight, 10) || el.parent().height() || el.height() || null;
						var url = isDynamicImage(el.attr('src')) || '';
							
						if (el.parent().is('a')) imagelink.val(el.parent().attr('href').replace('%7B', '{').replace('%7D', '}'));

						imagealt.val(el.attr('alt'));
						imageurl.val(url);

						el.data('id', el.attr('data-id'));
						
						$('.imageurl-popup').toggle(!!url);
						imagepreview.removeAttr('src').attr('src', el.attr('src'));
						assetstype = 'attachment';
						assetslist = base.find('.imagelist');
						currentimage = {
							id: el.data('id'),
							width: el.width()*fac,
							height: el.height()*fac
						}
						currentimage.asp = currentimage.width/currentimage.height;
						loadPosts();

					} else if (type == 'btn') {

						buttonalt.val(el.find('img').attr('alt'));
						buttonlink.val(el.attr('href').replace('%7B', '{').replace('%7D', '}'));
						
						assetstype = 'link';
						assetslist = base.find('.postlist').eq(0);
						loadPosts();
						
						$.each(base.find('.buttons img'), function () {
							var _this = $(this);
							_this.css('background-color',el.css('background-color'));
							(_this.attr('src') == btnsrc) ? _this.parent().addClass('active') : _this.parent().removeClass('active');
							
						});

					} else if (type == 'auto') {

						assetstype = 'post';
						assetslist = base.find('.postlist').eq(0);
						loadPosts();
						current.elements = {
							headlines: current.element.find('single'),
							bodies: current.element.find('multi'),
							buttons: current.element.find('a[editable]'),
							images: current.element.find('img[editable]')
						}
						
					} else if (type == 'codeview') {

						$.getScript( mymaildata.url + 'assets/js/codemirror.js', function(){
							if(codeview){
								codeview.clearHistory();
							}

							
							codeview = codeview || CodeMirror.fromTextArea(textarea.get(0), {
								mode: {
									name: "htmlmixed",
									scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									mode: null},
									{matches: /(text|application)\/(x-)?vb(a|script)/i,
										mode: "vbscript"}]
									},
								tabMode: "indent",
								lineNumbers: true,
								viewportMargin:Infinity,
								autofocus: true
							});
							
							codeview.setValue(html);
							
						});

					}

					bar.show(0, function () {

						if (type == 'single') {

							bar.find('input').focus().select();

						} else if (type == 'img') {

							imagewidth.val(current.width);
							imageheight.val(current.height);

						} else if (type == 'btn') {

							imagewidth.val(maxwidth);
							imageheight.val(maxheight);

						} else if (type == 'multi') {
							
							$('#mymail-editor').val(content);

							if(isTinyMCE && tinymce.activeEditor){
								tinymce.activeEditor.setContent(content);
								tinymce.execCommand('mceFocus', false, 'mymail-editor');
							}

						}
						

					});

					loader(false);

				});


			}

			function loadPosts() {
				
				var posttypes = $('#post_type_select').find('input:checked').serialize(),
					data = {
						type: assetstype,
						posttypes: posttypes,
						search: searchstring,
						offset: 0
					};
					
				if($(this).is('#rss_url')){
					data.type = '_rss';
					data.url = $.trim($('#rss_url').val());
					if(data.url == rssURL) return false;
					rssURL = data.url;
					if(!data.url){
						$('#rss_more').slideUp(200);
						return false;
					}
					$('.rss_info').html('');
				}
				if(assetstype == 'attachment'){
					data.id = currentimage.id;
				}

				assetslist.empty();
				loader();
				
				_ajax('get_post_list', data, function (response) {
					loader(false);
					if (response.success) {
						itemcount = response.itemcount;
						displayPosts(response.html, true);
						assetslist.find('.selected').trigger('click');
						if(response.rssinfo){
							$('#rss_more').slideDown(200);
							$('#rss_input').slideUp(200);
							$('.rss_info').html('<h4>'+response.rssinfo.title+' &ndash; '+response.rssinfo.description+'</h4><p class="tiny">'+response.rssinfo.copyright+'</p>');
						}
					}
				}, function(jqXHR, textStatus, errorThrown){
				
					loader(false);
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
					
				});
			}
			
			function loadMorePosts() {
				var $this = $(this),
				offset = $this.data('offset'),
				type = $this.data('type');
				loader();
				
				var posttypes = $('#post_type_select').find('input:checked').serialize();
				
				_ajax('get_post_list', {
					type: type,
					posttypes: posttypes,
					search: searchstring,
					offset: offset,
					url: $.trim($('#rss_url').val()),
					itemcount: itemcount
				}, function (response) {
					loader(false);
					if (response.success) {
						itemcount = response.itemcount;
						$this.parent().remove();
						displayPosts(response.html, false);
					}
				}, function(jqXHR, textStatus, errorThrown){
				
					loader(false);
					alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
					
				});
				return false;
			}
			
			function searchPost() {
				var $this = $(this);
				clearTimeout(searchTimeout);
				searchTimeout = setTimeout(function(){
					var str = $this.val();
					if(str == searchstring) return;
					searchstring = str;
					loadPosts();
				}, 300);
			}
			
			function loadSingleLink() {
				$('#single-link').slideDown(200);
				singlelink.focus().select();
				assetstype = 'link';
				assetslist = base.find('.postlist').eq(0);
				loadPosts();
				return false;
				
			}
			
			function displayPosts(html, replace, list) {
				if(!list) list = assetslist;
				if(replace) list.empty();
				if(!list.html()) list.html('<ul></ul>');
				
				list.find('ul').append(html);
			}

			function recentFeed(){
				
				$('#rss_url').val($(this).attr('href')).trigger('change');
			
				return false;
			}
			function changeRSS() {
				
				$('#rss_url').val('');
				$('#rss_more').slideUp(200);
				$('#rss_input').slideDown(200);
				
				return false;
			}
			
			function openURL() {
				$('.imageurl-popup').toggle();
				imageurl.focus().select();
				return false;
			}

			function openMedia() {
			
				if(mediauploader){
				
					var send_attachment = wp.media.editor.send.attachment;
					
					wp.media.editor.send.attachment = function(props, attachment) {
					
						var el = $('img');
	
						el.data({
							id: attachment.id,
							name: attachment.name,
							src: attachment.url
						});
				
						wp.media.editor.send.attachment = send_attachment;
						choosePic(null, el);
						
					}
					
					wp.media.editor.open();
					
						
				
				}else{
				
					var old_tb_remove = window.tb_remove;
					var send_to_editor = window.send_to_editor;
					
						
					window.tb_remove = function () {
						old_tb_remove();
						loadPosts();
						window.tb_remove = old_tb_remove;
						return false;
					}
	
					window.send_to_editor = function (html) {
						var el = $('img', html);
	
						el.data({
							id: html.match(/wp-image-(\d+)/)[1],
							name: el.attr('title'),
							src: el.attr('src')
						});
	
						window.send_to_editor = send_to_editor;
	
						choosePic(null, el);
						tb_remove();
						return false;
					}
	
					tb_show($(this).html(), 'media-upload.php?type=image&amp;post_id=&amp;TB_iframe=true');
				}
				
				return false;
			}

			function mceUpdater(editor) {
				clearTimeout(timeout);
				timeout = setTimeout(function () {
					if(!editor) return;
					var val = $.trim(editor.save());
					current.element.html(val);
				}, 100);
			}

			function close() {

				$(document).off('keypress.mymail');

				if (current.type == 'multi') {
					// if(isTinyMCE){
					// 	$('#mymail-editor-tmce').trigger('click');
					// 	if (tinymce.activeEditor) tinymce.execCommand(tinymce.majorVersion == 4 ? 'mceRemoveEditor' : 'mceRemoveControl', false, 'mymail-editor');
					// }
					//editor.appendTo('#mymail_editor_holder');
				}

				bar.hide(0, function(){
					loader(false);
					bar.removeClass('current-' + current.type);
					$('#single-link').hide();
					_save();
				});
				_refresh();

				//current = currentimage = currenttext = null;
			}

			init();

			return {
				open: function (data) {
					open(data);
				},
				close: function () {
					close();
				}
			}
		};



	var _modules = function () {

			var container = _iframe.contents().find('modules'),
				elements, modules = {},
				count = 0,
				modulesraw = $('#modules'),
				preview = $('#mymail_type_preview'),
				previewimg = $('<img>').appendTo(preview),
				selector;

			function up() {
				var module = $(this).parent().parent().parent();
				module.insertBefore(module.prev('module'));
				_refresh();
				_save();
				return false;
			}

			function down() {
				var module = $(this).parent().parent().parent();
				module.insertAfter(module.next('module'));
				_refresh();
				_save();
				return false;
			}

			function add() {
				var module = $(this).parent().parent().parent(),
					offset = Math.max(50, $(this).offset().top+55-selector.height()/2);
					
				selector.data('current', module).css({
					'top': offset
				}).show();
				return false;
			}

			function duplicate() {
				var module = $(this).parent().parent().parent(),
					clone = module.clone().hide();
					
				_container.addClass('noeditbuttons');
				
				clone.insertAfter(module);
				
				_resize(clone.height());
				
				clone.slideDown(function(){
					clone.css('display', 'block');
					_refresh();
					_save();
				});
				_scroll(Math.min(clone.offset().top + _container.offset().top - 100, (_container.offset().top+_container.height()-$(window).height()) + 100));
				return false;
			}
			
			function auto() {
				var module = $(this).parent().parent().parent();
				var data = {
					element: module,
					name: module.attr('label'),
					type: 'auto',
					offset: module.offset()
				}
				editbar.open(data);
				return false;
			}

			function addmodule() {
				var module = selector.data('current');
				insert($(this).data('id'), ((module.is('module')) ? module : false));
				hideSelector();
				return false;
			}

			function hideSelector() {
				selector.hide();
				return false;
			}

			function changeName() {
				var _this = $(this),
					value = _this.val(),
					module = _this.parent().parent();
				
				if(!value){
					value = _this.attr('placeholder');
					_this.val(value);
				}
				
				module.attr('label', value);
			}
			
			function remove() {
				var module = $(this).parent().parent().parent();
				module.fadeTo(100, 0, function () {
					_container.addClass('noeditbuttons');
					module.slideUp(function () {
						module.remove();
						_refresh();
						_save();
					});
				});
				return false;
			}

			function insert(id, before) {
			
				if (!modules[id]) return false;
				var clone = modules[id].el.clone();
				_container.addClass('noeditbuttons');
				
				(before) ? clone.hide().insertBefore(before) : clone.hide().appendTo(container);

				_resize(clone.height(), 0);

				clone.slideDown(function () {
					clone.addClass('active').css('display', 'block');
					_refresh();
					_save();
				});
				
				_scroll(Math.min(clone.offset().top + _container.offset().top - 100, (_container.offset().top+_container.height()-$(window).height()) + 100));

			}

			function codeView() {
				
				var module = $(this).parent().parent();
				var data = {
					element: module,
					name: module.attr('label'),
					type: 'codeview',
					offset: module.offset()
				}
				editbar.open(data);
				return false;
			}

			function showPreview() {
				//soon
				return false;
				var $this = $(this),
					type = $this.data('type');
				if (!type) return false;
				var pos = $(this).position();
				previewimg.attr('src', '');
				preview.css({
					top: pos.top + 50,
					right: 145
				}).stop().show();
			}

			function hidePreview() {
				preview.stop().fadeOut(100);
			}

			function init() {
				_container
				.on('click', 'a.addmodule', addmodule)
				.on('mouseenter', 'a.addmodule', showPreview)
				.on('mouseleave', 'a.addmodule', hidePreview)
				.on('mouseleave', '#moduleselector', hideSelector);
				
				refresh();
			}

			function refresh() {

				elements = $(modulesraw.val()).add(_iframe.contents().find('module'));
				
				container = _iframe.contents().find('modules');
				container
				.on('click', 'a.up', up)
				.on('click', 'a.down', down)
				.on('click', 'a.add', add)
				.on('click', 'a.auto', auto)
				.on('click', 'a.duplicate', duplicate)
				.on('click', 'a.remove', remove)
				.on('click', 'a.codeview', codeView)
				.on('change', 'input.modulelabel', changeName);

				var html = '<ul id="moduleselector">', x = '';

				//reset
				modules = [], count = 0;
				//add module buttons and add them to the list
				$.each(elements, function (i) {
					var $this = $(this);
					if ($this.is('module')) {
						var name = $this.attr('label'),
							p, custom = ($this.is('[custom]') ? ' custom-module' : ''),
							codeview = mymaildata.codeview ? '<a class="mymail-btn codeview" title="' + mymailL10n.codeview + '"></a>' : '',
							auto = ($this.is('[auto]') ? '<a class="mymail-btn auto" title="' + mymailL10n.auto + '"></a>' : '');
						
							name = _translateModuleName(name);
							
						$('<div class="modulebuttons">' + codeview + '<span>' + auto + '<a class="mymail-btn add" title="' + mymailL10n.add_module + '"></a><a class="mymail-btn duplicate" title="' + mymailL10n.duplicate_module + '"></a><a class="mymail-btn up" title="' + mymailL10n.move_module_up + '"></a><a class="mymail-btn down" title="' + mymailL10n.move_module_down + '"></a><a class="mymail-btn remove" title="' + mymailL10n.remove_module + '"></a></span><input class="modulelabel" type="text" value="' + name + '" placeholder="' + name + '" title="' + mymailL10n.module_label + '"></div>').prependTo($this);
						
						//x += "'"+name+"' => sprintf(_x('"+name+"', 'common module name', 'mymail'), '1/2'),\n";
						
						if(!$this.parent().length){
							
							modules.push({
								name: name,
								el: $this
							});
							p = $this.data('type') ? ' data-type="' + $this.data('type') + '"' : '';
							html += '<li><a class="mymail-btn addmodule '+custom+'" title="' + name + '"' + p + ' data-id="' + count + '">' + name + '</a></li>';
							count++;
						}
					}
				});
				
				//console.log(x);
				
				html += '</ul>';
				selector = $(html);
				selector.prependTo(_container);

				if (!container.find('.modulebuttons.main').length && count) container.prepend('<div class="modulebuttons main"><a class="mymail-btn add" title="' + mymailL10n.add_module + '" style="display:block"> ' + mymailL10n.add_module + '</a></div>')

				_refresh();

			}

			init();
			return {
				refresh: function () {
					refresh();
				}
			}
		}

	function _scroll(pos, callback) {
		if(animateDOM.scrollTop() == pos){
			callback && callback();
			return
		}
		animateDOM.animate({
			'scrollTop': pos
		}, callback &&
		function () {
			callback();
		});
	}

	function _refresh() {
		clearTimeout(refreshtimout);
		refreshtimout = setTimeout(function(){
			_resize();

			if (!_disabled) {
				if(_iframe[0].contentWindow.window.mymail_refresh) _iframe[0].contentWindow.window.mymail_refresh();
				_editButtons();
			} else {
				_clickBadges();
			}
		},10);
	}

	function _resize(extra, delay) {
		if (!iframeloaded) return false;
		setTimeout(function(){
			var height = _iframe[0].contentWindow.document.body.offsetHeight || _iframe.contents().find("html")[0].innerHeight || _iframe.contents().find("html").height();
			_iframe.attr("height", Math.max(500, height + 10 + (extra || 0)));
		}, delay ? delay : 500);
	}

	//write the html into the content;

	function _save() {
		if (!_disabled && iframeloaded) {

			var content = _getFrameContent();
			
			var length = _undo.length,
				lastundo = _undo[length];
			
			if (lastundo != content) {

				content = style + content;
				_content.val(content);

				$('#mymail_preheader').prop('readonly', !content.match('{preheader}'));
				
				_undo = _undo.splice(0, _currentundo + 1);

				_undo.push(content);
				if (length >= mymailL10n.undosteps) _undo.shift();
				_currentundo = _undo.length - 1;

				if (_currentundo) _obar.find('a.undo').removeClass('disabled');
				_obar.find('a.redo').addClass('disabled');

			}

		}
	}

	function _editButtons() {
		_container.find('.content.mymail-btn, .edit-sign').remove();
		var cont = _iframe.contents().find('html');
		var images = cont.find('img[editable]'),
			text = cont.find('multi, single'),
			buttoncontainer = cont.find('buttons'),
			buttons = cont.find('a[editable]'),
			repeatable = cont.find('[repeatable]');
			
		setTimeout(function(){

		$.each(text, function () {
			var $this = $(this),
				offset = $this.offset(),
				top = offset.top + 40,
				left = offset.left,
				name = $this.attr('label'),
				type, btn;

			if ($this.prop('tagName').toLowerCase() == 'multi') {
				type = 'multi';
			} else if ($this.prop('tagName').toLowerCase() == 'single') {
				type = 'single';
			}
			
			$this.off('click').one('click', function(){
				editbar.open({
					'offset': offset,
					'type' : type,
					'name' : name,
					'element' : $this
				});
			});
			
			
		});

		$.each(images, function () {
			var $this = $(this),
				offset = $this.offset(),
				top = offset.top + 61,
				left = offset.left,
				name = $this.attr('label'),
				type = 'img', btn;

			$this.off('click').one('click', function(){
				editbar.open({
					'offset': offset,
					'type' : type,
					'name' : name,
					'element' : $this
				});
			})
			
			
		});

		$.each(buttons, function () {
			var $this = $(this),
				offset = $this.offset(),
				top = offset.top + 40,
				left = offset.left,
				name = $this.attr('label'),
				type = 'btn', btn;

		$this.off('click').one('click', function(){
				editbar.open({
					'offset': offset,
					'type' : type,
					'name' : name,
					'element' : $this
				});
			});
			
			
		});
		
		$.each(buttoncontainer, function () {
		
			var $this = $(this),
				name = $this.attr('label'),
				offset = this.getBoundingClientRect(),
				top = offset.top + 46,
				left = offset.right + 18,
				btn;
			
			btn = $('<a class="addbutton content mymail-btn" title="' + mymailL10n.add_button + '"></a>').css({
				top: top,
				left: left
			}).appendTo(_container);
			
			btn.data('offset', offset).data('name', name);
			btn.data('element', $this);

		});
		
		$.each(repeatable, function () {
			var $this = $(this),
				name = $this.attr('label'),
				offset = this.getBoundingClientRect(),
				top = offset.top + 48,
				left = offset.right,
				btn;

			btn = $('<a class="addrepeater content mymail-btn" title="' + sprintf(mymailL10n.add_s, name) + '"></a>').css({
				top: top - 3,
				left: left + 18
			}).appendTo(_container);
			
			btn.data('offset', offset).data('name', name);
			btn.data('element', $this);
			
			btn = $('<a class="removerepeater content mymail-btn" title="' + sprintf(mymailL10n.remove_s, name) + '"></a>').css({
				top: top + 18,
				left: left + 18
			}).appendTo(_container);
			
			btn.data('offset', offset).data('name', name);
			btn.data('element', $this);

		});

		text.add(images).add(buttons).off('mouseenter').on('mouseenter', function(){
			var $this = $(this),
				element = $this.is('a') && $this.find('img').length ? $this.find('img')[0] : this,
				offset = element.getBoundingClientRect(),
				btn = $('<div class="edit-sign" title="' + sprintf(mymailL10n.click_to_edit, '&quot;'+$this.attr('label')+'&quot;') +'"></div>').appendTo(_container);
				btn.css({
					top: offset.top+42,
					left: offset.right-btn.width()+25
				});


			$this.data('btn', btn);
		}).off('mouseleave').on('mouseleave', function(){
			if($(this).data('btn')) $(this).data('btn').remove();
		});

		_container.removeClass('noeditbuttons');

		}, 500);

	}

	function _clickBadges(stats) {
		_container.find('.clickbadge').remove();
		var stats = stats || $('#mymail_click_stats').data('stats'),
			total = parseInt(stats.total, 10);

		if (!total) return;
		
		$.each(stats.clicks, function (href, countobjects) {
			
			$.each(countobjects, function (index, counts) {

				var link = _iframe.contents().find('a[href="' + href.replace('&amp;', '&') + '"]').eq(index);

				if (link.length) {
					link.css({'display': 'inline-block'});

					var offset = link.offset(),
						top = offset.top,
						left = offset.left + 5,
						percentage = (counts.clicks / total) * 100,
						v = (percentage < 1 ? '&lsaquo;1' : Math.round(percentage)) + '%',
						badge = $('<a class="clickbadge ' + (percentage < 40 ? 'clickbadge-outside' : '') + '" data-p="' + percentage + '" data-link="' + href + '" data-clicks="' + counts.clicks + '" data-total="' + counts.total + '"><span style="width:' + (Math.max(0, percentage - 2) ) + '%">' + (percentage < 40 ? '&nbsp;' : v) + '</span>'+(percentage < 40 ? ' ' + v : '')+'</a>')
							.css({
								top: top,
								left: left
							}).appendTo(_container);

				}

			});
		});
	}


	function _changeColor(from, to, element, save) {
		if(!from) from = to;
		if(!to) return false;
		if(from == to) return false;
		var raw = _getContent(),
			reg = new RegExp(from, 'gi'),
			m = $('#modules').val();

		if(element) element.data('value', to);

		$('#modules').val(m.replace(reg, to));

		$('#mymail-color-'+from.substr(1).toLowerCase()).attr('id', 'mymail-color-'+to.substr(1).toLowerCase());
		
		if(save){
			_setContent(raw.replace(reg, to), 300);
		}else{
			_content.val(raw.replace(reg, to));
		}
		
	}

	function _changeBackgroundLink(from, to, element, save) {
		if(!from) from = to;
		if(!to) return false;
		if(from == to) return false;
		var raw = _getContent(),
			reg = new RegExp('="'+from+'"', 'gi'),
			m = $('#modules').val(),
			mr, cr;
			
		from = '="'+from+'"';
		to = '="'+to+'"';
		mr = _replace(m, from, to);
		cr = _replace(raw, from, to);
		
		if(element) element.data('value', to);
		
		$('#modules').val(mr);
		
		(save)
			? _setContent(cr, 300)
			: _content.val(cr);
		
	}
	
	function _replace(str, match, repl) {
		if (match === repl)
			return str;
		do {
			str = str.replace(match, repl);
		} while(str.indexOf(match) !== -1);
		return str;
	}
	
	function _changeBG(file) {
		var raw = _getContent(),
			html = raw.replace(/body{background-image:url\(.*}/i, '');

		if (file) {
			var s = (file) ? "\tbody{background-image:url('" + file + "');background-repeat:repeat-y no-repeat;background-position:top center;}" : '',
			html = html.replace(/<style.*?>/i, '<style type="text/css">' + s)
			//.replace(/<td /i, '<td background="'+base+file+'"');
			.replace(/<td/i, '<td background="' + file + '"');
			//.replace(/background="([^"]*)"/i,'background="'+base+file+'"');
			$('ul.backgrounds > li > a').css({
				'background-image': "url('" + file + "')"
			});
		} else {

			var parts = html.match(/<td(.*)background="[^"]*"(.*)/i);

			if (parts) html = html.replace(parts[0], '<td ' + parts[1] + ' ' + parts[2]);
			//.replace(/<td(.*)background="([^"]*)"/i,'<td ');
			$('ul.backgrounds > li > a').css({
				'background-image': "none"
			});
			//.replace(/background="([^"]*)"/i,'background=""');
		}

		_setContent(html);
		return;
	}

	function _changeElements(version) {
		var raw = _getContent(),
			reg = /\/img\/version(\d+)\//g,
			to = '/img/' + version + '/';

		html = raw.replace(reg, to);

		var m = $('#modules').val();
		$('#modules').val(m.replace(reg, to));

		_setContent(html);

		return;
	}

	function _disable(buttononly) {
		isDisabled = true;
		$('#publishing-action').find('input').prop('disabled', true);
		$('.button').prop('disabled', true);
		if (buttononly !== true) $('input').prop('disabled', true);
	}

	function _enable() {
		$('#publishing-action').find('input').prop('disabled', false);
		$('.button').prop('disabled', false);
		$('input').prop('disabled', false);
		isDisabled = false;
	}

	function _getFrameContent() {
		if(typeof _iframe[0].contentWindow.document.body == 'null') return '';
		var content = _iframe[0].contentWindow.document.body.innerHTML;

		return $.trim(content);
	}

	function _getContent() {
		var c = _content.val() || _getFrameContent();
		return c;
	}

	function _getAutosaveString() {
		
		var str = _title.val() + _content.val() + _excerpt.val() + _subject.val() + _preheader.val();
		return str;
	}

	function _setContent(content, delay, saveit) {

		content = $('<div>' + content.replace(/^[\s\S]*<body.*?>|<\/body>[\s\S]*$/g, '') + '</div>').html();

		($.browser.webkit || $.browser.mozilla) ? _iframe[0].contentWindow.document.body.innerHTML = content : _idoc.body.innerHTML = content;

		if (delay !== false) {
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				modules.refresh();
			}, delay || 100);
		}
		if (typeof saveit == 'undefined' || saveit === true) _save();
	}

	function _ajax(action, data, callback, errorCallback, dataType){

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
			dataType: dataType ? dataType : "JSON"
		});
	}

	function _sanitize(string) {
		return $.trim(string).toLowerCase().replace(/ /g, '_').replace(/[^a-z0-9_-]*/g, '');
	}

	function _time() {

		var t, x, h, m, usertime = new Date(),
			elements = $('.time'),
			deliverytime = $('.deliverytime').eq(0),
			activecheck = $('#mymail_data_active'),
			servertime = parseInt(elements.data('timestamp'), 10) * 1000,
			seconds = true,
			offset = servertime - usertime.getTime() + (usertime.getTimezoneOffset() * 60000);

		var delay = (seconds) ? 1000 : 20000;

		function set() {
			t = new Date();

			usertime = t.getTime();
			t.setTime(usertime + offset);
			h = t.getHours();
			m = t.getMinutes();

			if (!_disabled && x && m != x[1] && !activecheck.is(':checked')) deliverytime.val(zero(h) + ':' + zero(m));
			x = [];
			x.push(t.getHours());
			x.push(t.getMinutes());
			if (seconds) x.push(t.getSeconds());
			for (var i = 0; i < 3; i++) {
				x[i] = zero(x[i]);
			};
			elements.html(x.join('<span class="blink">:</span>'));
			setTimeout(function () {
				set();
			}, delay);
		}

		function zero(value) {
			return (value < 10) ? '0' + value : value;
		}

		set();
	}


	function sprintf() {
		var a = Array.prototype.slice.call(arguments),
			str = a.shift();
		while (a.length) str = str.replace('%s', a.shift());
		return str;
	}
	
	function _translateModuleName(name){
		name = typeof name == 'string' ? name : 'undefined';
		return mymailL10n.modules[name.toLowerCase()] ? unescape(mymailL10n.modules[name.toLowerCase()]) : name;
	}
	
	function _getRealDimensions(el, callback){
		el = el.eq(0);
		if(el.is('img') && el.attr('src')){
			var image = new Image(), factor;
			image.onload = function(){
				factor = ((image.width/el.width()).toFixed(1) || 1);
				if(callback) callback.call(this, image.width, image.height, isFinite(factor) ? parseFloat(factor) : 1)
			}
			image.src = el.attr('src');
		};
	}
	
	function _rgbToHex(r, g, b, hash) {
		return (hash === false ? '' : '#') + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
	}

	if (document.selection && document.selection.createRange) {
	
		selectRange = function (input, startPos, endPos) {
			input.focus();
			input.select();
			var range = document.selection.createRange();
			range.collapse(true);
			range.moveEnd("character", endPos);
			range.moveStart("character", startPos);
			range.select();
			return true;
		}
		
	} else {
	
		selectRange = function (input, startPos, endPos) {
			input.selectionStart = startPos;
			input.selectionEnd = endPos;
			return true;
		}
	}

	if (window.getSelection) { // all browsers, except IE before version 9

		getSelect = function (input) {
			var selText = "";
			if (document.activeElement && (document.activeElement.tagName.toLowerCase() == "textarea" || document.activeElement.tagName.toLowerCase() == "input")) {
				var text = document.activeElement.value;
				selText = text.substring(document.activeElement.selectionStart, document.activeElement.selectionEnd);
			} else {
				var selRange = window.getSelection();
				selText = selRange.toString();
			}

			return selText;
		}
		
	} else {
	
		getSelect = function (input) {
			var selText = "";
			if (document.selection.createRange) { // Internet Explorer
				var range = document.selection.createRange();
				selText = range.text;
			}

			return selText;
		}
	}

	_init();

});