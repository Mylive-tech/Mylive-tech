<?php
/**
 * Shortcodes.
 *

 */

// columns ---------------------------------------------------------------------------

add_shortcode( 'row', 'sc_row' );

add_shortcode( 'one_second', 'sc_one_second' );
add_shortcode( 'one_second_last', 'sc_one_second_last' );

add_shortcode( 'one_third', 'sc_one_third' );
add_shortcode( 'one_third_last', 'sc_one_third_last' );
add_shortcode( 'two_third', 'sc_two_third' );
add_shortcode( 'two_third_last', 'sc_two_third_last' );

add_shortcode( 'one_fourth', 'sc_one_fourth' );
add_shortcode( 'one_fourth_last', 'sc_one_fourth_last' );
add_shortcode( 'two_fourth', 'sc_two_fourth' );
add_shortcode( 'two_fourth_last', 'sc_two_fourth_last' );
add_shortcode( 'three_fourth', 'sc_three_fourth' );
add_shortcode( 'three_fourth_last', 'sc_three_fourth_last' );

add_shortcode( 'one_sixth', 'sc_one_sixth' );
add_shortcode( 'one_sixth_last', 'sc_one_sixth_last' );
add_shortcode( 'two_sixth', 'sc_two_sixth' );
add_shortcode( 'two_sixth_last', 'sc_two_sixth_last' );
add_shortcode( 'three_sixth', 'sc_three_sixth' );
add_shortcode( 'three_sixth_last', 'sc_three_sixth_last' );
add_shortcode( 'four_sixth', 'sc_four_sixth' );
add_shortcode( 'four_sixth_last', 'sc_four_sixth_last' );
add_shortcode( 'five_sixth', 'sc_five_sixth' );
add_shortcode( 'five_sixth_last', 'sc_five_sixth_last' );

// general ---------------------------------------------------------------------------

add_shortcode( 'accordion', 'sc_accordion' );
add_shortcode( 'acc_item', 'sc_acc_item' );
add_shortcode( 'blockquote', 'sc_blockquote' );
add_shortcode( 'box_call_to_action', 'sc_box_call_to_action' );
add_shortcode( 'button', 'sc_button' );
add_shortcode( 'code', 'sc_code' );
add_shortcode( 'dropcap', 'sc_dropcap' );
add_shortcode( 'highlight', 'sc_highlight' );

add_shortcode( 'homepage_box', 'sc_homepage_box' );
add_shortcode( 'homepage_features', 'sc_homepage_features' );
add_shortcode( 'homepage_feature', 'sc_homepage_feature' );
add_shortcode( 'homepage_feature_last', 'sc_homepage_feature_last' );
add_shortcode( 'illustration_overlay', 'sc_illustration_overlay' );

add_shortcode( 'image', 'sc_image' );
add_shortcode( 'image_frame', 'sc_image_frame' );
add_shortcode( 'list', 'sc_list' );
add_shortcode( 'links', 'sc_links' );
add_shortcode( 'message', 'sc_message' );
add_shortcode( 'pricing_table', 'sc_pricing_table' );
add_shortcode( 'tabs', 'sc_tabs' );
add_shortcode( 'tab', 'sc_tab' );
add_shortcode( 'table', 'sc_table' );
add_shortcode( 'vimeo', 'sc_vimeo' );
add_shortcode( 'youtube', 'sc_youtube' );

// pages ---------------------------------------------------------------------------

add_shortcode( 'clients', 'sc_clients' );
add_shortcode( 'client', 'sc_client' );
add_shortcode( 'client_last', 'sc_client_last' );

add_shortcode( 'faq', 'sc_faq' );
add_shortcode( 'faq_answer', 'sc_faq_answer' );

add_shortcode( 'features', 'sc_features' );
add_shortcode( 'feature', 'sc_feature' );
add_shortcode( 'feature_last', 'sc_feature_last' );

add_shortcode( 'offer', 'sc_offer' );
add_shortcode( 'offer_item', 'sc_offer_item' );

add_shortcode( 'team', 'sc_team' );
add_shortcode( 'team_item', 'sc_team_item' );
add_shortcode( 'team_item_last', 'sc_team_item_last' );


/* ---------------------------------------------------------------------------
 * Shortcode [row] [/row]
 * --------------------------------------------------------------------------- */
