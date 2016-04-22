<?php

/**
 * Product Class
 *
 * Handles the products
 * 
 * @class 	  WC_Pos_Product
 * @package   WooCommerce POS
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      
 */

class WC_Pos_Product {

	/** @var string Contains the thumbnail size. */
	public $thumb_size;

	/** @var array Contains an array of tax rates, by tax class. */
	public $tax_rates = array();


	public function __construct() {

		// try and increase server timeout
		$this->increase_timeout();

		// init variables
		$this->thumb_size = get_option( 'shop_thumbnail_image_size', array( 'width' => 90, 'height' => 90 ) );

		// we're going to manipulate the wp_query to display products
		add_action( 'pre_get_posts', array( $this, 'get_product_variations' ) );

		#add_filter( 'posts_where', array( $this, 'hide_variations_without_parents') );

		// and we're going to filter on the way out
		add_filter( 'woocommerce_api_product_response', array( $this, 'filter_product_response' ) );

	}

	/**
	 * WC REST API can timeout on some servers
	 * This is an attempt t o increase the timeout limit
	 * TODO: is there a better way?
	 */
	public function increase_timeout() { 
		$timeout = 6000;
		if( !ini_get( 'safe_mode' ) )
			@set_time_limit( $timeout );

		@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );
		@ini_set( 'max_execution_time', (int)$timeout );
	}

	/**
	 * Get all the product ids 
	 * @return array
	 * TODO: there is a problem with updates returning the wrong result.
	 * product_variations do not modify their date in the database,
	 * either need a workaround based on parent_id, or submit change to wc
	 */
	public function get_all_ids() {

		// set up the args
		$args = array(
			'posts_per_page' =>  -1,
			'post_type' => array( 'product', 'product_variation' ),
			'tax_query' => array(
				array(
					'taxonomy' 	=> 'product_type',
					'field' 	=> 'slug',
					'terms' 	=> array( 'variable' ),
					'operator'	=> 'NOT IN'
				)
			),
			'fields'        => 'ids', // only get post IDs.
		);

		return(get_posts( $args ));

	}

	/**
	 * Get all the things
	 * @param  $query 		the wordpress query
	 */
	public function get_product_variations( $query ) {

		if(isset($_GET['version']) && $_GET['version'] == 'websql')
			$query->set( 'posts_per_page', -1 ); 


        
	}

	public function hide_variations_without_parents($where)
	{
		global $wpdb;
		$where .= " AND IF({$wpdb->posts}.post_type = 'product_variation', {$wpdb->posts}.post_parent, {$wpdb->posts}.ID ) != 0 ";
		return $where;
	}


	/**
	 * Filter product response from WC REST API for easier handling by backbone.js
	 * - unset unnecessary data
	 * - flatten some nested arrays
	 * @param  array $product_data
	 * @return array modified data array $product_data
	 */
	public function filter_product_response( $product_data ) {
		$removeKeys = array(
			'average_rating',
			'cross_sell_ids',
			'description',
			'dimensions', 
			'download_expiry',
			'download_limit',
			'download_type',
			'downloads',
			'images',
			'parent',
			'rating_count',
			'related_ids',
			'reviews_allowed',
			'shipping_class',
			'shipping_class_id',
			'shipping_required',
			'shipping_taxable',
			'short_description',
			'tags',
			'upsell_ids',
			'weight',
			'created_at',
			'updated_at',			
		);
		// flatten variable data
		$product_data['categories_ids'] = wp_get_post_terms( $product_data['id'], 'product_cat', array( "fields" => "ids" ) );

		if( $product_data['type'] == 'variable' && isset($product_data['variations'])) {


			$product = new WC_Product($product_data['id']);
			$attributes = array();

			foreach ( $product->get_attributes() as $attribute ) {

				// taxonomy-based attributes are comma-separated, others are pipe (|) separated
				if ( $attribute['is_taxonomy'] ) {
					$options = explode( ',', $product->get_attribute( $attribute['name'] ) );
				} else {
					$options = explode( '|', $product->get_attribute( $attribute['name'] ) );
				}

				$attributes[] = array(
					'name'      => ucwords( str_replace( 'pa_', '', $attribute['name'] ) ),
					'title'     => esc_html( wc_attribute_label( $attribute['name'] ) ),
					'position'  => $attribute['position'],
					'visible'   => (bool) $attribute['is_visible'],
					'variation' => (bool) $attribute['is_variation'],
					'options'   => array_map( 'trim', $options ),
				);
			}
			$product_data['attributes'] = $attributes;
			$parent_attr = array();
			if ( ! empty( $product_data['attributes'] ) ) {

					foreach ( $product_data['attributes'] as $attribute_key => $attribute ) {

						$tax = get_taxonomy( sanitize_title($attribute['name']) );
						if (empty($tax)){
							$tax = get_taxonomy( 'pa_'.sanitize_title($attribute['name']) );
						}
						if (!empty($tax)){
							$parent_attr[ $attribute_key ]['taxonomy'] = $tax->name;
						}else{
							$parent_attr[ $attribute_key ]['taxonomy'] = $attribute['name'];
						}
						$parent_attr[ $attribute_key ]['name']     = $attribute['name'];
						$parent_attr[ $attribute_key ]['title']    = esc_html( wc_attribute_label( $attribute['name'] ) );


						if ( ! empty( $attribute['options'] ) && is_array( $attribute['options'] ) ) {

							$k = 0; foreach ( $attribute['options'] as $option_key => $option ) {

								unset( $product_data['attributes'][ $attribute_key ]['options'][ $option_key ] );

								$parent_attr[ $attribute_key ]['options'][$k]['name'] = $option;

								if (!empty($tax)){
									$term = get_term_by('name', $option, $tax->name );
									if($term)
										$parent_attr[ $attribute_key ]['options'][$k]['slug'] = $term->slug;
								}else{
									$parent_attr[ $attribute_key ]['options'][$k]['slug'] = $option;								
								}

								//$product_data['parent_attr'][ $attribute_key ]['options']['option'][] = array( $option );
								$k++;
							}
						}
					}
				}


			foreach ($product_data['variations'] as $v_key => $variation) {

				$product_data['variations'][$v_key]['categories_ids'] = $product_data['categories_ids'];

				$product_data['variations'][$v_key]['featured_src'] = $variation['image'][0]['src'];

				// use thumbnails for images or placeholder
				/*if( $product_data['variations'][$v_key]['featured_src'] ) {
					$thumb_suffix = '-'.$this->thumb_size['width'].'x'.$this->thumb_size['height'];
					$product_data['variations'][$v_key]['featured_src'] = preg_replace('/(\.gif|\.jpg|\.png)/', $thumb_suffix.'$1', $product_data['variations'][$v_key]['featured_src']);

				} else {
					$product_data['variations'][$v_key]['featured_src'] = wc_placeholder_img_src();
				}*/

				if( !$product_data['variations'][$v_key]['featured_src'] ) {
					$product_data['variations'][$v_key]['featured_src'] = wc_placeholder_img_src();
				}


				$product_data['variations'][$v_key]['parent_id']    = $product_data['id'];

				$product = get_product( $variation['id'] );
				$product_data['variations'][$v_key]['pr_inc_tax']  = $product->get_price_including_tax();
				$product_data['variations'][$v_key]['pr_excl_tax'] = $product->get_price_excluding_tax();
				$product_data['price'] = wc_format_decimal( $product->get_price(), absint(get_option('woocommerce_price_num_decimals')) );

				// add special key for barcode, defaults to sku
				// TODO: add an option for any meta field
				$product_data['variations'][$v_key]['barcode'] = $variation['sku'];
				$product_data['variations'][$v_key]['f_title'] = '';
				if (!empty($variation['sku']))
					$product_data['variations'][$v_key]['f_title'] .= $variation['sku'] . ' &ndash; ';

				$product_data['variations'][$v_key]['f_title'] .= $product_data['title'];
				$product_data['variations'][$v_key]['title']    = $product_data['title'];

				

				if ( !empty($variation['attributes']) && is_array($variation['attributes'])){

					$product_var = array();
					$product_data['variations'][$v_key]['f_title'] .= ' &ndash; ';
					foreach ($variation['attributes'] as $k => $value) {
						if(isset($value['option'])){
						    $product_var[] = $value['option'];
							$product_data['variations'][$v_key]['attributes'][$k]['title'] = esc_html( wc_attribute_label( $value['name'] ) );
						}
						if(isset($value['options'])){
							$product_data['variations'][$v_key]['attributes'] = array();
							break;
						}
					}
					if(!empty($product_var))
						$product_data['variations'][$v_key]['f_title'] .= implode(', ', $product_var);
					
				}			

				if (!empty($variation['price'])){
					$product_data['variations'][$v_key]['f_title'] .= ' &ndash; ' . wc_price( $variation['price'] );
					$product_data['variations'][$v_key]['price_html'] = wc_price( $variation['price'] );
				}

				$product_data['variations'][$v_key]['parent_attr'] = $parent_attr;

				
				foreach($removeKeys as $key) {
					unset($product_data['variations'][$v_key][$key]);
				}
			}
		}

		$product_data['parent_attr'] = array();

		

		// use thumbnails for images or placeholder
		/*if( $product_data['featured_src'] ) {
			$thumb_suffix = '-'.$this->thumb_size['width'].'x'.$this->thumb_size['height'];
			$product_data['featured_src'] = preg_replace('/(\.gif|\.jpg|\.png)/', $thumb_suffix.'$1', $product_data['featured_src']);

		} else {
			$product_data['featured_src'] = wc_placeholder_img_src();
		}*/
		if( !$product_data['featured_src'] ) {
			$product_data['featured_src'] = wc_placeholder_img_src();
		}

		$product = get_product( $product_data['id'] );
		$product_data['pr_inc_tax'] = $product->get_price_including_tax();
		$product_data['pr_excl_tax'] = $product->get_price_excluding_tax();
		$product_data['price'] = wc_format_decimal( $product->get_price(), absint(get_option('woocommerce_price_num_decimals')) );

		// add special key for barcode, defaults to sku
		// TODO: add an option for any meta field
		$product_data['barcode'] = $product_data['sku'];
		$product_data['f_title'] = '';
		if (!empty($product_data['sku']))
			$product_data['f_title'] .= $product_data['sku'] . ' &ndash; ';

		$product_data['f_title'] .= $product_data['title'];

		$prefix = get_option('woocommerce_price_display_suffix');
		$product_data['price_html'] = str_replace('$prefix', '', wc_price($product_data['price']));

		if ( !empty($product_data['attributes']) && is_array($product_data['attributes'])){

			$product_var = array();
			$product_data['f_title'] .= ' &ndash; ';
			foreach ($product_data['attributes'] as $k => $value) {
				if(isset($value['option'])){
				  $product_var[] = $value['option'];
				}
				if(isset($value['options'])){
					$product_data['attributes'] = array();
					break;
				}
			}
			if(!empty($product_var))
				$product_data['f_title'] .= implode(', ', $product_var);
			
		}			

		if (!empty($product_data['price']))
			$product_data['f_title'] .= ' &ndash; ' . wc_price( $product_data['price'] );


		
		

		// remove some unnecessary keys
		// - saves storage space in IndexedDB
		// - saves bandwidth transferring the data
		// eg: removing 'description' reduces object size by ~25%
		
		foreach($removeKeys as $key) {
			unset($product_data[$key]);
		}
		return $product_data;
	}

	

}