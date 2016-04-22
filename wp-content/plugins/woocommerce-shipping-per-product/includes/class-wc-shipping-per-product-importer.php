<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Importer' ) ) {

	class WC_Shipping_Per_Product_Importer extends WP_Importer {

		var $id;
		var $file_url;
		var $import_page;
		var $delimiter;
		var $posts = array();
		var $imported;
		var $skipped;

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			$this->import_page = 'woocommerce_per_product_shipping_csv';
		}

		/**
		 * Registered callback function for the WordPress Importer
		 *
		 * Manages the three separate stages of the CSV import process
		 */
		function dispatch() {
			$this->header();

			if ( ! empty( $_POST['delimiter'] ) )
				$this->delimiter = stripslashes( trim( $_POST['delimiter'] ) );

			if ( ! $this->delimiter )
				$this->delimiter = ',';

			$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
			switch ( $step ) {
				case 0:
					$this->greet();
					break;
				case 1:
					check_admin_referer( 'import-upload' );
					if ( $this->handle_upload() ) {

						if ( $this->id )
							$file = get_attached_file( $this->id );
						else
							$file = ABSPATH . $this->file_url;

						add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

						if ( function_exists( 'gc_enable' ) )
							gc_enable();

						@set_time_limit(0);
						@ob_flush();
						@flush();

						$this->import( $file );
					}
					break;
			}
			$this->footer();
		}

		/**
		 * format_data_from_csv function.
		 *
		 * @access public
		 * @param mixed $data
		 * @param mixed $enc
		 * @return void
		 */
		function format_data_from_csv( $data, $enc ) {
			return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
		}

		/**
		 * import function.
		 *
		 * @access public
		 * @param mixed $file
		 * @return void
		 */
		function import( $file ) {
			global $woocommerce, $wpdb;

			$this->imported = $this->skipped = 0;

			if ( ! is_file($file) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-shipping-per-product' ) . '</strong><br />';
				echo __( 'The file does not exist, please try again.', 'woocommerce-shipping-per-product' ) . '</p>';
				$this->footer();
				die();
			}

			$new_rates = array();

			if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) {

				$header = fgetcsv( $handle, 0, $this->delimiter );

				if ( sizeof( $header ) == 6 ) {

					$loop = 0;

					while ( ( $row = fgetcsv( $handle, 0, $this->delimiter ) ) !== FALSE ) {

						list( $post_id, $country, $state, $postcode, $cost, $item_cost ) = $row;

						$country = trim( strtoupper( $country ) );
						$state   = trim( strtoupper( $state ) );

						if ( $country == '*' )
							$country = '';
						if ( $state == '*' )
							$state = '';
						if ( $postcode == '*' )
							$postcode = '';

						$wpdb->insert(
							$wpdb->prefix . "woocommerce_per_product_shipping_rules",
							array(
								'rule_country' 		=> esc_attr( $country ),
								'rule_state' 		=> esc_attr( $state ),
								'rule_postcode' 	=> esc_attr( $postcode ),
								'rule_cost' 		=> esc_attr( $cost ),
								'rule_item_cost' 	=> esc_attr( $item_cost ),
								'rule_order'		=> $loop,
								'product_id'		=> absint( $post_id )
							)
						);

						$loop++;
						$this->imported++;
				    }

				} else {

					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-shipping-per-product' ) . '</strong><br />';
					echo __( 'The CSV is invalid.', 'woocommerce-shipping-per-product' ) . '</p>';
					$this->footer();
					die();

				}

			    fclose( $handle );
			}

			// Show Result
			echo '<div class="updated settings-error below-h2"><p>
				'.sprintf( __( 'Import complete - imported <strong>%s</strong> shipping rates and skipped <strong>%s</strong>.', 'woocommerce-shipping-per-product' ), $this->imported, $this->skipped ).'
			</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		function import_end() {
			echo '<p>' . __( 'All done!', 'woocommerce-shipping-per-product' ) . '</p>';
			do_action( 'import_end' );
		}

		/**
		 * Handles the CSV upload and initial parsing of the file to prepare for
		 * displaying author import options
		 *
		 * @return bool False if error uploading or invalid file, true otherwise
		 */
		function handle_upload() {

			if ( empty( $_POST['file_url'] ) ) {

				$file = wp_import_handle_upload();

				if ( isset( $file['error'] ) ) {
					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-shipping-per-product' ) . '</strong><br />';
					echo esc_html( $file['error'] ) . '</p>';
					return false;
				}

				$this->id = (int) $file['id'];

			} else {

				if ( file_exists( ABSPATH . $_POST['file_url'] ) ) {

					$this->file_url = esc_attr( $_POST['file_url'] );

				} else {

					echo '<p><strong>' . __( 'Sorry, there has been an error.', 'woocommerce-shipping-per-product' ) . '</strong></p>';
					return false;

				}

			}

			return true;
		}

		/**
		 * header function.
		 *
		 * @access public
		 * @return void
		 */
		function header() {
			echo '<div class="wrap">';
			screen_icon();
			echo '<h2>' . __( 'Import Per-product Shipping Rates', 'woocommerce-shipping-per-product' ) . '</h2>';
		}

		/**
		 * footer function.
		 *
		 * @access public
		 * @return void
		 */
		function footer() {
			echo '</div>';
		}

		/**
		 * greet function.
		 *
		 * @access public
		 * @return void
		 */
		function greet() {
			global $woocommerce;

			echo '<div class="narrow">';
			echo '<p>' . __( 'Hi there! Upload a CSV file containing per-product shipping rates to import the contents into your shop. Choose a .csv file to upload, then click "Upload file and import".', 'woocommerce-shipping-per-product' ).'</p>';

			echo '<p>' . __( 'Rates need to be defined with columns in a specific order (6 columns). Product ID, Country Code, State Code, Postcode, Cost, Item Cost', 'woocommerce-shipping-per-product' ) . '</p>';

			$action = 'admin.php?import=woocommerce_per_product_shipping_csv&step=1';

			$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
			$size = wp_convert_bytes_to_hr( $bytes );
			$upload_dir = wp_upload_dir();
			if ( ! empty( $upload_dir['error'] ) ) :
				?><div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:'); ?></p>
				<p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
			else :
				?>
				<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
					<table class="form-table">
						<tbody>
							<tr>
								<th>
									<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label>
								</th>
								<td>
									<input type="file" id="upload" name="import" size="25" />
									<input type="hidden" name="action" value="save" />
									<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
									<small><?php printf( __('Maximum size: %s' ), $size ); ?></small>
								</td>
							</tr>
							<tr>
								<th><label><?php _e( 'Delimiter', 'woocommerce-shipping-per-product' ); ?></label><br/></th>
								<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" class="button" value="<?php esc_attr_e( 'Upload file and import' ); ?>" />
					</p>
				</form>
				<?php
			endif;

			echo '</div>';
		}

		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 * @return int 60
		 */
		function bump_request_timeout( $val ) {
			return 60;
		}
	}
}