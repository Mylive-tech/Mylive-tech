<?php
/**
 * The Header for the theme.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<!-- Head -->
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<!-- Meta -->
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php 
	$display =  doover_get_option( 'display' ); 
	if( $display['responsive'] ) echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0">';
?>

<meta name="viewport" content="width=980">
<title><?php global $page, $paged; wp_title( '|', true, 'right' ); bloginfo( 'name' );
if ( $paged >= 2 || $page >= 2 ) echo ' | ' . sprintf( __( 'Page %s', 'doover' ), max( $paged, $page ) );
?>
</title>
	
<link rel="profile" href="http://gmpg.org/xfn/11" />	
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	
<!-- Stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

<!--LT Family-->
<link href="<?php bloginfo('template_url'); ?>/lt-family-assets/lt-family-css.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans&amp;subset=latin">


<?php do_action('wp_styles'); ?>
<?php wp_enqueue_script("jquery"); ?>
<!-- WP Head -->
<?php wp_head();?>
<link rel="shortcut icon" href="<?php echo doover_get_option( 'favicon', THEME_URI .'/images/favicon.ico', true ); ?>" type="image/x-icon" />

<script type='text/javascript' src="<?php bloginfo( 'template_url' ); ?>/js/crawler.js"></script>

<?php if( is_single() ): ?><?php endif; ?>
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: 'da31857d-b063-4021-93d6-aacd8a3cf443', popup:'true'}); </script>
<?php do_action('wp_seo'); ?>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-31521160-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script src="<?php bloginfo( 'template_url' ); ?>/js/ltpopout.js" type="text/javascript" /></script>
</head>

<!-- Body -->
<body <?php body_class(); ?>>

	<header id="Header">
    <div id="stage" class="stage">
    <div id="clouds" class="stage"></div></div>
		<div class="header_overlay">
			<div class="Wrapper">
				<!-- Top -->
				<div class="top">
				
					<?php if( is_page_template( 'template-home.php' )) echo '<h1>'; ?>
						<a id="logo" href="http://www.livetechexchange.com" title="<?php bloginfo( 'name' ); ?>">
							<?php if( doover_get_option( 'logo_text_show' ) ):?>
								<?php echo doover_get_option( 'logo_text' ); ?>
							<?php else: ?>
								<img src="<?php echo doover_get_option( 'logo', THEME_URI .'/images/live-tech-logo.png', true ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
							<?php endif; ?>
						</a>
					<?php if( is_page_template( 'template-home.php' )) echo '</h1>'; ?>
							
					<div class="top_options">
                    
                    <div class="lt-fb-like"><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Flivetechexchange&amp;send=false&amp;layout=standard&amp;width=75&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=35&amp;appId=261100693918446" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:35px;" allowTransparency="true"></iframe> <div class="lt-gplus"><!-- Place this tag where you want the +1 button to render. -->
<div class="g-plusone" data-size="medium" data-href="http://www.livetechexchange.com"></div>

<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script></div><div class="lt-pin-it"><a href="http://pinterest.com/pin/create/button/?url=http%3A%2F%2Fwww.livetechexchange.com%2F&media=http%3A%2F%2Fwww.livetechexchange.com%2Flt-logo.png&description=Live-Tech%20hosted%20Microsoft%20Exchange%202010%20makes%20team%20collaboration%20easier%20than%20ever.%20Share%20everything%20on-the-go%20saves%20time%20%26%20money%2C%20improves%20productivity." class="pin-it-button" count-layout="horizontal" target="_blank" rel="nofollow"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div></div>
					
						<!-- Top links -->
						<ul class="top_links">
							<?php if ( doover_get_option( 'facebook_url' ) ):?>
								<li class="tl1"><a href="<?php echo doover_get_option( 'facebook_url' ); ?>" title="Live-Tech Facebook" target="_blank">Facebook</a></li>
							<?php endif;?>
							
							<?php if ( doover_get_option( 'google_url' ) ):?>
								<li class="tl7"><a href="<?php echo doover_get_option( 'google_url' ); ?>" title="Live-Tech on Google +" target="_blank">Google +</a></li>
							<?php endif;?>
							
							<?php if ( doover_get_option( 'twitter_url' ) ):?>
								<li class="tl2"><a href="<?php echo doover_get_option( 'twitter_url' );?>" title="Follow Live-Tech on Twitter" target="_blank">Twitter</a></li>
							<?php endif;?>
                           

							<?php if ( doover_get_option( 'youtube_url' ) ):?>
								<li class="tl6"><a href="<?php echo doover_get_option( 'youtube_url' ); ?>" title="Subscribe to Live-Tech Videos on YouTube" target="_blank">YouTube</a></li>
							<?php endif;?>
							
							<?php if ( doover_get_option( 'linkedin_url' ) ):?>
								<li class="tl5"><a href="<?php echo doover_get_option( 'linkedin_url' ); ?>" title="Live-Tech Company Profile on LinkedIN" target="_blank">LinkedIN</a></li>
							<?php endif;?>

							<?php if ( doover_get_option( 'rss' ) ):?>
								<li class="tl3"><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('RSS','doover'); ?>" target="_blank"><?php _e('RSS','doover'); ?></a></li>
							<?php endif; ?>
														
							<?php if( doover_get_option( 'sitemap' ) ): ?>
								<li class="tl4"><a href="<?php echo get_permalink( doover_get_option( 'sitemap' ) );?>" title="<?php _e('Sitemap','doover'); ?>"><?php _e('Sitemap','doover'); ?></a></li>
							<?php endif; ?>
						
						</ul>					
						
						<!-- Call us -->
						<?php $call_us =  doover_get_option( 'call_us' )?>
						<?php if ( $call_us['text'] ):?>
							<div class="call_us">
								<a href="tel:+18883618511" title="Click to Call Live-Tech"><span><?php echo $call_us['prefix']; ?> </span><?php echo $call_us['text']; ?></a> <span style="font-weight: normal; color:#999">|</span> <a href="tel:+17869752146" title="International Contact">1-(786) 975-2146</a>
							</div>
						<?php endif;?>	
		
					</div>	
				
					<!-- Navigation -->
					<?php
						// megamenu
						if( $display['megamenu'] ){
							doover_megamenu();
						} else {
							doover_wp_nav_menu();
						}
						
						// responsive
						if( $display['responsive'] && $display['megamenu'] ){
							doover_megamenu_dropdown();
						} else if( $display['responsive'] && ( ! $display['megamenu'] ) ) {
							dropdown_menu( array('dropdown_title' => '- - Main menu - -', 'indent_string' => '- - ', 'indent_after' => '','container' => 'nav', 'container_id' => 'responsive_navigation', 'theme_location'=>'primary') );	
						}
					?>
					
				</div>
				<?php 
				if( is_page_template( 'template-home.php' ) || is_page_template( 'template-homepage-left.php' ) || is_page_template( 'template-homepage-right.php' ) ) : 
					
					// if using the homepage template
					switch( doover_get_option( 'homepage_style' ) ) {
					    
						case 'slider_offer':
					        get_template_part( 'includes/header', 'slider-offer' );
					        break;
					    
					   	case 'slider_photos':
					        get_template_part( 'includes/header', 'slider-photos' );
					        break;
					        
					   	case 'image':
					        get_template_part( 'includes/header', 'image' );
					        break;
					        
					   	case 'simple':
					        get_template_part( 'includes/header', 'simple' );
					        break;
					    
						default:
					    	echo '<br style="clear:both;">';
					}
   	
                elseif( is_404() ): 
                	// else if 404 page - 404
                	get_template_part( 'includes/header', '404' );
                	
                else: 
                	// else if not home template - subpage title
                	get_template_part( 'includes/header', 'title' );
				endif;
				?>
				
			</div>
		</div>
       
	</header>
    
    <nav id="lt-float-wrapper">
		<nav class="lt-float-accordionButton"><img src="<?php bloginfo( 'template_url' ); ?>/images/portal-login.jpg" alt="Exchange Management Portal" width="100" height="38"></nav>
		<nav class="lt-float-accordionContent"><span><a href="http://www.livetechexchange.com/manage" title="Management Portal Exchange 2010 Login" target="_blank"><img src="<?php bloginfo( 'template_url' ); ?>/images/float-lte-2010-icon.png" width="20" height="20" alt="Exchange 2010">2010</a></span> <span><a href="http://www.livetechexchange.com/manage2013" title="Management Portal Exchange 2013 Login" target="_blank" ><img src="<?php bloginfo( 'template_url' ); ?>/images/float-lte-2013-icon.png" width="20" height="20" alt="Exchange 2013">2013</a></span></nav>
		<nav class="lt-float-accordionButton"><img src="<?php bloginfo( 'template_url' ); ?>/images/owa-login.jpg" alt="Outlook WebApp OWA Login" width="100" height="38"></nav>
		<nav class="lt-float-accordionContent"><span><a href="http://www.livetechexchange.com/owa" title="Outlook WebApp OWA 2010 Login" target="_blank"><img src="<?php bloginfo( 'template_url' ); ?>/images/float-owa-2010-icon.png" width="20" height="20" alt="Outlook WebApp 2010">2010</a></span> <span><a href="http://www.livetechexchange.com/owa2013" title="Outlook WebApp OWA 2013 Login" target="_blank"><img src="<?php bloginfo( 'template_url' ); ?>/images/float-owa-2013-icon.png" width="20" height="20" alt="Outlook WebApp 2013">2013</a></span></nav>
		<nav class="lt-float-accordionButton"><a href="http://www.livetechexchange.com/skynox" title="SkyNox Portal" target="_blank"><img src="<?php bloginfo( 'template_url' ); ?>/images/skynox-login.jpg" alt="SkyNox Portal" height="38" width="100"></a></nav>
		<nav class="lt-float-accordionButton"><a href="https://login.microsoftonline.com/" title="Office 365 Portal" target="_blank"><img src="<?php bloginfo( 'template_url' ); ?>/images/office-365-portal.png" alt="Office 365 Portal" height="38" width="100"></a></nav>
		
		<nav class="lt-float-accordionButton"><a href="http://www.livetechexchange.com/request-a-demo/" title="Request A Demo"><img src="<?php bloginfo( 'template_url' ); ?>/images/demo-icon.jpg" alt="Request A Demo" width="100" height="38"></a></nav>
		
	</nav>
