{if !empty($topCategs)}

	<li class="group">Categories</li>        
	{foreach from=$topCategs item=cat name=categs}
		<li><a href="{$cat->getUrl()}" target="_self" title="{$cat.TITLE|escape}">{$cat.TITLE|escape}</a></li>
	{/foreach}

{/if}
