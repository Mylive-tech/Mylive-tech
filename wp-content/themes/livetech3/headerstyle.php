<?php
 
  if ( is_product_category( 'lynk-voip-phone-service' ) ) {
    add_filter('woocommerce_get_catalog_ordering_args', 'am_woocommerce_catalog_orderby');
function am_woocommerce_catalog_orderby( $args ) {
    $args['meta_key'] = '_price';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'asc'; 
    return $args;
}
  }
 
?>

<style>
#g1-preheader-bar{display:none}
</style>
<script>
		jQuery(document).ready(function(){
		jQuery("#g1-preheader-bar").delay(1).show();
});
	
</script>

<?php
if(is_shop()) {
?>
<style type="text/css">
.g1-secondary-narrow #primary{ width:100% !important;}
/*.g1-nav-breadcrumbs{ display:none !important;}*/
.g1-pagination{ display: none !important;}
.totalproduct{display:none; width:100%; background:#F4F4F4 !important; float:left; height:auto !important;}
.totalproduct ul li{ list-style:none !important; float:left !important;}

ul.product_list li {float: left;width: 24%;min-height: 300px;padding-bottom:0 !important;height:auto;}
.product_list li img {height: 125px;width: 125px;}
.product_list ul {margin: 0;padding: 0;}
.product_list {width: 100%;float: left;}

ul.product_list_cat li {
    float: left;
    width: 24% !important;
    min-height: 220px;
    padding-bottom: 0px;
	height:auto;
}
.product_list_cat ul {
    margin: 0;
    padding: 0;
}
.product_list_cat {
    width: 100%;
    float: left;
	margin-bottom:0;
	background:#93FFFF;
}

.prodarea{ position:relative !important; margin:0 auto;}

/*********************21-04-2014****************************/
#totalproduct ul.product_list_cat{margin:0;}
.totalproduct h2.subcad-head{padding-top: 10px;padding-bottom:15px;text-align: center; background:#FFF9AE; margin: 0;}

/*************************************************/

.totalproduct ul li h4{ font-size:14px !important;}
.button{ border-radius:15px !important;}
.pricespan{ width:100%; float:left;}

.specialoffer{ float:left !important; width:100% !important; margin: 0 0 8px 0!important;}
.g1-secondary-narrow #primary{ background:none !important;}
/*.shop_box{width: 100%; border: thin solid #EEEEEE; box-shadow: 0 8px 6px -6px #999999; padding: 10px; margin: 6px!important; background-color:#F4F4F4;}*/
.shop_box:last-child{margin-right:0!important;}
.totalproduct h2{ padding-left:25px !important;}

.specialoffer p { margin:0px;}
.specialoffer #g1-button-2.g1-button{background-color:#cc0000 !important; border-color:#cc0000 !important;}
.specialoffer #g1-button-3.g1-button{background-color:#012C59 !important; border-color:#012C59 !important;}

.shop_box {background-color: #F4F4F4; border: thin solid #EEEEEE; box-shadow: 0 8px 6px -6px #999999;
    margin: 6px 6px 6px 0 !important;  padding: 0 0 15px 0;   width: 100%;}
.shop_box h4{ padding:10px 10px 5px 10px; margin:0;}


#g1-section-1.g1-section {
    float: left;
    margin: 15px 0;

}
#g1-section-2.g1-section {
    float: left;
}





#g1-id .site-title { margin: 0; font-size: 24px; font-weight: normal; line-height: 24px; position:absolute; }


@media only screen and (max-width: 320px) {
#g1-id .site-title {
    position: relative!important;
}

#content ul.g1-grid .first_shop {
    list-style: none outside none;
    margin: 15px 0 0!important;
}

#content ul.g1-grid .second_shop {
    list-style: none outside none;
    margin: 15px 0 0!important;
}

#g1-section-1{ display:none;}
#g1-section-2{ display:none;}
#g1-section-3{ display:none;}

#g1-divider-1{ display:none;}
#g1-divider-2{ display:none;}
#g1-divider-3{ display:none;}

.specialofferbuttons{ display:none;}

#totalproduct{float:left !important; width:95% !important;}

.g1-collection__item.cadpro{width:100%;}

ul.product_list li {
    float: left !important;
    padding-bottom: 0;
    width: 100%!important;
	text-align:left !important;
}


ul.product_list_cat li {
    float: left;
    padding-bottom: 20px;
    width: 100% !important;
	text-align:center;
}

.totalproduct h2 {
    float: left;
    padding-left: 25px !important;
    width: 90%;
}

.catprodselection{ display:block !important;}


}
@media only screen and (max-width: 768px) {
.menu > li > a {padding: 5px 20px !important;}
.dropdown { border-bottom:none !important;}
.catprodselection{ display:block !important;}
.maincatnav {display:none}
}

@media only screen and (max-width:767px) {.shop_box{width:95%} .shop_box img {margin:0;max-width:60px} .shop_box a {display:block;max-width:50%;margin:auto} .shop_box p {padding:7px;font-size:13px} .shop_box h4 {font-size:15px;}}

