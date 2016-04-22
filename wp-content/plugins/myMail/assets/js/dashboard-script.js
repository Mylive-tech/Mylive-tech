jQuery(document).ready(function($) {

	"use strict"

	var wpnonce = $('#mymail_dashboard_nonce').val(),
		campaings = $('.mymail-db-campaings'),
		subscribers = $('.mymail-db-subscribers'),
		campselect = $('#mymail-campaign-select'),
		subscriberselect = $('#mymail-subscriber-range'),
		chartelement = $('#subscriber-chart-wrap'),
		canvas = $('#subscriber-chart'),
		tooltip = $('#mymail-chart-tooltip'),
		tooltipbody = tooltip.find('div'),
		chart, currentID,
		ctx,

		chartoptions = {
			responsive: true,
			//bezierCurveTension : 0.2,
			animationEasing : "easeOutExpo",
			maintainAspectRatio: false,
			tooltipTemplate: "<%= value  %>",
			scaleLabel : "<%= (value >= 1000) ? (value/1000).toFixed(1)+'K' : value + ''%>",
			customTooltips : function(obj,b,c) {
				if(!obj){
					tooltip.removeClass('is-visible');
					return;
				}
				tooltipbody.html(obj.text+' Subscribers');
				tooltip.addClass('is-visible');
				tooltip.css({top: obj.y-(tooltip.outerHeight())+15, left: obj.x-(tooltip.outerWidth()/2)+10});
			}
		};

	if(canvas.length) ctx = canvas[0].getContext("2d");

	campselect.on('change', function(){
		loadCamp($(this).val());
	}).trigger('change');

	subscriberselect.on('change', function(){
		drawChart();
	}).trigger('change');

	$('.piechart').easyPieChart({
		animate: 1000,
		rotate: 180,
		barColor: '#2BB3E7',
		trackColor: '#f3f3f3',
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
			loadCamp(currentID, true);
		});
	}

	function drawChart(sets, scale, limit, offset){

		subscribers.addClass('loading');

		_ajax('get_dashboard_chart', {
			range: subscriberselect.val()
		}, function(response){

			resetChart();
			subscribers.removeClass('loading');

			if(!chart) chart = new Chart(ctx);
			chart.Line(response.chart, chartoptions);
		});

	}

	function resetChart(){
		chart = null;
		if(canvas) canvas.remove();
		canvas = $('<canvas>').prependTo(chartelement);
		ctx = canvas[0].getContext("2d");
		canvas.attr({
			'width': chartelement.width(),
			'height': chartelement.height()
		});

	}

	function loadCamp(ID, silent){

		if(!silent) campaings.addClass('loading');

		_ajax('get_dashboard_camp', {
			id: ID
		}, function(response){

			var camp = response.camp;

			currentID = camp.ID;

			$('#camp_name').html(camp.name).attr('href', 'post.php?post='+camp.ID+'&action=edit').removeAttr('class');
			(camp.active) ? $('#camp_name').addClass('status-active') :  $('#camp_name').addClass('status-finished');

			$('#stats_total').html(camp.sent);
			$('#stats_open').data('easyPieChart').update(camp.openrate*100);
			$('#stats_clicks').data('easyPieChart').update(camp.clickrate*100);
			$('#stats_unsubscribes').data('easyPieChart').update(camp.unsubscriberate*100);
			$('#stats_bounces').data('easyPieChart').update(camp.bouncerate*100);

			campaings.removeClass('loading');

		});


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


});
