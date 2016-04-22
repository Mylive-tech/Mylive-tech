<li class="group">{l}My Submissions{/l}</li>
{if $feat_links->countWithoutLimit() > 0}
    {foreach from=$feat_links item=link}
        <li>      

{if $edit_link}
	<span id="T_{$link.ID}">{$link.TITLE|escape|trim}</span>
	{if !empty($link.URL)}
		&nbsp;&nbsp;
		<a class="link" id="id_{$link.ID}" href="{$link.URL|escape|trim}" title="{$link.TITLE|escape|trim}"
		{if $link.NOFOLLOW} rel="nofollow"{/if}
		target="_blank">
		<span class="link"><img src="{$smarty.const.DOC_ROOT}/images/external_link.png" alt="{$link.TITLE|escape|trim}" border="0"/></span>
		</a>
	{/if}
{else}
<a class="link" id="id_{$link.ID}" href="{$link.URL|escape|trim}" title="{$link.TITLE|escape|trim}"
{if $link.NOFOLLOW} rel="nofollow"{/if}
target="_blank">{$link.TITLE|escape|trim|truncate:45:"...":true}</a>   
{/if}

<div style="width:95%">	   
	{if $link.DESCRIPTION}
	<p id="description{$link.ID}" style="font-size:15px;font-weight:normal;">
	   <span id="editdescrip_{$link.ID}">{if !empty($link.DESCRIPTION)}{$link.DESCRIPTION|trim|truncate:230:"...":true}{else}[{l}No Description{/l}]{/if}</span>
	</p>	
	{/if}
	<a class="readMore" href="{$link->getUrl()}" title="{l}Read more about{/l}: {$link.TITLE|escape|trim}" target="_self">{l}Read&nbsp;more{/l}</a>
	{if ($smarty.const.REQUIRE_REGISTERED_USER == 1 || $smarty.const.REQUIRE_REGISTERED_USER_ARTICLE == 1) and !empty ($regular_user_details) and ($regular_user_details.ID == $link.OWNER_ID)}
	,&nbsp;<a class="readMore" href="{$smarty.const.DOC_ROOT}/submit?linkid={$link.ID}" title="{l}Edit or Remove your link{/l}">{l}Review{/l}</a>
	{/if}
</div>
</li>
    {/foreach}
{/if}
<hr/>
{if $links->countWithoutLimit() > 0}
    {foreach from=$links item=link}
       <li>      

{if $edit_link}
	<span id="T_{$link.ID}">{$link.TITLE|escape|trim}</span>
	{if !empty($link.URL)}
		&nbsp;&nbsp;
		<a class="link" id="id_{$link.ID}" href="{$link.URL|escape|trim}" title="{$link.TITLE|escape|trim}"
		{if $link.NOFOLLOW} rel="nofollow"{/if}
		target="_blank">
		<span class="link"><img src="{$smarty.const.DOC_ROOT}/images/external_link.png" alt="{$link.TITLE|escape|trim}" border="0"/></span>
		</a>
	{/if}
{else}
<a class="link" id="id_{$link.ID}" href="{$link.URL|escape|trim}" title="{$link.TITLE|escape|trim}"
{if $link.NOFOLLOW} rel="nofollow"{/if}
target="_blank">{$link.TITLE|escape|trim|truncate:45:"...":true}</a>   
{/if}

<div style="width:95%">	   
	{if $link.DESCRIPTION}
	<p id="description{$link.ID}" style="font-size:15px;font-weight:normal;">
	   <span id="editdescrip_{$link.ID}">{if !empty($link.DESCRIPTION)}{$link.DESCRIPTION|trim|truncate:230:"...":true}{else}[{l}No Description{/l}]{/if}</span>
	</p>	
	{/if}
	<a class="readMore" href="{$link->getUrl()}" title="{l}Read more about{/l}: {$link.TITLE|escape|trim}" target="_self">{l}Read&nbsp;more{/l}</a>
	{if ($smarty.const.REQUIRE_REGISTERED_USER == 1 || $smarty.const.REQUIRE_REGISTERED_USER_ARTICLE == 1) and !empty ($regular_user_details) and ($regular_user_details.ID == $link.OWNER_ID)}
	,&nbsp;<a class="readMore" href="{$smarty.const.DOC_ROOT}/submit?linkid={$link.ID}" title="{l}Edit or Remove your link{/l}">{l}Review{/l}</a>
	{/if}
</div>
</li>
    {/foreach}
{/if}

{if $feat_links->countWithoutLimit() == 0 AND $links->countWithoutLimit() == 0}
    <li>There are currently no submissions listed for this user</li>
{/if}