<?php
/**
 * Template for the general settings
 */
?>

<h3><?php _e( 'Customer Options', 'woocommerce-pos' ); ?></h3>

<table class="wc_pos-form-table">

  <tr>
    <th scope="row">
      <label for="pos_only_products"><?php /* translators: woocommerce */ _e( 'Account Creation', 'woocommerce' ) ?></label>
    </th>
    <td>
      <input type="checkbox" name="generate_username" id="generate_username" />
      <label for="generate_username"><?php /* translators: woocommerce */ _e( 'Automatically generate username from customer email', 'woocommerce' ) ?></label>
    </td>
  </tr>

</table>