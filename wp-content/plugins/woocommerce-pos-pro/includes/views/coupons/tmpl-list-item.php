<div>{{code}}</div>
<div>{{type}}</div>
<div>{{{money amount}}}</div>
<div>{{description}}</div>
<div>
  {{#each product_ids}}
  <a href="#products/{{this}}">{{this}}</a>
  {{/each}}
</div>
<div>{{usage_count}} / {{usage_limit}}</div>
<div>{{formatDate expiry_date format='MMMM Do YYYY'}}</div>