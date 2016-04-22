<?php
// External Classes
include(get_stylesheet_directory() . '/class-wc-widget-product-categories.php');

// Custom login screen
function custom_login_css() {
echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri().'/custom/livetech-login-styles.css" />';
}
add_action('login_head', 'custom_login_css');
// login screen font
function custom_fonts() {
echo '<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,700" rel="stylesheet" type="text/css">';
}
add_action('login_head', 'custom_fonts');
add_filter( 'login_headerurl', 'custom_login_header_url' );
//login home link
function custom_login_header_url($url) {
return  'https://www.mylive-tech.com' ;
}

// User admin subscription page
add_action('woocommerce_my_subscriptions_after_subscription_id', 'action_woocommerce_my_subscriptions_after_subscription_id');
function action_woocommerce_my_subscriptions_after_subscription_id($subscription) {
	$ProductStr = '<span class="subscription-productname">';
	foreach ( $subscription->get_items() as $item_id => $item ) {
		$ProductStr .= esc_html( apply_filters( 'woocommerce_order_item_name', $item['name'], $item ) ) . ', ';
	};
	echo substr(trim($ProductStr), 0, -1) . '</span>';
}

// Store Front
add_filter('woocommerce_product_get_rating_html', 'filter_woocommerce_product_get_rating_html');
function filter_woocommerce_product_get_rating_html($rating_html, $rating)
{
	if ( empty($rating_html)) {
		$rating_html .= '<div class="star-rating" title="This product is unrated"></div>';
	}
	return $rating_html;
}

// Custom WooCommerce Admin Product feilds
add_action( 'woocommerce_product_options_sku', 'action_woocommerce_product_options_sku');
add_action( 'woocommerce_process_product_meta', 'action_woo_add_custom_general_fields_save' );
$conditions = array(
	'false' => __( '-Do Not Display-', 'woocommerce' ), 
	'1' => __( 'New', 'woocommerce' ), 
	'2' => __( 'Like New', 'woocommerce' ), 
	'3' => __( 'Used', 'woocommerce' ), 
	'4' => __( 'Refurbished', 'woocommerce' )
);
/// Admin display fields
function action_woocommerce_product_options_sku() {
	global $woocommerce, $post, $conditions;

	echo '<div class="options_group">';
	// UPC Code
	woocommerce_wp_text_input( 
		array( 
			'id'          => '_upc', 
			'label'       => __( 'UPC Code', 'woocommerce' ), 
			'placeholder' => '# ##### ##### #',
			'desc_tip'    => 'true',
			'description' => __( 'UPC 12 digit Code. Field is shown to customers', 'woocommerce' ) 
		)
	);
	// Internal Cost
	woocommerce_wp_text_input( 
		array( 
			'id'          => '_internal_cost', 
			'label'       => __( 'Internal Cost', 'woocommerce' ), 
			'placeholder' => '0.00',
			'desc_tip'    => 'true',
			'description' => __( 'The cost of the product before markup. Field is not shown to customers', 'woocommerce' ) 
		)
	);
	// Condition
	woocommerce_wp_select( 
	array( 
		'id'      => '_condition', 
		'label'   => __( 'Condition', 'woocommerce' ), 
		'options' => $conditions
		)
	);
	echo '</div>';
};
/// Save Fields
function action_woo_add_custom_general_fields_save( $post_id ){
	
	// Production Cost
	$woocommerce_internal_cost = $_POST['_internal_cost'];
	if( !empty( $woocommerce_internal_cost ) ) {
		update_post_meta( $post_id, '_internal_cost', esc_attr( $woocommerce_internal_cost ) );
	}
	// Production Cost
	$woocommerce_upc = $_POST['_upc'];
	if( !empty( $woocommerce_upc ) ) {
		update_post_meta( $post_id, '_upc', esc_attr( $woocommerce_upc ) );
	}
	// Condition
	$woocommerce_condition = $_POST['_condition'];
	if( !empty( $woocommerce_condition ) ) {
		update_post_meta( $post_id, '_condition', esc_attr( $woocommerce_condition ) );
	}
		
};
/// Product Page display above Sku
add_action( 'woocommerce_product_meta_start', 'action_woocommerce_product_meta_start');
function action_woocommerce_product_meta_start(){
	$upc = get_post_meta( get_the_ID(), '_upc', true );
	if(isset($upc) && !empty($upc)) {
		?>
	<span class="sku_wrapper">UPC: <span class="sku" itemprop="upc"><?php echo $upc; ?></span></span>
		<?php
	};
}
/// Product Page display below Brands
add_action( 'woocommerce_product_meta_end', 'action_woocommerce_product_meta_end');
function action_woocommerce_product_meta_end(){
	global $conditions;
	$condition = get_post_meta( get_the_ID(), '_condition', true );
	if(isset($condition) && !empty($condition) && $condition != 'false') {
		?>
	<span class="sku_wrapper">Condition: <span class="sku" itemprop="condition"><?php echo $conditions[$condition]; ?></span></span>
		<?php
	};
}


