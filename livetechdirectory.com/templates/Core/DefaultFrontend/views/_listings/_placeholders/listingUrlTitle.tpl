{if $LINK.TITLE}
    {* <a class="listing-title" id="id_{$LINK.ID}" href="{$LINK->getUrl()|escape|trim}" title="{$LINK.TITLE|escape|trim}" *}		<a class="listing-title" id="ID_{$LINK.ID}" href="{if $LINK.URL}{$LINK.URL}{else}{$LINK->getUrl()|escape|trim}{/if}"
    {if $LINK.NOFOLLOW} rel="nofollow"{/if}>
    {$LINK.TITLE|escape|trim}
</a>
{/if}