 jQuery(document).ready(function($) {

	"use strict"

	var body = $('body'),uploader,container,modules,images,buttons;

	body.addClass('mymail-loading');

	$(window).on('load', function(){
		body
		.on('click', 'a', function(event) {
			event.preventDefault();
		});
		_refreshMain();
	});

	function _refresh(){

		container = $('modules');
		modules = container.find('module');
		images = $('img[editable]');
		buttons = $('buttons');

		_sortable();
		_draggable();
		_upload();

		body.removeClass('mymail-loading');
		return;
	}

	function _sortable(){

		if(container.data('sortable')) container.sortable('destroy');
		if(buttons.data('sortable')) buttons.sortable('destroy');

		buttons.sortable({
			stop: function (event, ui) {
				event.stopPropagation();
				container.removeClass('dragging');
				setTimeout(function(){_refreshMain();},200);
			},
			start: function (event, ui) {
				event.stopPropagation();
				_hideButtons();
				container.addClass('dragging');
			},
			containment: 'parent',
			revert: 100,
			placeholder: "sortable-placeholder",
			items: "> a",
			distance: 5,
			forcePlaceholderSize: true,
			helper: 'clone',
			zIndex: 10000
		});


		if(modules.length < 2) return;

		container.sortable({
			stop: function (event, ui) {
				event.stopPropagation();
				container.removeClass('dragging');
				setTimeout(function(){_refreshMain();},200);
			},
			start: function (event, ui) {
				event.stopPropagation();
				_hideButtons();
				container.addClass('dragging');
			},
			containment: 'body',
			revert: 100,
			axis: 'y',
			placeholder: "sortable-placeholder",
			items: "> module",
			delay: 20,
			distance: 5,
			scroll:true,
			scrollSensitivity: 10,
			forcePlaceholderSize: true,
			helper: 'clone',
			zIndex: 10000

		});

	}

	function _draggable(){

		if(images.data('draggable')) images.draggable('destroy');
		if(images.data('droppable')) images.droppable('destroy');

		images
		.draggable({
			helper: "clone",
			scroll:true,
			scrollSensitivity: 10,
			opacity: 0.7,
			zIndex:1000,
			revert:'invalid',
			addClasses: false,
			create: function(event, ui){
				$(event.target).removeClass('ui-draggable-handle');
			},
			start: function(){
				body.addClass('ui-dragging');
			},
			stop: function(){
				body.removeClass('ui-dragging');
				_refreshMain();

			}
		}).droppable({
			addClasses: false,
			over: function( event, ui ) {
				$(event.target).addClass('ui-drag-over');
			},
			out: function( event, ui ) {
				$(event.target).removeClass('ui-drag-over');
			},
			drop: function( event, ui ) {
				var org = $(ui.draggable[0]),
					target = $(event.target),
					target_id;

					target.removeClass('ui-drag-over');

					if(!org.is('img') || !target.is('img')) return;

					target_id = target.attr('data-id') ? parseInt(target.attr('data-id'), 10) : null,
					org_id = org.attr('data-id') ? parseInt(org.attr('data-id'), 10) : null,
					copy = org.clone();

					org.addClass('mymail-loading');
					target.addClass('mymail-loading');

					_getRealDimensions(org, function(org_w,org_h,org_f){
						_getRealDimensions(target, function(target_w,target_h,target_f){

							if(event.altKey){
								org.removeClass('mymail-loading');
								target.removeClass('mymail-loading');
							}else if(target_id) {
								_ajax('create_image', {
									id: target_id,
									width: org_w,
									_height: org_h
								}, function (response) {

									org.removeAttr('src').attr({
										'data-id': target_id,
										'title': target.attr('title'),
										'alt': target.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width/org_f),
										'height': Math.round(response.image.height/org_f)
									}).data('id', target_id).removeClass('mymail-loading');

								}, function(jqXHR, textStatus, errorThrown){

									alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');

								});
							}else{

								org.removeAttr('src').attr({
									'data-id': 0,
									'title': target.attr('title'),
									'alt': target.attr('alt'),
									'src': target.attr('src'),
									'width': Math.round(org_w/org_f),
									//'height': Math.round(org_h/org_f)
									'height': Math.round((org_w/(target_w/target_h))/org_f)
								}).data('id', 0).removeClass('mymail-loading');

							}

							if(org_id) {
								_ajax('create_image', {
									id: org_id,
									width: target_w,
									_height: target_h
								}, function (response) {

									target.removeAttr('src').attr({
										'data-id': org_id,
										'title': org.attr('title'),
										'alt': org.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width/target_f),
										'height': Math.round(response.image.height/target_f)
									}).data('id', org_id).removeClass('mymail-loading');

									_refreshMain();

								}, function(jqXHR, textStatus, errorThrown){

									alert(textStatus+' '+jqXHR.status+': '+errorThrown+'\n\nCheck the JS console for more info!');

								});
							}else{

								target.removeAttr('src').attr({
									'data-id': 0,
									'title': copy.attr('title'),
									'alt': copy.attr('alt'),
									'src': copy.attr('src'),
									'width': Math.round(target_w/target_f),
									'height': Math.round((target_w/(org_w/org_h))/target_f)
								}).data('id', 0).removeClass('mymail-loading');

							}

							if(!org_id && !target_id) _refreshMain();

						});
					});

			}
		});

	}

	function _upload(){

		if(typeof mOxie != 'undefined'){

			$.each(images, function(){

				var _this = $(this),
					dropzone;

				if(_this.data('has-dropzone')) return;

				dropzone = new mOxie.FileDrop({
					drop_zone: this,
				});

				dropzone.ondrop = function( e ) {

					if(parent.window.mymail_is_modulde_dragging) return;
					_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');

					var file = dropzone.files.shift(),
						altkey = window.event && event.altKey,
						dimensions = [_this.width(), _this.height()],
						position = _this.offset(),
						preview = $('<div class="mymail-upload-info"><div class="mymail-upload-info-bar"></div><div class="mymail-upload-info-text"></div></div>').css({
							width: dimensions[0],
							height: dimensions[1],
							top: position.top,
							left: position.left

						}),

						preloader = new mOxie.Image(file);

					preloader.onerror = function( e ) {

						alert(mymailL10n.unsupported_format);

					}
					preloader.onload = function( e ) {

						preview.appendTo('body');

						file._element = _this;
						file._altKey = altkey;
						file._preview = preview;
						file._dimensions = [preloader.width, preloader.height, preloader.width/preloader.height];

						preloader.downsize( dimensions[0], dimensions[1] );
						preview.find('.mymail-upload-info-bar').css({
							'background-image': 'url('+preloader.getAsDataURL()+')',
							'background-size': dimensions[0]+'px '+(dimensions[0]/file._dimensions[2])+'px'
						});

						uploader.addFile( file );
					};

					preloader.load( file );

				};
				dropzone.ondragenter = function( e ) {
					if(parent.window.mymail_is_modulde_dragging) return;
					_this.addClass('ui-drag-over-file');
					if(window.event && event.altKey) _this.addClass('ui-drag-over-file-alt');
				};
				dropzone.ondragleave = function( e ) {
					if(parent.window.mymail_is_modulde_dragging) return;
					_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
				};
				dropzone.onerror = function( e ) {
					if(parent.window.mymail_is_modulde_dragging) return;
					_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
				};

				dropzone.init();

				_this.data('has-dropzone', true);

			});


			if(!uploader){


				$('<button id="mymail-editorimage-upload-button" />').hide().appendTo('body');
				uploader = new plupload.Uploader(mymaildata.plupload);

				uploader.bind('Init', function(up) {
					$('.moxie-shim').remove();
				});

				uploader.bind('FilesAdded', function(up, files) {

					var source = files[0].getSource();

					_getRealDimensions(source._element, function(width, height, factor){

						up.settings.multipart_params.width = width;
						up.settings.multipart_params.height = height;
						up.settings.multipart_params.factor = factor;
						up.settings.multipart_params.altKey = source._altKey;
						up.refresh();
						up.start();
					});

				});

				uploader.bind('BeforeUpload', function(up, file) {
				});

				uploader.bind('UploadFile', function(up, file) {
				});

				uploader.bind('UploadProgress', function(up, file) {

					var source = file.getSource();

					source._preview.find('.mymail-upload-info-bar').width(file.percent+'%');
					source._preview.find('.mymail-upload-info-text').html(file.percent+'%');

				});

				uploader.bind('Error', function(up, err) {
					var source = err.file.getSource();

					alert(err.message);

					source._preview.remove();
				});

				uploader.bind('FileUploaded', function(up, file, response) {

					var source = file.getSource(),
						delay, height;

					try {
						response = $.parseJSON(response.response);

						source._preview.find('.mymail-upload-info-text').html(mymailL10n.ready);
						source._element.on('load', function(){
							clearTimeout(delay);
							source._preview.fadeOut(function(){
								$(this).remove();
								_refreshMain();
							});
						});

						height = Math.round(source._element.width()/response.image.asp);

						source._element.attr({
							'src': response.image.url,
							//'height': Math.round(response.image.height/up.settings.multipart_params.factor),
							'height': height,
							'data-id': response.image.id || 0
						}).data('id', response.image.id || 0);

						source._preview.height(height);

						delay = setTimeout(function(){
							source._preview.fadeOut(function(){
								$(this).remove();
								_refreshMain();
							});
						}, 3000);
					}
					catch(err) {
						source._preview.addClass('error').find('.mymail-upload-info-text').html(mymailL10n.error);
						alert(mymailL10n.error_occurs+"\n"+err.message);
						source._preview.fadeOut(function(){
							$(this).remove();
						});
					}

				});

				uploader.bind('UploadComplete', function(up, files) {
				});

				uploader.init();

			}

		}

	}

	window.mymail_refresh = function(){
		_refresh();
	}
	window.mymail_onsave = function(){
		_refresh();
	}

	function _hideButtons(){
		if(parent.window.mymail_hideButtons) parent.window.mymail_hideButtons();
	}
	function _refreshMain(){
		if(parent.window.mymail_refresh) parent.window.mymail_refresh()
	}

	function _getRealDimensions(el, callback){
		el = el.eq(0);
		if(el.is('img') && el.attr('src') && callback){
			var image = new Image(), factor;
			image.onload = function(){
				factor = Math.max(1, Math.min(2, ((image.width/(el.attr('width') || el.width())).toFixed(1) || 1)));
				callback.call(this, image.width, image.height, isFinite(factor) ? parseFloat(factor) : 1)
			}
			image.src = el.attr('src');
		}
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
			url: window.mymaildata.ajaxurl,
			data: $.extend({action: 'mymail_'+action, _wpnonce:window.mymaildata._wpnonce}, data),
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

});