// Checkout
add_filter( 'woocommerce_checkout_show_terms','filter_woocommerce_checkout_show_terms');
function filter_woocommerce_checkout_show_terms($bool)
{
	$bool = false;
	return $bool;
}

add_action( 'woocommerce_review_order_before_submit', 'action_woocommerce_review_order_before_submit' );
function action_woocommerce_review_order_before_submit() {
?>
			<p class="form-row terms">
				<label for="terms" class="checkbox"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?></label>
				<input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
			</p>
<?php
};


// Frontend Footer
add_action('wp_footer', 'action_wp_footer');
function action_wp_footer(){ ?>
<script type="text/javascript">
jQuery( document ).ready(function($) {
var offset = $('#masthead').offset().top;
$(window).scroll(function () {
	if( $(window).scrollTop() > offset && !($('#masthead').hasClass('stuck'))){
		$('#masthead').addClass('stuck');
	} else if ($(window).scrollTop() <= offset){
		$('#masthead').removeClass('stuck');
	}
});
});
</script>
<?php };

// Store Homepage
add_action( 'woocommerce_archive_description', 'action_woocommerce_archive_description', 50 );
function action_woocommerce_archive_description(){?>

<h3 class="section-title clearfix title_center" style="margin-bottom:0"><span>Check our Latest products!</span></h3>
<div class="right" style="margin-bottom:15px">
	<?php woocommerce_result_count();?>
	<?php woocommerce_catalog_ordering();?>
</div>

<?php
}

// Product store page
/// Move Breadcrumb to top of page
add_action( 'woocommerce_before_single_product', 'filter_woocommerce_before_single_product',1);
function filter_woocommerce_before_single_product() {
	echo '<div class="product-breadcrumb">';
	woocommerce_breadcrumb();
	echo "</div>";
}

/// Reform Pricing when on sale
add_filter( 'woocommerce_get_price_html_from_to', 'filter_woocommerce_get_price_html_from_to', 10, 4);
function filter_woocommerce_get_price_html_from_to($price, $from, $to, $this)
{
	$price  = '<span class="tablet-hide list-price">List Price: <del>' . ( ( is_numeric( $from ) ) ? wc_price( $from ) : $from ) . '</del></span>';
	$price .= '<span class="sale-price"><span class="tablet-hide">Your </span>Price: <ins>' . ( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) . '</ins></span>';
	$price .= '<span class="tablet-hide savings">You Save: ' . round(100 - ($to / $from * 100), 2) . '%</span>';
	return $price;
};

add_filter( 'woocommerce_variable_subscription_price_html', 'filter_woocommerce_variable_subscription_price_html', 10, 4);
function filter_woocommerce_variable_subscription_price_html($from, $this)
{
	return '<span class="sale-price">' . $from .'</span>';
};

add_filter( 'woocommerce_get_price_html_from_text', 'filter_woocommerce_get_price_html_from_text', 10, 4);
function filter_woocommerce_get_price_html_from_text($from, $this)
{
	return 'Starting From: ';
};

add_filter( 'woocommerce_price_html', 'filter_woocommerce_price_html', 10, 4);
function filter_woocommerce_price_html($price, $this)
{
	return '<div style="color: #009933;"><span class="tablet-hide">Your </span>Price: <ins>' . $price . '</ins></div>';
};

/// Change defualt image to full for gallary
add_filter( 'single_product_large_thumbnail_size', 'filter_single_product_large_thumbnail_size');

function filter_single_product_large_thumbnail_size ($size) {
	return 'full';
}

