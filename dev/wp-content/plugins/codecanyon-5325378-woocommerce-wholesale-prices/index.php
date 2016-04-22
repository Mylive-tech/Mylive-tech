<?php
/*
Plugin Name: WooCommerce Wholesale Pricing
Plugin URI: http://www.danyob.org/woocommerce-wholesale-pricing
Description: WooCommerce Wholesale Pricing - A extension for WooCommerce, which adds wholesale functionality to your store.
Version: 1.9
Author: Danyo Borg
Author URI: http://www.danyob.org
*/

add_action('admin_menu', 'woo_wholesale_page',99);
add_action('admin_init', 'register_woo_wholesale_settings');
add_option('wwo_savings_label', 'You Save', '', 'yes');
add_option('wwo_rrp_label', 'RRP', '', 'yes');
add_option('wwo_wholesale_label', 'Your Price', '', 'yes');
add_option('wwo_wholesale_role', 'wholesale_customer', '', 'yes');

function woo_wholesale_page() {
	add_submenu_page( 'woocommerce', 'Wholesale Pricing', 'Wholesale Pricing', 'manage_options', 'manage-wholesale-pricing', 'woo_wholesale_page_call' ); 
}

function register_woo_wholesale_settings() {
	register_setting( 'woo_wholesale_options', 'wwo_savings' );
	register_setting( 'woo_wholesale_options', 'wwo_savings_label' );
	register_setting( 'woo_wholesale_options', 'wwo_percentage' );
	register_setting( 'woo_wholesale_options', 'wwo_rrp' );
	register_setting( 'woo_wholesale_options', 'wwo_rrp_label' );
	register_setting( 'woo_wholesale_options', 'wwo_wholesale_label' );
	register_setting( 'woo_wholesale_options', 'wwo_min_quantity' );
	register_setting( 'woo_wholesale_options', 'wwo_min_quantity_value' );
	register_setting( 'woo_wholesale_options', 'wwo_max_quantity' );
	register_setting( 'woo_wholesale_options', 'wwo_max_quantity_value' );
	register_setting( 'woo_wholesale_options', 'wwo_wholesale_role' );
}

function woo_wholesale_page_call() {
	include('options-page.php');
} 

add_role('wholesale_customer', 'Wholesale Customer', array(
    'read' => true, 
    'edit_posts' => false,
    'delete_posts' => false, 
));


add_action( 'save_post', 'wwp_save_simple_wholesale_price' );
function wwp_save_simple_wholesale_price( $post_id ) {
	if (isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))return;
	$new_data = $_POST['wholesale_price'];
	$post_ID = $_POST['post_ID'];
	update_post_meta($post_ID, '_wholesale_price', $new_data) ;
}

add_action( 'woocommerce_product_options_pricing', 'wwp_add_admin_simple_wholesale_price', 10, 2 );
function wwp_add_admin_simple_wholesale_price( $loop ){ 
$wholesale = get_post_meta( get_the_ID(), '_wholesale_price', true );
?>

<tr>
  <td><div>
      <p class="form-field _regular_price_field ">
        <label><?php echo __( 'Wholesale Price', 'woocommerce' ) . ' ('.get_woocommerce_currency_symbol().')'; ?></label>
        <input step="any" type="number" class="wc_input_price short" name="wholesale_price" value="<?php echo $wholesale; ?>"/>
      </p>
    </div></td>
</tr>
<?php }

//Display Fields
add_action( 'woocommerce_product_after_variable_attributes', 'wwp_add_variable_wholesale_price', 10, 2 );
//JS to add fields for new variations
add_action( 'woocommerce_product_after_variable_attributes_js', 'wwp_add_variable_wholesale_price_js' );
//Save variation fields
add_action( 'woocommerce_process_product_meta_variable', 'wwp_variable_wholesale_price_process', 10, 1 );

function wwp_add_variable_wholesale_price( $loop, $variation_data ) {
?>
<tr>
  <td><div>
      <label><?php echo __( 'Wholesale Price', 'woocommerce' ) . ' ('.get_woocommerce_currency_symbol().')'; ?></label>
      <input  step="any" type="number" size="5" name="wholesale[<?php  echo $loop; ?>]" value="<?php echo $variation_data['_wholesale_price'][0]; ?>"/>
    </div></td>
</tr>
<?php
}

function wwp_add_variable_wholesale_price_js() {
?>
<tr>
  <td><div>
      <label><?php echo __( 'Wholesale Price', 'woocommerce' ) . ' ('.get_woocommerce_currency_symbol().')'; ?></label>
      <input step="any" type="number" size="5" name="wholesale[' + loop + ']" />
    </div></td>
</tr>
<?php
}
function wwp_variable_wholesale_price_process( $post_id ) {
if (isset( $_POST['variable_sku'] ) ) :
    $variable_sku = $_POST['variable_sku'];
    $variable_post_id = $_POST['variable_post_id'];

    $wholesale_field = $_POST['wholesale'];
    
    for ( $i = 0; $i < sizeof( $variable_sku ); $i++ ) :
        $variation_id = (int) $variable_post_id[$i];
        if ( isset( $wholesale_field[$i] ) ) {
            update_post_meta( $variation_id, '_wholesale_price', stripslashes( $wholesale_field[$i] ) );
			update_post_meta( $variation_id, '_parent_product', $post_id );
        }
    endfor;
	update_post_meta( $post_id, '_variation_prices', $wholesale_field );
	update_post_meta( $post_id, '_wholesale_price', '' );
endif;
}


