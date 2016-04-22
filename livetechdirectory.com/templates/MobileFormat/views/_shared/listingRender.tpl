{*{if $linkColumns == 2}
    {assign var="gridClass" value='phpld-g50 woo'}
{elseif  $linkColumns == 3}
    {assign var="gridClass" value='phpld-g33 woo'}
{elseif  $linkColumns == 4}
    {assign var="gridClass" value='phpld-g25 woo'}
{elseif  $linkColumns == 5}
    {assign var="gridClass" value='phpld-g20 woo'}
{else}
    {assign var="gridClass" value='phpld-gbox '}
{/if}


{if $linkColumns > 1}
    <script type="text/javascript">
        {literal}    
    $(window).load(function() { 
            $('img').each(function(){
                $(this).attr('height', jQuery(this).outerHeight());
            });  
    });    

    var handler{/literal}{$widgetID}{literal} = null;   
    $(document).ready(function() {
            
        if($('#woogrid{/literal}{$widgetID}{literal} div.woo').length > 1){  
        //if(handler) handler.wookmarkClear();    
        handler{/literal}{$widgetID}{literal} = $('#woogrid{/literal}{$widgetID}{literal} div.woo');

            var options = {
                autoResize: true,  
                container: jQuery('#woogrid{/literal}{$widgetID}{literal}'), // Optional, used for some extra CSS styling
                offset: 0 // Optional, the distance between grid items       
            };
        setInterval( function() { handler{/literal}{$widgetID}{literal}.wookmark(options); } , 1000)
            
      }
      //setTimeout(setWook(), 1000);
    });
        {/literal}
    </script> 
{/if}
{assign var="i" value=1}

<div class="phpld-grid listing-style-{$linkStyle}" {if $linkColumns > 1} id="woogrid{$widgetID}" {/if} style="position: relative;">
    {foreach from=$links item=LINK name=name}
        <div class="{$gridClass}{if $LINK.FEATURED} featured{/if}">
            {$LINK->listing($linkStyle)}
        </div>
    {/foreach}
</div>*}
{if !empty($PAGETITLE)}
<li class="group">{$PAGETITLE}</li>
{else}
<li class="group">Links</li>
{/if}
{foreach from=$links item=link name=name}
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

