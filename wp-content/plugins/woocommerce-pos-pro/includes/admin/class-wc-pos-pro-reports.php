<?php
/**
 * POS Reports
 *
 * @class 	  WooCommerce_POS_Pro_Reports
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_Reports {


	public function __construct() {
		add_filter( 'woocommerce_admin_reports', array( $this, 'woocommerce_admin_reports' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * @param $reports
	 *
	 * @return mixed
	 */
	public function woocommerce_admin_reports( $reports ) {

		$reports['pos'] = array(
			'title'     => __( 'POS', 'woocommerce-pos-pro' ),
			'reports'   => array(
				'sales' => array(
					'title'       => __( 'POS vs Online', 'woocommerce-pos-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'get_report' )
				),
				'cashier' => array(
					'title'       => __( 'Sales by cashier', 'woocommerce-pos-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'get_report' )
				),
				'store' => array(
					'title'       => __( 'Sales by store', 'woocommerce-pos-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'get_report' )
				),
				'gateway' => array(
					'title'       => __( 'Sales by payment method', 'woocommerce-pos-pro' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'get_report' )
				),
			)
		);

		return $reports;
	}

	/**
	 * Load report class and output report
	 *
	 * @param $name
	 */
	public static function get_report( $name ) {
		$class = 'WC_POS_Pro_Admin_Reports_' . $name;
		$report = new $class();
		$report->output_report();
	}

	/**
	 * Output report-specific css.
	 */
	public function enqueue_admin_styles() {
		$css = '
			table.pos-report-table { margin-top:20px;text-align:center; }
			table.pos-report-table thead tr:first-of-type th { border-left:1px solid #e1e1e1; }
			table.pos-report-table thead tr:first-of-type th:first-of-type { border-left:none; }
			table.pos-report-table th { text-align:center; }
			table.pos-report-table tr th:first-of-type { text-align:left; }
			table.pos-report-table th.col-pos,
			table.pos-report-table td.col-pos { background-color:#f5f5f5;border-left:1px solid #e1e1e1; }
		';
		wp_add_inline_style( 'wp-admin', $css );

	}

} 