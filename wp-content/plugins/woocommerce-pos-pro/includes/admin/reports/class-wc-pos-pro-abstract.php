<?php

/**
 * Abstract Reports Page Class
 *
 * @class    WC_POS_Admin_Settings_Page
 * @package  WooCommerce POS
 * @author   Paul Kilmurray <paul@kilbot.com.au>
 * @link     http://www.woopos.com.au
 */

abstract class WC_POS_Pro_Admin_Reports_Abstract extends WC_Admin_Report {

  // colors used by woo reports
  public $colors = array(
    'rgb(44 , 62 , 80 )', //'#2c3e50', navy
    'rgb(211, 84 , 0  )', //'#d35400', brown
    'rgb(52 , 152, 219)', //'#3498db', light blue
    'rgb(46 , 204, 113)', //'#2ecc71', green
    'rgb(241, 196, 15 )', //'#f1c40f', yellow
    'rgb(52 , 73 , 94 )', //'#34495e', dark blue
    'rgb(26 , 188, 156)', //'#1abc9c', aqua
    'rgb(230, 126, 34 )', //'#e67e22', orange
    'rgb(231, 76 , 60 )', //'#e74c3c', pink
    'rgb(41 , 128, 185)', //'#2980b9', blue
    'rgb(142, 68 , 173)', //'#8e44ad', purple
    'rgb(22 , 160, 133)', //'#16a085', turquoise
    'rgb(39 , 174, 96 )', //'#27ae60', light green
    'rgb(243, 156, 18 )', //'#f39c12', light orange
    'rgb(192, 57 , 43 )', //'#c0392b'  red
  );

  //
  public $secondary_colors = array(
    'rgb(213, 216, 220)', //'rgba(44,62,80,0.2)',   navy
    'rgb(246, 221, 204)', //'rgba(211,84,0,0.2)',   brown
    'rgb(214, 234, 248)', //'rgba(52,152,219,0.2)', light blue
    'rgb(213, 245, 227)', //'rgba(46,204,113,0.2)', green
    'rgb(252, 243, 207)', //'rgba(241,196,15,0.2)', yellow
    'rgb(214, 219, 223)', //'rgba(52,73,94,0.2)',   dark blue
    'rgb(209, 242, 235)', //'rgba(26,188,156,0.2)', aqua
    'rgb(250, 229, 211)', //'rgba(230,126,34,0.2)', orange
    'rgb(250, 219, 216)', //'rgba(231,76,60,0.2)',  pink
    'rgb(212, 230, 241)', //'rgba(41,128,185,0.2)', blue
    'rgb(232, 218, 239)', //'rgba(142,68,173,0.2)', purple
    'rgb(208, 236, 231)', //'rgba(22,160,133,0.2)', turquoise
    'rgb(212, 239, 223)', //'rgba(39,174,96,0.2)',  light green
    'rgb(253, 235, 208)', //'rgba(243,156,18,0.2)', light orange
    'rgb(242, 215, 213)', //'rgba(192,57,43,0.2)',  red
  );

  // default is filter by date template
  public $report_by_date = true;

  /**
   * Helper function called just before html output
   */
  protected function prepare_report_data(){}

  /**
   * Output the template
   */
  public function output_report() {

    if($this->report_by_date){
      return $this->output_report_by_date();
    }

    echo 'No template';

  }

  /**
   * Output report by date template
   */
  protected function output_report_by_date() {

    // prepare range settings
    $current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
    if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
      $current_range = '7day';
    }
    $this->calculate_current_range( $current_range );

    $ranges = array(
      'year'         => /* translators: woocommerce */ __( 'Year', 'woocommerce' ),
      'last_month'   => /* translators: woocommerce */ __( 'Last Month', 'woocommerce' ),
      'month'        => /* translators: woocommerce */ __( 'This Month', 'woocommerce' ),
      '7day'         => /* translators: woocommerce */ __( 'Last 7 Days', 'woocommerce' )
    );

    // prepare report data
    $this->prepare_report_data();

    include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );

  }

  /**
   * Output an export link
   */
  public function get_export_button() {
    $current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
    $filename = str_replace( 'WC_POS_Pro_Admin_Reports_', '', get_class( $this ) ) .'-report-';
    $filename .= esc_attr( $current_range ) .'-';
    $filename .= date_i18n( 'Y-m-d', current_time('timestamp') ) . '.csv';
    $export_type = isset($this->export_type) ? $this->export_type : 'chart';
    include 'views/export-button.php';
  }

  /**
   * Return a color
   * @param integer $idx
   * @param string $type
   * @return
   */
  protected function chart_color( $idx = 0, $type = 'colors' ){
    return $this->{$type}[ $idx % count($this->colors) ];
  }

  /**
   * @param $id
   * @param $label
   * @param $data
   * @param $color
   * @return array
   */
  protected function line_series($id, $label, $data, $color){
    return array(
      'id'    => $id,
      'label' => $label,
      'data'  => $data,
      'yaxis' => 2,
      'color' => $color,
      'points' => array(
        'show'      => true,
        'radius'    => 5,
        'lineWidth' => 3,
        'fillColor' => '#fff',
        'fill'      => true
      ),
      'lines' => array(
        'show'      => true,
        'lineWidth' => 4,
        'fill'      => false
      ),
      'shadowSize' => 0,
      'prepend_tooltip' => get_woocommerce_currency_symbol()
    );
  }

  /**
   * @param $id
   * @param $label
   * @param $data
   * @param $color
   * @return array
   */
  protected function average_line_series($id, $label, $data, $color){
    return array(
      'id'    => $id,
      'label' => $label,
      'data'  => $data,
      'yaxis' => 2,
      'color' => $color,
      'points' => array(
        'show'      => true,
        'radius'    => 1,
        'lineWidth' => 2,
        'fill'      => false
      ),
      'lines' => array(
        'show'      => true,
        'lineWidth' => 2,
        'fill'      => false
      ),
      'shadowSize' => 0,
      'prepend_tooltip' => get_woocommerce_currency_symbol()
    );
  }

  /**
   * @param $id
   * @param $label
   * @param $data
   * @param $color
   * @return array
   */
  protected function bar_series($id, $label, $data, $color){
    return array(
      'id'    => $id,
      'label' => $label,
      'data'  => $data,
      'bars' => array(
        'fillColor' => $color,
        'fill'      => true,
        'show'      => true,
        'lineWidth' => 0,
        'barWidth'  => $this->barwidth * 0.5,
        'align'     => 'center'
      ),
      'shadowSize' => 0,
      'hoverable'  => false,
      'stack'      => 'stack',
    );
  }

}