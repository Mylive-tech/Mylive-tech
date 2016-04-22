{foreach from=$NEWS item=feed}

<h4><a href="{$feed.URL}">{$feed.TITLE}</a></h4>
   <p id="description{$feed.ID}">
		
      <span id="editdescrip_{$feed.ID}">{$feed.DESCRIPTION|trim}</span>
   </p>

{/foreach}