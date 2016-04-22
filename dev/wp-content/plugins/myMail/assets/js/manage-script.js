jQuery(document).ready(function ($) {

	"use strict"
	
	var importstatus = $('.import-status'),
		exportstatus = $('.export-status'),
		progress = $('#progress'),
		progressbar = progress.find('.bar'),
		wpnonce = $('#mymail_nonce').val(),
		importdata = null,
		importerrors = 0,
		importstarttime,
		importidentifier,
	
	uploader_init = function() {
		var uploader = new plupload.Uploader(wpUploaderInit);

		uploader.bind('Init', function(up) {
			var uploaddiv = $('#plupload-upload-ui');

			if ( up.features.dragdrop && ! $(document.body).hasClass('mobile') ) {
				uploaddiv.addClass('drag-drop');
				$('#drag-drop-area').bind('dragover.wp-uploader', function(){ // dragenter doesn't fire right :(
					uploaddiv.addClass('drag-over');
				}).bind('dragleave.wp-uploader, drop.wp-uploader', function(){
					uploaddiv.removeClass('drag-over');
				});
			} else {
				uploaddiv.removeClass('drag-drop');
				$('#drag-drop-area').unbind('.wp-uploader');
			}

			if ( up.runtime == 'html4' )
				$('.upload-flash-bypass').hide();
				
		});

		uploader.bind('FilesAdded', function(up, files) {
			$('#media-upload-error').html('');
			$('#wordpress-users').fadeOut();

			setTimeout(function(){
				up.refresh();
				up.start();
			}, 1);

		});

		uploader.bind('BeforeUpload', function(up, file) {
			progress.show().removeClass('finished error');
			importstatus.html('uploading');
		});

		uploader.bind('UploadFile', function(up, file) {
		});

		uploader.bind('UploadProgress', function(up, file) {
			importstatus.html(sprintf(mymailL10n.uploading, file.percent+'%'));
			progressbar.stop().animate({'width': file.percent+'%'}, 100);
		});

		uploader.bind('Error', function(up, err) {
			importstatus.html(err.message);
			progress.addClass('error');
			up.refresh();
		});

		uploader.bind('FileUploaded', function(up, file, response) {
			response = $.parseJSON(response.response);
			importidentifier = response.identifier;
		});

		uploader.bind('UploadComplete', function(up, files) {
			importstatus.html(mymailL10n.prepare_data);
			progress.addClass('finished');
			get_import_data();
		});

		uploader.init();
	}

	if ( typeof(wpUploaderInit) == 'object' )
		uploader_init();
		
		
	$('.wrap')
	.on('change', '#signup', function(){
		$('#signupdate').prop('disabled', !$(this).is(':checked'));
	})
	.on('click', '.do-import', function(){
	
		var lists = $('#lists').serialize(),
			order = $('#subscriber-table').serialize();
		
		if (!/%5D=email/.test(order)) {
			alert(mymailL10n.select_emailcolumn);
			return false;
		}
		if (!lists) {
			alert(mymailL10n.no_lists);
			return false;
		}
		if (!$('input[name="status"]:checked').length) {
			alert(mymailL10n.select_status);
			return false;
		}
		
		if(!confirm(mymailL10n.confirm_import)) return false;
		
		
		var _this = $(this).prop('disabled', true),
			status = $('input[name="status"]:checked').val(),
			existing = $('input[name="existing"]:checked').val(),
			signup = $('#signup').is(':checked'),
			signupdate = $('#signupdate').val(),
			keepstatus = $('#keepstatus').is(':checked'),
			loader = $('#import-ajax-loading').css({ 'display': 'inline-block' }),
			identifier = $('#identifier').val(),
			performance = $('#performance').is(':checked');
			
		
		
		progress.show();
		progressbar.stop().width(0);
		$('.step1').slideUp();
		$('.step2-body').html('<br><br>').parent().show();
		
		importstarttime = new Date();
		
		do_import(0, {
			identifier: identifier,
			order: order,
			lists: lists,
			status: status,
			keepstatus: keepstatus,
			existing: existing,
			signupdate: signup ? signupdate : null,
			performance: performance
		});
		
		importstatus.html(sprintf(mymailL10n.import_contacts, ''));
		
		window.onbeforeunload = function(){
			return mymailL10n.onbeforeunloadimport;
		};
		
		
	})
	.on('click', '.wordpress-users-toggle', function () {
		$(this).parent().parent().parent().find('li input').prop('checked', $(this).prop('checked'));
	})
	.on('click', '#addlist', function () {
		var val = $('#new_list_name').val();
		if (!val) return false;

		$('<li><label><input name="lists[]" value="' + val + '" type="checkbox" checked> ' + val + ' </label></li>').appendTo('#lists > ul');
		$('#new_list_name').val('');

	});
	
	$('#paste-import')
	.on('focus', function(){
		$(this).val('').addClass('focus');
	})
	.on('blur', function(){
		$(this).removeClass('focus');
		var value = $.trim($(this).val());
		
		if(value){
			_ajax('import_subscribers_upload_handler', {
				data: value
			}, function(response){
				
				if(response.success){
					importidentifier = response.identifier;
					$('#wordpress-users').fadeOut();
					get_import_data();
				}
			}, function(){
			
				importstatus.html('Error');
			});	
		}
	});
	$('#import_wordpress')
	.on('submit', function(){
	
		var data = $(this).serialize();
		_ajax('import_subscribers_upload_handler', {
			wordpressusers: data
		}, function(response){
			
			if(response.success){
				importidentifier = response.identifier;
				$('#wordpress-users').fadeOut();
				get_import_data();
			}
		}, function(){
		
			importstatus.html('Error');
		});	
		
		return false;
	});
	
	$( ".export-order" ).sortable({
		containment: "parent"
	});

	$('#export-subscribers').on('submit', function(){
	
		var data = $(this).serialize();
		
		progress.show().removeClass('finished error');
		
		$('.step1').slideUp();
		$('.step2').slideDown();
		$('.step2-body').html(sprintf(mymailL10n.write_file, '0.00 Kb'));
		_ajax('export_contacts', {
				data: data,
			},function(response){
			
				if(response.success){
					
					window.onbeforeunload = function(){
						return mymailL10n.onbeforeunloadexport;
					};
					
					var limit = $('.performance').val();
	
					do_export(0, limit, response.count, data);
				
				}else{
					
					alert(response.msg);
					
				}
				
			},function(jqXHR, textStatus, errorThrown){
			
				alert(textStatus);
				
			});
		
		
		return false;
	});

	$('#delete-subscribers').on('submit', function(){

		var input = prompt(mymailL10n.confirm_delete, '');

		if(!input) return false;
	
		if('delete' == input.toLowerCase()){
		
			var data = $(this).serialize();
			
			progress.show().removeClass('finished error');
			
			$('.step1').slideUp();
			progressbar.stop().animate({'width': '99%'}, 25000);
			
			_ajax('delete_contacts', {
					data: data,
				},function(response){
					
					if(response.success){
						progressbar.stop().animate({'width': '100%'}, 200, function(){
							$('.delete-status').html(response.msg);
							progress.addClass('finished');
						});
					}else{
						progressbar.stop();
						$('.delete-status').html(response.msg);
						progress.addClass('error');
					}
				
				},function(jqXHR, textStatus, errorThrown){
					
					progressbar.stop();
					$('.delete-status').html('['+jqXHR.status+'] '+errorThrown);
					progress.addClass('error');
					
				});
		
		}
		
		return false;
	});

	$('input.selectall').on('change', function(){
		var _this = $(this),
			name = _this.attr('name');
			
		$('input[name="'+name+'"]').prop('checked', _this.prop('checked'));
	});
	

	
	function do_export(offset, limit, count, data) {
	
		var t = new Date().getTime(),
			percentage = (Math.min(1, (limit*offset)/count)*100);
						
		exportstatus.html(sprintf(mymailL10n.prepare_download, ''));

		_ajax('do_export',{
			limit: limit,
			offset: offset,
			data: data
		}, function(response){
		
			var finished = percentage >= 100 && response.finished;
		
			if(response.success){
			
				if(!finished) do_export(offset+1, limit, count, data);
				
				progressbar.stop().animate({'width': (percentage)+'%'}, {
					duration: finished ? 100 : (new Date().getTime()-t)*0.9,
					easing: 'swing',
					queue:false,
					step: function(percentage){
						exportstatus.html(sprintf(mymailL10n.prepare_download, Math.ceil(percentage)+'%'));
					},
					complete: function(){
						exportstatus.html(sprintf(mymailL10n.prepare_download, Math.ceil(percentage)+'%'));
						if(finished){
							window.onbeforeunload = null;
							progress.addClass('finished');
							$('.step2-body').html(mymailL10n.download_finished);
							
							exportstatus.html(mymailL10n.downloading);
							if(response.filename) setTimeout( function() { document.location = response.filename }, 1000);
							
						}else{
							$('.step2-body').html(sprintf(mymailL10n.write_file, response.total));
						}
					}
				});
			}else{
			}
		}, function(jqXHR, textStatus, errorThrown){
		
			
		});
		
	}
	function do_import(id, options) {
	
		//if(id > importdata.parts) return;
		
		var t = new Date().getTime(),
			percentage = 0;
		
		if(!id) id = 0;
		
		_ajax('do_import',{
			id:id,
			options: options
		}, function(response){
		
			percentage = (Math.min(1, (response.imported+response.errors)/response.total)*100);
			
			$('.step2-body').html('<p>'+get_stats(response.f_imported, response.f_errors, response.f_total, percentage, response.memoryusage)+'</p>');
			importerrors = 0;		
			var finished = percentage >= 100;
		
			if(response.success){
			
				if(!finished) do_import(id+1, options);
				progressbar.stop().animate({'width': (percentage)+'%'}, {
					duration: finished ? 100 : (new Date().getTime()-t)*0.9,
					easing: 'swing',
					queue:false,
					step: function(percentage){
						importstatus.html(sprintf(mymailL10n.import_contacts, Math.ceil(percentage)+'%'));
					},
					complete: function(){
						importstatus.html(sprintf(mymailL10n.import_contacts, Math.ceil(percentage)+'%'));
						if(finished){
							window.onbeforeunload = null;
							progress.addClass('finished');
							$('.step2-body').html(response.html).slideDown();
							
						}
					}
				});
			}else{
				upload_error_handler(percentage, id, options);
			}
		}, function(jqXHR, textStatus, errorThrown){
		
			upload_error_handler(percentage, id, options);
			
		});

	}
	
	function get_import_data(){
	
		progress.removeClass('finished error');
		
		_ajax('get_import_data', { identifier : importidentifier }, function(response){
			progress.hide().removeClass('finished');

			$('.step1').slideUp();
			$('.step2-body').html(response.html).parent().show();

			$('input.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				showAnim: 'fadeIn',
				onClose: function () {
				}
			});

			importstatus.html('');
			
			importdata = response.data;
		});
		
	}
	
	function upload_error_handler(percentage, id, options){
			
		importerrors++;
		
		if(importerrors >= 5){
			
			alert(mymailL10n.error_importing);
			window.onbeforeunload = null;
			return;
		}
		
		var i = importerrors*5,
			str = '',
			errorint = setInterval(function(){
	
			if(i <= 0) {
				clearInterval(errorint);
				progress.removeClass('paused');
				do_import(id, options);
				str = Math.round(percentage)+'%';
			}else{
				progress.addClass('paused');
				str = '<span class="error">'+sprintf(mymailL10n.continues_in, (i--))+'</span>';

			}
			importstatus.html(sprintf(mymailL10n.import_contacts, str));
			
			
		}, 1000);
	}


	function get_stats(imported, errors, total, percentage, memoryusage) {

		var timepast = new Date().getTime()-importstarttime.getTime(),
			timeleft = Math.ceil(((100 - percentage) * (timepast/percentage))/60000);
			
		return sprintf(mymailL10n.current_stats, '<strong>'+imported+'</strong>', '<strong>'+total+'</strong>', '<strong>'+errors+'</strong>', '<strong>'+memoryusage+'</strong>')+'<br>'+
				sprintf(mymailL10n.estimate_time, timeleft);

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
		var a = Array.prototype.slice.call(arguments),
			str = a.shift();
		while (a.length) str = str.replace('%s', a.shift());
		return str;
	}

});



