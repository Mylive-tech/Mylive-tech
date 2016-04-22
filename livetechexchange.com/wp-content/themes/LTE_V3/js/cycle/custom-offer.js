if ( typeof( os_args )==='undefined' ) os_args = [];
var os_speed = ( os_args.speed ) ? os_args.speed : 1000;
var os_timeout = 10000;

var os_title = '.title';	// slide title element selector
var os_desc = '.desc';		// slide description element selector

var os_pagerElementsOnPage = 5;		// number of pager elements on page
var os_pagerElementWidth = 160;
var os_pagerWidth = os_pagerElementsOnPage * os_pagerElementWidth;

var os_manual = false;		// true - pause, false - play
var os_currPage = 0;		// current page number ( 0 - start )
var os_slidesCount = 0;		// slides count
var os_pagesCount = 0;		// pages count

function setPagerWidth() {
	var pagerLenght = jQuery('#os_pager > ul > li').length;
	var pagerWidth = pagerLenght * os_pagerElementWidth; 
	jQuery('#os_pager > ul').width(pagerWidth);
}

function goToPage(page) {
	jQuery('#os_pager > ul').animate({
		'left': '-' + os_pagerWidth * page + 'px' 
	},500);
}

function goToNextPage() {
	var os_nextPageFirstSlide = 0;
	var os_nextPage = os_currPage + 1;

	if ( os_nextPage < os_pagesCount) {
		os_nextPageFirstSlide = os_nextPage * os_pagerElementsOnPage;
	} else {
		// tu mozna zrobic blokowanie powrotu do poczatku jesli ma nie byc petli
	}

	os_manual = false; // pokaz ma sie wznawiac po kliknieciu next/prev
	jQuery('#os_cycle').cycle(os_nextPageFirstSlide);
}

function goToPrevPage() {
	var os_prevPageFirstSlide = 0;
	var os_prevPage = os_currPage - 1;

	if ( os_prevPage >= 0 ) {
		os_prevPageFirstSlide = os_prevPage * os_pagerElementsOnPage;
	} else {
		// tu mozna zrobic blokowanie powrotu do poczatku jesli ma nie byc petli
		os_prevPageFirstSlide = ( os_pagesCount - 1 ) * os_pagerElementsOnPage;
	}
	
	os_manual = false; // pokaz ma sie wznawiac po kliknieciu next/prev
	jQuery('#os_cycle').cycle(os_prevPageFirstSlide);
}

// creates pager elements
function pagerAnchorBuilder(id, slide) {
	var output = "";
	var title = jQuery('#os_cycle > li:eq('+id+') .title h2').text();
	var thumbnail = jQuery('#os_cycle > li:eq('+id+') .thumbnail img').attr('src');
	output += "<li>"+
		"<a href='#'>"
		+"<div class='icon'>"
		+"<img src='"+ thumbnail +"' />"		
		+"</div>"
		+"<p>"+ title +"</p>"
		+"</a>"
		+"</li>";	
	
    return output;
}    

function onBefore(curr, next, opts, fwd) {
    jQuery(next).find(os_title).css({'display':'none', 'right':'-950px'});
    jQuery(next).find(os_desc).css({'display':'none', 'left':'-950px'});
     
    // prevent doing this when pager element click
    if (!os_manual)
	{
    	if (opts.nextSlide == 0) {
    		os_currPage = 0;	
    	}
    	else if (opts.nextSlide % os_pagerElementsOnPage == 0) {			
			os_currPage += 1;
		}
    	goToPage(os_currPage);
	}
}

function onAfter(curr, next, opts, fwd) {
	jQuery(this).find(os_title).css({'display':'block'}).delay(70).stop().animate({'right':'0px'}, 250, 'easeOutQuad');
	jQuery(this).find(os_desc).css({'display':'block'}).delay(250).stop().animate({'left':'0px'}, 400, 'easeOutQuad');
}

function init() {
	setPagerWidth()
	os_slidesCount =  jQuery('#os_pager > ul > li').length;
	os_pagesCount = os_slidesCount / os_pagerElementsOnPage;
	os_pagesCount = Math.ceil(os_pagesCount);
}

jQuery(document).ready(function($){
	
	$('#Offer_slider .controls').append('<div id="os_pager"><ul></ul></div>');
	
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
		next:   '#next_slide1', 
        toggle:   '#pause_slide1',
		before:		onBefore,
		after:		onAfter,
		pager:		'#os_pager > ul',
		pagerAnchorBuilder: pagerAnchorBuilder 
	});
	
	init();
	
	$('#os_pager > ul > li > a').click(function(){
		os_manual = true;
		$('#os_cycle').cycle('pause');
	})

	$('#next_arrow').click(function(){
		goToNextPage();		
		return false;
	});
	
	$('#prev_arrow').click(function(){
		goToPrevPage();		
		return false;
	});
	
	$(function(){
    $('#Offer_slider .slides li img').show();
});

});