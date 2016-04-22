{if $show_title eq 1}
{$TITLE}
<br/>
{/if}
<ul class="boxPopCats">
	{foreach item=cat from=$popularCategs}
    <li>
		<a href="{$cat->getUrl()}" title="{$cat.TITLE|escape}">{$cat.TITLE|escape}</a>
	</li>
    {/foreach}
</ul>