@media only screen and (min-width:768px) and (max-width:1023px) {.shop_box{width:95%} .shop_box img {margin:0;max-width:60px} .shop_box a {display:block;max-width:50%;margin:auto} .shop_box p {padding:7px;font-size:12px} .shop_box h4 {font-size:18px;}}
@media only screen and (width:1024px) {.shop_box{width:100%} .shop_box img {margin:0;max-width:60px} .shop_box a {display:block;max-width:50%;margin:auto} .shop_box p {padding:7px;font-size:12px} .shop_box h4 {font-size:18px;}}

#thirdshopbox{ display:none;}

.wait {
    background: none repeat scroll 0 0 #FEFEFE;
    /*border: 1px solid #FF0000 !important;*/
    bottom: 0 !important;
    /*min-height: 300px !important;*/
    left: 0%;
    opacity: 0.5;
    padding: 2px;
    position: absolute;
    top: 100px;
    width: 100% !important;
    z-index: 99999999 !important;
}

.wait img {
   /* border: 1px solid #FF0000;*/
    margin: 5% 45%;
    float: left;
    padding: 0;
	height:150px;
	width:150px;
}

    
</style>


<script type="text/javascript">

function get_product(st) {

 //event.preventDefault(); 
 var pcatid = jQuery(st).attr('id'); 
 var url = '<?php echo get_template_directory_uri(); ?>/categoryproduct.php';
 
 jQuery('.totalproduct').show();
 jQuery('.totalproduct').html("<center style='height:200px; padding-top:50px; color:red; font-weight:bold;'><img src='<?php echo get_template_directory_uri(); ?>/images/lt-logo-anim.gif' width='117' height='89'></center>");
 jQuery('html,body').animate({scrollTop: jQuery('.totalproduct').offset().top-65}, 1000);
 var posting = jQuery.post( url, { pcat: pcatid } );
 
  posting.done(
  function( data ) {
  if(data) {
  jQuery( ".totalproduct" ).html(data);
  }else{
  alert('No Product Found');
  }
  
  });
 
}
function showorder(cats, divnum)
{
//event.preventDefault(); 
 var pcatid = cats; 
 var order_list = document.getElementById('orderby-'+divnum).value;
 var url = '<?php echo get_template_directory_uri(); ?>/categoryproduct.php';
 //var posting = jQuery.post( url, { order_bylist: order_list } );
 var divid = divnum;
 //alert(divid);
 var fulldivlen = jQuery('#product_list-'+divid).height();
 
// alert(fulldivlen);
 
 jQuery('.totalproduct').show();
 jQuery(document).ajaxStart(function(){
    jQuery(".wait").css({"display":"block","height":+fulldivlen});
  });
  jQuery(document).ajaxComplete(function(){
   jQuery(".wait").css("display","none");
  });
 //jQuery('.totalproduct').html('<center style="height:200px; padding-top:50px; color:red; font-weight:bold;">loading...</center>');
 //jQuery('html,body').animate({scrollTop: jQuery('.totalproduct').offset().top-65}, 1000);
 var posting = jQuery.post( url, { pcat: pcatid,order_bylist: order_list} );
 
  posting.done(
  function( data ) {
  if(data) {
  jQuery(".totalproduct").html(data);
  }else{
  alert('No Product Found');
  }
  
  });
}

jQuery(document).ready(function() {



 jQuery('.p-cat').on("click", function(event) { 
 
 event.preventDefault(); 
 var pcatid = jQuery(this).attr('id'); 
 var url = '<?php echo get_template_directory_uri(); ?>/categoryproduct.php';
 
 jQuery('.totalproduct').show();
 jQuery('.totalproduct').html("<center style='height:200px; padding-top:50px; color:red; font-weight:bold;'><img src='<?php echo get_template_directory_uri(); ?>/images/lt-logo-anim.gif' width='117' height='89'></center>");
 jQuery('html,body').animate({scrollTop: jQuery('.totalproduct').offset().top-99}, 1000);
 var posting = jQuery.post( url, { pcat: pcatid } );
 
  posting.done(
  function( data ) {
  if(data) {
  jQuery( ".totalproduct" ).html(data);
  }else{
  alert('No Product Found');
  jQuery( ".totalproduct" ).hide();
  }
  
  });
 
 });
 
 jQuery('li.specialoffer a').on("click", function(event) { 
  event.preventDefault();
  var ids =  jQuery(this).attr('id');
  
  if(ids=='g1-button-2') {
  jQuery('html,body').animate({scrollTop: jQuery('#g1-section-1').offset().top-95}, 1000);
  }
  else if(ids=='g1-button-3') {
   jQuery('html,body').animate({scrollTop: jQuery('#g1-section-2').offset().top-95}, 1000);
  }
  else {
   jQuery('html,body').animate({scrollTop: jQuery('#g1-section-3').offset().top-95}, 1000);
  }
 
 }); 

 
}); 
</script>

