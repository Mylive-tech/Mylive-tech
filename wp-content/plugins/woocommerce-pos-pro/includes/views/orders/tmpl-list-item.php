<div class="status"><i class="icon-{{status}} icon-lg" title="{{status_label}}" data-toggle="tooltip"></i></div>
<div class="order"><a data-action="show" href="#" href="#">{{order_number}}</a></div>
<div class="customer">
  {{#compare customer_id '===' 0}}<?php /* translators: woocommerce */ _e( 'Guest', 'woocommerce' ); ?>{{else}}
  <a data-action="customer" href="#" data-customer="{{customer_id}}">{{customer_name}}</a>
  {{/compare}}
</div>
<div class="note">{{#if note}}<i class="icon-note" title="{{note}}" data-toggle="tooltip"></i>{{/if}}</div>
<div class="date">{{formatDate created_at format='MMMM Do YYYY, h:mm a'}}</div>
<div class="total">{{{money total}}}</div>
<div class="actions"><a class="btn btn-success" href="#" data-action="show"><?php /* translators: wordpress */ _e( 'View' ); ?></a></div>