<?php

/**
 * Stores Admin
 *
 * @class    WC_POS_Pro_Admin_Stores
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_Stores {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Admin menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ),  99 );
		add_action( 'parent_file', array( $this, 'admin_submenu_correction' ) );
		add_action( 'current_screen', array( $this, 'conditional_init' ) );

  }

	/**
	 * @param $current_screen
	 */
	public function conditional_init( $current_screen ) {

		if( $current_screen->id == 'edit-stores' ) {

			// Store admin columns
			add_action( 'manage_stores_posts_custom_column', array( $this, 'stores_columns_content' ), 10, 2 );
			add_filter( 'manage_edit-stores_columns', array( $this, 'stores_columns_header' ) );

			// Stores admin scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_store_edit_scripts' ) );
		}

		if( $current_screen->id == 'stores' ) {

			// Stores meta boxes
			add_action( 'add_meta_boxes_stores', array( $this, 'add_meta_boxes' ), 30 );
			add_action( 'save_post_stores', array( $this, 'save_stores_meta' ) );
			add_filter( 'default_hidden_meta_boxes', array( $this, 'default_hidden_meta_boxes' ), 10, 2 );
			add_filter( 'admin_post_thumbnail_html', array( $this, 'admin_post_thumbnail_html' ), 10, 2 );
			add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );

			// Stores admin scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_store_scripts' ) );
		}

	}

	/**
	 * Rearrange the admin menu
	 */
	public function admin_menu() {
		global $submenu;

		$submenu[WC_POS_PLUGIN_NAME][0][0] = __( 'Stores', 'woocommerce-pos-pro' );
		$submenu[WC_POS_PLUGIN_NAME][0][2] = 'edit.php?post_type=stores';

		$stores = get_pages( array( 'post_type' => 'stores' ) );
		if( count( $stores ) > 0 )
			unset( $submenu[WC_POS_PLUGIN_NAME][500] );

	}

	/**
	 * Tidy admin menu
	 *
	 * @param $parent_file
	 *
	 * @return string
	 */
	public function admin_submenu_correction( $parent_file ) {
		global $current_screen, $submenu_file;

		if ( $current_screen->post_type == 'stores' ) {
			$submenu_file = 'edit.php?post_type=stores';
			$parent_file = WC_POS_PLUGIN_NAME;
		}

		return $parent_file;
	}

	/**
	 * Content for the stores admin table
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function stores_columns_content( $column, $post_id ) {
		global $post;

		$country 	= get_post_meta( $post->ID, '_country', true );
		$state 		= get_post_meta( $post->ID, '_state', true );
		$city 		= get_post_meta( $post->ID, '_city', true );
		$postcode 	= get_post_meta( $post->ID, '_postcode', true );

		switch( $column ) {

			case 'address' :

				/* Get the post meta. */
