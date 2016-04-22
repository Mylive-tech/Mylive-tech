<?php
/**
 *
 * The template for displaying the footer.
 *
 *
 *
 * @package flatsome
 *
 */

global $flatsome_opt;
?>


<!-- #main-content -->

<footer class="footer-wrapper" role="contentinfo">	
<?php if(isset($flatsome_opt['html_before_footer'])){
	// BEFORE FOOTER HTML BLOCK
	echo do_shortcode($flatsome_opt['html_before_footer']);
} ?>

<!-- FOOTER 1 -->
<?php if ( is_active_sidebar( 'sidebar-footer-1' ) ) : ?>
<div class="footer footer-1 <?php echo $flatsome_opt['footer_1_color']; ?>"style="background-color:<?php echo $flatsome_opt['footer_1_bg_color']; ?>">
	<div class="row">
		<?php dynamic_sidebar('sidebar-footer-1'); ?>		
	</div><!-- end row -->
</div><!-- end footer 1 -->
<?php endif; ?>


<!-- FOOTER 2 -->
<?php if ( is_active_sidebar( 'sidebar-footer-2' ) ) : ?>
<div class="footer footer-2 <?php echo $flatsome_opt['footer_2_color']; ?>" style="background-color:<?php echo $flatsome_opt['footer_2_bg_color']; ?>">
	<div class="row">

 		<?php dynamic_sidebar('sidebar-footer-2'); ?>		
	</div><!-- end row -->
</div><!-- end footer 2 -->
<?php endif; ?>

<?php if(isset($flatsome_opt['html_after_footer'])){
	// AFTER FOOTER HTML BLOCK
	echo do_shortcode($flatsome_opt['html_after_footer']);
} ?>

<div class="absolute-footer <?php echo $flatsome_opt['footer_bottom_style']; ?>" style="background-color:<?php echo $flatsome_opt['footer_bottom_color']; ?>">
<div class="row">
	<div class="large-12 columns">
		<div class="left">
			 <?php if ( has_nav_menu( 'footer' ) ) : ?>
				<?php
						wp_nav_menu(array(
							'theme_location' => 'footer',
							'menu_class' => 'footer-nav',
							'depth' => 1,
							'fallback_cb' => false,
						));
				?>						
			<?php endif; ?>
		<div class="copyright-footer"><?php if(isset($flatsome_opt['footer_left_text'])) {echo do_shortcode($flatsome_opt['footer_left_text']);} else{ echo 'Define left footer text / navigation in Theme Option Panel';} ?></div>
		<!-- .left -->
		<div class="right">
				<?php if(isset($flatsome_opt['footer_right_text'])){ echo do_shortcode($flatsome_opt['footer_right_text']);} else {echo 'Define right footer text in Theme Option Panel';} ?>
		</div>
	</div><!-- .large-12 -->
</div><!-- .row-->
</div><!-- .absolute-footer -->
</footer><!-- .footer-wrapper -->
</div><!-- #wrapper -->

<!-- back to top -->
<a href="#top" id="top-link" class="animated fadeInUp"><span class="icon-angle-up"></span></a>

<?php if(isset($flatsome_opt['html_scripts_footer'])){
	// Insert footer scripts
	echo $flatsome_opt['html_scripts_footer'];
} ?>
<!---->

<div id="floating" style="display:none">
<div id="top-link" class="support-center" style="float:right; margin-right:45px; margin-top:-139px;">
  <a href="#" data-popup-target="#ltsupport-popup" title="Live-Tech Support Center">
    <div class="support-center-text" style="height:auto">
      <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-center.png" width="139" height="38"/>
      <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-livechat.png" width="139" height="38" />
      <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-start-remote-session.png" width="139" height="38" />
      <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-submit-ticket.png" width="139" height="38"/>
      <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-suite.png" width="139" height="38" />
      <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-knowledgebase.png" width="139" height="38"/>
    </div>
  </a>
</div>
<div id="ltsupport-popup" class="popup">
<div class="popup-body"><span class="popup-exit"><i class="fa fa-times"></i></span>
	<div class="popup-content">
	<h2 class="popup-title">Live-Tech Support Center</h2>
	<p>You can also call us toll free on 1 (888) 361-8511</p>
	<ul>
		<li><a title="Live Chat With Agent" onclick="window.open('http://support.mylive-tech.com/visitor/index.php?/LiveChat/Chat/Request/_sessionID=n0hp51kk9ey391n0r9v731p74nu221rd/_proactive=0/_filterDepartmentID=/_randomNumber=13/_fullName=/_email=/_promptType=chat', 'newwindow', 'width=507, height=647'); return false;" href="#" target="_blank"><span class=""><i class="fa fa-comments"></i> <br>
		Start Live Chat</span></a> </li>
		<li><a href="http://www.mylive-tech.com/connect/" title="Start A Remote Session" target="_blank"><i class="fa fa-laptop"></i> <br>
		Start A Remote Session</a></li>
		<li><a href="http://support.mylive-tech.com/Tickets/Submit" title="Submit A Ticket" target="_blank"><i class="fa fa-edit"></i> <br>
		Submit A Ticket</a></li>
		<li><a href="http://support.mylive-tech.com/" title="Live-Tech Support Suite"><i class="fa fa-cog"></i> <br>
		Live-Tech Support Suite</a></li>
		<li><a href="http://support.mylive-tech.com/index.php?/Knowledgebase/List" title="Knowledge Base Articles" target="_blank"><i class="fa fa-file-text"></i> <br>
		Knowledge Base</a></li>
	</ul>
	</div>
</div>
</div>
</div>

<div class="popup-overlay" style="display:none"></div>

<?php wp_footer(); ?>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/cycle-all-min.js"></script>
<script type="text/javascript">
jQuery(document).ready( function($){
	jQuery('#floating').delay(14000).fadeIn(1500);
	jQuery("#support-center").show();

	$('.support-center-text').cycle({
		fx: 'scrollLeft' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	});

	$('[data-popup-target]').click(function() {
		$('html').addClass('overlay');
		var activePopup = $(this).attr('data-popup-target');
		$(activePopup).addClass('visible');
		$(".popup-overlay").show();
	});

	$(document).keyup(function (e) {
		if (e.keyCode == 27 && $('html').hasClass('overlay')) {
			clearPopup();
		}
	});

	$('.popup-exit').click(function() {
		clearPopup();
	});

	$('.popup-overlay').click(function() {
		clearPopup();
	});

	function clearPopup() {
		$('.popup.visible').addClass('transitioning').removeClass('visible');
		$('html').removeClass('overlay');
		$(".popup-overlay").hide();
		setTimeout(function () {
			$('.popup').removeClass('transitioning');
		}, 200);
	}

});
</script>
</body>
</html>