<?php } if(is_front_page()) { ?>

<style>
	.panel {
		/*float: left;*/
		width: 200px;
		height: 200px;
		margin: 20px;
		position: relative;
		font-size: .8em;
		margin:10px auto;
		-webkit-perspective: 600px;
		-moz-perspective: 600px;
						perspective: 600px;				
	}

#g1-prefooter-widget-area .g1-one-fourth, #g1-preheader .g1-one-fourth {animation-name: none}

.home .g1-one-fourth {animation-name: slideLeft;
	-webkit-animation-name: slideLeft;	

	animation-duration: 1s;	
	-webkit-animation-duration: 1s;

	animation-timing-function: ease-in-out;	
	-webkit-animation-timing-function: ease-in-out;	

	visibility: visible !important;	}
	
	.home .g1-one-fourth:nth-of-type(1) {
    -webkit-animation-delay:0s;
    -moz-animation-delay: 0s;
}
	
.home .g1-one-fourth:nth-of-type(2) {
    -webkit-animation-delay: 0.3s;
    -moz-animation-delay: 0.3s;
}
.home .g1-one-fourth:nth-of-type(3) {
    -webkit-animation-delay: 0.5s;
    -moz-animation-delay: 0.5s;
}
.home .g1-one-fourth:nth-of-type(4) {
    -webkit-animation-delay: 0.7s;
    -moz-animation-delay: 0.7s;
}
	
	
@keyframes slideLeft {
	0% {
		transform: translateX(150%);
	}
	50%{
		transform: translateX(-8%);
	}
	65%{
		transform: translateX(4%);
	}
	80%{
		transform: translateX(-4%);
	}
	95%{
		transform: translateX(2%);
	}			
	100% {
		transform: translateX(0%);
	}
}

@-webkit-keyframes slideLeft {
	0% {
		-webkit-transform: translateX(150%);
	}
	50%{
		-webkit-transform: translateX(-8%);
	}
	65%{
		-webkit-transform: translateX(4%);
	}
	80%{
		-webkit-transform: translateX(-4%);
	}
	95%{
		-webkit-transform: translateX(2%);
	}			
	100% {
		-webkit-transform: translateX(0%);
	}
}


	
	/* -- make sure to declare a default for every property that you want animated -- */
	/* -- general styles, including Y axis rotation -- */
	.panel .front {
		float: none;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 900;
		width: inherit;
		height: inherit;
		border: 1px solid #ccc;
		background: #000;
		color:#FFFFFF;
		text-align: center;
		box-shadow: 0 1px 5px rgba(0,0,0,0.9);

		-webkit-transform: rotateX(0deg) rotateY(0deg);
			 -moz-transform: rotateX(0deg) rotateY(0deg);
						transform: rotateX(0deg) rotateY(0deg);

		-webkit-transform-style: preserve-3d;
			 -moz-transform-style: preserve-3d;
						transform-style: preserve-3d;

		-webkit-backface-visibility: hidden;
			 -moz-backface-visibility: hidden;
						backface-visibility: hidden;

		/* -- transition is the magic sauce for animation -- */
		-webkit-transition: all .4s ease-in-out;
						transition: all .4s ease-in-out;
	}
	.panel.flip .front {
		z-index: 900;
		border-color: #eee;
		/*background: #333;*/
		box-shadow: 0 15px 50px rgba(0,0,0,0.2);

		-webkit-transform: rotateY(180deg);
			 -moz-transform: rotateY(180deg);
						transform: rotateY(180deg);
	}

	.panel .back {
		float: none;
		position: absolute;
		top: 0;
		left: 0;
		z-index: 800;
		width: inherit;
		height: inherit;
		border: 1px solid #666!important;
		background: #333;
		text-shadow: 1px 1px 1px rgba(0,0,0,0.6);

		-webkit-transform: rotateY(-180deg);
			 -moz-transform: rotateY(-179deg); /* setting to 180 causes an unnatural-looking half-flip */
						transform: rotateY(-179deg);

		-webkit-transform-style: preserve-3d;
			 -moz-transform-style: preserve-3d;
						transform-style: preserve-3d;

		-webkit-backface-visibility: hidden;
			 -moz-backface-visibility: hidden;
						backface-visibility: hidden;

		/* -- transition is the magic sauce for animation -- */
		-webkit-transition: all .4s ease-in-out;
						transition: all .4s ease-in-out;
	}

	.panel.flip .back {
		z-index: 1000;
		background: #000;
		color:#FFFFFF;

		-webkit-transform: rotateX(0deg) rotateY(0deg);
			 -moz-transform: rotateX(0deg) rotateY(0deg);
						transform: rotateX(0deg) rotateY(0deg);

		box-shadow: 0 15px 50px rgba(0,0,0,0.2);
	}

	/* -- X axis rotation for click panel -- */
	.click .front {
		cursor: pointer;
		-webkit-transform: rotateX(0deg);
			 -moz-transform: rotateX(0deg);
						transform: rotateX(0deg);
	}
	.click.flip .front {
		-webkit-transform: rotateX(180deg);
			 -moz-transform: rotateX(180deg);
						transform: rotateX(180deg);
	}
	.click .back {
		cursor: pointer;
		-webkit-transform: rotateX(-180deg);
			 -moz-transform: rotateX(-180deg);
						transform: rotateX(-180deg);
	}
	.click.flip .back {
		-webkit-transform: rotateX(0deg);
			 -moz-transform: rotateX(0deg);
						transform: rotateX(0deg);
	}

  /* -- contact panel -- */
  .contact {
    width: 290px;
    height: 240px;
  }

	/* -- diagonal axis rotation -- */
	.diagonal .front {
		-webkit-transform: rotate3d(45,45,0,0deg);
			 -moz-transform: rotate3d(45,45,0,0deg);
						transform: rotate3d(45,45,0,0deg);
	}
	.diagonal .front:hover {
		/* for the patient :) */
		-webkit-transition-duration: 10s;
			 -moz-transition-duration: 10s;
						transition-duration: 10s;

		-webkit-transform: rotate3d(45,45,0,-36deg);
			 -moz-transform: rotate3d(45,45,0,-36deg);
						transform: rotate3d(45,45,0,-36deg);
	}

	.diagonal.flip .front,
	.diagonal.flip .front:hover {
		-webkit-transform: rotate3d(-45,-45,0,150deg);
			 -moz-transform: rotate3d(-45,-45,0,150deg);
						transform: rotate3d(-45,-45,0,150deg);

		-webkit-transition: all .4s ease-in-out;
						transition: all .4s ease-in-out;
	}

	.diagonal .front .message {
	  opacity: 0;
		font-size: 1.4em;
	}
	.diagonal .front:hover .message {
		opacity: .4;
		-webkit-transition-duration: 12s;
			 -moz-transition-duration: 12s;
						transition-duration: 12s;

		-webkit-transition-delay: 4s;
			 -moz-transition-delay: 4s;
						transition-delay: 4s;

		-webkit-transform: translateX(-30px) translateZ(40px) scale(1.4);
			 -moz-transform: translateX(-30px) translateZ(40px) scale(1.4);
						transform: translateX(-30px) translateZ(40px) scale(1.4);
	}
	.diagonal.flip .front .message {
		-webkit-transition-duration: 1s;
						transition-duration: 1s;
	  -webkit-transform: translateZ(0px) scale(.5);
					  transform: translateZ(0px) scale(.5);
	}

	.diagonal .back {
		-webkit-transform: rotate3d(45,45,0,-180deg);
			 -moz-transform: rotate3d(45,45,0,-180deg);
						transform: rotate3d(45,45,0,-180deg);
	}
	.diagonal.flip .back {
		-webkit-transform: rotate3d(45,45,0,-30deg);
			 -moz-transform: rotate3d(45,45,0,-30deg);
						transform: rotate3d(45,45,0,-30deg);
	}

  /* -- swing like a gate -- */
  .swing .front,
  .swing .back {
    width: 140px;
    -webkit-backface-visibility: visible;
			 -moz-backface-visibility: visible;
						backface-visibility: visible;

    -webkit-transition-duration: 1s;
			 -moz-transition-duration: 1s;
						transition-duration: 1s;

    -webkit-transform-origin: 170px 0;
			 -moz-transform-origin: 170px 0;
						transform-origin: 170px 0;
  }
  .swing .front {
    -webkit-transform: rotateY(0deg);
			 -moz-transform: rotateY(0deg);
						transform: rotateY(0deg);
  }
  .swing .back {
    background-color: #555; /* hiding this side, so get darker */
    -webkit-transform: rotateY(-180deg) translateX(198px) translateZ(2px);
			 -moz-transform: rotateY(-180deg) translateX(198px) translateZ(2px);
  }

  .swing.flip .front {
    background-color: #222; /* hiding this side, so get darker */
    -webkit-transform: rotateY(180deg);
			 -moz-transform: rotateY(180deg);
  }
  .swing.flip .back {
    background-color: #80888f;
    -webkit-transform: rotateY(0deg) translateX(198px) translateZ(2px);
			 -moz-transform: rotateY(0deg) translateX(198px) translateZ(2px);
  }


	/* -- cosmetics -- */
	.panel .pad {padding: 0 15px; }
	.panel.flip .action {display: none; }
	.block ol li {text-align: left; margin: 0 0 0 28px; }
	.block .action {display: block; padding: 3px; background: #333; text-align: right; font-size: .8em; opacity: 0; position: absolute; cursor: pointer; -webkit-transition: opacity .2s linear; }
	.block:hover .action {opacity: .7; }
	.circle div {border-radius: 100px; }
	.circle div h2 {padding-top: 3em; text-align: center; }
	
	.abc li{ float:left; list-style:none;}



.circle h3 {
    bottom: -40px !important;
    color: #FFFFFF !important;
    font-size: 13px !important;
    height: 80px !important;
    left: 5px !important;
    line-height: 20px !important;
    opacity:0.85;
    padding-left: 20px;
    padding-right: 10px;
    padding-top: 10px !important;
    position: absolute !important;
    text-shadow: none !important;
    width: 80% !important;
	font-weight:bold!important;
}

.circle h3 {
    background: none repeat scroll 0 0 #043A5F !important;
}

.click .front {
    overflow: hidden!important;
}

/*******************************/
.back {
    background:#f6e50d !important;
    height: 100% !important;
    left: 0 !important;
    opacity: 0.9;
    position: absolute !important;
    width: 100% !important;
}

.back p {color: #000;font-size:13px;margin: 15px 15px 0 15px !important; padding:12px 14px 0 14px !important;ine-height:16px;text-shadow:none;}

.back p:nth-child(3) { margin: 8px 0 8px !important; padding: 0 !important;}
.back p:nth-child(2) {    margin: 0 0 0 !important; padding: 0 10px !important;}


.back .ring-icon{ width:100%;text-align:center;top:2px;}
.front span {display: block; font-size: 11px;font-weight: bold;}


.lt-apply {
    border-right: thin solid #999999;
    float: left;
    padding: 10px;
    width: 45%;
}
/** {
    transition: all 0.8s ease 0s;
}*/
body {
    font-family: 'Open Sans',Arial,Sans-serif;
}



.amr_widget{width:auto !important;}

/*****************************************************************/

.dev_timer {
    margin: -45px 0 0;
    padding: 0 0 0;
    width: 100%;
	float:left;
	border-bottom:thin solid #DDDDDD;
}

.dev_timer_f {
    float: left;
    width: 24%;
	font-size:25px;
	color:#000;
	text-align:right;
	font-weight:bold;
	/*display:none;*/
}
.dev_timer_s {
    float: left;
    margin: 0 0 0 2%;
    padding: 0;
    width:18%;
}
.dev_timer_t {
display:inline-block;
    margin: 0;
    padding: 0;
	font-size:25px;
	color:#000;
    width: 45%;
	font-weight:bold;
	/*display:none;*/
	float:left;
}

.homeheading{ margin:-20px 0 0 0;}



div h2.headingtext{margin:0; text-align:center !important; font-size:24px; padding:10px 0px;}

.specialcontent{ display:none; /*background:#F8F8F8; padding:10px;*/ margin-bottom:15px; width:100%; float:left;overflow:hidden}

.specialcontent .maincert.div-color1{background:#e5f1d4; padding:3% 2% 3% 2%!important; width:96%;}
.specialcontent .maincert.div-color2{background:#e1e2f2; padding:3% 2% 3% 2%!important; width:96%; overflow:hidden}
.specialcontent .maincert.div-color3{background:#efe1dc; padding:3% 2% 3% 2%!important; width:96%;}

/********************css for home page certification, testimonia and why choose live tech************************/

.specialcontent .maincert {float: left; width: 100%;padding: 10px 0;}
.specialcontent .maincert h3 {margin:0; padding:0;}
.maincert .cert_img {float: left; width: 35%; text-align:right;}
.maincert .certcontnet {float: left; width: 63%;}
.maincert .certcontnet p{ padding: 30px 0 0;text-align: center; font-weight:normal;}

.maincert.testimonial{ float:left !important; width:100% !important; height:250px; overflow:hidden; border:1px solid #e1e1e1 !important;}
.maincert.testimonial.fulltestcontn{ float:left; width:100%; font-size:14px;}
.maincert.testimonial.fulltestcontn p { float:left; width:100% !important;} 
.foo_content > span {
    display: block;
    font-weight: normal;
    margin-bottom: 5px;
    text-align: center !important;
}

#testi p {

    background: url("http://199.101.49.90/~mylive5/mylive-tech.com/wp-content/plugins/gc-testimonials/assets/images/quotes.png") no-repeat scroll 0 14px rgba(0, 0, 0, 0);
    margin: 0;
    padding: 15px 36px;
	position:relative;
}
.testi-righticon {position:absolute; width:4%; height:65px; right:0; bottom:0px;    background: url("http://199.101.49.90/~mylive5/mylive-tech.com/wp-content/plugins/gc-testimonials/assets/images/quotes-right.png") no-repeat scroll 0 8px rgba(0, 0, 0, 0);
}


#testi .foo_content {
   /* border: 1px solid #e1e1e1;*/
    margin: 0;
    padding: 0;
	float:left !important;
	width:100% !important;
	height:auto;
	position:relative !important;
	
}

.cert_Testimonials a{  width:100%; height:auto; display:inline-block!important; /*padding:1% 3%!important;*/ margin:0!important;}

#testi{
overflow:hidden;
}
#news{}
.mews-list_f{overflow:hidden;}


.maincert.whychoose{ float:left; width:100%; border: 1px solid #e1e1e1;}
.lt-whychoose1 {text-align:center}

.whychoose ul li h4 { font-size:18px !important; font-weight:normal !important;}

.testidiv-img {
    float: left;
    margin: 0;
    padding: 10px;
	text-align:right;
    width: 17%;
}

.cert_Testimonials {
    float: left;
    margin: 0 0 10px 0;
    padding: 0 10px;
    width: 78%;
}
#icon-3.g-icons {
    background-color: #0099CC;
    border-color: #0099CC;
    color: #FFFFFF;
}
#icon-2.g-icons {
    background-color: #0099CC;
    border-color: #0099CC;
    color: #FFFFFF;
}
#icon-1.g-icons {
    background-color: #0099CC;
    border-color: #0099CC;
    color: #FFFFFF;
}
#g1-button-6.certification-button {background-color: #3367D0; border-color: #3367D0; width:25% !important; margin:40px auto 0px; padding:5px 0 5px 0;}


.whychoose { width:100% !important; float:left !important; height:auto;}
.choos_div{width:100% !important; float:left !important; height:auto; position:relative !important;}
.choos_div h3.why_chose{text-align: right;width: 100%; padding: 0 0 15px 0 !important;}
.choos_div ul#news{ }
.read_but{width:100%; margin:-2% 0 0 10% !important; float:left !important;}

@media only screen and )max-width:600px) {

}