//				$company = get_post_meta( $post_id, '_company', true );
//				if( empty($company) ) $company = get_bloginfo( 'name' );

				$address = array(
//					'first_name'=> '',
//					'last_name'=> '',
//					'company' 	=> get_the_title( $post_id ),
					'address_1' => get_post_meta( $post_id, '_address_1', true ),
					'address_2' => get_post_meta( $post_id, '_address_2', true ),
					'city'		=> $city,
					'state' 	=> $state,
					'postcode' 	=> $postcode,
					'country' 	=> $country
				);

				echo WC()->countries->get_formatted_address( $address );

				break;

			case 'view_pos' :

				echo '<a class="button-primary" href="'. wc_pos_url('?store='.$post->ID) .'">'. __( 'Open POS', 'woocommerce-pos-pro' ) .'</a>';

				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}

	/**
	 * Change the default columns for the stores admin page
	 *
	 * @return array
	 */
	public function stores_columns_header() {

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Store', 'woocommerce-pos-pro' ),
			/* translators: woocommerce */
			'address' => __( 'Address', 'woocommerce' ),
			'view_pos' => ''
		);

		return $columns;

	}

	/**
	 * Add meta boxes to the edit stores page
	 */
	public function add_meta_boxes( $post ) {

		add_meta_box(
			'preview-receipt',
			__( 'Preview Receipt', 'woocommerce-pos-pro' ),
			array( $this, 'render_preview_receipt'),
			'stores', 'side', 'default'
		);

		add_meta_box(
			'tax-rate',
			/* translators: woocommerce */
			__( 'Tax Rate', 'woocommerce' ),
			array( $this, 'render_tax_rate'),
			'stores', 'side', 'default'
		);

		add_meta_box(
			'opening-hours',
			__( 'Opening Hours', 'woocommerce-pos-pro' ),
			array( $this, 'render_opening_hours'),
			'stores', 'side', 'default'
		);

		remove_meta_box( 'postimagediv', 'stores', 'side' );

		add_meta_box(
			'postimagediv',
			__( 'Logo', 'woocommerce-pos-pro' ),
			'post_thumbnail_meta_box',
			'stores', 'normal', 'default'
		);

		add_meta_box(
			'store-address',
			/* translators: woocommerce */
			__( 'Address', 'woocommerce' ),
			array( $this, 'render_store_address'),
			'stores', 'normal', 'default'
		);

		add_meta_box(
			'contact-details',
			/* translators: wordpress */
			__( 'Contact Info' ),
			array( $this, 'render_contact_details'),
			'stores', 'normal', 'default'
		);

		add_meta_box(
			'receipt-options',
			__( 'Receipt Options', 'woocommerce-pos-pro' ),
			array( $this, 'render_receipt_options'),
			'stores', 'normal', 'default'
		);

	}

	/**
	 * Processes all $_POST data starting with 'pos_'
	 *
	 * @param $post_id
	 */
	public function save_stores_meta( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['wc_pos_stores_nonce'] ) )
			return $post_id;

		$nonce = $_POST['wc_pos_stores_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wc_pos_stores' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;

		// get all the pos data
		$pos_POST = array_intersect_key($_POST, array_flip(preg_grep('/^pos_/', array_keys($_POST))));

		// save the data
		foreach( $pos_POST as $key => $value ) {
			if( is_array($value) ) {
				update_post_meta( $post_id, substr($key, 3), $value );
			} elseif( !empty($value) ) {
        $allowed_html = array(
          'strong' => array(),
          'em' => array(),
          'b' => array(),
          'i' => array(),
          'br' => array()
        );
        $filtered_value = wp_kses($value, $allowed_html);
				update_post_meta( $post_id, substr($key, 3), $filtered_value );
			} else {
				delete_post_meta( $post_id, substr($key, 3) );
			}
		}

	}

	/**
	 * Change featured image links
	 *
	 * @param $content
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function admin_post_thumbnail_html( $content, $post_id ) {
		if( 'stores' !== get_post_type( $post_id ) ) return $content;
		$content = str_replace(__('Set featured image'), __('Set store logo', 'woocommerce-pos-pro'), $content);
		$content = str_replace(__('Remove featured image'), __('Remove store logo', 'woocommerce-pos-pro'), $content);
		return $content;
	}

	/**
	 * Change media modal text
	 *
	 * @param $strings
	 * @param $post_id
	 *
	 * @return
	 */
	public function media_view_strings( $strings, $post_id ) {
		if( 'stores' !== get_post_type( $post_id ) ) return $strings;
		$strings['setFeaturedImageTitle'] 	= __( 'Set Store Logo', 'woocommerce-pos-pro' );
		$strings['setFeaturedImage'] 		= __( 'Set store logo', 'woocommerce-pos-pro' );
		return $strings;
	}

	/**
	 * Content for Store Address meta box
	 *
	 * @param $post
	 * @param $metabox
	 */
	public function render_store_address( $post, $metabox ) {

		$country = get_post_meta( $post->ID, '_country', true ) ? get_post_meta( $post->ID, '_country', true ) : WC()->countries->get_base_country() ;
		$state = get_post_meta( $post->ID, '_state', true ) ? get_post_meta( $post->ID, '_state', true ) : WC()->countries->get_base_state() ;
		$countries = WC()->countries->get_countries();
		$states = WC()->countries->get_states($country);

		include 'views/stores/address.php';
	}

	/**
	 * Content for Contact Details meta box
	 *
	 * @param $post
	 * @param $metabox
	 */
	public function render_contact_details( $post, $metabox ) {
		include 'views/stores/contact.php';
	}

	/**
	 * Content for Opening Hours meta box
	 *
	 * @param $post
	 * @param $metabox
	 */
	public function render_opening_hours( $post, $metabox ) {

		$days = array(
			/* translators: wordpress */ __( 'Monday' ),
			/* translators: wordpress */ __( 'Tuesday' ),
			/* translators: wordpress */ __( 'Wednesday' ),
			/* translators: wordpress */ __( 'Thursday' ),
			/* translators: wordpress */ __( 'Friday' ),
			/* translators: wordpress */ __( 'Saturday' ),
			/* translators: wordpress */ __( 'Sunday' )
		);

		$hours = get_post_meta( $post->ID, '_opening_hours', true );

		include 'views/stores/opening-hours.php';
	}

	/**
	 * Render Receipt Options
	 *
	 * @param $post
	 * @param $metabox
	 */
	public function render_receipt_options( $post, $metabox ) {
		include 'views/stores/receipt-options.php';
	}

	/**
	 * Render Receipt Button
	 *
	 * @param $post
	 * @param $metabox
	 */
	public function render_preview_receipt( $post, $metabox ) {

		// nonce
		wp_nonce_field( 'wc_pos_stores', 'wc_pos_stores_nonce' );

		// button
		?>
		<p><?php printf( __( 'Please <a href="%s">read the docs</a> for information on how to customize your POS receipts.', 'woocommerce-pos-pro' ), 'http://woopos.com.au/docs/receipts/' ); ?></p>
		<a class="button-primary" href="<?php echo wc_pos_url('?store='.$post->ID.'#print'); ?>" id="wc-pos-print-template" target="_blank"><?php /* translators: woocommerce-pos */ _e( 'View Sample Receipt', 'woocommerce-pos' ); ?></a>
    <?php
	}

	/**
	 * Render Tax Rates
	 *
	 * @param $post
	 * @param $metabox
	 */
	public function render_tax_rate( $post, $metabox ) {

    $tax_address  = get_post_meta( $post->ID, '_tax_address', true );
    $base_rates   = WC_POS_Tax::tax_rates();
    $tax_rates    = WC_POS_Pro_Tax::tax_rates($base_rates, $post->ID, $tax_address);
    $tax_labels   = WC_POS_Tax::tax_classes();

    include 'views/stores/tax-rate.php';

	}

	/**
	 * Hide default meta boxes
	 *
	 * @param $hidden
	 * @param $screen
	 *
	 * @return array
	 */
	public function default_hidden_meta_boxes( $hidden, $screen ) {
		if ( 'stores' == $screen->id ) {
			$hidden = array( 'postcustom', 'slugdiv', 'pageparentdiv' );
		}
		return $hidden;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 */
	public function enqueue_store_edit_scripts() {

		$css = '
			.column-view_pos { width:100px; }
			.column-tax_rates { width:30%; }
			.column-tax_rates table { width:100%; }
			.column-tax_rates table th { font-weight:normal; font-size:13px; padding:2px; }
			.column-tax_rates table td { font-size:13px; padding:2px; }
			#the-list .column-tax_rates table th { border-bottom:1px solid #eee !important; }
		';

		if ( isset( $css ) ) wp_add_inline_style( 'wp-admin', $css );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 */
	public function enqueue_store_scripts() {

		$css = '
			.form-group { width:100%; display:table }
			.form-group label { color:#777; font-weight:bold; display:table-cell; width:150px; }
			.form-group input { width:100%; }
			.form-group select { width:100%; }
			.form-group input.small-input { width:100px; }
			.form-group textarea { width:100%; height:80px; }

			#opening-hours td { vertical-align:top; }
			#opening-hours .hours { width:140px; }
			#opening-hours label { color:#777; font-weight:bold; line-height:27px; }
			#opening-hours input { width:50px; }
			#opening-hours textarea { width:100%;height:50px; }
			#opening-hours i { float:right; line-height:27px; }
			#opening-hours a { text-decoration:none; }
			#opening-hours a.remove-hours { color:#a00; }
			#opening-hours a.remove-hours:hover { color:red; }

			#tax-rate label { display:block; margin:5px 0; }
			#tax-rate table { width:100%; margin-top:10px; border-collapse: collapse; }
			#tax-rate table th { border-top:1px solid #eee; border-bottom:1px solid #eee; padding:2px; }
			#tax-rate table td { text-align:center; padding:2px; }
			#tax-rate table tr.alternate td { color: #b2b2b2 }

			@media only screen and (min-width: 850px) {
				.form-group { margin-top:10px; }
				.form-group label { display:block; margin-bottom:3px; }
				.form-col-2 .form-group { float:left; width:49%; }
				.form-col-2 .form-group:last-of-type { float:right; }
			}
		';

		if ( isset( $css ) ) wp_add_inline_style( 'wp-admin', $css );

	}

}