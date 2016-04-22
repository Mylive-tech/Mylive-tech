<li class="group">{$category.TITLE}</li>   
{if !empty($topCategs)}

	     
	{foreach from=$topCategs item=cat name=categs}
		<li><a href="{$cat->getUrl()}" target="_self" title="{$cat.TITLE|escape}">{$cat.TITLE|escape}</a></li>
	{/foreach}

{/if}