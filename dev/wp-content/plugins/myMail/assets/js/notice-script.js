jQuery(document).ready(function($) {

	"use strict"
	
	var timeout, notices = $('.mymail-notices');

	function _init(){

		$.each(notices, function(i,notice){
			notice = $(notice);
			notice
			.on('click', 'a.mymail-dismiss', function(){

				var _this = $(this),
					el = _this.hide().parent().fadeTo(1, 0.5);

				$.get(_this.attr('href'), function(response){
					el.fadeTo(100, 0, function(){
						el.slideUp(100, function(){
							el.remove();
							console.log(notice.children().length);
							if(notice.children().length <= 1) notice.remove();
						});
					})

				})

				return false;
			})
			.on('click', 'a.mymail-dismiss-all', function(){

				var _this = $(this),
					el = _this.hide().parent().fadeTo(1, 0.5);

				$.get(_this.attr('href'), function(response){
					el.fadeTo(100, 0, function(){
						el.slideUp(100, function(){
							el.remove();
							if(!notice.children().length) notice.remove();
						});
					})

				})

				return false;
			});

		});

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