@media only screen and (max-width: 320px) {
.choos_div h3.why_chose{text-align: center;width: 100%; padding: 0 0 15px 0 !important;}
.read_but{float: right;width:90%!important;}
/*ul.g1-grid div.panel {margin-left: 15% !important;}*/
.dev_timer_s {margin: 0 0 0 25%!important;}
.dev_timer_f {text-align: center!important; width: 100%;}
.dev_timer_t {text-align:center;width: 100%;}
.g1-grid {width: 90%!important; margin-left: 10%!important;}

.g1-grid > .topheadingbutton{ width:49% !important; float:left !important; margin-left:1% !important;}
.g1-grid > .topheadingbutton a{ padding:5px 6px !important;}
/***************************************************/
.maincert .cert_img {float: left;text-align: center;width: 94%!important;}
.maincert .certcontnet {float: left;width: 94%;}
#g1-button-6 .g1-button {background-color: #3367D0;border-color: #3367D0; margin: 0 auto; text-align: center; width: 75% !important;}




.testidiv-img{ padding: 10px 0;text-align: center; width:100%!important;}
.cert_Testimonials {margin: 0; padding: 0; width: 100%; float:left; height:auto;}
#g1-button-6.certification-button {margin: 0px auto 0 !important;}

ul#news {
    margin:0!important;
}
}