// Wishlist page
add_action( 'yith_wcwl_before_wishlist_title', 'action_yith_wcwl_before_wishlist_title');
function action_yith_wcwl_before_wishlist_title()
{
	$share_email_enabled = get_option( 'yith_wcwl_share_email' ) == 'yes';
	$share_links_title = apply_filters( 'plugin_text', urlencode( get_option( 'yith_wcwl_socials_title' ) ) );
	$share_link_url = ( ! empty( $wishlist_id ) ) ? YITH_WCWL()->get_wishlist_url( 'view' . '/' . $wishlist_id ) : YITH_WCWL()->get_wishlist_url( 'user' . '/' . get_current_user_id() );
	$share_summary = urlencode( str_replace( '%wishlist_url%', $share_link_url, get_option( 'yith_wcwl_socials_text' ) ) );
	?>
	<div class="yith-wcwl-share right">
    <ul>

        <?php if( $share_email_enabled ): ?>
            <li style="list-style-type: none; display: inline-block;">
                <a style="width: 160px;" class="email" href="mailto:?subject=<?php echo urlencode( apply_filters( 'yith_wcwl_email_share_subject', __( 'MyLive-Tech Wishlist', 'yith-woocommerce-wishlist' ) ) )?>&amp;body=<?php echo apply_filters( 'yith_wcwl_email_share_body', $share_link_url ) ?>&amp;title=<?php echo $share_link_title ?>" title="<?php _e( 'Email', 'yith-woocommerce-wishlist' ) ?>"><span style="font-family:Helvetica,Arial,sans-serif;padding-left:8px;">Email Wishlist</span></a>
            </li>
        <?php endif; ?>
    </ul>
</div>
<?php
};


// My Account > Downloads tab
add_action( 'woocommerce_after_downloads', 'action_woocommerce_after_downloads');
function action_woocommerce_after_downloads()
{?>
	<h2>Live-Tech Free Downloads</h2>
	<ul class="digital-downloads">
		<li><a href="https://support.mylive-tech.com/Knowledgebase/Article/GetAttachment/115/6508">Remote Support Software (Windows)</a></li>
		<li><a href="http://www.mylive-tech.com/public_downloads/macsupport.dmg">Remote Support Software (Mac)</a></li>
		<li><a href="https://support.mylive-tech.com/Knowledgebase/Article/GetAttachment/117/6375">Live-Tech Customer Suite (Windows)</a></li>
	</ul>
<?php
};

// Override Shortcode: [ux-image]
function shortcode_ux_image( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'id' => '',
		'title' => '',
		'image_size' => 'large',
		'image_width' => '',
		'image_pull' => '0px',
		'width' => '',
		'height' => '',
		'drop_shadow' => '',
		'lightbox' => '',
		'link' => '',
		'target' => '',
	), $atts ) );

	$img = $id;
	if (strpos($img,'http://') !== false || strpos($img,'https://') !== false) {
		$img = $img;
	}
	else {
		$img = wp_get_attachment_image_src($img, 'large');
		if(empty($width) && empty($height)) {
			$width = $img[1];
			$height = $img[2];
		}
		$img = $img[0];
	}

	if($target) $target = 'target="'.$target.'"';



	$link_start = '';
	$link_end = '';

	if($link){
			$link_start = '<a href="'.$link.'" '.$target.'>';
			$link_end = '</a>';
	}

	if($lightbox){
		 $link_start = '<a class="image-lightbox" href="'.$img.'">';
		 $link_end = '</a>';
	}
 
	if($drop_shadow) $drop_shadow = 'box-shadow';

	if(!empty($width)) {
		$width = ' width="' . $width . '"';
	}
	if(!empty($height)) {
		$height = ' height="' . $height . '"';
	}
	$content = '<div class="ux-img-container '.$drop_shadow.'">'.$link_start.'<img src="'.$img.'" alt="'.$title.'" title="'.$title.'"'.$height.$width.' style="bottom:-'.$image_pull.'"/>'.$link_end.'</div>';
	return $content;
}

// Remove/add Shortcode
add_action( 'after_setup_theme', 'action_after_setup_theme' );
function action_after_setup_theme() {
   remove_shortcode( 'ux_image' );
   add_shortcode( 'ux_image', 'shortcode_ux_image' );
}

// Remove Actions & Filters
add_action( 'init' , 'action_init');
function action_init() {
	remove_action_setup( 'flatsome_shop_category_nav_right', 'woocommerce_result_count');
	remove_action_setup( 'flatsome_shop_category_nav_right', 'woocommerce_catalog_ordering');
	remove_action_setup( 'flatsome_product_before_title', 'woocommerce_breadcrumb');
}

function remove_action_setup($action, $function) {
	remove_action( $action, $function, has_action($action, $function) );
}


//----


// Plugin Hacks
// Woocommerce see ticket: OTT-212-33191
add_action( 'woocommerce_product_subcategories_after_loop', 'action_woocommerce_product_subcategories_after_loop');
function action_woocommerce_product_subcategories_after_loop()
{?>
	</ul>
	<hr class="rule-light" style="clear: both; width: 100%;"/>
	<ul class="products small-block-grid-2 large-block-grid-5">
<?php
};