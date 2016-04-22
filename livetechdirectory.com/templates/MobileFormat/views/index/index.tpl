{if !empty($categs)}
	<li class="group">Categories</li>        
	{foreach from=$categs item=cat name=categs}
    	<li><a href="{$smarty.const.DOC_ROOT}/{$cat.CACHE_URL|escape}" target="_self">{$cat.TITLE|escape} ({$cat.COUNT})</a></li>
    {/foreach}
{/if}