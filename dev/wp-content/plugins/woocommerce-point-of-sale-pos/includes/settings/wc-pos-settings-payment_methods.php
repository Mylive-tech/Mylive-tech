<?php
/**
 * WooCommerce POS General Settings
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/settings
 * @category	Class
 * @since     0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_POS_Settings_Payment_Methods' ) ) :

/**
 * WC_POS_Settings_General
 */
class WC_POS_Settings_Payment_Methods extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'payment_methods_pos';
		$this->label = __( 'Payment Methods', 'woocommerce' );

		add_filter( 'wc_pos_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'wc_pos_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_admin_field_installed_payment_gateways', array( $this, 'installed_payment_gateways_setting' ) );
		add_action( 'wc_pos_settings_save_' . $this->id, array( $this, 'save' ) );

	}

		/**
	 * Output installed payment gateway settings.
	 *
	 * @access public
	 * @return void
	 */
	public function installed_payment_gateways_setting() {
		?>
		<tr valign="top">
	    <td class="forminp" colspan="2">
	    <style>
	    .wc_gateways th {width: auto;}
	    </style>
				<table class="wc_gateways widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								$columns = array(
									'enabled'   => __( 'Enabled', 'woocommerce' ),
									'name'     => __( 'Gateway', 'woocommerce' )
								);

								foreach ( $columns as $key => $column ) {
									echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
			        	<?php
			        	$enabled_gateways = get_option( 'pos_enabled_gateways', array() );

			        	foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
			        		
			        		echo '<tr>';

			        		foreach ( $columns as $key => $column ) {
								switch ( $key ) {
									case 'enabled' :
									$checked = in_array(esc_attr( $gateway->id ), $enabled_gateways);
										echo '<td width="1%" class="default">
					        				<input type="checkbox" name="pos_enabled_gateways[]" value="' . esc_attr( $gateway->id ) . '" ' . checked( $checked, true, false ) . ' />
					        				<input type="hidden" name="pos_exist_gateways[]" value="' . esc_attr( $gateway->id ) . '" />
					        			</td>';
									break;
									case 'name' :
										echo '<td class="name">
					        				' . $gateway->get_title() . '
					        			</td>';
									break;
									default :
										do_action( 'woocommerce_payment_gateways_setting_column_' . $key, $gateway );
									break;
								}
							}

							echo '</tr>';
			        	}
			        	$checked = in_array(esc_attr( 'pos_chip_pin' ), $enabled_gateways);
			        	?>
			        	<tr>
			        		<td width="1%" class="default">
			        			<input type="checkbox" name="pos_enabled_gateways[]" value="pos_chip_pin" <?php checked( $checked, true, true ); ?> />
		        				<input type="hidden" name="pos_exist_gateways[]" value="pos_chip_pin" />
			        		</td>
			        		<td class="name">
			        		<?php _e('Chip & PIN', 'wc_point_of_sale'); ?>
			        		</td>
			        	</tr>
					</tbody>
				</table>
				<p><?php  _e( 'To configure each payment gateway, please go to the Checkout tab under WooCommerce > Settings or click <a href="admin.php?page=wc-settings&tab=checkout">here</a>', 'wc_point_of_sale' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		global $woocommerce;
		return apply_filters( 'woocommerce_point_of_sale_tax_settings_fields', array(

			array( 'title' => __( 'Payment Gateways', 'woocommerce' ), 'type' => 'title', 'id' => 'payment_gateways_options' ),

			array( 'type' => 'installed_payment_gateways' ),			

			array( 'type' => 'sectionend', 'id' => 'payment_gateways_options'),

		) ); // End general settings

	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();

		$pos_enabled_gateways = ( isset( $_POST['pos_enabled_gateways'] ) ) ?  $_POST['pos_enabled_gateways'] : array();
		update_option( 'pos_enabled_gateways', $pos_enabled_gateways );

		$pos_exist_gateways = ( isset( $_POST['pos_exist_gateways'] ) ) ?  $_POST['pos_exist_gateways'] : array();
		update_option( 'pos_exist_gateways', $pos_exist_gateways );
		
		WC_Pos_Settings::save_fields( $settings );
	}

}

endif;

return new WC_POS_Settings_Payment_Methods();
