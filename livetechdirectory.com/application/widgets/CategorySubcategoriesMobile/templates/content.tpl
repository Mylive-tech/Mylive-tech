{if $subcategories->countWithoutLimit() > 0}
{foreach from=$subcategories item=cat name=categs}
		<li><a href="{$cat->getUrl()}" target="_self" title="{$cat.TITLE|escape}">{$cat.TITLE|escape}</a></li>
	{/foreach}
{/if}