function sc_row( $attr, $content = null )
{
	$output  = '<div class="row clearfix">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [one_second] [/one_second]
 * --------------------------------------------------------------------------- */
function sc_one_second( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col one_second'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_one_second_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_one_second( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [one_third] [/one_third]
 * --------------------------------------------------------------------------- */
function sc_one_third( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col one_third'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_one_third_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_one_third( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [two_third] [/two_third]
 * --------------------------------------------------------------------------- */
function sc_two_third( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col two_third'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_two_third_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_two_third( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [one_fourth] [/one_fourth]
 * --------------------------------------------------------------------------- */
function sc_one_fourth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col one_fourth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_one_fourth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_one_fourth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [two_fourth] [/two_fourth]
 * --------------------------------------------------------------------------- */
function sc_two_fourth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col two_fourth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_two_fourth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_two_fourth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [three_fourth] [/three_fourth]
 * --------------------------------------------------------------------------- */
function sc_three_fourth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col three_fourth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_three_fourth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_three_fourth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [one_sixth] [/one_sixth]
 * --------------------------------------------------------------------------- */
function sc_one_sixth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col one_sixth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_one_sixth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_one_sixth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [two_sixth] [/two_sixth]
 * --------------------------------------------------------------------------- */
function sc_two_sixth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col two_sixth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_two_sixth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_two_sixth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [three_sixth] [/three_sixth]
 * --------------------------------------------------------------------------- */
function sc_three_sixth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col three_sixth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_three_sixth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_three_sixth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [four_sixth] [/four_sixth]
 * --------------------------------------------------------------------------- */
function sc_four_sixth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col four_sixth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_four_sixth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_four_sixth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [five_sixth] [/five_sixth]
 * --------------------------------------------------------------------------- */
function sc_five_sixth( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'class' => '',
	), $attr));
	
	$output  = '<div class="col five_sixth'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}

function sc_five_sixth_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_five_sixth( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [code] [/code]
 * --------------------------------------------------------------------------- */
function sc_code( $attr, $content = null )
{
	$output  = '<pre>';
	$output .= muffin_remove_autop($content);
	$output .= '</pre>'."\n";
	
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [message] [/message]
 * --------------------------------------------------------------------------- */
function sc_message( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'style' => 'info',
	), $attr));
	
	$output  = '<div class="'. $style .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
		
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [dropcap] [/dropcap]
 * --------------------------------------------------------------------------- */
function sc_dropcap( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'style' => '',
		'colour' => '',
	), $attr));
	
	$class = "dropcap";
	if( $style ) $class .= " dropcap_". $style;
	if( $colour ) $class .= " dropcap_circle_". $colour;
	
	$output  = '<div class="'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
		
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [highlight] [/highlight]
 * --------------------------------------------------------------------------- */
function sc_highlight( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'colour' => '',
	), $attr));
	
	$class = "highlight";
	if( $colour ) $class .= " highlight_". $colour;
	
	$output  = '<span class="'. $class .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</span>';
		
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [image_frame]
 * --------------------------------------------------------------------------- */
function sc_image_frame( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'width' => '',
		'height' => '',
		'image' => '',
		'zoom' => '',
		'alt' => '',
		'float' => '',
	), $attr));
	
	if( $float ) $float = ' image_frame_'. $float;
	if( $width ) $width = ' width="'. $width .'"';
	if( $height ) $height = ' height="'. $height .'"';
	
	$output = '<figure class="image_frame'. $float .'">';
	if( $zoom ) $output .= '<a rel="gallery" class="fancybox" href="'. $zoom .'">';
	$output .= '<img src="'. $image .'"'. $width . $height .' alt="'. $alt .'" />';
	if( $zoom ) $output .= '</a>';
	$output .= '</figure>'."\n";
	
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [list] [/list]
 * --------------------------------------------------------------------------- */
function sc_list( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'style' => '',
	), $attr));
		
	$output  = '<ul class="'. $style .'">';
	$output .= muffin_remove_autop($content);
	$output .= '</ul>'."\n";
		
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [links] [/links]
 * --------------------------------------------------------------------------- */
function sc_links( $attr, $content = null )
{
	$output  = '<div class="links">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
		
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [homepage_box] [/homepage_box]
 * --------------------------------------------------------------------------- */
function sc_homepage_box( $attr, $content = null )
{
	$output  = '<div class="homepage-box clearfix">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";
	
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [image]
 * --------------------------------------------------------------------------- */
function sc_image( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'image' => '',
		'alt' => '',
		'width' => '',
		'height' => '',
	), $attr));
	
	if( $width ) $width = ' width="'. $width .'"';
	if( $height ) $height = ' height="'. $height .'"';
	
	$output = '<img src="'. $image .'" alt="'. $alt .'"'. $width . $height .' />'."\n";
	
    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [button]
 * --------------------------------------------------------------------------- */