@media only screen and (max-width: 480px) {


.dev_timer_f {text-align: left; width: 100%;}
.dev_timer_t { width: 100%;}

/***************************************************/
.choos_div h3.why_chose {padding: 0 0 15px !important; text-align: center; width: 100%;}

.maincert .cert_img {float: left; text-align: center; width: 94%!important;}
.maincert .certcontnet {float: left; width: 94%;}

#g1-button-6 .g1-button {background-color: #3367D0; border-color: #3367D0; margin: 0 auto ;text-align: center;width: 75% !important;}


.testidiv-img{ padding: 10px 0;text-align: center; width:100%!important;}
.cert_Testimonials {margin: 0; padding: 0; width: 100%; float:left; height:auto;}
#g1-button-6.certification-button {background-color: #3367D0; border-color: #3367D0; margin: 0px auto 0 !important;padding: 5px 0; width: 75% !important;}
#news{/*overflow:visible!important*/}
#testi{overflow:visible!important}.


ul#news {
    margin:0!important;
}
}
@media only screen and (max-width: 640px) {
.dev_timer_f {text-align: left;width: 100%;}
.dev_timer_t {width: 100%;}
.dev_timer_s {margin: 0 0 0 20% !important;}
ul.g1-grid div.panel {/*margin-left: 20%;*/}

/***************************************************/
.maincert .cert_img {float: left; text-align: center;width: 94%!important;}
.maincert .certcontnet { float: left; width: 94%;}
#g1-button-6 .g1-button {background-color: #3367D0; border-color: #3367D0; margin: 0 auto; text-align: center; width: 75% !important;}


.testidiv-img{ padding: 10px 0;text-align: center; width:100%!important;}
.cert_Testimonials {margin: 0; padding: 0; width: 100%;}
#g1-button-6.certification-button {background-color: #3367D0; border-color: #3367D0; margin: 40px auto 0;padding: 5px 0; width: 75% !important;}

}

