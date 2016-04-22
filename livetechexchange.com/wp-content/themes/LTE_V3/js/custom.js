jQuery(document).ready(function($){
	
/* ---------------------------------------------------------------------------
 * Main menu
 * --------------------------------------------------------------------------- */
$("#navigation > ul").muffingroup_menu({
	delay: 0,
	hoverClass: 'hover',
	arrows: false,
	animation: 'fade'
});

/* ---------------------------------------------------------------------------
 * Mega menu
 * --------------------------------------------------------------------------- */
$("#megamenu > ul").muffingroup_menu({
	delay:		0,
	hoverClass:	'hover',
	arrows:		false,
	animation:	'fade',
	addLast:	false
});

/* ---------------------------------------------------------------------------
 * Fancybox
 * --------------------------------------------------------------------------- */
$("a.fancybox, .gallery-icon a, .attachment a").fancybox({
	'overlayShow'	: false,
	'transitionIn'	: 'elastic',
	'transitionOut'	: 'elastic'
});

$("a.iframe").fancybox({
	'transitionIn'	: 'none',
	'transitionOut'	: 'none'
});

/* ---------------------------------------------------------------------------
 * WP Gallery
 * --------------------------------------------------------------------------- */
$(".gallery-icon a").attr("rel","gallery");

/* ---------------------------------------------------------------------------
 * Tabs
 * --------------------------------------------------------------------------- */
$(".jq-tabs").tabs();

/* ---------------------------------------------------------------------------
 * Accordion
 * --------------------------------------------------------------------------- */
$(".jq-accordion").accordion({
	autoHeight: false
});

/* ---------------------------------------------------------------------------
 * Back to top
 * --------------------------------------------------------------------------- */
$("a.back_to_top").click(function() {
	$("html, body").animate( {
		scrollTop : 0
	}, 1000);
	return false;
});

/* ---------------------------------------------------------------------------
 * Last child in submenu
 * --------------------------------------------------------------------------- */
$(".sidebar .submenu ul li:last-child").addClass("last-item");

/* ---------------------------------------------------------------------------
 * Last child in breadcrumbs
 * --------------------------------------------------------------------------- */
$(".subpage_header ul.breadcrumbs li:last-child").addClass("last");

/* ---------------------------------------------------------------------------
 * Last child in Portfolio
 * --------------------------------------------------------------------------- */
$(".portfolio_2_cols .item:nth-child(2n)").addClass("last");
$(".portfolio_2_cols .item:nth-child(2n+3)").css("clear", "both");
$(".portfolio_3_cols .item:nth-child(3n)").addClass("last");
$(".portfolio_3_cols .item:nth-child(3n+4)").css("clear", "both");

$(".portfolio .item .photo a").hover( function () {
	$(this).find("span").fadeIn(500);
	$(this).find(".overlay").fadeIn(500);
}, function () {
	$(this).find("span").fadeOut(500);
	$(this).find(".overlay").fadeOut(500);
});

/* ---------------------------------------------------------------------------
 * Our Team
 * --------------------------------------------------------------------------- */
$(".our_team .item").hover( function () {
	$(this).find(".desc").fadeIn(500);
	$(this).find("span").animate({ width: '160px' }, 700 );
}, function () {
	$(this).find(".desc").fadeOut(500);
	$(this).find("span").animate({ width: '0px' }, 700 );
});

/* ---------------------------------------------------------------------------
 * Faq
 * --------------------------------------------------------------------------- */
$(".faq .question:first").addClass("first");
$(".faq .question:not(:first)").children(".answer").hide();
$(".faq .question:first").addClass("active");

$(".faq .question > h5").click(function() {
	if($(this).parent().hasClass("active")) {
		$(this).parent().removeClass("active").children(".answer").slideToggle(200);
	}
	else
	{
		$(".faq .question").each(function(index) {
			if($(this).hasClass("active")) {
				$(this).removeClass("active").children(".answer").slideToggle(200);
			}
		});
		$(this).parent().addClass("active");
		$(this).next(".answer").slideToggle(200);
	}
});

/* ---------------------------------------------------------------------------
 * Features
 * --------------------------------------------------------------------------- */
$(".features ul li.last").next().css("clear", "both");

});