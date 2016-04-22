<?php

/*
 Plugin Name: SB Add WooCommerce Tabs
 Plugin URI: http://www.tortoise-it.co.uk
 Description: Simple plugin to add a new tab to the WooCommerce product page controllable via a new WYSIWYG area.
 Author: Sean Barton (Tortoise IT)
 Author URI: http://www.tortoise-it.co.uk
 Version: 1.1
 */
 
 $sb_awt_tabs = array(
    'Specifications'=>15,
'Compare Plans'=>16,
'Add-ons'=>17,
'System Requirements'=>18,
'Package Contents'=>19,
'What&#39;s Covered & Not'=>21,
	'F.A.Q.'=>22,
	'Resources'=>23,
    'More Info / Videos'=>60
    // , 'New Tab 3'=>50
    // , 'New Tab 4'=>50
 );

add_action( 'add_meta_boxes', 'sb_awt_add_custom_box' );
add_action( 'save_post', 'sb_awt_save_postdata' );

function sb_awt_add_custom_box() {
    add_meta_box(
        'sb_awt_sectionid',
        __( 'Additional Product Tabs', 'sb_awt' ),
        'sb_awt_inner_custom_box',
        'product'
    );
}

function sb_awt_inner_custom_box( $post ) {
  global $sb_awt_tabs;
  
  wp_nonce_field( plugin_basename( __FILE__ ), 'sb_awt_noncename' );

  foreach ($sb_awt_tabs as $tab=>$priority) {
   $tab_key = sanitize_title($tab);
   $value = get_post_meta( $post->ID, '_' . $tab_key, true );
  
   echo '<p><label for="sb_awt_new_field"><strong>' . $tab . ' ' . __("Content", 'sb_awt' ) . '</strong></label></p>';

        $settings = array(
		'quicktags' 	=> array( 'buttons' => 'em,strong,link' ),
		'textarea_name'	=> $tab_key,
		'quicktags' 	=> true,
		'tinymce' 	=> true,
		'editor_css'	=> '<style>#wp-' . $tab_key . '-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

	wp_editor( htmlspecialchars_decode( $value ), $tab_key, $settings );   
   
  }
}

/* When the post is saved, saves our custom data */
function sb_awt_save_postdata( $post_id ) {
 global $sb_awt_tabs;

  if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
  }

  if ( ! isset( $_POST['sb_awt_noncename'] ) || ! wp_verify_nonce( $_POST['sb_awt_noncename'], plugin_basename( __FILE__ ) ) ) {
    return;
  }

  foreach ($sb_awt_tabs as $tab=>$priority) {
   $tab_key = sanitize_title($tab);
   $tab_content = $_POST[$tab_key];
   update_post_meta($post_id, '_' . sanitize_title($tab), $tab_content);
  }
}

add_filter( 'woocommerce_product_tabs', 'sb_awt_new_tabs', 50);

function sb_awt_tab_content($tab_name) {
 global $sb_awt_tabs;
 
 foreach ($sb_awt_tabs as $tab=>$priority) {
  if (sanitize_title($tab) == $tab_name) {
   
   if ($content = get_post_meta(get_the_ID(), '_' . $tab_name, true)) {
    echo do_shortcode($content);
   }

   break;
  }
 }
 
}

function sb_awt_new_tabs($tabs) {
  global $sb_awt_tabs;
  
  foreach ($sb_awt_tabs as $tab=>$priority) {
   $tab_name = sanitize_title($tab);
   
   if ($content = get_post_meta(get_the_ID(), '_' . $tab_name, true)) {
    $tabs[$tab_name] = array(
    'title' => __( $tab, 'woocommerce' ),
    'priority' => $priority,
    'callback' => 'sb_awt_tab_content'
    );
   }
   
  }
  
 return $tabs;
}
?>