@media only screen and (max-width: 768px) {
.dev_timer_f {text-align: center;width: 100%;}
.dev_timer_t {width: 100%; text-align:center;}
.dev_timer_s {margin: 0 0 0 38%;}

#content ul.g1-grid li.g1-column {float: left; margin: 0 0 0 8%; width: 40%;}
/***************************************************/
.maincert .cert_img {float: left;text-align: center;width: 94%!important;}
.maincert .certcontnet {float: left;width: 94%;}
#g1-button-6 .g1-button {background-color: #3367D0; border-color: #3367D0; margin: 0 auto; text-align: center; width: 75% !important;}

.testidiv-img{ padding: 10px 0;text-align: center; width:100%!important;}
.cert_Testimonials {margin: 0; padding: 0; width: 100%; float:left; height:auto;}
#g1-button-6.certification-button {background-color: #3367D0; border-color: #3367D0; margin: 40px auto 0;padding: 5px 0; width: 75% !important;}

#testi{overflow:visible!important;}
#news{/*overflow:visible!important;*/}
.testi-righticon {width: 5%!important;}
.read_but {
    margin: 0 auto!important;
    width: 100%!important;
}

}
</style>


<script>
	jQuery(document).ready(function(){

		// set up click/tap panels
		jQuery('.click').hover(function(){
			jQuery(this).addClass('flip');
		},function(){
			jQuery(this).removeClass('flip');
		});

		
		/////// show content on click on our certification, testimonial or live tech button
		
		jQuery('.specialhomebutton').click(function(event){
		
		event.preventDefault(); 
		var butid = jQuery(this).attr('href').replace('http://','');
		var purl = '<?php echo get_template_directory_uri(); ?>/getpage-content.php';
		
		//alert(butid);
		
		jQuery('.specialcontent').show();
       jQuery('html,body').animate({scrollTop: jQuery('.specialcontent').offset().top-200}, 1000);
        var posting = jQuery.post( purl, { pid: butid } );
 
        posting.done(
        function( data ) {
        if(data) {
        jQuery(".specialcontent" ).html(data);
		
		//jQuery('.specialcontent').delay(10000).hide();
        }else{
        alert('Nothing Found');
		jQuery('.specialcontent').hide();
        }
		});
		
		
		
		
		
		});
		
		//////// end here ///////
		
	});
	
