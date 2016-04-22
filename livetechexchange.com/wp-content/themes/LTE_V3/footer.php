<?php
/**
 * The template for displaying the footer.
 *

 */
?>
	
<!-- Footer -->
<footer id="Footer">
	<div class="Wrapper">
    <div class="row clearfix">
   <div id="clients"><h3> 20000+ Businesses Endorse Our Services </h3> </div>
    <div class="client" id="left">
		<img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-01.jpg" width="744" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-02.jpg" width="879" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-03.jpg" width="784" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-04.jpg" width="1029" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-05.jpg" width="837" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-06.jpg" width="1164" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-07.jpg" width="1062" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-08.jpg" width="827" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-09.jpg" width="1061" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-10.jpg" width="605" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-11.jpg" width="753" height="60" alt="Live-Tech Exchange Client Logos" /><img src="<?php bloginfo('template_url'); ?>/images/clients/lt-clients-12.jpg" width="753" height="60" alt="Live-Tech Exchange Client Logos" />
	</div>
    <script type='text/javascript' src="<?php bloginfo( 'template_url' ); ?>/js/jquery.spritely-0.6.1.js"></script>
    <script type="text/javascript">

        (function($) {
            $(document).ready(function() {
                $('#logo').click(function() {
                    document.location.href = '#';
                });
                $('#clouds').pan({fps: 15, speed: 0.7, dir: 'left', depth: 10});
                $('#hill1, #hill2, #clouds').spRelSpeed(8);
            });
        })(jQuery);
    
    </script>
    
    <script type="text/javascript">
marqueeInit({
	uniqueid: 'left',
	style: {

	},
	inc: 0.25, //speed - pixel increment for each iteration of this marquee's movement
	mouse: 'cursor driven', //mouseover behavior ('pause' 'cursor driven' or false)
	moveatleast:1,
	neutral: 150,
	savedirection: true,
	random: true
});
</script>
    
    </div>
    
	
		<?php get_sidebar( 'footer' ); ?>
		
		<div class="row clearfix">
			
			<div class="two_third col">
				
				<div class="copyrights">
					<p class="author">&copy; <?php echo date( 'Y' ); ?> <span><?php bloginfo( 'name' ); ?></span>. <?php _e('All Rights Reserved.',''); ?></p>
                    <p>All Logos / Images used on this website are Trademarks / Copyrights of their respective owners.</p>				</div>
				
			</div>
			
			<div class="one_third col last">
				<!--<a class="back_to_top" href="#" title="<?php _e('Back to top','doover'); ?>"><?php _e('Back to top','doover'); ?></a>-->
			</div>
								
		</div>
	</div>
    <!--FOOTER TOOLBAR START-->
    <a class="back-to-top" href="#top">Back to Top</a>
<div id="footer-toolbar"> 
<ul id="toolbar-inner"> 
<li class="ft1"> <a title="Live Chat With Agent" onclick="window.open('http://support.mylive-tech.com/visitor/index.php?/LiveChat/Chat/Request/_sessionID=n0hp51kk9ey391n0r9v731p74nu221rd/_proactive=0/_filterDepartmentID=/_randomNumber=13/_fullName=/_email=/_promptType=chat', 'newwindow', 'width=507, height=647'); return false;" href="#" target="_blank"> Live Chat with Support </a> </li>
<li class="ft2"><a href="http://www.mylive-tech.com/connect" title="Start A Remote Session" target="_blank"> Start A Remote Session </a> </li>
<li class="ft3"> <a href="http://support.mylive-tech.com/index.php?/Tickets/Submit" title="Submit A Ticket" target="_blank">Submit A Ticket </a></li>
<li class="ft4"><a href="http://support.mylive-tech.com/" title="Support Suite" target="_blank">Support Suite</a></li>
<li class="ft5"><a href="http://support.mylive-tech.com/index.php?/Knowledgebase/List" title="Read Our Knowledge Base" target="_blank">Read Knowledge Base</a></li>
</ul>
</div>
<!--FOOTER TOOLBAR END-->
</footer>


<?php wp_footer(); ?>
                
<script type="text/javascript">
TableThing = function(params) {
    settings = {
        table: $('#comparetable'),
        thead: []
    };

    this.fixThead = function() {
        // empty our array to begin with
        settings.thead = [];
        // loop over the first row of td's in &lt;tbody> and get the widths of individual &lt;td>'s
        $('tbody tr:eq(1) td', settings.table).each( function(i,v){
            settings.thead.push($(v).width());
        });

        // now loop over our array setting the widths we've got to the &lt;th>'s
        for(i=0;i<settings.thead.length;i++) {
            $('thead th:eq('+i+')', settings.table).width(settings.thead[i]);
        }

        // here we attach to the scroll, adding the class 'fixed' to the &lt;thead> 
        $(window).scroll(function() {
            var windowTop = $(window).scrollTop();

            if (windowTop > settings.table.offset().top) {
                $("thead", settings.table).addClass("fixed");
            }
            else {
                $("thead", settings.table).removeClass("fixed");
           }			
        });
    }
}
$(function(){
    var table = new TableThing();
    table.fixThead();
    $(window).resize(function(){
        table.fixThead();
    });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
	$('#megamenu > ul').bind('mouseover', function(){
		$('body').spotlight({exitEvent:'mouseover', speed:200});
	});
});
</script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=185403098253787";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>

 <!-- BEGIN SUPPORT SUITE TAG - DO NOT EDIT! --><div id="proactivechatcontainer5ssj1hdd06"></div><div id="swifttagcontainer5ssj1hdd06" style="display: none;"><div id="swifttagdatacontainer5ssj1hdd06"></div></div> <script type="text/javascript">var swiftscriptelem5ssj1hdd06=document.createElement("script");swiftscriptelem5ssj1hdd06.type="text/javascript";var swiftrandom = Math.floor(Math.random()*1001); var swiftuniqueid = "5ssj1hdd06"; var swifttagurl5ssj1hdd06="http://support.mylive-tech.com/visitor/index.php?/LiveChat/HTML/Monitoring/cHJvbXB0dHlwZT1jaGF0JnVuaXF1ZWlkPTVzc2oxaGRkMDYmdmVyc2lvbj00LjY2LjImcHJvZHVjdD1mdXNpb24mY3VzdG9tb25saW5lPSZjdXN0b21vZmZsaW5lPSZjdXN0b21hd2F5PSZjdXN0b21iYWNrc2hvcnRseT0KMGY3MmRiMzQxZTIxM2UyMjFkNGZmN2FkZGFjZTRlYTRiYjRhZWUwOQ==";setTimeout("swiftscriptelem5ssj1hdd06.src=swifttagurl5ssj1hdd06;document.getElementById('swifttagcontainer5ssj1hdd06').appendChild(swiftscriptelem5ssj1hdd06);",1);</script><!-- END SUPPORT SUITE TAG - DO NOT EDIT! --> 

</body>
</html>