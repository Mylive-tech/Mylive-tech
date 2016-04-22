{if $show_title eq 1}
{$TITLE}
<br/>
{/if}
	{section name=i loop=$latest_com}
	<div {if $smarty.section.i.last}class="boxSponsoredLast"{else}class="boxSponsored"{/if}>
		<b>{$latest_com[i].DAT}:</b> {$latest_com[i].USER_NAME} said on  
		<a class="boxSponsoredA" href="{$smarty.const.SITE_URL}listing/{$latest_com[i].CATEGORY_URL}{$latest_com[i].CACHE_URL}" title="{$latest_com[i].TITLE|escape|trim}">
			{$latest_com[i].TITLE|escape|trim}
		</a>
		<br/>
		{$latest_com[i].COMMENT|escape|trim}
        <br/><br/>
        
	</div>
	{/section}