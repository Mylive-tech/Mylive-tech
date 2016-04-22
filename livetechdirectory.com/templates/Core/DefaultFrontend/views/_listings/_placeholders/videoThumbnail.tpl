<div class="thumbnail">
<a class="link" id="id_{$LINK.ID}" href="{$LINK->getUrl()}" title="{$LINK.TITLE|escape|trim}"
{if $LINK.NOFOLLOW} rel="nofollow"{/if}>    
    {videothumb listing=$LINK}
</a>    
</div>