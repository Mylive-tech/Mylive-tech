<?php
/*
	Plugin Name: Sorting WooCommerce Pro
	Plugin URI: http://codecanyon.net/item/sorting-woocommerce-pro/5771321
	Description: Add sorting by attributes for WooCommerce products
	Version: 2.0
	Author: Galal Aly
	Author URI: http://galalaly.me
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 **/
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // WooCommerce is not active/installed
    function sorting_woocommerce_admin_notice() {
    ?>
	    <div class="updated">
	        <p>WooCommerce is <strong>NOT</strong> active/installed. <strong>Sorting WooCommerce plugin is not working either</strong></p>
	    </div>
	    <?php
	}
	add_action( 'admin_notices', 'sorting_woocommerce_admin_notice' );
	return;
}


define( 'sorting_woocommerce_path', plugin_dir_path(__FILE__) ); // path for the plugin's folder
define( 'sorting_woocommerce_url', plugin_dir_url(__FILE__) ); // url for plugin's folder

$sorting_woocommerce_options = sorting_woocommerce_get_option('sorting-woocommerce-options');

$sorting_woocommerce_default_attributes = array( 
        '_sku' => 'SKU',
        '_price' => 'Price (def)',
        '_weight' => 'Weight',
        'total_sales' => 'Total Sales (def)',
        'popularity' => 'Popularity (def)',
        'date' => 'Date (def)',
        'rating' => 'Rating (def)',
        'menu_order' => 'Menu order (def)',
        'title' => 'Post/Product title (def)',
        '_featured' => 'Featured Products (def)',
        'rand' => 'Random Sorting'
     );

add_action( 'init', 'sorting_woocommerce_disable_sorting' );

/**
 * Only usage is to hide the dropdown sorting thing in the shop page
 * @return [type] [description]
 */
function sorting_woocommerce_disable_sorting() {
	global $sorting_woocommerce_options;
	
	if( !empty( $sorting_woocommerce_options['hide_dropdown_from_shop'] ) )
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

	if( empty( $sorting_woocommerce_options['attribute'] ) && !empty( $sorting_woocommerce_options['show_only_these'] ) )
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
}


add_action( 'admin_menu', 'sorting_woocommerce_admin_menu' );

/**
 * Adds the Sorting Att. link under WooCommerce in the Dashboard's menu
 */
function sorting_woocommerce_admin_menu() {
	add_submenu_page( 'woocommerce', 'Sorting WooCommerce Attributes', 'Sorting Att.', 'create_users', 'sorting_woocommerce/options.php', 'sorting_woocommerce_options_page' );
}

/**
 * Callback for the menu link. Loads the options page.
 */
function sorting_woocommerce_options_page() {
	global $sorting_woocommerce_options, $sorting_woocommerce_default_attributes;
	// Ensure the css/scripts for wordpress file upload is enqueued
	wp_enqueue_media();
	// Include the options page 
	require_once( sorting_woocommerce_path . '/options.php' );
}

add_action('admin_init', 'sorting_woocommerce_admin_init');

/**
 * Register settings group and database options key for the options page
 */
function sorting_woocommerce_admin_init() {
	register_setting( 'sorting-woocommerce-options-group', 'sorting-woocommerce-options', 'sorting_woocommerce_submitted_options_callback' );
}

add_filter('woocommerce_get_catalog_ordering_args', 'sorting_woocommerce_get_catalog_ordering_args', 99);

/**
 * Edits the returned default sorting if in a category
 * @param  [type] $orderby [description]
 * @return [type]          [description]
 */
function sorting_woocommerce_get_category_default( $orderby ) {
	// Check category default sorting
	if( is_product_category() ) {
		$term_id = get_queried_object()->term_id;
		$test = get_option( 'sorting_category_' . $term_id );
		if( !empty( $test ) ) {
			// If there is not default sorting for the category, sort using the default of the store
			$orderby = $test;
		}
	}
	
	return $orderby;
}

add_filter( 'woocommerce_default_catalog_orderby', 'sorting_woocommerce_get_category_default' );