function sc_button( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'style' => 'small',
		'colour' => 'gray',
		'title' => 'Read more',
		'link' => '',
		'target' => '',
	), $attr));
	
	$output = '<a class="button button-'. $style .' button-'. $style .'-'. $colour .'" href="'. $link .'" target="'. $target .'"><span>'. $title .'</span></a>'."\n";

    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [box_call_to_action] [/box_call_to_action]
 * --------------------------------------------------------------------------- */
function sc_box_call_to_action( $attr, $content = null )
{	
	extract(shortcode_atts(array(
		'title'		=> '',
		'style'		=> '',
		'link'		=> '',
		'btn_title' => '',
	), $attr));
	
	if( $style ){
		$style = ' call_to_action_'. $style;
	}
	
	$output  = '<div class="Call-to-action">'."\n";
	$output .= '<div class="inside clearfix">'."\n";
	$output .= '<h4>'. $title .'</h4>'."\n";
	$output .= '<h5>';
	$output .= muffin_remove_autop($content);
	$output .= '</h5>'."\n";
	if( $link ){
		$output .= '<a class="call_to_action '. $style .'" href="'. $link .'"><span>'. $btn_title .'</span></a>'."\n";
	}
	$output .= '</div>'."\n";
	$output .= '</div>'."\n";

    return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [illustration_overlay]
 * --------------------------------------------------------------------------- */
function sc_illustration_overlay( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'image' => '',
		'overlay' => '',
		'alt' => '',
	), $attr));
	
	$output  = '<div id="homepage-illustration">';
	$output .= '<img src="'. $image .'" alt="'. $alt .'" />';
	$output .= '<div class="overlay" style="background:url('. $overlay .') top center no-repeat;"></div>';
	$output .= '</div>'."\n";	
    
	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [homepage_features] [/homepage_features]
 * --------------------------------------------------------------------------- */
function sc_homepage_features( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'title' => '',
		'desc' => '',
		'help_link' => '',
		'help_title' => '',
		'btn_link' => '',
		'btn_title' => '',
	), $attr));
	
	$output  = '<div id="homepage-features">';
	$output .= '<h3>'. __( $title, 'MG' ) .'</h3>';
	$output .= '<h5>'. __( $desc, 'MG' ) .'</h5>';
	$output .= '<ul>';
	$output .= muffin_remove_autop($content);
	$output .='</ul>';
	
	if ( $help_link ) {
		$output .=' <a class="help" href="'. $help_link .'">'. $help_title .'</a>';
	}
	if ( $btn_link ) {
		$output .=' <a class="button button-small button-small-gray rs" href="'. $btn_link .'"><span>'. $btn_title .'</span></a>';
	}

	$output .= '</div>'."\n";	
    
	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [homepage_feature] [/homepage_feature]
 * --------------------------------------------------------------------------- */
function sc_homepage_feature( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'title' => '',
		'img' => '',
		'alt' => '',
		'link_title' => 'Read more',
		'link_href' => '',
		'class' => '',
	), $attr));
	
	$output  = '<li>';
	if ($class) $output = '<li class="'. $class .'">';
	$output .= '<img src="'. $img .'" alt="'. $alt .'" />';
	$output .= '<div class="ins">'."\n";
	$output .= '<h5>'. $title .'</h5>';
	$output .= muffin_remove_autop($content);
	if( $link_href ){
		$output .='<a href="'. $link_href .'">'. $link_title .'</a>';
	}
	$output .= '</div>'."\n";	
	$output .= '</li>'."\n";	

	return $output;
}

function sc_homepage_feature_last( $attr, $content = null )
{
	$attr['class'] = 'clearfix';
	return sc_homepage_feature( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [blockquote] [/blockquote]
 * --------------------------------------------------------------------------- */
function sc_blockquote( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'author' => '',
		'link' => '',
		'link_title' => '',
	), $attr));
	
	$output  = '<blockquote>';
	$output .= '<p>'. muffin_remove_autop($content) .'</p>';
	$output .= '<address>'. $author; 
	
	if( $link_title ){
		$output .=', <a href="'. $link .'">'. $link_title .'</a>';
	}
	
	$output .= '</address>';	
	$output .= '</blockquote>'."\n";	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [tabs] [/tabs]
 * --------------------------------------------------------------------------- */
