<?php
include('../../../wp-config.php');

$pid = (int)$_POST['pid'];

if($pid!='') {


 if($pid=='1954') {
 
 ?>

 <div class="maincert div-color1">
 <div style="float:left; width:98%;padding:10px;">
  
  <div class="cert_img">
  <img src="<?php echo home_url(); ?>/wp-content/uploads/cisco-ccna-certificate.jpg" width="80" height="125" />
  <img src="<?php echo home_url(); ?>/wp-content/uploads/cissp-certificate.jpg" width="80" height="125" />
  <img src="<?php echo home_url(); ?>/wp-content/uploads/compTIA+certificate.jpg" width="80" height="125" /><br />
  <img src="<?php echo home_url(); ?>/wp-content/uploads/MCDA-certificate.jpg" width="80" height="125" />
  <img src="<?php echo home_url(); ?>/wp-content/uploads/MCTS-certificate.jpg" width="80" height="125" />
  <img src="<?php echo home_url(); ?>/wp-content/uploads/SOC2-TypeII-certificate.jpg" width="80" height="125" />
  
  </div>
  
  <div class="certcontnet">
  <h3 style="text-align:center;">Our Certifications</h3>
  <p>Live-Tech always work on ensuring that the proper<br />resources are assigned to your request in order to bring a fast resolution</p>
  <style type="text/css" scoped="">
  #g1-button-6.certification-button {background-color: #3367D0; border-color: #3367D0; width:30% !important; margin:0 auto; padding:5px 20px;}
  @media only screen and (max-width: 320px) {
   #g1-button-6.certification-button { width:100% !important;}
  }
  </style>
  <a target="_blank" id="g1-button-6" class="g1-button g1-button--small g1-button--solid g1-button--wide  g1-new-window certification-button" href="<?php echo get_permalink($pid); ?>" title="See All Our Certifications">See All Our Certifications</a>
  
  </div>
  </div>
 </div>
 
 <?php
 exit();
 } 
 elseif($pid=='504') {
 ?>
 
    <script type="text/javascript">
	 jQuery(document).ready(
				function(){
					jQuery('#testi').bxSlider({
						 auto: true,
						 infiniteLoop: true,
						 adaptiveHeight: false,
						 mode: 'fade',
						 autoControls: false,
						 responsive: true,
						 pager: false,
						 controls: false,
						 speed: 500,
						 pause: 4000,
						 autoHover: true,
					});
					
					
			});
  	</script>
 
 <div class="maincert div-color2">

 <div class="testidiv-img">
  <img src="<?php echo home_url(); ?>/wp-content/uploads/thumbs-up-icon.png" alt="Testimonials"/>
</div>
 <div class="cert_Testimonials" style="text-align:center">
  <h3 style="text-align:center;">Testimonials</h3>
 <div class="testimonial" id="testi">
  <?php
  $args_test = array('post_type'=>'testimonial','posts_per_page'=>4);
  $testimonials = new WP_Query( $args_test );
  $i=1;
  while ( $testimonials->have_posts() ) : $testimonials->the_post();
  $compname = get_post_meta(get_the_ID(), 'company_name',true);
  ?>
  <div class="foo_content">
 
 <?php 
     echo content(40); ?><span>- <?php the_title(); if($compname!='') { echo ', '. $compname; } ?></span>
 </div>
  <?php $i++; endwhile; wp_reset_query(); ?>
  </div>
  <a target="_blank" id="g1-button-6" class="g1-button g1-button--small g1-button--solid g1-button--wide  g1-new-window certification-button" href="<?php echo get_permalink($pid); ?>" title="View All Our Client Testimonials">See All Testimonials</a>
 
 </div>

 
 
 
 

 </div>
 <?php 
 exit();
 }
 elseif($pid=='437') {
 ?>
 

    <script type="text/javascript">
	   jQuery(document).ready(
				function(){
					jQuery('#news').bxSlider({
						auto: true,
						 infiniteLoop: true,
						 adaptiveHeight: true,
						 mode: 'fade',
						 autoControls: false,
						 responsive: true,
						 pager: false,
						 controls: false,
					   });
			});
  	</script>

    

	
	<style type="text/css">
	ul#news li{ text-align:center !important;}
	</style>
<div class="maincert div-color3" style="padding:10px 0 25px 0">
   <div class="whychoose">
   
	 
   <div>
   
	<div class="choos_div">
	<!-- <h3 class="why_chose">Why Choose Live-Tech</h3>-->
     <div id="news">
	 
     <div style="overflow:hidden;" >
	 <div class="testidiv-img" style="padding:1px 10px;"><i id="icon-1" class="icon-thumbs-up g1-icon g-icons g1-icon--solid g1-icon--big g1-icon--circle "></i></div>
	 <div class="cert_Testimonials lt-whychoose1">
	 <h3><strong> 100% full satisfaction guarantee </strong></h3>
<p> At Live-Tech your satisfaction is guaranteed or your money back. </p>
</div>
</div>
     <div class="lt-whychoose1" style="overflow:hidden;" >
	 	 <div class="testidiv-img" style="padding:1px 10px;"><i id="icon-2" class="icon-stethoscope g1-icon g-icons g1-icon--solid g1-icon--big g1-icon--circle "></i></div>
	 <div class="cert_Testimonials" >
	 <h3> <strong>Keeping 30000+ computers healthy </strong> </h3>
<p>We're successfully servicing more than 30000+ computers globally to keep them healthy</p>
</div>
</div>
     <div class="lt-whychoose1"  style="overflow:hidden;"  >
	 	 <div class="testidiv-img" style="padding:1px 10px;"> <i id="icon-3" class="icon-time g1-icon g-icons g1-icon--solid g1-icon--big g1-icon--circle "></i></div>
	 <div class="cert_Testimonials">
	 <h3> <strong>No delays, We Connect, Detect & Resolve </strong></h3>
<p>Live-Tech is dedicated to quickly address any technical issue with full resolution.</p>
</div>
</div>
    
     </div>
	 <div class="read_but">
	  <style type="text/css" scoped="">#g1-button-6.certification-button {background-color: #3367D0; border-color: #3367D0; width:25% !important; margin:0 auto; padding:5px 0 5px 0;}</style>
  <a target="_blank" id="g1-button-6" class="g1-button g1-button--small g1-button--solid g1-button--wide  g1-new-window certification-button" href="<?php echo get_permalink($pid); ?>" title="Read More">Read More</a>
  </div>
	 </div>
	 
	 </div>
	 
	 
	
  
  </div>
  
 </div>
	 

 <?php 
 exit();
 }else{
 
 
 echo 'Nothing Found';
 exit();
 
 }






}



?>