</script>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/custom-js/jquery.bxslider.min.js"></script>


<?php } if(is_product_category()) { ?>
<style type="text/css">
.woocommerce-ordering{display:none!important;}
.prodpage{ display:block !important; width:30% !important;}
.woocommerce-result-count{ display:none !important;}
#infscr-loading{ width:100%; float:left !important; text-align:center !important;}
#infscr-loading div{ width:100% !important; text-align:center; float:left !important;}
#infscr-loading div em{ letter-spacing:3px !important;}
.g1-pagination{ display:none !important;}

</style>

<?php } if(is_checkout()) { ?>

<script type="text/javascript">

jQuery(document).ready(function() {


	jQuery("#billing_company_field").removeClass('form-row-wide');
	jQuery("#billing_company_field").addClass('form-row-first');
	jQuery("#billing_address_1_field").removeClass('form-row-wide');
	jQuery("#billing_address_1_field").addClass('form-row-first');
	jQuery("#billing_address_2_field").removeClass('form-row-wide');
	jQuery("#billing_address_2_field").addClass('form-row-last specialpadd');
	jQuery("#billing_city_field").removeClass('form-row-wide');
	jQuery("#billing_city_field").addClass('form-row-last');
	jQuery("#billing_state_field").removeClass('form-row-first');
	jQuery("#billing_state_field").addClass('form-row-last specialstate');
	jQuery("#billing_postcode_field").removeClass('form-row-last');
	jQuery("#billing_postcode_field").addClass('form-row-first');
	
	/***************************add or remove class for shipping**************************/
	
	jQuery("#shipping_company_field").removeClass('form-row-wide');
	jQuery("#shipping_company_field").addClass('form-row-first');
	jQuery("#shipping_address_1_field").removeClass('form-row-wide');
	jQuery("#shipping_address_1_field").addClass('form-row-first');
	jQuery("#shipping_address_2_field").removeClass('form-row-wide');
	jQuery("#shipping_address_2_field").addClass('form-row-last specialpadd');
	jQuery("#shipping_city_field").removeClass('form-row-wide');
	jQuery("#shipping_city_field").addClass('form-row-last');
	jQuery("#shipping_state_field").removeClass('form-row-first');
	jQuery("#shipping_state_field").addClass('form-row-last specialstate');
	jQuery("#shipping_postcode_field").removeClass('form-row-last');
	jQuery("#shipping_postcode_field").addClass('form-row-first');


});
</script>

<style type="text/css">
.specialpadd{ margin-top:0px !important;}
.specialstate {
    width: 40% !important;
}

.woocommerce form .form-row-last {
    float: right;
    width: 41.454% !important;
}

.form-row.notes {
    float: left;
    width: 100%;
}

</style>


<?php } if(is_woocommerce()) { ?>
<style type="text/css">
.archive-header.g1-layout-inner{ margin-top:0px !important;}
#g1-precontent .entry-header{ margin-top:5px !important;}
.input-text.qty.text{ border:none !important;}

@media only screen and (max-width: 320px) {
.g1-background .maincatnav{ margin-top:130px !important;}
#g1-precontent .archive-header.g1-layout-inner{ margin-top:20px !important;}
#g1-precontent .entry-header{ margin-top:50px !important;}
}
</style>
<?php } ?>