add_filter( 'manage_edit-product_columns', 'wpp_add_wholesale_column' ) ;
function wpp_add_wholesale_column( $columns ) {
$offset = 2;
$newArray = array_slice($columns, 0, $offset, true) +
	array('wholesale' => 'Wholesale') +
	array_slice($columns, $offset, NULL, true);
	return $newArray;
}

add_action( 'manage_product_posts_custom_column', 'wwp_manage_wholesale_product_columns', 10, 2 );
function wwp_manage_wholesale_product_columns( $column, $post_id ) {
	global $post;
	switch( $column ) {
		case 'wholesale' :
			$wholesale = get_post_meta( get_the_ID(), '_wholesale_price', true );
			if ( empty( $wholesale ) )
				echo __( '--' );
			else
				echo woocommerce_price($wholesale);
			break;
	}
}

add_filter( 'manage_edit-product_sortable_columns', 'wpp_sortable_wholesale_column' );
function wpp_sortable_wholesale_column( $columns ) {
	$columns['wholesale'] = 'wholesale';
	return $columns;
}

function wwp_custom_variation_price( $price, $product ) {
	
	$current_user = new WP_User(wp_get_current_user()->ID);
	$user_roles = $current_user->roles;
	$current_role = get_option('wwo_wholesale_role');
	foreach ($user_roles as $roles) {
		if ($roles == $current_role ){
			
			$variations = $product->get_available_variations();
			$lowestvar = array();
			
			foreach ($variations as $variation){
				$lowestvar[] = get_post_meta($variation['variation_id'],'_wholesale_price', true);
				array_multisort($lowestvar, SORT_ASC);
			}
			
			$minp = min($lowestvar);
			$maxp = max($lowestvar);
			
			if ($minp == $maxp){ 
				$price = woocommerce_price($minp);
			} else { 
				$price = woocommerce_price($minp).' - '.woocommerce_price($maxp); 
			}
				 
			}
			
		}
	
	return $price;
}
add_filter('woocommerce_variable_price_html', 'wwp_custom_variation_price', 10, 2);


//DISPLAY WHOLSALE PRICE IF USER IS WHOLESALER FOR VARIABLE PRODUCTS//

add_filter( 'woocommerce_available_variation', 'wwp_update_dropdown_variation_price', 10, 3);
function wwp_update_dropdown_variation_price( $data, $product, $variation ) {

	
	$data['price_html'] = '<span class="price">'.woocommerce_price(get_post_meta( $data['variation_id'], '_price', true )).'</span>';
	
	$current_user = new WP_User(wp_get_current_user()->ID);
	$user_roles = $current_user->roles;
	$current_role = get_option('wwo_wholesale_role');
	$wholesalep = get_post_meta( $data['variation_id'], '_wholesale_price', true );
	
	foreach ($user_roles as $roles) {
		if  ($roles == $current_role ){
			
			if ($wholesalep !== ''){
   				$data['price_html'] = '<span class="price">'.woocommerce_price($wholesalep).'</span>'; 
			}
    
		}  
		
	} 
	
	return $data;
	
}


