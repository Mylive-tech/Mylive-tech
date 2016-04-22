jQuery(document).ready(function($) {

	"use strict"
	
	var timeout,
		_id = $('#ID'),
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