/** 
 * Modify the ordering query when the orderby parameter is set
 */
function sorting_woocommerce_get_catalog_ordering_args( $args ) {
	global $sorting_woocommerce_options, $sorting_woocommerce_default_attributes, $woocommerce;

	if( is_array( $args ) ) {
		// User chose an option form the dropdown menu
		if(isset( $_GET['orderby'] ) ){
			$orderby = woocommerce_clean( $_GET['orderby'] );
		} else {
			// Default sorting
			$orderby = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		}
			
		// If the key is EXACTLY one of the default, do not affect the functionality
		if( array_key_exists( $orderby, $sorting_woocommerce_default_attributes ) )
			return $args; // don't touch the query and return

		// Check our custom defined criteria
		if( ! empty( $sorting_woocommerce_options['attribute'] ) ) {
			foreach( $sorting_woocommerce_options['attribute'] as $att ):
				if( $orderby == ( $att['slug'] . '_' . $att['order'] ) ) {
					// Handle the case when the user defined a custom criteria using the 
					// default attributes
					if( array_key_exists( $att['slug'], $sorting_woocommerce_default_attributes) ) {
						switch ( $att['slug'] ) {
							case 'date' :
								$args['orderby']  = 'date';
								$args['order']    = $att['order'];
								$args['meta_key'] = '';
							break;
							case 'price' :
							case '_price':
								$args['orderby']  = 'meta_value_num';
								$args['order']    = $att['order'];
								$args['meta_key'] = '_price';
							break;
							case 'popularity' :
								$args['orderby']  = 'meta_value_num';
								$args['order']    = $att['order'];
								$args['meta_key'] = 'total_sales';
							break;
							case 'rating' :
								$args['orderby']  = 'menu_order title';
								$args['order']    = $att['order'];
								$args['meta_key'] = '';

								add_filter( 'posts_clauses', array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
								add_filter( 'posts_clauses', 'sorting_woocommerce_alter_rating_query', 9999 );
							break;
							case '_featured':
								$args['orderby'] = 'meta_value';
								$args['order'] = $att['order'];
								$args['meta_key'] = '_featured';
							break;
							case 'title':
								$args['orderby'] = 'title';
								$args['order'] = $att['order'];
								$args['meta_key'] = '';
							break;
							case '_sku':
								$args['order'] = $att['order'];
								$args['meta_key'] = $att['slug'];
								$args['orderby'] = $att['comparison'];
							break;
							case 'rand':
								$args['orderby'] = 'rand';
								$args['meta_key'] = '';
							break;
							case 'menu_order':
							default :
								$args['orderby']  = 'menu_order title';
								$args['order']    = $att['order'];
								$args['meta_key'] = '';
							break;
						}
					} else {
						global $sorting_woocommerce_sorting_slug;
						$args['order'] = $att['order'];
						$args['meta_key'] = $sorting_woocommerce_sorting_slug = $att['slug'];
						$args['orderby'] = $att['comparison'];
					}
					break; // out of the loop - we found it!
				} // end order by == att slug
			endforeach;
		}
	}
	return $args;
}



add_filter( 'woocommerce_catalog_orderby', 'sorting_woocommerce_catalog_orderby_front' );
add_filter( 'woocommerce_default_catalog_orderby_options' , 'sorting_woocommerce_catalog_orderby_defaults' );

/**
 * Generates the dropdown menu for the "Default Sorting" option in WooCommerce -> Settings -> Catalog
 * @param  array $sortby the original array
 * @return array         the modified array
 */
function sorting_woocommerce_catalog_orderby_defaults( $sortby ) {
	return sorting_woocommerce_catalog_orderby_handler( $sortby, true );
}

/**
 * Generates the dropdown menu for the shop page
 * @param  array $sortby the original array
 * @return array         the modified options array
 */
function sorting_woocommerce_catalog_orderby_front( $sortby ) {
	return sorting_woocommerce_catalog_orderby_handler( $sortby, false );
}

/**
 * Generates the array of sorting options to display in the default woocommerce option or in the shop page
 * @param  array  $sortby         the original array
 * @param  boolean $default_select whether this is the woocommerce -> settings -> Catalog -> default sorting option dropdown menu
 * @return array                  the modified array with all sorting options created by user
 */
function sorting_woocommerce_catalog_orderby_handler( $sortby, $default_select = false ) {

	global $sorting_woocommerce_options, $sorting_woocommerce_default_attributes;

	if( empty( $sorting_woocommerce_options['attribute'] ) )
		return $sortby;
	
	if( ! empty( $sorting_woocommerce_options['show_only_these'] ) ) {
		$sortby = array();
	}

	$sorting_woocommerce_options['all_sorting_options'] = $sortby;

	foreach( $sorting_woocommerce_options['attribute'] as $att ) {
		// Slug price_DESC for example
		$slug = $att['slug'] . '_' . $att['order'];
		// This is used in the sorting icon - thus not useless
		$sorting_woocommerce_options['all_sorting_options'][ $slug ] = $att['sorting_label'];
		// If the option is set to be hidden, continue
		if( !empty( $att['hide_from_list'] ) && !$default_select ) {
			continue;
		}

		if( !$default_select ) {
			// Make sure this is the category assigned to this sorting option OR no categories were assigned - if any is true, show
			if( ( !empty( $att['categories'] ) && is_tax( 'product_cat', $att['categories'] ) ) || empty( $att['categories'] ) ) {

				$sortby[ $slug ] = $att['sorting_label'];
			}
		} else {
			$sortby[ $slug ] = $att['sorting_label'];
		}
	}

	return $sortby;
}

/**
 * Changes the rating query to support the ordering chosen by the user (either ASC or DESC)
 * @param  [array] $args [an array containing different query parts]
 * @return [array]       [modified query]
 */
function sorting_woocommerce_alter_rating_query( $args ) {
	$orderby = woocommerce_clean( $_GET['orderby'] );
	$order = explode( '_', $orderby );
	if( $order[1] != 'ASC' && $order[1] != 'DESC' ) {
		$order[1] = 'ASC';
	}
	$args['orderby'] = "average_rating {$order[1]}";
	return $args;
}

add_action( 'added_post_meta', 'sorting_woocommerce_importer_handler', 10, 4 );
add_action( 'updated_post_meta', 'sorting_woocommerce_importer_handler', 10, 4 );

/**
 * When post's meta data is updated on import, get product attributes and convert them to meta data
 * to be used in sorting. Also delete the meta record for removed attributes so as to not
 * overload the DB.
 * @param  [type] $post_id [description]
 * @return [type]          [description]
 */
function sorting_woocommerce_importer_handler( $meta_id, $obj_id, $meta_key, $meta_value, $oldies = false ) {

	if( ! $oldies ) {
		if( ! ( $meta_key == '_product_attributes' ) )
			return;
	}

	$product = get_product( $obj_id );

	$attributes = $product->get_attributes();

	$existing_keys = get_post_meta( $obj_id, 'sorting_woocommerce_keys', true );

	if( empty( $existing_keys ) ) // an empty string according to the codex
		$existing_keys = array();

	$new_keys = array();

	if( !empty( $attributes ) ) {
		foreach( $attributes as $key => $att ) {
			if( $att['is_taxonomy'] == '1' ) {
				$terms = wp_get_object_terms( $obj_id, $key );
				if( !is_wp_error( $terms ) && !empty( $terms ) ) {
					$value = $terms[0]->name;
				} else {
					// Skip and delete if not terms are assigned.
					delete_post_meta( $post_id, $key );
					continue;
				}
			} else {
				$value = $att['value'];
			}
			$new_keys[] = $key;
			$value = trim( strtolower( $value ) );
			update_post_meta( $obj_id, $key, $value );
		}
	}

	// Remove previously saved attributes meta data.
	foreach( $existing_keys as $k ) {
		if( ! in_array( $k, $new_keys ) ) {
			delete_post_meta( $obj_id, $k );
		}
	}

	// Update the keys reference
	update_post_meta( $obj_id, 'sorting_woocommerce_keys', $new_keys );
}

add_action( 'wp_ajax_woocommerce_save_attributes', 'sorting_woocommerce_save_woocommerce_ajax_attr_to_meta', 1 );

/**
 * When an ajax save attributes request is sent
 * @return void 
 */
function sorting_woocommerce_save_woocommerce_ajax_attr_to_meta() {
	check_ajax_referer( 'save-attributes', 'security' );

	// Get post data
	parse_str( $_POST['data'], $data );
	$post_id = absint( $_POST['post_id'] );

	if( isset( $data['attribute_names'] ) && isset( $data['attribute_values'] ) ) {
		sorting_woocommerce_attributes_handler( $data['attribute_names'], $data['attribute_values'], $post_id );
	}
}

add_action( 'save_post', 'sorting_woocommerce_save_post_attr_to_meta' );

/**
 * When the product is updated (the user clicked the update blue button), handle the submission
 * @param  [integer] $post_id [the prpduct id]
 */
function sorting_woocommerce_save_post_attr_to_meta( $post_id ) {
	if( ! empty( $_REQUEST['attribute_names'] ) && ! empty( $_REQUEST['attribute_values'] ) ) {
		sorting_woocommerce_attributes_handler( $_REQUEST['attribute_names'], $_REQUEST['attribute_values'], $post_id );
	}
}

/**
 * Saves the values of the submitted request as 
 * post meta and delete post meta that exists without 
 * a corresponding attribute in the new submitted data
 * @param  [array] $names   [Array of attributes' names]
 * @param  [array] $values  [Array of attributes' values]
 * @param  [integer] $post_id [The product id]
 */
function sorting_woocommerce_attributes_handler( $names, $values, $post_id ) {
	$product = get_product( $post_id );
	$attributes = $product->get_attributes();
	
	foreach( $attributes as $key => $existing_values ) {
		if( !in_array( $key, $names ) ) {
			// it does not exist, delete it
			// if the old attribute key is not submitted in the new form (i.e. removed), we delete its meta data (kind of clean up)
			delete_post_meta( $post_id, $key );
		}
	}

	// Loop on the submitted data
	foreach( $names as $index => $key ) {
		// If the values are not empty
		if( !empty( $values[$index] ) ) {
			$value = $values[$index];
			// If the value is an array ( an attribute with select elements )
			if( is_array( $value ) ) {
				// choose only the first element so that the query can compare
				$value = $value[0];
			}
			$value = trim(strtolower($value));
			update_post_meta( $post_id, $key, $value );
		}
	}
}

/**
 * Orders the list of elements according to the order specified by the user
 * @param  [array] $first_element  [element to be compared]
 * @param  [array] $second_element [element to be compared with]
 * @return [integer]                 [-1, 0, 1 if the first element is less than, equal to or greater than the second one respectively]
 */
function sorting_woocommerce_sorting_array( $first_element, $second_element ) {
	if( empty( $first_element['order_in_list'] ) && empty( $second_element['order_in_list'] ) )
		return 1;

	if( empty( $first_element['order_in_list'] ) )
		$first_element['order_in_list'] = 0;

	if( empty( $second_element['order_in_list'] ) )
		$second_element['order_in_list'] = 0;

	if( $first_element['order_in_list'] == $second_element['order_in_list'] )
		return 0;
	return ( $first_element['order_in_list'] < $second_element['order_in_list'] )?-1:1;
}

/**
 * Sorting the attributes according to the order_in_list when they're updated
 * and remove anything with an empty value 
 * @param  [array] $data [submitted data from the options.php]
 * @return [array]       [modified array with sorted attribtues according to their order_in_list values]
 */
function sorting_woocommerce_submitted_options_callback( $data ) {

	// Sorting
	if( !empty( $data['attribute'] ) ) {
		// Remove empty labels
		foreach( $data['attribute'] as $index => $output ) {
			$break = false;
			foreach( $output as $option => $v ) {
				if( $option !== 'order_in_list' && empty( $v ) ) {
					unset( $data['attribute'][ $index ] );
					$break = true;
				}
			}
			if( $break ) {
				continue;
			}
			// If categories default is set
			if( !empty( $output['categories_default'] ) ) {
				foreach( $output['categories_default'] as $term_id ) {
					update_option( 'sorting_category_' . $term_id, $output['slug'] . '_' . $output['order'] );
				}
			}
		}

		usort( $data['attribute'], 'sorting_woocommerce_sorting_array');
	}
	

	delete_transient( 'sorting-woocommerce-options' );

	return $data;
}

add_action( 'wp_ajax_sorting_woocommerce_process_oldies', 'sorting_woocommerce_process_oldies' );

/**
 * Processes the existing products (convert _product_attributes to meta data)
 */
function sorting_woocommerce_process_oldies() {

	if( ! is_numeric( $_POST['offset'] ) ) {
		echo "Sorry, an error occurred";
		die();
	}
	
	$offset = $_POST['offset'];

	$posts = get_posts( array(
		'post_type' => 'product',
		'offset' => $_POST['offset'],
		'posts_per_page' => 10
	) );

	if( empty( $posts ) )
		$offset = -1;
	else
		$offset += 10;

	$to_output = new stdClass;
	$to_output->posts = array();

	foreach( $posts as $post ) {
		sorting_woocommerce_importer_handler( null, $post->ID, null, null, true );
		$to_output->posts[] = $post->post_title . ' - Processed';
	}

	$to_output->new_offset = $offset;

	echo json_encode( $to_output );
	
	die();
}

add_action( 'wp_ajax_sorting_woocommerce_mass_assign', 'sorting_woocommerce_mass_assign' );

/**
 * Handles the ajax request for mass assigning an attribute to all products that do not have it already
 * @return void 
 */
function sorting_woocommerce_mass_assign() {
	if( ! is_numeric( $_POST['offset'] ) ) {
		echo "Sorry, an error occurred";
		die();
	}
	
	$offset = $_POST['offset'];
	$term = $_POST['term']; // the taxonomy!
	$value = empty( $_POST['value'] ) ? '0' : $_POST['value']; // the term to set in the taxonomy
	$is_visible = $_POST['is_visible'];

	$args = array(
		'post_type' => 'product',
		'offset' => $_POST['offset'],
		'posts_per_page' => 10
	);

	if( !empty( $_POST['category'] ) && $_POST['category'] != '-1' ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => $_POST['category']
			)
		);
	}

	$posts = get_posts( $args );

	if( empty( $posts ) )
		$offset = -1;
	else
		$offset += 10;

	$to_output = new stdClass;
	$to_output->posts = array();

	if ( ! function_exists( 'attributes_cmp' ) ) {
		function attributes_cmp( $a, $b ) {
		    if ( $a['position'] == $b['position'] ) return 0;
		    return ( $a['position'] < $b['position'] ) ? -1 : 1;
		}
	}

	foreach( $posts as $post ) {

		$test = get_the_terms( $post->ID, $term );
		// if the product has no value set for the attribute, add the value!
		if( empty( $test ) || is_wp_error( $test ) ) {
			
			// set the taxonomy!
			wp_set_object_terms( $post->ID, $value, $term );

			// Set the product attributes meta data
			$product = get_product( $post->ID );

			// get attributes
			$attributes = $product->get_attributes();

			$attributes[ $term ] = array(
		 		'name' 			=> $term,
		 		'value' 		=> '',
		 		'position' 		=> '1',
		 		'is_visible' 	=> $is_visible,
		 		'is_variation' 	=> '0',
		 		'is_taxonomy' 	=> '1'
		 	);

			uasort( $attributes, 'attributes_cmp' );

			update_post_meta( $post->ID, '_product_attributes', $attributes );

			$to_output->posts[] = $post->post_title . ' - Added attribute';
		}
	}

	$to_output->new_offset = $offset;

	echo json_encode( $to_output );
	
	die();
}

