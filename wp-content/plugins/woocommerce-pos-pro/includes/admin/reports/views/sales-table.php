<table class="widefat pos-report-table">
  <thead>
    <tr>
      <th class="cell-period" rowspan="2"><?php _e( 'Period', 'woocommerce-pos-pro' ); ?></th>
      <th colspan="2"><?php _e( 'Orders', 'woocommerce-pos-pro' ); ?><span class="hidden">","</span></th>
      <th colspan="2"><?php _e( 'Total Sales', 'woocommerce-pos-pro' ); ?> <a class="tips" data-tip="<?php _e("This is the sum of the 'Order Total' field within your orders.", 'woocommerce-pos-pro'); ?>" href="#">[?]</a><span class="hidden">","</span></th>
      <th colspan="2"><?php _e( 'Total Shipping', 'woocommerce-pos-pro' ); ?> <a class="tips" data-tip="<?php _e("This is the sum of the 'Shipping Total' field within your orders.", 'woocommerce-pos-pro'); ?>" href="#">[?]</a><span class="hidden">","</span></th>
      <th colspan="2"><?php _e( 'Total Tax', 'woocommerce-pos-pro' ); ?> <a class="tips" data-tip="<?php esc_attr_e( 'This is the total tax for the rate (shipping tax + product tax).', 'woocommerce-pos-pro' ); ?>" href="#">[?]</a><span class="hidden">","</span></th>
      <th colspan="2"><?php _e( 'Net profit', 'woocommerce-pos-pro' ); ?> <a class="tips" data-tip="<?php _e("Total sales minus shipping and tax.", 'woocommerce-pos-pro'); ?>" href="#">[?]</a><span class="hidden">","</span></th>
    </tr>
    <tr>
      <th class="col-pos"><span class="hidden">","</span><?php _e( 'POS', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-online"><?php _e( 'Online', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-pos"><?php _e( 'POS', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-online"><?php _e( 'Online', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-pos"><?php _e( 'POS', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-online"><?php _e( 'Online', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-pos"><?php _e( 'POS', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-online"><?php _e( 'Online', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-pos"><?php _e( 'POS', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-online"><?php _e( 'Online', 'woocommerce-pos-pro' ); ?></th>
    </tr>
  </thead>
  <?php if( $dates ): ?>
  <tbody>
    <?php foreach( $dates as $key => $date ):
      $gross['pos']        = $this->data['pos']['total_sales'][$key][1] - $this->data['pos']['total_shipping'][$key][1];
      $gross['online']     = $this->data['online']['total_sales'][$key][1] - $this->data['online']['total_shipping'][$key][1];
      $total_tax['pos']    = $this->data['pos']['total_tax'][$key][1] + $this->data['pos']['total_shipping_tax'][$key][1];
      $total_tax['online'] = $this->data['online']['total_tax'][$key][1] + $this->data['online']['total_shipping_tax'][$key][1];
    ?>
    <tr>
      <th scope="row"><?php echo $date ?></th>
      <td class="col-pos"><?php echo $this->data['pos']['count'][$key][1]; ?></td>
      <td class="col-online"><?php echo $this->data['online']['count'][$key][1]; ?></td>
      <td class="col-pos"><?php echo wc_price( $gross['pos'] ); ?></td>
      <td class="col-online"><?php echo wc_price( $gross['online'] ); ?></td>
      <td class="col-pos"><?php echo wc_price( $this->data['pos']['total_shipping'][$key][1] ); ?></td>
      <td class="col-online"><?php echo wc_price( $this->data['online']['total_shipping'][$key][1] ); ?></td>
      <td class="col-pos"><?php echo wc_price( $total_tax['pos'] ); ?></td>
      <td class="col-online"><?php echo wc_price( $total_tax['online'] ); ?></td>
      <td class="col-pos"><?php echo wc_price( $gross['pos'] - $total_tax['pos'] ); ?></td>
      <td class="col-online"><?php echo wc_price( $gross['online'] - $total_tax['online'] ); ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <?php
      $gross['pos']        = $this->data['pos']['net_sales'] - $this->data['pos']['net_shipping'];
      $gross['online']     = $this->data['online']['net_sales'] - $this->data['online']['net_shipping'];
      $total_tax['pos']    = $this->data['pos']['net_tax'] + $this->data['pos']['net_shipping_tax'];
      $total_tax['online'] = $this->data['online']['net_tax'] + $this->data['online']['net_shipping_tax'];
    ?>
    <tr>
      <th scope="row"><?php _e( 'Total', 'woocommerce-pos-pro' ); ?></th>
      <th class="col-pos"><?php echo $this->data['pos']['total']; ?></th>
      <th class="col-online"><?php echo $this->data['online']['total']; ?></th>
      <th class="col-pos"><?php echo wc_price( $gross['pos'] ); ?></th>
      <th class="col-online"><?php echo wc_price( $gross['online'] ); ?></th>
      <th class="col-pos"><?php echo wc_price( $this->data['pos']['net_shipping'] ); ?></th>
      <th class="col-online"><?php echo wc_price( $this->data['online']['net_shipping'] ); ?></th>
      <th class="col-pos"><?php echo wc_price( $total_tax['pos'] ); ?></th>
      <th class="col-online"><?php echo wc_price( $total_tax['online'] ); ?></th>
      <th class="col-pos"><?php echo wc_price( $gross['pos'] - $total_tax['pos'] ); ?></th>
      <th class="col-online"><?php echo wc_price( $gross['online'] - $total_tax['online'] ); ?></th>
    </tr>
  </tfoot>
<?php else: ?>
<!-- no data -->
<?php endif; ?>
</table>