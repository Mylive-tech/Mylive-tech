<?php
/**
 * Template for the license activation
 */
?>

<h3><?php _e( 'Pro License Activation', 'woocommerce-pos-pro' ); ?></h3>

{{#if activated}}
<div class="activated">
  <i class="wc_pos-icon-success wc_pos-icon-lg wc_pos-text-success"></i> <strong><?php _e( 'Thank you!', 'woocommerce-pos-pro' ) ?></strong> <?php _e( 'Your license is active', 'woocommerce-pos-pro' ) ?>.
</div>
{{else}}
<table class="wc_pos-form-table">
  <tr>
    <th scope="row">
      <label for="email"><?php _e( 'License Email', 'woocommerce-pos-pro' ) ?></label>
    </th>
    <td>
      <input type="text" name="email" id="email" class="all-options" />
    </td>
  </tr>
  <tr>
    <th scope="row">
      <label for="key"><?php _e( 'License Key', 'woocommerce-pos-pro' ) ?></label>
    </th>
    <td>
      <input type="text" name="key" id="key" class="all-options" />
    </td>
  </tr>
</table>
{{/if}}