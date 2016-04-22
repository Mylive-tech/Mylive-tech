<div class="img">{{#if avatar_url}}<img src="{{avatar_url}}" title="#{{id}}">{{/if}}</div>
<div class="username">{{#if username}}<a data-action="edit" href="#">{{username}}</a>{{/if}}</div>
<div class="first-name">{{first_name}}</div>
<div class="last-name">{{last_name}}</div>
<div class="email">{{email}}</div>
<div class="spent">{{{money total_spent}}}</div>
<div class="orders">
  {{#compare orders_count '!==' 0}}
  <a data-action="showOrders" href="#">{{orders_count}}</a>
  {{else}}
  {{orders_count}}
  {{/compare}}
</div>
<div class="last-order">
  {{#if last_order_id}}
  <a data-action="showOrder" href="#" title="{{formatDate last_order_date format='MMMM Do YYYY, h:mm:ss a'}}" data-toggle="tooltip">
    {{last_order_id}}
  </a>
  {{/if}}
</div>
<div class="actions"><a class="btn btn-success" data-action="edit" href="#"><?php /* translators: wordpress */ _e( 'Edit' ); ?></a></div>