/**
 * Returns the sorting woocommerce options
 * @param  string $option option name
 * @return mixed         the value
 */
function sorting_woocommerce_get_option( $option ) {
	$result = get_transient( $option );
	if( empty( $result ) ) {
		$result = get_option( $option );
		set_transient( $option, $result, 0 );
	}
	return $result;
}

add_shortcode( 'sorting_woocommerce_icon', 'sorting_woocommerce_reverse_sorting' );

/**
 * Puts the link for the reverse sorting instead of the shortcode [sorting_woocommerce_icon]
 * @return [string] The html to replace the shortcode in the page.
 */
function sorting_woocommerce_reverse_sorting() {

	global $sorting_woocommerce_options;
	
	// The footer script that handles the icon's actions
	add_action( 'wp_footer', 'sorting_woocommerce_footer_script' );

	// What to display? The icon or the default icon?
	if(isset( $_GET['orderby'] ) ){
		$orderby = woocommerce_clean( $_GET['orderby'] );
	} else {
		// Default sorting
		$orderby = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	}

	// Which sorting icon: the DESC or the ASC?
	$prefix = $default = '';
	if( str_ends_with_sorting_woocommerce( $orderby, '_DESC') ) {
		$prefix = 'asc';
		$default = '&uarr;'; // the default icon in case the user entered an empty url
	} elseif( str_ends_with_sorting_woocommerce( $orderby, '_ASC') ) {
		$prefix = 'desc';
		$default = '&darr;';
	}

	// Finally, the HTML to return
	$display = '';
	if( empty( $sorting_woocommerce_options[ $prefix . '_sorting_icon'] ) ) {
		$display = $default;
	} else {
		$display = '<img src="' . $sorting_woocommerce_options[ $prefix . '_sorting_icon'] . '" class="sorting_woocommerce_icon"/>';
	}

	return '<a href="javascript:;" style="display:none" onclick="sorting_woocommerce_reverse(false)" id="sorting_woocommerce_icon">' . apply_filters( 'sorting_woocommerce_icon', $display ) . '</a>';
}

