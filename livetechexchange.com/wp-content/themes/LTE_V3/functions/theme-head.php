<?php
/**
 * Header functions.
 *

 */
 

/* ---------------------------------------------------------------------------
 * Meta and title
 * --------------------------------------------------------------------------- */
function doover_seo() 
{
	// ---------------------------- meta -----------------------------------

	if( doover_get_option('meta_description') ){
		echo '<meta name="description" content="'.stripslashes(doover_get_option('meta_description')).'" />'."\n";
	}
	if( doover_get_option('meta_keywords') ){
		echo '<meta name="keywords" content="'.stripslashes(doover_get_option('meta_keywords')).'" />'."\n";
	}
	
	$seo = doover_get_option('meta_robots');	
	if( $seo['follow'] ) { $seo['follow'] = 'follow'; } else { $seo['follow'] = 'nofollow'; }
	if( $seo['index'] ) { $seo['index'] = 'index'; } else { $seo['index'] = 'noindex'; }
	echo '<meta name="robots" content="'.$seo['index'].', '.$seo['follow'].'" />'."\n";
	
	echo ''."\n";
	
	if( doover_get_option('google_analytics') ){
		echo doover_get_option('google_analytics');
	}
}
add_action('wp_seo', 'doover_seo');


/* ---------------------------------------------------------------------------
 * Styles
 * --------------------------------------------------------------------------- */
function dover_styles() 
{
	global $options;
	$style = doover_get_option('style','blue',true);

	echo '<link rel="stylesheet" href="'. THEME_URI .'/css/ui/jquery.ui.all.css?ver='.THEME_VERSION.'" media="screen" />'."\n";
	echo '<link rel="stylesheet" href="'. THEME_URI .'/js/fancybox/jquery.fancybox-1.3.4.css?ver='.THEME_VERSION.'" media="screen" />'."\n";
	echo '<link rel="stylesheet" href="'. THEME_URI .'/css/themes/'. $style .'/'. $style .'_theme.css?ver='.THEME_VERSION.'" media="screen" />'."\n";
	
	// responsive
	$display =  doover_get_option( 'display' );
	if( $display['responsive'] ) echo '<link rel="stylesheet" href="'. THEME_URI .'/css/responsive.css?ver='.THEME_VERSION.'" media="screen" />'."\n";
	
	echo '<!-- Custom styles -->'."\n";
	if( doover_get_option('custom_header') && ($header_bg = doover_get_option('header_bg')) ){
		
		$header_bg_defaults = array(
			'color' => '#000',
			'repeat' => 'no-repeat',
			'position' => 'top center',
		);
		$header_bg = doover_array_defaults( $header_bg, $header_bg_defaults );
	
		echo '<style>'."\n";
		echo '#Header {'."\n";
		echo 'background-color:'. $header_bg['color'] .';'."\n";
		if( $header_bg['image'] ){
			echo 'background-image:url('. $header_bg['image'] .');'."\n";
			echo 'background-repeat:'. $header_bg['repeat'] .';'."\n";
			echo 'background-position:'. $header_bg['position'] .';'."\n";
		}
		echo '}'."\n";
		echo '#Header .header_overlay { background:none;}'."\n";
		echo '</style>'."\n";
	}
	
	if( doover_get_option('custom_css') ){
		
		echo '<style>'."\n";
		echo doover_get_option('custom_css')."\n";
		echo '</style>'."\n";
	}
}
add_action('wp_styles', 'dover_styles');


/* ---------------------------------------------------------------------------
 * Fonts & Colors
 * @since 1.1.0
 * --------------------------------------------------------------------------- */