<style type="text/css">
@media only screen and (max-width: 320px) {
.g1-grid { margin-left: 5% !important;width: 90% !important;}

#g1-primary-nav {/*margin-right: 83px;*/}


.g1-grid {margin-left: 5% !important; width: 90% !important;}
.livetech-family {margin: 0 !important;}
.g1-mailchimp .g1-form-row input { max-width: 156px!important;}
#g1-footer-nav-menu > li {
    display:inline-block;
    margin-right: 1.25em;
}

#g1-id .site-title {
    font-size: 24px;
    font-weight: normal;
    line-height: 24px;
    margin: 0;
}



.product-addon h3{ width:100%!important; float:left;}
.product-addon p{ float:left; width:100% !important;}

.mainnav{ float:left !important; width:100% !important; background:#F6F6F6; padding:5px 0px; margin:0px 0 0px 0px!important; border:1px solid #BBBBBB !important;}
.mainnav .prev-product{ width:100% !important; text-align:left !important;}
.mainnav .next-product{ width:100% !important; text-align:left !important;}

h1.site-title {
    padding-left: 30px !important;
}

p.site-title {
    padding-left: 30px !important;
}

}

.tagcloud{ text-align:center !important;line-height:23px !important}
.product-addon h3{ float:left !important; width:40%; text-align:left !important; line-height:25px !important; font-weight:bold !important; color:#666 !important;}

.product-addon p{ width:45% !important; float:left !important; overflow:hidden !important;}

.product-addon p.form-row-wide{ clear:none !important;}

.woocommerce form.cart .variations td.label{ width:45.2% !important;}

.woocommerce .g1-complete span.onsale{ margin:-15px 0 0 -16px !important;}

#g1-back-to-top{text-indent:0 !important; white-space:normal!important; text-align:center!important; font-size:12px!important; height:auto!important;line-height:normal}
/*#g1-back-to-top:before{ content:none!important; }*/

div.customer_details {
    text-align: center;
    width: 100%;
}

/*********************************************************************/

.ilc_ps{ position:relative !important;}
a.ilc_ps_prev, a.ilc_ps_next {
   	position:absolute;
}
a.ilc_ps_prev {
    background-position: 0 0;
    left: 0;
}

a.ilc_ps_prev {			left: -3px;
					background-position: 0 0; }
a.ilc_ps_prev:hover {		background-position: 0 -50px; }
a.ilc_ps_next {			right: -3px;
					background-position: -50px 0; }
a.ilc_ps_next:hover {		background-position: -50px -50px; }

a.ilc_ps_prev span, a.ilc_ps_next span {
	display: none;
}
.clearfix {
	float: none;
	clear: both;
}

a.ilc_ps_prev span, a.ilc_ps_next span{ display:none;}

#g1-tabs-1 .g1-tabs-nav-current-item{ background:#0099CC; color:#fff;}

.pagination{ display:none !important;}

#infscr-loading{ text-align:center !important;}

.minus{  background: url("<?php echo get_template_directory_uri(); ?>/images/down.png") no-repeat !important; color:transparent !important;}
.plus{  background: url("<?php echo get_template_directory_uri(); ?>/images/up.png") no-repeat !important; color:transparent !important;}

.mainnav{ float:left !important; width:99.7% !important; margin:0px 0 13px 0px!important;}

.mainnav .prev-product{ float:left; width:48%; text-align:left;font-size:14px; color:#fff;}
.mainnav .next-product{ float:right; width:48%; text-align:right; font-size:14px; color:#fff;}
.mainnav .next-product a{ color:#ffffff!important; display:block; padding:10px; background:#899752}
.mainnav .prev-product a{ color:#ffffff!important;  display:block; padding:10px;background:#487890}
.mainnav .next-product a:hover, .mainnav .prev-product a:hover {background:#666}

#top.woocommerce-checkout .container .clear {
clear: both !important;
}
.maincatnav{ margin-top:0px !important;}

.ilc_ps_hidden{
	height:230px !important;
	overflow:hidden !important;
	visibility:<?php if(is_front_page()) { echo 'hidden'; }else{ echo 'visible'; } ?>
}

/***********************today style here**********************************************/
.stButton{margin:0!important; padding:0!important;}
a.ilc_ps_prev, a.ilc_ps_next{ top:60px !important;}

@media only screen and (width:768px) {
	li.toll-free {display:block !important;margin-left:0!important} .home .lt-axn-buttons-new {display:none} .tagcloud {line-height:21px !important} .toll-free label:before {right:4px} .toll-free label:after {right:14px} .toll-free select {max-width:85%} .g1-grid.numbrs li.g1-column {width:23%;float:left}
	.maincatnav {display:none} .menu {display: none;}
	}
	
	@media only screen and (width:1024px) {
		.toll-free label:before {right:0} .toll-free label:after {right:11px}
	}
	
	@media only screen and (max-width:767px) {
		.g1-grid.numbrs li.g1-column {width:100%;float:none}
		#g1-back-to-top {line-height:13px}
	}

</style>