global $tabs_array, $tabs_count;
function sc_tabs( $attr, $content = null )
{
	global $tabs_array, $tabs_count;

	extract(shortcode_atts(array(
		'uid' => uniqid(),
	), $attr));
	
	do_shortcode( $content );
	
	if( is_array( $tabs_array ) )
	{
		$output  = '<div class="jq-tabs">';
		$output .= '<ul>';
		
		$i = 1;
		$output_tabs = '';
		foreach( $tabs_array as $tab )
		{
			$output .= '<li><a href="#tab-'. $i .'">' . $tab['title'] . '</a></li>';
			$output_tabs .= '<div id="tab-'. $i .'">' .muffin_remove_autop( $tab['content'] ).'</div>';
			$i++;
		}
		
		$output .= '</ul>';
		$output .= $output_tabs;
		$output .= '</div>';
		
		$tabs_array = '';
		$tabs_count = 0;

		return $output;
	}
}


/* ---------------------------------------------------------------------------
 * Shortcode [tab] [/tab]
 * --------------------------------------------------------------------------- */
$tabs_count = 0;
function sc_tab( $attr, $content = null )
{
	global $tabs_array, $tabs_count;
	
	extract(shortcode_atts(array(
		'title' => 'Tab title',
	), $attr));
	
	$tabs_array[] = array(
		'title' => $title,
		'content' => do_shortcode( $content )
	);	
	$tabs_count++;

	return true;
}


/* ---------------------------------------------------------------------------
 * Shortcode [table]
 * --------------------------------------------------------------------------- */
function sc_table( $attr, $content = null )
{
	$output  = '';
	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [pricing_table]
 * --------------------------------------------------------------------------- */
function sc_pricing_table( $attr, $content = null )
{
	$output  = '';
	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [accordion] [/accordion]
 * --------------------------------------------------------------------------- */
function sc_accordion( $attr, $content = null )
{
	$output  = '<div class="jq-accordion">';
	$output .= muffin_remove_autop($content);	
	$output .= '</div>'."\n";	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [acc_item] [/acc_item]
 * --------------------------------------------------------------------------- */
function sc_acc_item( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'title' => 'Accordion item title',
	), $attr));
	
	$output  = '<h3><a href="#">'. $title .'</a></h3>'."\n";
	$output .= '<div>';
	$output .= muffin_remove_autop($content);	
	$output .= '</div>';	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [vimeo]
 * --------------------------------------------------------------------------- */
function sc_vimeo( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'width' => '710',
		'height' => '426',
		'video' => '',
	), $attr));
	
	$output  = '<div class="article_video">';
	$output .= '<iframe width="'. $width .'" height="'. $height .'" src="http://player.vimeo.com/video/'. $video .'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'."\n";
	$output .= '</div>';
	
	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [youtube]
 * --------------------------------------------------------------------------- */
function sc_youtube( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'width' => '710',
		'height' => '426',
		'video' => '',
	), $attr));
	
	$output  = '<div class="article_video">';
	$output .= '<iframe width="'. $width .'" height="'. $height .'" src="http://www.youtube.com/embed/'. $video .'?wmode=opaque" frameborder="0" allowfullscreen></iframe>'."\n";
	$output .= '</div>';
	
	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [clients] [/clients]
 * --------------------------------------------------------------------------- */
function sc_clients( $attr, $content = null )
{
	$output  = '<div class="clients">'."\n";
	$output .= muffin_remove_autop($content);	
	$output .= '</div>'."\n";	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [client]
 * --------------------------------------------------------------------------- */
function sc_client( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'image'		=> '',
		'width'		=> '',
		'height'	=> '',
		'link'		=> '',
		'title'		=> '',
		'class'		=> '',
	), $attr));
	
	if( $width ) $width = ' width="'. $width .'"';
	if( $height ) $height = ' height="'. $height .'"';
		
	$output  = '<div class="item'.$class.'">';
	if( $link ) $output .= '<a href="'. $link .'" title="'. $title .'">';
	$output .= '<img src="'. $image .'"'. $width . $height .' alt="" />';
	if( $link ) $output .= '</a>';	
	$output .= '</div>'."\n";	

	return $output;
}