function dover_fonts_colours() 
{
	$doover_options = get_option( 'doover' );
	
	if( is_array( $doover_options ) ){
		
		$css_content = '';
		$gf_array = array();
		
		// colors array
		$arr_colors = array(
			'bg_content' => array(
				'dom' => '#Content',
				'attr' => 'background',
			),
			'bg_footer' => array(
				'dom' => '#Footer',
				'attr' => 'background',
			),
			'footer' => array(
				'dom' => '.copyrights',
				'attr' => 'color',
			),
			'link' => array(
				'dom' => 'a',
				'attr' => 'color',
			),
			'menu_hover' => array(
				'dom' => '#navigation ul > li > a:hover, #navigation > ul > li.hover > a, #megamenu ul > li > a:hover,  #megamenu > ul > li.hover > a',
				'attr' => 'background',
			),
		);
		
		// fonts array
		$arr_fonts = array(
			'text' => 'body',
			'menu' => '#navigation > ul > li > a, #megamenu > ul > li > a',
			'h1' => 'h1, .subpage_header h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
		);

		foreach( $doover_options as $key => $value ){
			
			// colours
			if( $value && preg_match('/^colour_(.*)$/', $key, $tmp) ){								
				if( key_exists($tmp[1], $arr_colors) && $arr_colors[$tmp[1]]['dom'] ){
					$css_content .= $arr_colors[$tmp[1]]['dom'] .' { '. $arr_colors[$tmp[1]]['attr'] .':'. $value .' }'."\n";
					if( $tmp[1] == "bg_header" ){
						$css_content .= '#Header .header_overlay { background:none;}'."\n";
					}
				}
			}
			
			// fonts
			if( $value && preg_match('/^font_(.*)$/', $key, $tmp) ){								
				if( key_exists($tmp[1], $arr_fonts) && $arr_fonts[$tmp[1]] ){
					
					// font-weight & font-style
					$tmp_style = '';
					switch ($value['style']) {
						case 'normal':
							$tmp_style = 'font-weight:normal; font-style:normal;';
							$gstyle = 'r';
							if( $tmp[1] == "text" ) $gstyle = 'r,b,i';
							break;
						case 'bold':
							$tmp_style = 'font-weight:bold; font-style:normal;';
							$gstyle = 'b';
							break;
						case 'italic':
							$tmp_style = 'font-weight:normal; font-style:italic;';
							$gstyle = 'i';
							break;
						case 'bold italic':
							$tmp_style = 'font-weight:bold; font-style:italic;';
							$gstyle = 'bi';
							break;
					}
					
					$google_fonts = doover_google_fonts();
					if( in_array($value['face'], $google_fonts) ){
//						echo '<link rel="stylesheet" href="http://fonts.googleapis.com/css?family='. $value['face'] . $gstyle .'">'."\n";
						$gf_array[$value['face']][] = $gstyle;
					}
					
					$value['face'] = str_replace("+", " ", $value['face']);
					$css_content .= $arr_fonts[$tmp[1]] .' { font-size:'. $value['size'] .'; font-family:'. $value['face'] .', Arial; '. $tmp_style .' color:'. $value['color'] .';}'."\n";					
				}
			}
	
		}
		
		// google fonts link generator
		if( is_array( $gf_array ) ){
			$gf_string = "";
			foreach( $gf_array as $k => $v ){
				$gf_string_part = ":";
				foreach( $gf_array[$k] as $kk => $vv ){
					$gf_string_part .= $vv .',';
				}
				$gf_string_part = substr($gf_string_part, 0, -1);
				$gf_string .= $k . $gf_string_part ."|";
			}
			$gf_string = substr($gf_string, 0, -1);
			echo '<link rel="stylesheet" href="http://fonts.googleapis.com/css?family='. $gf_string .'">'."\n";
		} else {
			echo '<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans:b,r">'."\n";
		}
		
		if( $css_content ){
			echo '<style>'."\n";
			echo $css_content;
			echo '</style>'."\n";
		}
	} else {
		echo '<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans:b,r">'."\n";
	}
}
add_action('wp_styles', 'dover_fonts_colours');


/* ---------------------------------------------------------------------------
 * Scripts
 * --------------------------------------------------------------------------- */
