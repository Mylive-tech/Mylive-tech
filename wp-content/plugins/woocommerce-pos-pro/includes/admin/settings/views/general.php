<table class="wc_pos-form-table">
  <tbody>
    <tr>
      <th scope="row">
        <label for="barcode_field"><?php _e( 'Barcode Field', 'woocommerce-pos-pro' ); ?></label>
        <img title="<?php esc_attr_e( 'Select a meta field to use as the product barcode', 'woocommerce-pos-pro' ) ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" data-toggle="wc_pos-tooltip">
      </th>
      <td>
        <select name="barcode_field" id="barcode_field" class="select2" style="width:200px"></select>
      </td>
    </tr>
  </tbody>
</table>