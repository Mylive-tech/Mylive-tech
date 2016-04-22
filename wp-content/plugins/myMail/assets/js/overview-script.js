jQuery(document).ready(function ($) {

	"use strict"

	if(typeof wp == 'undefined' || !wp.heartbeat) return;

	var current;

	$('.column-status')
	.on('click', 'a.live-action', function(){

		if($(this).hasClass('finish') && !confirm(mymailL10n.finish_campaign)){
			return false;
		}

		var row = $(this).parent().parent().parent().addClass('loading');

		$.get($(this).attr('href'), function(){
			wp.heartbeat.connectNow();
		});
		return false;

	});

	$(document)
	.on('heartbeat-send', function(e, data) {

		var ids = [];

		$('tr.type-newsletter').each(function(){
			ids.push(parseInt($(this).find('input').eq(0).val(), 10));
		});

		data['mymail'] = {
			page: 'overview',
			ids: ids
		};

	})
	.on( 'heartbeat-tick', function(e, data) {

		var first = false;

		if(data['mymail']){

			if(!current){
				current = data['mymail'];
				first = true;
				//return;
			}

			var change = false;

			$.each(data['mymail'], function(id, data){

				var row = $('#post-'+id).removeClass('loading');

				if(!data) return;

				$.each(data, function(key, value){
					if(current && current[id][key] == value) return;

					var statuschange = current && data.status != current[id].status;

					switch(key){
						case 'status':
							if(statuschange){
								row.removeClass('status-'+current[id].status).addClass('status-'+data.status);
							}
						case 'sent':
						case 'total':
						case 'sent_formatted':
							break;
						case 'column-status':
							if(data.status == 'active' && !statuschange ){
								var progress = row.find('.campaign-progress'),
									p = (data.sent/data.total*100);
								progress.find('.bar').width(p+'%');
								progress.find('span').eq(1).html(data.sent_formatted);
								progress.find('var').html(Math.round(p)+'%');
								if(!first) break;
							}
						default:
							var el = row.find('.'+key);
							if(!el.is(':visible')) break;
							el.fadeTo(10, 0.01, function(){
								el.html(value).fadeTo(200, 1);
							});

					}

					change = true;

				});


			});

			if(change) wp.heartbeat.interval( 'fast' );


			current = data['mymail'];
		}

	});

	wp.heartbeat.interval( 'fast' );
	if(wp.heartbeat.connectNow) wp.heartbeat.connectNow();

});



