// JavaScript Document
$(document).ready(function() {
 
	//ACCORDION BUTTON ACTION	
	$('nav.lt-float-accordionButton').click(function() {
		$('nav.lt-float-accordionContent').slideUp('normal');	
		$(this).next().slideDown('normal');
	});
 
	//HIDE THE DIVS ON PAGE LOAD	
	$("nav.lt-float-accordionContent").hide();
 
});