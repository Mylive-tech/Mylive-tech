{if $LINK.TITLE} 
    <a class="listing-title" id="id_{$LINK.ID}" href="{$LINK->getUrl()|escape|trim}" title="{$LINK.TITLE|escape|trim}"  
    {if $LINK.NOFOLLOW} rel="nofollow"{/if}> 
    {$LINK.TITLE|escape|trim} 
</a> 
{/if} 