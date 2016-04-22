<?php

/**
 * Payment Method Report
 *
 * @class 	  WC_POS_Pro_Report_Payment_Method
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_Reports_Gateway extends WC_POS_Pro_Admin_Reports_Abstract {

  private $data;
  private $gateways = array();
  private $legend = array();

  public function __construct(){
    $this->get_gateways();
  }

  /**
   * Create array of gateway_id => gateway_title from orders
   */
  private function get_gateways(){
    global $wpdb;

    $results = $wpdb->get_results("
      SELECT DISTINCT pm1.meta_value AS method, pm2.meta_value AS title
      FROM $wpdb->postmeta pm1
      LEFT JOIN $wpdb->postmeta pm2
      ON (pm1.post_id = pm2.post_id)
      WHERE pm1.meta_key = '_payment_method'
      AND pm2.meta_key = '_payment_method_title'
    ");

    // in cases of payment_id = multiple titles, reverse so most recent is used
    if( $results ): foreach( array_reverse($results) as $result ) :
      $this->gateways[$result->method] = $result->title;
    endforeach; endif;
  }

  /**
   *
   */
  public function prepare_report_data(){
    foreach( $this->gateways as $method => $title ){
      $data = $this->_prepare_report_data($method);
      if($data['total'] == 0 && $data['net_sales'] == 0){
        unset($this->gateways[$method]);
      } else {
        $this->data[$method] = $data;
      }
    }
  }

  /**
   * @param $method
   * @return array
   */
  private function _prepare_report_data($method){

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
          'meta_key'   => '_payment_method',
          'meta_value' => $method,
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

    foreach( $this->gateways as $method => $title ){
      $this->legend[$method] = array(
        'title' => '<strong>'. $title .'</strong>' . $method,
        'color' => $this->chart_color($i++),
        'highlight_series' => $method,
        'label' => $title
      );
    }

    return $this->legend;
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
    $legend = array();
    $chart_data = array();
    $append_tooltip = /*  translators: woocommerce */ __( 'orders', 'woocommerce' );

    // cycle through main legend
    foreach($this->legend as $method => $array){
      // legend
//      $legend[] = array(
//        'color' => $array['color'],
//        'title' => $array['label']
//      );

      // chart data
      $chart_data[] = array(
        'id'    => $method,
        'label' => $array['label'],
        'data'  => $this->data[$method]['total'],
        'color' => $array['color']
      );
    }

    include 'views/pie-chart.php';

  }

  /**
   * Get the main chart
   * @return string
   */
  public function get_main_chart() {

    $chart_data = array();

    // order count
    $i = 0; foreach( $this->gateways as $method => $title ){
      $data = array_values( $this->data[$method]['count'] );
      $color = $this->chart_color($i++, 'secondary_colors');
      $chart_data[] = $this->bar_series($method, $title, $data, $color);
    }

    // total_sales
    $i = 0; foreach( $this->gateways as $method => $title ){
      $data = array_values( $this->data[$method]['total_sales'] );
      $color = $this->chart_color($i++);
      $chart_data[] = $this->line_series($method, $title, $data, $color);
    }

    // average
    $i = 0; foreach( $this->gateways as $method => $title ){
      $min = min( array_keys( $this->data[$method]['total_sales'] ) );
      $max = max( array_keys( $this->data[$method]['total_sales'] ) );
      $data = array(
        array( $min, $this->data[$method]['average'] ),
        array( $max, $this->data[$method]['average'])
      );
      $color = $this->chart_color($i++);
      $chart_data[] = $this->average_line_series($method, $title, $data, $color);
    }

    include 'views/main-chart.php';

  }

}