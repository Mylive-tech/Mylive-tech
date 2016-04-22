<div>
  <div class="input-group">
    <input data-toggle="dropdown" type="text" class="form-control" placeholder="<?php /* translators: woocommerce */ _e( 'Search customers', 'woocommerce' ); ?>">
    <span class="input-group-btn">
      <a href="#" data-action="add" class="btn btn-success" title="<?php _e('Add Customer', 'woocommerce-pos-pro'); ?>"><i class="icon-plus"></i></a>
    </span>
  </div>
</div>
<div>
  {{#if customer_id}}<a href="#" data-action="remove" title="<?php /* translators: wordpress */ _e( 'Remove' ); ?>"><i class="icon-remove"></i></a> {{/if}}
  <a href="#" data-action="edit" title="<?php /* translators: wordpress */ _e( 'Edit' ); ?>">
    {{formatCustomerName customer}}
  </a>
</div>