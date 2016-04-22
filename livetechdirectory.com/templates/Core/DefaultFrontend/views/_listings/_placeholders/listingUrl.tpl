{if $LINK.URL}
<div class="url">
    <label>{l}Website URL{/l}: </label><a class="link" id="id_{$LINK.ID}" href="{$LINK->getUrl()|escape|trim}" title="{$LINK.TITLE|escape|trim}"
    {if $LINK.NOFOLLOW} rel="nofollow"{/if} target="_blank">
        {$LINK.URL|escape|trim}
    </a>
</div>
{/if}