add_action( 'woocommerce_get_price_html' , 'wwp_get_wholesale_price' );
function wwp_get_wholesale_price($price){

	$current_user = new WP_User(wp_get_current_user()->ID);
	$user_roles = $current_user->roles;
	$current_role = get_option('wwo_wholesale_role');
	foreach ($user_roles as $roles) {
	if  ($roles == $current_role ){
		$wholesale = get_post_meta( get_the_ID(), '_wholesale_price', true );
		$rrp = get_post_meta( get_the_ID(), '_price', true );
		$savings  = $rrp - $wholesale;
		$division = $rrp ? $savings / $rrp : 0;
		
		$wwo_percentage = get_option( 'wwo_percentage' );
		$wwo_savings = get_option( 'wwo_savings' );
		$wwo_rrp = get_option( 'wwo_rrp' );
		
		$res = $division * 100;
		$res = round($res, 0);
		$res = round($res, 1);
		$res = round($res, 2);
			if ($wholesale){
				
	if ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '1' ) {
		$price = get_option( 'wwo_rrp_label' ).': '.woocommerce_price($rrp).'</br>'.get_option( 'wwo_wholesale_label' ).': '.woocommerce_price($wholesale).'</br>'.get_option( 'wwo_savings_label' ).': '.woocommerce_price($savings).' ('.$res.'%)';	
	} 
	
	elseif ($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '1' ) {
		$price = get_option( 'wwo_wholesale_label' ).': '.woocommerce_price($wholesale).'</br>'.get_option( 'wwo_savings_label' ).': '.woocommerce_price($savings).' ('.$res.'%)';
	}
	
	elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '1' ) {
		$price = get_option( 'wwo_wholesale_label' ).': '.woocommerce_price($wholesale).'</br>'.get_option( 'wwo_savings_label' ).': '.woocommerce_price($savings);
	}
	
	
	elseif ($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '' ) {
		$price = get_option( 'wwo_wholesale_label' ).': '.woocommerce_price($wholesale).'</br>'.get_option( 'wwo_savings_label' ).': ('.$res.'%)';
	}
	
	elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '' ) {
		$price = get_option( 'wwo_wholesale_label' ).': '.woocommerce_price($wholesale).'</br>'.get_option( 'wwo_rrp_label' ).': '.woocommerce_price($rrp);
	}
	
	elseif ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '' ) {
		$price = get_option( 'wwo_wholesale_label' ).': '.woocommerce_price($wholesale).'</br>'.get_option( 'wwo_rrp_label' ).': '.woocommerce_price($rrp).'</br>'.get_option( 'wwo_savings_label' ).': ('.$res.'%)';
	}
	
	elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '1' ) {
		$price = get_option( 'wwo_wholesale_label' ).': '.woocommerce_price($wholesale).'</br>'.get_option( 'wwo_rrp_label' ).': '.woocommerce_price($rrp).'</br>'.get_option( 'wwo_savings_label' ).': '.woocommerce_price($savings);
	}
	
	elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '' ) {
		$price = woocommerce_price($wholesale);
	}

 
		
		}
	}
}
return $price;	

}




add_action( 'woocommerce_before_calculate_totals', 'wwp_simple_add_cart_price' );
function wwp_simple_add_cart_price( $cart_object ) {
	$current_user = new WP_User(wp_get_current_user()->ID);
	$user_roles = $current_user->roles;
	$current_role = get_option('wwo_wholesale_role');
	foreach ($user_roles as $roles) {
	if  ($roles == $current_role ){
 		foreach ( $cart_object->cart_contents as $key => $value ) {
			$wholesale = get_post_meta( $value['data']->id, '_wholesale_price', true );
			$wholesalev = get_post_meta( $value['data']->variation_id, '_wholesale_price', true );
				

				if ($wholesale){$value['data']->price = $wholesale;}
				if ($wholesalev){$value['data']->price = $wholesalev;}


} 
}




}}

add_filter( 'woocommerce_quantity_input_min', 'wpp_add_minimum_quantity' );

function wpp_add_minimum_quantity($input_value) {

$current_user = new WP_User(wp_get_current_user()->ID);
$user_roles = $current_user->roles;
$current_role = get_option('wwo_wholesale_role');
foreach ($user_roles as $roles) {
if  ($roles == $current_role ){
		if (get_option( 'wwo_min_quantity' ) == '1' ) {
			return get_option( 'wwo_min_quantity_value' );			
		} else {
			return $input_value;
		}		
		}
}
}

add_filter( 'woocommerce_quantity_input_max', 'wpp_add_maximum_quantity' );
function wpp_add_maximum_quantity($input_value) {
	$current_user = new WP_User(wp_get_current_user()->ID);
	$user_roles = $current_user->roles;
	$current_role = get_option('wwo_wholesale_role');
	foreach ($user_roles as $roles) {
	if  ($roles == $current_role ){
		if (get_option( 'wwo_max_quantity' ) == '1' ) {
			return get_option( 'wwo_max_quantity_value' );			
		} else {
			return $input_value;
		}		
		}
}
}

function wpp_mini_cart_prices( $product_price, $values, $cart_item) {
	
	global $woocommerce;
	
	$current_user = new WP_User(wp_get_current_user()->ID);
	$user_roles = $current_user->roles;
	$current_role = get_option('wwo_wholesale_role');
	$varwp = get_post_meta( $values['variation_id'], '_wholesale_price', true );
	$varnp = get_post_meta( $values['variation_id'], '_price', true );
	$simplewp = get_post_meta( $values['product_id'], '_wholesale_price', true );
	$simplenp = get_post_meta( $values['product_id'], '_price', true );
	foreach ($user_roles as $roles) {
	if  ($roles == $current_role ){
		
		if ( $values['variation_id'] > 0 ){
			
			if ($varwp == ''){
    		return woocommerce_price($varnp);
			} else {
			return woocommerce_price($varwp);	
			}
			
		} else {
			if ($simplewp == ''){
			return woocommerce_price($simplenp);
			} else {
			return woocommerce_price($simplewp);	
			}
		}

	}
				
	}
	
return $product_price;
	
}
add_filter('woocommerce_cart_item_price', 'wpp_mini_cart_prices', 10, 3);


