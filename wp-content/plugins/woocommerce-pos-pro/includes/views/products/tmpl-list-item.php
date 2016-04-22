<div class="img"><img src="{{featured_src}}" title="#{{id}}"></div>
<div class="title">
  {{#is type 'variation'}}
  <strong>{{title}}</strong>
  {{else}}
  <strong contenteditable="true">{{title}}</strong>
  {{/is}}
  {{#with product_attributes}}
  <i class="icon-info-circle" data-toggle="tooltip" title="
    <dl>
      {{#each this}}
      <dt>{{name}}:</dt>
      <dd>{{#list options ', '}}{{this}}{{/list}}</dd>
      {{/each}}
    </dl>
    "></i>
  {{/with}}
  {{#with product_variations}}
  <dl class="variations">
    {{#each this}}
    <dt>{{name}}:</dt>
    <dd>{{#list options ', '}}<a href="#" data-name="{{../name}}">{{this}}</a>{{/list}}</dd>
    {{/each}}
    <dt></dt><dd>
      <a href="#" class="expand-all" data-action="expand"><?php /* translators: woocommerce */ _e( 'Expand all', 'woocommerce' ); ?></a>
      <a href="#" class="close-all" data-action="close"><?php /* translators: woocommerce */ _e( 'Close all', 'woocommerce' ); ?></a>
    </dd>
  </dl>
  {{/with}}
  {{#is type 'variation'}}
  <dl class="variant">
    {{#each attributes}}
    <dt>{{name}}:</dt>
    <dd>{{option}}</dd>
    {{/each}}
  </dl>
  {{/is}}
</div>
<div>
  <input type="text" name="stock_quantity" data-label="<?php /* translators: woocommerce */ _e( 'Quantity', 'woocommerce' ); ?>" data-placement="bottom" data-numpad="quantity" class="form-control autogrow">
  <label class="small c-input c-checkbox" for="managing_stock[{{id}}]">
    <input type="checkbox" name="managing_stock" id="managing_stock[{{id}}]">
    <span class="c-indicator"></span>
    <?php /* translators: woocommerce */ _e( 'Manage stock?', 'woocommerce' ); ?>
  </label>
</div>
<div>
  {{#is type 'variable'}}
  <span data-name="regular_price"></span>
  <label class="small c-input c-checkbox" for="taxable[{{id}}]">
    <input type="checkbox" name="taxable" id="taxable[{{id}}]">
    <span class="c-indicator"></span>
    <?php /* translators: woocommerce */ _e( 'Taxable', 'woocommerce' ); ?>
  </label>
  {{else}}
  <input type="text" name="regular_price" data-label="<?php /* translators: woocommerce */ _e('Regular price', 'woocommerce'); ?>" data-placement="bottom" data-numpad="amount" class="form-control autogrow">
    {{#is type 'variation'}}{{else}}
    <label class="small c-input c-checkbox" for="taxable[{{id}}]">
      <input type="checkbox" name="taxable" id="taxable[{{id}}]">
      <span class="c-indicator"></span>
      <?php /* translators: woocommerce */ _e( 'Taxable', 'woocommerce' ); ?>
    </label>
    {{/is}}
  {{/is}}
</div>
<div>
  {{#is type 'variable'}}
  <span data-name="sale_price"></span>
  {{else}}
  <input type="text" name="sale_price" data-label="<?php /* translators: woocommerce */ _e('Sale price', 'woocommerce'); ?>" data-placement="bottom" data-numpad="amount" class="form-control autogrow">
  <label class="small c-input c-checkbox" for="on_sale[{{id}}]">
    <input type="checkbox" name="on_sale" id="on_sale[{{id}}]">
    <span class="c-indicator"></span>
    <?php _e( 'On Sale?', 'woocommerce-pos-pro' ); ?>
  </label>
  {{/is}}
</div>