/**
 * Outputs the javascript function in the footer of the pages. This is called by the reverse link.
 * @return void
 */
function sorting_woocommerce_footer_script() {
	global $sorting_woocommerce_options;
	# For reversing
	if(isset( $_GET['orderby'] ) ){
		$orderby = woocommerce_clean( $_GET['orderby'] );
	} else {
		// Default sorting
		$orderby = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	}
	?>
	<script type="text/javascript">
		function sorting_woocommerce_reverse( init ) {
			var original_val = val = '<?php echo $orderby; ?>';
			var all_sorting_options = <?php echo json_encode( $sorting_woocommerce_options['all_sorting_options'] ); ?>;
			// We will be searching for val later
			if( val.match(/_DESC$/) ) {
				val = val.replace('DESC', 'ASC');
			} else {
				val = val.replace('ASC', 'DESC');
			}
			// If no replacement happened, it's not our custom sorting option - don't do anything
			if( original_val === val ) {
				return;
			}
			var found = false;
			//jQuery('.orderby:first option').each(function(){
			jQuery.each( all_sorting_options, function(key, value){
				my_val = key;
				if( my_val === val ) {
					if( init ) {
						found = true;
					} else {
						jQuery('.orderby:first').append('<option value="'+my_val+'"></option>');
						jQuery('.orderby:first').val( my_val );
						jQuery('.orderby:first').change();
					}
				}
			});
			if( init && found ) {
				jQuery('#sorting_woocommerce_icon').show();
			}
		}

		jQuery(document).ready(function(){
			sorting_woocommerce_reverse(true);
		});
	</script>
	<?php
}

function str_ends_with_sorting_woocommerce( $haystack, $needle ) {
	 return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

/**
 * Var_dump the variable passed to a txt file. Used for debugging.
 * @param  [any] $var [The variable to be written to the file]
 */
// function sorting_woocommerce_debug( $var ) {
// 	file_put_contents( sorting_woocommerce_path . '/debug.txt', '#######################################################' . PHP_EOL, FILE_APPEND );
// 	file_put_contents( sorting_woocommerce_path . '/debug.txt', var_export($var, true) . PHP_EOL, FILE_APPEND );
// }


?>