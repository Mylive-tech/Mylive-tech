jQuery(document).ready(function($) {
		
	"use strict"
	
	var campaigndata = [],
		campaigncount = 0,
		chartelement = $('#dashboard_chart'),
		chartData,chart;
		
	function _init(){
		
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
		
		$.each($('.camp'), function(){
			var _this = $(this),
				data = _this.data('data'),
				ID = _this.data('id');

			campaigndata[ID] = {
				ID:	ID,
				name: _this.data('name'),
				active: !!_this.data('active'),
				total: parseInt(data.sent, 10),
				opens: parseInt(data.opens, 10),
				clicks: parseInt(data.clicks, 10),
				unsubscribes: parseInt(data.unsubscribes, 10),
				bounces: parseInt(data.bounces, 10),
				
			};

		});
		
		campaigncount = campaigndata.length;
		
		$('#mymail-campaign-select').on('change', function(){
			loadCamp(campaigndata[$(this).val()]);
		});

		if(chartelement.length){

			// mymailL10n.data.unshift(
			// 	[mymailL10n.date,mymailL10n.unsubscribers,mymailL10n.subscribers,mymailL10n.clicks,mymailL10n.clicks,mymailL10n.clicks]
			// );

			chartData = google.visualization.arrayToDataTable(mymailL10n.data);
			chart = new google.visualization.ColumnChart(chartelement[0])
			//chart = new google.visualization.LineChart(chartelement[0])
			chart.draw(chartData, {
				curveType: 'function',
				animation:{
	       			duration: 10000,
	       			easing: 'out',
				},
				legend: {
					position: 'none'
				},
				bar: {
					groupWidth: '99%'
				},
				chartArea: {width: '90%', height: '75%'},
				lineWidth: 5,
				backgroundColor: 'none',
				vAxis: {minValue:0, format:'#', logScale: 0, viewWindowMode: "explicit", viewWindow:{ min: 0 }},
				colors: ['#FF5B64', '#E6D1A1', '#50545B', '#A4A687', '#D8D5BC'],
				width: '100%',
				height: 250
			});
		}


		
		
	}
	
	function loadCamp(camp){

		$('#camp_name').html(camp.name).attr('href', 'post.php?post='+camp.ID+'&action=edit');
		(camp.active) ? $('#stats_cont').addClass('isactive') :  $('#stats_cont').removeClass('isactive');
		
		$('#stats_total').html(camp.total);
		$('#stats_open').data('easyPieChart').update(camp.opens/camp.total*100);
		$('#stats_clicks').data('easyPieChart').update(camp.clicks/camp.opens*100);
		$('#stats_unsubscribes').data('easyPieChart').update(camp.unsubscribes/camp.opens*100);
		$('#stats_bounces').data('easyPieChart').update(camp.bounces/(camp.total+camp.bounces)*100);
		
	}
	
	_init();
	
});
google.load('visualization', '1', {'packages':['geochart', 'corechart']});