<?php
/**
 * Tax Rate meta box
 */
?>

<div class="form-group">
	<label for="pos_tax_address"><?php _e( 'Calculate Tax Based On', 'woocommerce-pos-pro' ); ?></label>
	<select name="pos_tax_address" id="pos_tax_address">
		<option value="store" <?php echo $tax_address != 'base' ? 'selected="selected"' : ''; ?>><?php _e( 'this store address', 'woocommerce-pos-pro' ); ?></option>
		<option value="base" <?php echo $tax_address == 'base' ? 'selected="selected"' : ''; ?>><?php _e( 'base location', 'woocommerce-pos-pro' ); ?></option>
	</select>
</div>

<?php if($tax_rates): ?>
<table>
  <thead>
    <tr>
      <th><?php /* translators: woocommerce */ _e( 'Tax Name', 'woocommerce' ); ?></th>
      <th><?php /* translators: woocommerce */ _e( 'Rate %', 'woocommerce' ); ?></th>
      <th><?php /* translators: woocommerce */ _e( 'Shipping', 'woocommerce' ); ?></th>
      <th><?php /* translators: woocommerce */ _e( 'Compound', 'woocommerce' ); ?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($tax_rates as $slug => $rates): ?>
    <tr class="alternate"><td colspan="4"><?php echo $tax_labels[$slug] ?></td></tr>
    <?php foreach($rates as $rate): ?>
    <tr>
      <td><?php echo $rate['label'] ?></td>
      <td><?php echo $rate['rate'] ?></td>
      <td><?php echo $rate['shipping'] ?></td>
      <td><?php echo $rate['compound'] ?></td>
    </tr>
    <?php endforeach; ?>
  <?php endforeach; ?>
  </tbody>
</table>
<?php else: _e( 'No tax rates set', 'woocommerce-pos-pro' ); endif; ?>

<p>
  <?php
    echo sprintf( __( 'The tax rates above will be used at the point of sale. You can <a href="%s">change the tax rate</a> in WooCommerce settings.', 'woocommerce-pos-pro' ),
    admin_url('admin.php?page=wc-settings&amp;tab=tax&amp;section=standard') );
  ?>
</p>