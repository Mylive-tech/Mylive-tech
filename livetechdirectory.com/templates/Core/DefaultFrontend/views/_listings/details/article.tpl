<div class="listing-details listing-details-article">
    <h1>{$LINK.TITLE}</h1>
    <div class="description-detail">
    {$LINK.DESCRIPTION}
    </div>
    <table style="width:100%; border:0px;">

    {$LISTING_SUBMIT_ITEMS}

    </table>
    {if $add_links}
    <tr>
        <td class="label">{l}Deep Links{/l}:</td>
        <td class="smallDesc" align="left">
            {foreach from=$add_links item=add_link name=add_links}
                <a href="{$add_link.URL}" target="_blank" title="{$add_link.TITLE}">{$add_link.TITLE}</a>{if !$smarty.foreach.add_links.last}, {/if}
            {/foreach}
        </td>
    </tr>
    {/if}
    {$PAGERANK}
    {$ADDRESS}
    {$LISTING_AUTHOR_INFO}
    {$LISTING_TELL_FRIEND}
    {$LISTING_RATING}
    {$LISTING_COMMENTS}
</div>