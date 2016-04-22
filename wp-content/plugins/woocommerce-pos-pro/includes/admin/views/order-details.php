<p class="form-field form-field-wide">
  <label for="wc_pos_pro_store"><?php _e( 'Store', 'woocommerce-pos-pro' ) ?>:</label>
  <select id="wc_pos_pro_store" name="wc_pos_pro_store" class="wc-enhanced-select">
    <option value="">&nbsp;</option>
    <?php foreach($stores as $store): ?>
      <option value="<?php echo $store->ID; ?>" <?php selected( $store_id, $store->ID ); ?>><?php echo $store->post_title; ?></option>
    <?php endforeach; ?>
  </select>
</p>