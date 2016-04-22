if ( typeof( os_args )==='undefined' ) os_args = [];
var os_speed = ( os_args.speed ) ? os_args.speed : 500;
var os_timeout = os_args.timeout;

function pagerAnchorBuilder(id, slide) {
	output = "<li><a href='#'>"+ id +"</a></li>";	
    return output;
}   

jQuery(document).ready(function($){
		
	$('#os_cycle').cycle({
		fx:			'scrollLeft',
		easing:		'easeOutQuad',
		cleartype:	false,
		speed:		os_speed,
		timeout:	os_timeout,
		nowrap:		0,
		sync:		0,
		pause:   	1,	// pause on hover
		randomizeEffects:	0,
		pager:		'#os_pager',
		pagerAnchorBuilder: pagerAnchorBuilder
		 
	});

	$('#os_pager > li').click(function(){
		$('#os_cycle').cycle('pause');
	})

});