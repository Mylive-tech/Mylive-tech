jQuery(document).ready(function($) {

	"use strict"
	
	var iframe = $('#mymail_iframe'),
		wpnonce = $('#_wpnonce').val(),
		base = iframe.data('base'),
		templateeditor = $('#templateeditor'),
		templatecontent = $('textarea.editor'),
		uploadinfo = $('.uploadinfo'),
		animateDOM = $.browser.webkit ? $('body') : $('html'),
		codemirror;
		
		
	$('#mymail_templates')
	.on('click', '.edit', function(){
		var $this = $(this),
			$container = $this.closest('.available-template'),
			$templates = $('.available-template'),
			loader = $('.template-ajax-loading').css({ 'display':'inline' }),
			href = $this.attr('href'),
			slug = $this.data('slug');


		if($this.parent().hasClass('disabled')) return false;
			
		if(!$this.parent().is('.nav-tab')){
		
			$templates.removeClass('edit');
		
			$container.addClass('edit');
			
			var id = $container.data('id');
			var count = Math.floor( ($('#available-templates').outerWidth())/($container.width()+22) );
			var pos = Math.floor(id/count)*count+count-1;

			templateeditor.find('textarea').val('');
			templateeditor.find('h3').html($container.find('h3').html());
			templateeditor.slideDown();
			
			templateeditor.insertAfter($templates.eq(pos).length ? $templates.eq(pos) : $templates.last());
			_scroll(templateeditor.offset().top-50);
			
		}
		
		get_template_html(slug, href);
		return false;
	})
	.on('click', '.nav-tab', function(){
		return false;
	})
	.on('click', '.remove-file', function(){
		
		var $this = $(this);
		
		if(confirm($this.data('confirm'))){
		
			_ajax('remove_template', {
				file: $this.data('file'),
			}, function(response){
				if(response.success){

					$this.parent().fadeOut();
					$this.parent().prev().find('a.edit').click();

				}
				
			});
		}
			
		return false;
	})
	.on('click', 'a.cancel', function(){
		templateeditor.slideUp();
		$('.available-template').removeClass('edit');
		return false;
	})
	.on('click', 'button.save, button.saveas', function(){
		var $this = $(this),
			loader = $('.template-ajax-loading'),
			content = codemirror.getValue(),
			message = $('span.message'),
			name;
		
		if($this.is('.saveas') && !(name = prompt(mymailL10n.enter_template_name+':', ''))) return false;	
	
			
		loader.css({ 'display':'inline' }),
		$this.prop('disabled', true);
		
		_ajax('set_template_html', {
			content: content,	
			name: name,	
			slug: $('#slug').val(),
			file: $('#file').val()
		}, function(response){
			loader.hide();
			$this.prop('disabled', false);
		
			if(response.success){
				message.fadeIn(10).html(response.msg).delay(2000).fadeOut();
				if(response.newfile){
					get_template_html($('#slug').val(), 'mymail/'+response.newfile);

				}
			}else{
				alert(response.msg);
			}
			
		}, function(jqXHR, textStatus, errorThrown){
			loader.hide();
			$this.prop('disabled', false);
			
			alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
		});
		
		return false;
	})
	.on('click', '.thickbox-preview', function(){

		var _this = $(this),
			name = _this.attr('title'),
			slug = _this.data('slug'),
			href = _this.attr('href');

		$('#thickboxbox').find('iframe').attr('src', href);
		$('.thickbox-filelist').empty().hide();
		tb_show(name, '?&width=900&inlineId=thickboxbox&TB_inline', null);
		$('#TB_window').width( 936 ).height(700).css( 'margin-left', - 936 / 2 ).css( 'margin-top', - 700 / 2 );
		_ajax('get_file_list', {
			slug: slug
		}, function(response){
			//loader.hide();

			if(response.success){
				var html = '';
				$.each(response.files, function(file, data){
					html += '<li><a href="'+response.base+'/'+file+'" class="thickbox-file"><img src="'+data.screenshot+'"><span>'+data.label+' ('+file+')</span></a></li>';
				});

				$('.thickbox-filelist').html(html).slideDown();

			}else{
				alert(response.msg);
			}
			
		}, function(jqXHR, textStatus, errorThrown){
			loader.hide();
			$this.prop('disabled', false);
			
			alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');
		});
		return false;
	})
	.on('click', 'a.activate', function(){

		var _this = $(this), license = '', oldlicense = _this.data('license') || '';

		license = prompt(mymailL10n.enter_license, oldlicense);

		if(oldlicense != license && license !== null)
			document.location.href = _this.attr('href')+'&license='+(license == true ? '' : license);

		return false;
	})
	.on('click', 'a.download', function(){
		$(this).prop('disabled', true);
	})
	.on('click', 'a.update', function(){

		return confirm(mymailL10n.update_note);
	});


;

	$(document).on('click', 'a.thickbox-file', function(){

		$('.thickbox-iframe').attr('src', $(this).attr('href'));

		return false;
	})




	var uploader_init = function() {
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

		uploader.init();

		uploader.bind('FilesAdded', function(up, files) {
			
			setTimeout(function(){
				up.refresh();
				up.start();
			}, 1);

		});

		uploader.bind('BeforeUpload', function(up, file) {

		});

		uploader.bind('UploadFile', function(up, file) {
		});

		uploader.bind('UploadProgress', function(up, file) {
			uploadinfo.html(sprintf(mymailL10n.uploading, file.percent+'%'));
			//progressbar.stop().animate({'width': file.percent+'%'}, 100);
		});

		uploader.bind('Error', function(up, err) {
			uploadinfo.html(err.message);
			// progress.addClass('error');
			up.refresh();
		});

		uploader.bind('FileUploaded', function(up, file, response) {
			console.log(up, file, response);
			response = $.parseJSON(response.response);
			if(response.success){
				location.reload();
			}else{
				uploadinfo.html(response.error);
			}
			console.log(response);
		});

		uploader.bind('UploadComplete', function(up, files) {
			//progress.addClass('finished');
		});
	}

	if ( typeof(wpUploaderInit) == 'object' )
		uploader_init();
		


	
	function get_template_html(slug, href){
		_ajax('get_template_html', {
			slug: slug,
			href: href
		}, function(response){
			$('.template-ajax-loading').hide();
			
			$('#file').val(response.file);
			$('#slug').val(response.slug);
			templatecontent.val(response.html);
			
			if(!codemirror){
				var mixedMode = {
					name: "htmlmixed",
					scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
					mode: null},
					{matches: /(text|application)\/(x-)?vb(a|script)/i,
						mode: "vbscript"}]
				};
				codemirror = CodeMirror.fromTextArea(templatecontent.get(0), {
					mode: mixedMode,
					tabMode: "indent",
					lineNumbers: true,
					autofocus: true
				});
			}else{
				codemirror.setValue(response.html);
			}
			var html = '';
			
			$.each(response.files, function(name,data){
				html += ' <span class="nav-tab '+(name == response.file ? 'nav-tab-active' : '')+'"><a href="mymail/'+name+'" data-slug="'+slug+'" class="edit">'+data.label+'</a>';
				if(name != 'index.html' && name != 'notification.html')
				html += '<a class="remove-file mymail-icon" data-file="'+slug+'/'+name+'" data-confirm="'+sprintf(mymailL10n.delete_template_file, data.label, data.name)+'"></a>';
				html += '</span>';
			});
			templateeditor.find('.nav-tab-wrapper').html(html);
		});
	}
		
	function _scroll(pos, callback) {
		animateDOM.animate({
			'scrollTop': pos  
		}, callback && function(){
			callback();
		});
	}
	
	function sprintf() {
		var a = Array.prototype.slice.call(arguments),
			str = a.shift();
		while (a.length) str = str.replace('%s', a.shift());
		return str;
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
	
});
