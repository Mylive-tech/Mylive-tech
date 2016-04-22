<?php if ( 'on' == et_get_option( 'divi_back_to_top', 'false' ) ) : ?>

<span class="et_pb_scroll_top et-pb-icon"></span>
<?php endif;















if ( ! is_page_template( 'page-template-blank.php' ) ) : ?>
<?php wp_reset_query(); ?>



<footer id="main-footer">
<div class="container">
    <div id="footer-widgets" class="clearfix">
      <div class="footer-widget">
        <?php  //get_sidebar('Footer Area 5'); ?>
        <?php dynamic_sidebar('Fast Shipping'); ?>
      </div>
      <div class="footer-widget">
        <?php dynamic_sidebar('B2B'); ?>
      </div>
      <div class="footer-widget">
        <?php dynamic_sidebar('24x7 Support'); ?>
      </div>
      <div class="footer-widget last">
        <?php dynamic_sidebar('Secure Payment'); ?>
      </div>
    </div>
  </div>




  <?php get_sidebar( 'footer' ); ?>
  <div class="container">
    <div id="footer-widgets" class="clearfix">
      <div class="footer-widget">
        <?php  //get_sidebar('Footer Area 5'); ?>
        <?php dynamic_sidebar('Footer Area 5'); ?>
      </div>
      <div class="footer-widget">
        <?php dynamic_sidebar('Footer Area 6'); ?>
      </div>
      <div class="footer-widget">
        <?php dynamic_sidebar('Footer Area 7'); ?>
      </div>
      <div class="footer-widget last">
        <?php dynamic_sidebar('Footer Area 8'); ?>
      </div>
    </div>
  </div>
  <?php







			if ( has_nav_menu( 'footer-menu' ) ) : ?>
  <div id="et-footer-nav">
    <div class="container">
      <?php







							wp_nav_menu( array(







								'theme_location' => 'footer-menu',







								'depth'          => '1',







								'menu_class'     => 'bottom-nav',







								'container'      => '',







								'fallback_cb'    => '',







							) );







						?>
    </div>
  </div>
  <!-- #et-footer-nav -->
  <?php endif; ?>
  <div id="footer-bottom">
    <div class="container clearfix">
      <?php







					if ( false !== et_get_option( 'show_footer_social_icons', true ) ) {







						get_template_part( 'includes/social_icons', 'footer' );







					}







				?>
      <p id="footer-info">© 2002-2015 Live-Tech. All rights reserved. Some images, logos & product names are properties of their respective trademark owners.</p>
    </div>
    <!-- .container -->
  </div>
</footer>
<!-- #main-footer -->
</div>
<!-- #et-main-area -->
<?php endif; // ! is_page_template( 'page-template-blank.php' ) ?>
</div>
<!-- #page-container -->
<!-- -->
<div id="support-center" style="display:none"> <a href="#" data-popup-target="#ltsupport-popup" title="Live-Tech Support Center">
  <div class="support-center-text"> <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-center.png" width="139" height="38" /> <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-livechat.png" width="139" height="38" /> <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-start-remote-session.png" width="139" height="38" /> <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-submit-ticket.png" width="139" height="38" /> <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-suite.png" width="139" height="38" /> <img src="<?php bloginfo( 'template_url' ); ?>/new-cp/lt-support-knowledgebase.png" width="139" height="38" /> </div>
  </a> </div>
<div id="ltsupport-popup" class="popup">
  <div class="popup-body"><span class="popup-exit"><i class="fa fa-times"></i></span>
    <div class="popup-content">
      <h2 class="popup-title">Live-Tech Support Center</h2>
      <p> You can also call us toll free on 1 (888) 361-8511 </p>
      <ul>
        <li><a title="Live Chat With Agent" onclick="window.open('http://support.mylive-tech.com/visitor/index.php?/LiveChat/Chat/Request/_sessionID=n0hp51kk9ey391n0r9v731p74nu221rd/_proactive=0/_filterDepartmentID=/_randomNumber=13/_fullName=/_email=/_promptType=chat', 'newwindow', 'width=507, height=647'); return false;" href="#" target="_blank"><span class=""><i class="fa fa-comments"></i> <br>
          Start Live Chat</span></a> </li>
        <li><a href="http://www.mylive-tech.com/connect/" title="Start A Remote Session" target="_blank"><i class="fa fa-laptop"></i> <br>
          Start A Remote Session</a></li>
        <li><a href="http://support.mylive-tech.com/Tickets/Submit" title="Submit A Ticket" target="_blank"><i class="fa fa-edit"></i> <br>
          Submit A Ticket</a></li>
        <li><a href="http://support.mylive-tech.com/" title="Live-Tech Support Suite"><i class="fa fa-cog"></i> <br>
          Live-Tech Support Suite</a></li>
        <li><a href="http://support.mylive-tech.com/index.php?/Knowledgebase/List" title="Knowledgebase Articles" target="_blank"><i class="fa fa-file-text"></i> <br>
          Knowledge Base</a></li>
      </ul>
    </div>
  </div>
</div>
<div class="popup-overlay" style="display:none"></div>
<?php wp_footer(); ?>
<!-- include Cycle plugin -->
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/cycle-all-min.js"></script>
<script type="text/javascript">







jQuery(document).ready(function($) {







    $('.support-center-text').cycle({







		fx: 'scrollLeft' // choose your transition type, ex: fade, scrollUp, shuffle, etc...







	});







});







</script>
<script>







jQuery(window).load(function($){







jQuery(document).ready(function ($) {







 







    $('[data-popup-target]').click(function () {







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







 







    $('.popup-exit').click(function () {







        clearPopup();







 







    });







 







    $('.popup-overlay').click(function () {







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







});







</script>
<script>







		$(window).load(function(e){







		jQuery("#support-center").show();







});







</script>
<!-- BEGIN TAG CODE - DO NOT EDIT! -->
<div id="proactivechatcontainerylldvetv1h"></div>
<div id="swifttagcontainerylldvetv1h" style="display: none;">
  <div id="swifttagdatacontainerylldvetv1h"></div>
</div>
<script type="text/javascript">var swiftscriptelemylldvetv1h=document.createElement("script");swiftscriptelemylldvetv1h.type="text/javascript";var swiftrandom = Math.floor(Math.random()*1001); var swiftuniqueid = "ylldvetv1h"; var swifttagurlylldvetv1h="https://support.mylive-tech.com/visitor/index.php?/LiveChat/HTML/Monitoring/cHJvbXB0dHlwZT1jaGF0JnVuaXF1ZWlkPXlsbGR2ZXR2MWgmdmVyc2lvbj00LjY3LjAmcHJvZHVjdD1mdXNpb24mY3VzdG9tb25saW5lPSZjdXN0b21vZmZsaW5lPSZjdXN0b21hd2F5PSZjdXN0b21iYWNrc2hvcnRseT0KMTIzMGM5OGE3ODEwYzFlNDRhMjRlOTA0YmMwZTRmYTMwMGQ3MzlhNw==";setTimeout("swiftscriptelemylldvetv1h.src=swifttagurlylldvetv1h;document.getElementById('swifttagcontainerylldvetv1h').appendChild(swiftscriptelemylldvetv1h);",1);</script>
<!-- END TAG CODE - DO NOT EDIT! -->
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51e51a4a6b29bf63" async="async"></script>
<script type="text/javascript">
var addthis_config = addthis_config||{};
addthis_config.data_track_addressbar = false;
addthis_config.data_track_clickback = false;
</script>
</body></html>