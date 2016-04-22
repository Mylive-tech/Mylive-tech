<div class="wrap">
  <div id="icon-options-general" class="icon32"><br />
  </div>
  <h2>WooCommerce Wholesale Pricing</h2>
  <br />
  <form method="post" action="options.php">
    <?php settings_fields( 'woo_wholesale_options' ); ?>
    <table class="form-table" >
      <tr valign="top">
        <th scope="row"><h3>User Role Options</h3></th>
        <td></td>
      </tr>
      <tr valign="top">
        <th scope="row">Wholesale User Role:</th>
        <td><select name="wwo_wholesale_role">
            <?php
 global $wp_roles;
 $roles = $wp_roles->get_names();
 foreach($roles as $role) { 
 	$role = str_replace(' ', '_', $role);
	$role = strtolower($role); ?>
            <option name="role" <?php if ( get_option('wwo_wholesale_role') == $role ) { echo 'selected="selected"'; } ?> value="<?php echo $role; ?>"><?php echo $role; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <tr valign="top">
        <th scope="row"><h3>Pricing Options</h3>
        </th>
        <td></td>
      </tr>
      <tr valign="top">
        <th scope="row">Show Wholesale Savings:</th>
        <td><input name="wwo_savings" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_savings' ) ); ?> />
          <code>Show wholesale savings by the price?</code><br />
          <input size="50" name="wwo_savings_label" type="text" value="<?php echo get_option( 'wwo_savings_label' ); ?>" />
          <code>Label for savings? Default: "You Save"</code></td>
      </tr>
      <tr valign="top">
        <th scope="row">Show Percentage Savings:</th>
        <td><input name="wwo_percentage" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_percentage' ) ); ?> />
          <code>Show percentage of savings by the price?</code></td>
      </tr>
      <tr valign="top">
        <th scope="row">Show RRP :</th>
        <td><input name="wwo_rrp" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_rrp' ) ); ?> />
          <code>Show RRP to wholesale customers?</code> <br />
          <input size="50" name="wwo_rrp_label" type="text" value="<?php echo get_option( 'wwo_rrp_label' ); ?>" />
          <code>Label for normal price? Default: "RRP"</code></td>
      </tr>
      <tr valign="top">
        <th scope="row">Show RRP :</th>
        <td><input size="50" name="wwo_wholesale_label" type="text" value="<?php echo get_option( 'wwo_wholesale_label' ); ?>" />
          <code>Label for Wholesale price? Default: "Your Price". Please not this will only show if options above have been selected.</code></td>
      </tr>
      <tr valign="top">
        <th scope="row"><h3>Quantity Options</h3>
        </th>
        <td></td>
      </tr>
      <tr valign="top">
        <th scope="row">Set Minimum Quantity:</th>
        <td><input name="wwo_min_quantity" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_min_quantity' ) ); ?> />
          <code>Enable minimum quantity?</code><br />
          <input size="50" name="wwo_min_quantity_value" type="text" value="<?php echo get_option( 'wwo_min_quantity_value' ); ?>" />
          <code>Your minimum quantity. Example: 10</code></td>
      </tr>
      <tr valign="top">
        <th scope="row">Set Maximum Quantity:</th>
        <td><input name="wwo_max_quantity" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_max_quantity' ) ); ?> />
          <code>Enable Maximum quantity?</code><br />
          <input size="50" name="wwo_max_quantity_value" type="text" value="<?php echo get_option( 'wwo_max_quantity_value' ); ?>" />
          <code>Your Maximum quantity. Example: 20</code></td>
      </tr>
      <tr valign="top">
        <th scope="row"><?php submit_button(); ?></th>
      </tr>
    </table>
  </form>
</div>
