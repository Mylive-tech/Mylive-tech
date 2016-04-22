jQuery(document).ready(function($) {
	
	var colors = ['#21759b','#464646'];
	
	// Daily graph
	var vfb_daily = Morris.Line({
		element: 'vfb-graph-daily',
		data: vfb_graph_object( 'daily' ),
		xkey: 'date',
		ykeys: ['count', 'avg'],
		labels: ['Entries', 'Daily Average'],
		lineColors: colors,
	});
	
	// Weekly graph
	var vfb_monthly = Morris.Area({
		element: 'vfb-graph-monthly',
		data: vfb_graph_object( 'monthly' ),
		xkey: 'date',
		ykeys: ['count'],
		labels: ['Entries'],
		xLabels: 'month',
		lineColors: colors,
	});
	
	// Filter start/end date
	$( '#analytics-filter' ).click( function(e){
		e.preventDefault();
		
		var start = $( 'select[name=analytics-start-date] option:selected' ).val(),
			end   = $( 'select[name=analytics-end-date] option:selected' ).val();
		
		vfb_daily.setData( vfb_graph_object( 'daily', start, end ) );
		vfb_monthly.setData( vfb_graph_object( 'monthly', start, end ) );
	});	
	
	// Get the graph data
	function vfb_graph_object( view, start, end ) {
		var obj = [{date:"",count:0,avg:0}];
		
		start = ( typeof start !== 'undefined' ) ? start : 0;
		end   = ( typeof end   !== 'undefined' ) ? end   : 0;
		
		$( 'img.waiting' ).show();
		
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			async: false,
			cache: false,
			dataType: 'json',
			data:{
				action: 'visual_form_builder_graphs',
				form: $( '#analytics-switcher select[name="form_id"] option:selected' ).val(),
				view: view,
				date_start: start,
				date_end: end
			},
			success: function( response ) {
				$( 'img.waiting' ).hide();
				
				obj = response.entries;
			},
			error: function( xhr,textStatus,e ) {
				alert( xhr + ' ' + textStatus + ' ' + e );
				return; 
			}
		});
		
		return obj;
	}
});