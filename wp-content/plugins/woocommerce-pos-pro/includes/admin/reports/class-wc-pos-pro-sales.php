<?php
/**
 * POS vs Online Report
 *
 * @class 	  WooCommerce_POS_Pro_Report_Sales
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Admin_Reports_Sales extends WC_POS_Pro_Admin_Reports_Abstract {

  private $data;
  private $legend = array();
  public $export_type = 'table';

  /**
   *
   */
  public function prepare_report_data(){
    $all = $this->_prepare_report_data('all');
    $pos = $this->_prepare_report_data('pos');

    // online = all - pos
    $online = array(
      'total'             => $all['total'] - $pos['total'],
      'net_sales'         => $all['net_sales'] - $pos['net_sales'],
      'net_shipping'      => $all['net_shipping'] - $pos['net_shipping'],
      'net_tax'           => $all['net_tax'] - $pos['net_tax'],
      'net_shipping_tax'  => $all['net_shipping_tax'] - $pos['net_shipping_tax'],
      'average'           => $all['average'] - $pos['average'],
      'count'             => $this->array_diff($all['count'], $pos['count']),
      'total_sales'       => $this->array_diff($all['total_sales'], $pos['total_sales']),
      'total_shipping'    => $this->array_diff($all['total_shipping'], $pos['total_shipping']),
      'total_tax'         => $this->array_diff($all['total_tax'], $pos['total_tax']),
      'total_shipping_tax'=> $this->array_diff($all['total_shipping_tax'], $pos['total_shipping_tax'])
    );

    $this->data = array(
      'all'    => $all,
      'pos'    => $pos,
      'online' => $online
    );
  }

  /**
   * @param $all
   * @param $pos
   * @return array
   */
  public function array_diff($all, $pos){
    $online = array();

    foreach( array_keys($all) as $key ){
      $online[$key] = array(
        $key,
        $all[$key][1] - $pos[$key][1]
      );
    }

    return $online;
  }

  /**
   * @param string $id
   * @return array
   */
  private function _prepare_report_data( $id ){

    $where_meta = array();

    if( $id == 'pos' ) {
      $where_meta = array(
        array(
          'meta_key'   => '_pos',
          'meta_value' => 1,
          'operator'   => '='
        )
      );
    }

    $order_counts = (array) $this->get_order_report_data( array(
      'data' => array(
        'ID' => array(
          'type'     => 'post_data',
          'function' => 'COUNT',
          'name'     => 'count',
          'distinct' => true,
        ),
        'post_date' => array(
          'type'     => 'post_data',
          'function' => '',
          'name'     => 'post_date'
        )
      ),
      'where_meta'   => $where_meta,
      'group_by'     => $this->group_by_query,
      'order_by'     => 'post_date ASC',
      'query_type'   => 'get_results',
      'filter_range' => true,
      'order_types'  => wc_get_order_types( 'order-count' ),
      'order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
      'nocache'      => true
    ) );


    /**
     * Order totals by date. Charts should show GROSS amounts to avoid going -ve.
     */
    $orders = (array) $this->get_order_report_data( array(
      'data' => array(
        '_order_total' => array(
          'type'     => 'meta',
          'function' => 'SUM',
          'name'     => 'total_sales'
        ),
        '_order_shipping' => array(
          'type'     => 'meta',
          'function' => 'SUM',
          'name'     => 'total_shipping'
        ),
        '_order_tax' => array(
          'type'     => 'meta',
          'function' => 'SUM',
          'name'     => 'total_tax'
        ),
        '_order_shipping_tax' => array(
          'type'     => 'meta',
          'function' => 'SUM',
          'name'     => 'total_shipping_tax'
        ),
        'post_date' => array(
          'type'     => 'post_data',
          'function' => '',
          'name'     => 'post_date'
        ),
      ),
      'where_meta'   => $where_meta,
      'group_by'     => $this->group_by_query,
      'order_by'     => 'post_date ASC',
      'query_type'   => 'get_results',
      'filter_range' => true,
      'order_types'  => wc_get_order_types( 'sales-reports' ), // Orders, not refunds
      'order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
      'nocache'      => true
    ) );

    $data['count']          = $this->prepare_chart_data( $order_counts, 'post_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
    $data['total_sales']    = $this->prepare_chart_data( $orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
    $data['total_shipping'] = $this->prepare_chart_data( $orders, 'post_date', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
    $data['total_tax']      = $this->prepare_chart_data( $orders, 'post_date', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );
    $data['total_shipping_tax'] = $this->prepare_chart_data( $orders, 'post_date', 'total_shipping_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );

    $data['total']            = absint( array_sum( wp_list_pluck( $order_counts, 'count' ) ) );
    $data['net_sales']        = wc_format_decimal( array_sum( wp_list_pluck( $orders, 'total_sales' ) ), 2 );
    $data['net_shipping']     = wc_format_decimal( array_sum( wp_list_pluck( $orders, 'total_shipping' ) ), 2 );
    $data['net_tax']          = wc_format_decimal( array_sum( wp_list_pluck( $orders, 'total_tax' ) ), 2 );
    $data['net_shipping_tax'] = wc_format_decimal( array_sum( wp_list_pluck( $orders, 'total_shipping_tax' ) ), 2 );
    $data['average']          = wc_format_decimal( $data['net_sales'] / ( $this->chart_interval + 1 ), 2 );

    return $data;

  }

  public function get_chart_legend(){
    $this->legend = array(
      'pos' => array(
        'title' => '<strong>'. __('POS Sales', 'woocommerce-pos-pro') .'</strong>',
        'color' => $this->chart_color(0),
        'highlight_series' => 'pos',
        'label' => __('POS Sales', 'woocommerce-pos-pro')
      ),
      'online' => array(
        'title' => '<strong>'. __('Online Sales', 'woocommerce-pos-pro') .'</strong>',
        'color' => $this->chart_color(1),
        'highlight_series' => 'online',
        'label' => __('Online Sales', 'woocommerce-pos-pro')
      )
    );

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

    $append_tooltip = /*  translators: woocommerce */ __( 'orders', 'woocommerce' );

    // cycle through main legend
    foreach($this->legend as $id => $array){
      // legend
//      $legend[] = array(
//        'color' => $array['color'],
//        'title' => $array['label']
//      );

      // chart data
      $chart_data[] = array(
        'id'    => $id,
        'label' => $array['label'],
        'data'  => $this->data[$id]['total'],
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

    // order counts
    $chart_data[] = $this->bar_series(
      'pos',
      __('POS Sales', 'woocommerce-pos-pro'),
      array_values( $this->data['pos']['count'] ),
      $this->chart_color(0, 'secondary_colors')
    );
    $chart_data[] = $this->bar_series(
      'online',
      __('Online Sales', 'woocommerce-pos-pro'),
      array_values( $this->data['online']['count'] ),
      $this->chart_color(1, 'secondary_colors')
    );

    // order count
    $chart_data[] = $this->line_series(
      'pos',
      __('POS Sales', 'woocommerce-pos-pro'),
      array_values( $this->data['pos']['total_sales'] ),
      $this->chart_color(0)
    );
    $chart_data[] = $this->line_series(
      'online',
      __('Online Sales', 'woocommerce-pos-pro'),
      array_values( $this->data['online']['total_sales'] ),
      $this->chart_color(1)
    );

    // average
    $min = min( array_keys( $this->data['all']['total_sales'] ) );
    $max = max( array_keys( $this->data['all']['total_sales'] ) );
    $pos_average = array(
      array( $min, $this->data['pos']['average'] ),
      array( $max, $this->data['pos']['average'])
    );
    $chart_data[] = $this->average_line_series('pos', __('POS Sales', 'woocommerce-pos-pro'), $pos_average, $this->chart_color(0));

    $online_average = array(
      array( $min, $this->data['online']['average'] ),
      array( $max, $this->data['online']['average'])
    );
    $chart_data[] = $this->average_line_series('online', __('POS Sales', 'woocommerce-pos-pro'), $online_average, $this->chart_color(1));

    include 'views/main-chart.php';

    $this->table_data();

  }

  /**
   *
   */
  private function table_data(){
    $keys = array_keys( $this->data['all']['count'] );
    foreach($keys as $key){
      if ( $this->chart_groupby == 'month' ) {
        $date = date_i18n( 'F', substr($key, 0, -3) );
      } else {
        $date = date_i18n( get_option( 'date_format' ), substr($key, 0, -3) );
      }
      $dates[$key] = $date;
    }
    include 'views/sales-table.php';
  }

}