function dover_scripts() 
{
	if( ! is_admin() ) 
	{
		wp_deregister_script( 'jquery' );
		wp_enqueue_script( 'jquery', THEME_URI. '/js/jquery-1.7.1.min.js', false, THEME_VERSION );
		
		wp_enqueue_script( 'jquery.easing', THEME_URI .'/js/cycle/jquery.easing.1.3.js', false, THEME_VERSION );
		wp_enqueue_script( 'jquery.easing-comp', THEME_URI .'/js/jquery.easing.compatibility.js', false, THEME_VERSION );

		wp_enqueue_script( 'jquery.ui.core', THEME_URI .'/js/ui/jquery.ui.core.js', false, THEME_VERSION );
		wp_enqueue_script( 'jquery.ui.widget', THEME_URI .'/js/ui/jquery.ui.widget.js', false, THEME_VERSION );
		wp_enqueue_script( 'jquery.ui.tabs', THEME_URI .'/js/ui/jquery.ui.tabs.js', false, THEME_VERSION );
		wp_enqueue_script( 'jquery.ui.accordion', THEME_URI .'/js/ui/jquery.ui.accordion.js', false, THEME_VERSION );
		
		wp_enqueue_script( 'jquery.mousewheel', THEME_URI .'/js/fancybox/jquery.mousewheel-3.0.4.pack.js', false, THEME_VERSION );
		wp_enqueue_script( 'jquery.fancybox', THEME_URI .'/js/fancybox/jquery.fancybox-1.3.4.pack.js', false, THEME_VERSION );
		wp_enqueue_script( 'jquery.form', THEME_URI .'/js/jquery.form.js', false, THEME_VERSION );
		
		// Sliders
		if( in_array( doover_get_option( 'homepage_style' ), array( 'slider_offer', 'slider_photos' ) ) ){
			doover_slider_configuration();
			wp_enqueue_script( 'jquery.cycle.all', THEME_URI .'/js/cycle/jquery.cycle.all.js', false, THEME_VERSION );
			if( doover_get_option( 'homepage_style' ) == 'slider_offer' ){
				// Slider Offer
				wp_enqueue_script( 'jquery.cycle.custom-offer', THEME_URI .'/js/cycle/custom-offer.js', false, THEME_VERSION );
			}
			elseif( doover_get_option( 'homepage_style' ) == 'slider_photos' )
			{
				// Slider Photos
				wp_enqueue_script( 'jquery.cycle.custom-photos', THEME_URI .'/js/cycle/custom-photos.js', false, THEME_VERSION );
			}
		};

		wp_enqueue_script( 'jquery.muffingroup_menu', THEME_URI .'/js/muffingroup_menu.js', false, THEME_VERSION );
		wp_enqueue_script( 'custom', THEME_URI .'/js/custom.js', false, THEME_VERSION );

		if ( is_singular() && get_option( 'thread_comments' ) ) 
		{ 
			wp_enqueue_script( 'comment-reply' ); 
		}
	}
}
add_action('wp_print_scripts', 'dover_scripts');


/* ---------------------------------------------------------------------------
 * Slider configuration
 * --------------------------------------------------------------------------- */
function doover_slider_configuration()
{
	echo '<script>'."\n";
	echo '//<![CDATA['."\n";
	echo 'var os_args = { "timeout":'. doover_get_option( 'slider_timeout',0,true ) .', "speed":'. doover_get_option( 'slider_speed',0,true ) .' };'."\n";
	echo '//]]>'."\n";
	echo '</script>'."\n";
}


/* ---------------------------------------------------------------------------
 * IE fix
 * --------------------------------------------------------------------------- */
function dover_ie_fix() 
{
	if( ! is_admin() )
	{
		echo "\n".'<!--[if lt IE 9]>'."\n";
		echo '<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>'."\n";
		echo '<script src="'. THEME_URI .'/js/ie.js"></script>'."\n";
		echo '<![endif]-->'."\n";
		
		echo '<!--[if IE 7]>'."\n";
		echo '<link rel="stylesheet" href="'. THEME_URI .'/css/ie7.css" />'."\n";
		echo '<![endif]-->'."\n\n";
	}	
}
add_action('wp_head', 'dover_ie_fix');


/* ---------------------------------------------------------------------------
 * Annoying styles remover
 * --------------------------------------------------------------------------- */
function dover_remove_recent_comments_style() {  
    global $wp_widget_factory;  
    remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );  
}  
add_action( 'widgets_init', 'dover_remove_recent_comments_style' ); 

?>