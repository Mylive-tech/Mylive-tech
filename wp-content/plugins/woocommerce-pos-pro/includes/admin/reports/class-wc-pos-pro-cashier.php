<?php

/**
 * Cashier Report
 *
 * @class 	  WooCommerce_POS_Pro_Report_Cashier
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_Reports_Cashier extends WC_POS_Pro_Admin_Reports_Abstract {

  private $data;
  private $cashiers = array();
  private $legend = array();

	/**
	 * Constructor
	 */
	public function __construct() {
    $this->get_cashiers();
	}

  /**
   * Store array of all cashiers
   */
  private function get_cashiers(){
    global $wpdb;

    $result = $wpdb->get_col("
      SELECT DISTINCT pm.meta_value
      FROM $wpdb->postmeta pm
      WHERE pm.meta_key = '_pos_user'
    ");

    $this->cashiers = array_map('get_userdata', $result);
  }

  /**
   * Get user info for cashiers, store data
   */
  public function prepare_report_data(){
    foreach( $this->cashiers as $key => $user ){
      $data = $this->_prepare_report_data($user->ID);
      if($data['total'] == 0 && $data['net_sales'] == 0){
        unset($this->cashiers[$key]);
      } else {
        $this->data[$user->user_login] = $data;
      }
    }
  }

  /**
   * @param $user_id
   * @return array
   */
  private function _prepare_report_data($user_id){

    $result = $this->get_order_report_data( array(
      'data' => array(
        'ID' => array(
          'type'     => 'post_data',
          'function' => 'COUNT',
          'name'     => 'count',
          'distinct' => true,
        ),
        '_order_total' => array(
          'type'     => 'meta',
          'function' => 'SUM',
          'name'     => 'total_sales'
        ),
        'post_date'    => array(
          'type'     => 'post_data',
          'function' => '',
          'name'     => 'post_date'
        ),
      ),
      'group_by'     => $this->group_by_query,
      'order_by'     => 'post_date ASC',
      'query_type'   => 'get_results',
      'filter_range' => true,
      'order_types'  => wc_get_order_types( 'sales-reports' ),
      'order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
      'where_meta'   => array(
        array(
          'meta_key'   => '_pos_user',
          'meta_value' => $user_id,
          'operator'   => '='
        )
      )
    ) );

    $data['count'] = $this->prepare_chart_data( $result, 'post_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
    $data['total_sales'] = $this->prepare_chart_data( $result, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
    $data['total'] = absint( array_sum( wp_list_pluck( $result, 'count' ) ) );
    $data['net_sales'] = wc_format_decimal( array_sum( wp_list_pluck( $result, 'total_sales' ) ), 2 );
    $data['average'] = wc_format_decimal( $data['net_sales'] / ( $this->chart_interval + 1 ), 2 );

    return $data;

  }

  /**
   * Get the legend for the main chart sidebar
   * @return array
   */
  public function get_chart_legend() {
    $i = 0;

    foreach( $this->cashiers as $key => $user ){

      $this->legend[$user->user_login] = array(
        'title' => '<strong>'. $user->first_name .' '. $user->last_name .'</strong>' . $user->user_email,
        'color' => $this->chart_color($i++),
        'highlight_series' => $user->user_login,
        'full_name' => $user->first_name .' '. $user->last_name
      );
    }

    return $this->legend;
  }

  /**
   * Get the main chart
   * @return string
   */
  public function get_main_chart() {

    $chart_data = array();

    // order count
    $i = 0; foreach( $this->cashiers as $key => $user ){
      $data = array_values( $this->data[$user->user_login]['count'] );
      $color = $this->chart_color($i++, 'secondary_colors');
      $title = $user->first_name .' '. $user->last_name;
      $chart_data[] = $this->bar_series($user->user_login, $title, $data, $color);
    }

    // total_sales
    $i = 0; foreach( $this->cashiers as $key => $user ){
      $data = array_values( $this->data[$user->user_login]['total_sales'] );
      $color = $this->chart_color($i++);
      $title = $user->first_name .' '. $user->last_name;
      $chart_data[] = $this->line_series($user->user_login, $title, $data, $color);
    }

    // average
    $i = 0; foreach( $this->cashiers as $key => $user ){
      $min = min( array_keys( $this->data[$user->user_login]['total_sales'] ) );
      $max = max( array_keys( $this->data[$user->user_login]['total_sales'] ) );
      $data = array(
        array( $min, $this->data[$user->user_login]['average'] ),
        array( $max, $this->data[$user->user_login]['average'])
      );
      $color = $this->chart_color($i++);
      $title = $user->first_name .' '. $user->last_name;
      $chart_data[] = $this->average_line_series($user->user_login, $title, $data, $color);
    }

    include 'views/main-chart.php';

  }

  /**
   * Chart Widgets
   * @return array
   */
  public function get_chart_widgets() {
    $widgets = array();

    $widgets[] = array(
      'title' => '',
      'callback' => array( $this, 'pie_chart' )
    );

    return $widgets;
  }

  /**
   * Number of orders Pie Chart
   */
  public function pie_chart() {
//    $legend = array();
    $chart_data = array();
    $append_tooltip = /*  translators: woocommerce */ __( 'orders', 'woocommerce' );

    // cycle through main legend
    foreach($this->legend as $user_login => $array){
      // legend
//      $legend[] = array(
//        'color' => $array['color'],
//        'title' => $array['full_name']
//      );

      // chart data
      $chart_data[] = array(
        'id'    => $user_login,
        'label' => $array['full_name'],
        'data'  => $this->data[$user_login]['total'],
        'color' => $array['color']
      );
    }

    include 'views/pie-chart.php';

  }

}