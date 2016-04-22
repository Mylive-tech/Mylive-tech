<?php
/**
 * Template Name: Contact Page Template
 *

 */

get_header(); ?>

<!-- Content -->
<div id="Content">
	<div class="Wrapper">
	
		<?php $latlng = doover_get_option( 'google_maps_lat_lng' );?>
		<div class="content contact<?php if( ! $latlng ) echo " contact_no_map";?>">
					
			<div class="about">
				<?php $contact = doover_get_option( 'contact_email' ); ?>
				
				<?php if ( have_posts() ) : the_post(); ?>	
					<?php the_content(); ?>
					<?php if( $contact ) echo "<hr />";?>
				<?php endif; ?>

				<?php if( $contact ):?>
					<h4><?php _e('Send us a question','doover'); ?>:</h4>
					<div class="contact_form">
						<form id="json_contact_form" method="POST" action="<?php echo( LIBS_URI .'/theme-mail.php'); ?>">
							<input type="hidden" name="To" value="<?php echo $contact;?>" />
							<input type="hidden" name="Subject" value="<?php echo '['. __('contact form','doover'). '] '. get_bloginfo( 'name' );?>" />
	
							<fieldset>
								<span class="nick_ico"></span>
								<span class="email_ico"></span>
								<input id="Name" class="nick required" name="Name" placeholder="<?php _e('Your name','doover'); ?>" type="text" />
								<input id="Email" class="email required" name="Email" placeholder="<?php _e('Your e-mail','doover'); ?>" type="text" />
								<textarea id="Message" name="Message" class="required"></textarea>
								<input type="submit" value="<?php _e('Send message','doover'); ?>" />
							</fieldset>
						</form>
					</div>
				<?php endif; ?>
				
			</div>
			
			<?php if( $latlng ):?>
				<div class="map">
					<div id="google_map_area" style="width:290px; height:550px;">&nbsp;</div>
				</div>
				
				<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
				<script>
				//<![CDATA[
					function google_maps(){
						var latlng = new google.maps.LatLng(<?php echo( $latlng );?>);
						var myOptions = {
							zoom: 13,
							center: latlng,
							zoomControl: true, 
							mapTypeControl: false,
							streetViewControl: false,
							scrollwheel: false,
						            
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
	
						var map = new google.maps.Map(document.getElementById('google_map_area'), myOptions);
						var marker = new google.maps.Marker({
							position: latlng,
							map: map,
						});
					}

					jQuery(document).ready(function($){
						google_maps();
					});	
					//]]>
				</script>
			<?php endif; ?>		
			
			<?php if( $contact ):?>	
				<script>
				//<![CDATA[
					function validate(){
						var error = false;
						jQuery('input.required, textarea.required').removeClass('inp_error');
						
						jQuery('input.required, textarea.required').each(function (index, domEle) {
					        if ( (! this.value) || ( this.value==this.defaultValue ) ) {
					        	jQuery(this).addClass('inp_error');
								error = true;
							}
						});
	
						var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
						if( ! emailReg.test(jQuery('#Email').val()) )
						{
							jQuery('#Email').addClass('inp_error');
							error = true;
						}
							
						if( error ){
							return false;
						}
						return true;
					}
					
					function processJson(data){
						if( data.status && data.status == 'ok' ){
							jQuery('.contact_form').html('<div class="success"><?php _e('Thanks, your email was sent.','doover')?></div>');
						} else {
							jQuery('.contact_form').append('<div class="error"><?php _e('Error sending email. Please try again later.','doover')?></div>');
						}
					}
				
					jQuery(document).ready(function($){
						$('#json_contact_form').ajaxForm({ 
					        dataType:		'json', 
							beforeSubmit:	validate,
					        success:		processJson 
					    }); 
					});	  
				//]]>
				</script>
			<?php endif; ?>	
			
		</div>

	</div>
</div>

<?php get_footer(); ?>