{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="Latest" titleheading="3"}
<div class="listing-style-list">
    {if $links->countWithoutLimit() > 0}
        {foreach from=$links item=LINK}
            {$LINK->listing()}
        {/foreach}
    {/if}
</div>