function sc_client_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_client( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [faq] [/faq]
 * --------------------------------------------------------------------------- */
function sc_faq( $attr, $content = null )
{
	$output  = '<div class="faq">';
	$output .= muffin_remove_autop($content);	
	$output .= '</div>'."\n";	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [faq_answer] [/faq_answer]
 * --------------------------------------------------------------------------- */
function sc_faq_answer( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'question' => 'Question',
	), $attr));
		
	$output  = '<div class="question">'."\n";	
	$output .= '<h5>'. $question .'</h5>'."\n";	
	$output .= '<div class="answer">';
	$output .= muffin_remove_autop($content);
	$output .= '</div>'."\n";	
	$output .= '</div>';	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [features] [/features]
 * --------------------------------------------------------------------------- */
function sc_features( $attr, $content = null )
{
	$output  = '<div class="features">'."\n";
	$output .= '<ul class="clearfix">';
	$output .= muffin_remove_autop($content);	
	$output .= '</ul>'."\n";	
	$output .= '</div>'."\n";	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [feature] [/feature]
 * --------------------------------------------------------------------------- */
function sc_feature( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'title' => '',
		'image' => '',
		'image_width' => '',
		'image_height' => '',
		'link' => '',
		'link_text' => 'Read more',
		'class' => '',
	), $attr));
	
	if( $image_width ) $image_width = ' width="'. $image_width .'"';
	if( $image_height ) $image_height = ' height="'. $image_height .'"';
	
	$output  = '<li>'."\n";
	if ($class) $output = '<li class="'. $class .'">';
	$output .= '<img src="'. $image .'"'. $image_width . $image_height .' alt="" />'."\n";
	$output .= '<div class="ins">'."\n";
	$output .= '<h5>'. $title .'</h5>';
	$output .= muffin_remove_autop($content);
	if( $link ) $output .= '<a href="'. $link .'">'. $link_text .'</a>'."\n";
	$output .= '</div>'."\n";	
	$output .= '</li>';	

	return $output;
}

function sc_feature_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_feature( $attr, $content );
}


/* ---------------------------------------------------------------------------
 * Shortcode [offer] [/offer]
 * --------------------------------------------------------------------------- */
function sc_offer( $attr, $content = null )
{
	$output  = '<div class="offer">';
	$output .= muffin_remove_autop($content);	
	$output .= '</div>'."\n";	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [offer_item] [/offer_item]
 * --------------------------------------------------------------------------- */
function sc_offer_item( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'image_position' => 'left',
	), $attr));
		
	$output  = '<div class="item item_'. $image_position .'">';	
	$output .= muffin_remove_autop($content);
	$output .= '</div>';	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [team] [/team]
 * --------------------------------------------------------------------------- */
function sc_team( $attr, $content = null )
{
	$output  = '<div class="our_team">';
	$output .= muffin_remove_autop($content);	
	$output .= '</div>'."\n";	

	return $output;
}


/* ---------------------------------------------------------------------------
 * Shortcode [team_item] [/team_item]
 * --------------------------------------------------------------------------- */
function sc_team_item( $attr, $content = null )
{
	extract(shortcode_atts(array(
		'image' => '',
		'width' => '210',
		'height' => '210',
		'name' => '',
		'job' => '',
		'email' => '',
		'phone' => '',
		'class' => '',
	), $attr));
	
	if( $width ) $width = ' width="'. $width .'"';
	if( $height ) $height = ' height="'. $height .'"';
	
	$output  = '<div class="item '. $class .'">'."\n";
	$output .= '<div class="overlay"></div>'."\n";
	$output .= '<div class="desc">'."\n";
	$output .= '<div class="about">'."\n";
	$output .= '<h6>'. $name .'</h6>'."\n";
	if( $job ) $output .= '<p class="job">'. $job .'</p>'."\n";
	$output .= '</div>'."\n";
	if( $email ) $output .= '<p class="email"><a href="mailto:'. $email .'">'. $email .'</a></p>'."\n";
	if( $phone ) $output .= '<p class="phone">'. $phone .'</p>'."\n";
	$output .= '</div>'."\n";
	$output .= '<span class="line"></span>'."\n";
	$output .= '<img src="'. $image .'"'. $width . $height .' alt="" />'."\n";	
	$output .= '</div>';	

	return $output;
}

function sc_team_item_last( $attr, $content = null )
{
	$attr['class'] = ' last';
	return sc_team_item( $attr, $content );
}

?>