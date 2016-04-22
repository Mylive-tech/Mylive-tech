<table class="table">
  <tr>
    <td>
      <label for="first_name"><?php /* translators: woocommerce */ _e('First Name', 'woocommerce'); ?></label>
      <input class="form-control" type="text" name="first_name" id="first_name">
    </td>
    <td>
      <label for="last_name"><?php /* translators: woocommerce */ _e('Last Name', 'woocommerce'); ?></label>
      <input class="form-control" type="text" name="last_name" id="last_name">
    </td>
    <td>
      <label for="email"><?php /* translators: woocommerce */ _e('Email', 'woocommerce'); ?> <sup class="required" title="<?php /* translators: woocommerce */ _e('Email', 'woocommerce'); ?>">*</sup></sup></label>
      <input class="form-control" type="text" name="email" id="email">
    </td>
    <?php if( ! wc_pos_get_option( 'customers', 'generate_username' ) ) : ?>
      <td>
        <label for="username"><?php /* translators: wordpress */ _e('Username'); ?> <sup class="required" title="<?php /* translators: wordpress */ _e('Username'); ?>">*</sup></sup></label>
        <input class="form-control" type="text" name="username" id="username">
      </td>
    <?php endif; ?>
  </tr>
</table>
<table class="table billing-address">
  <thead>
  <tr><th colspan="2"><?php /* translators: woocommerce */ _e('Billing Details', 'woocommerce'); ?></th></tr>
  </thead>
  <tbody>
  <?php
  $billing_fields = WC_POS_Pro_Template::customer_fields('billing');
  if($billing_fields): $i = 0; foreach($billing_fields as $key => $field):
    if($i % 2 == 0) echo '<tr>';
    ?>
    <td <?php if(isset($field['colspan'])) { $i++; echo 'colspan="'.$field['colspan'].'"'; } ?>>
      <label for="billing_address_<?php echo $key; ?>"><?php echo $field['label']; ?></label>
      <input class="form-control" type="text" data-handler="address" name="billing_address[<?php echo $key; ?>]" id="billing_address_<?php echo $key; ?>">
    </td>
    <?php
    if(++$i % 2 == 0) echo '</tr>';
  endforeach; if($i % 2 != 0) echo '</tr>'; endif;
  ?>
  </tbody>
</table>
<table class="table shipping-address">
  <thead>
  <tr><th colspan="2"><?php /* translators: woocommerce */ _e('Shipping Details', 'woocommerce'); ?></th></tr>
  <tr>
    <td colspan="2">
      <label class="c-input c-checkbox" for="copy_billing_address">
        <input type="checkbox" name="copy_billing_address" id="copy_billing_address" />
        <span class="c-indicator"></span>
        <?php /* translators: woocommerce */ _e('Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce'); ?>
      </label>
    </td>
  </tr>
  </thead>
  <tbody>
  <?php
  $shipping_fields = WC_POS_Pro_Template::customer_fields('shipping');
  if($shipping_fields): $i = 0; foreach($shipping_fields as $key => $field):
    if($i % 2 == 0) echo '<tr>';
    ?>
    <td <?php if(isset($field['colspan'])) { $i++; echo 'colspan="'.$field['colspan'].'"'; } ?>>
      <label for="shipping_address_<?php echo $key; ?>"><?php echo $field['label']; ?></label>
      <input class="form-control" type="text" data-handler="address" name="shipping_address[<?php echo $key; ?>]" id="shipping_address_<?php echo $key; ?>">
    </td>
    <?php
    if(++$i % 2 == 0) echo '</tr>';
  endforeach; if($i % 2 != 0) echo '</tr>'; endif;
  ?>
  </tbody>
</table>