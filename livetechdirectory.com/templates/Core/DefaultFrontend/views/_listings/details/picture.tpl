<div class="listing-details listing-details-picture">
    <h1>{$LISTING_URL_TITLE}</h1>
    <div class="description-detail">
        {$LISTING_IMAGE_WITH_URL}
        {$LINK.DESCRIPTION}
        <div class="read-more">
            <a href="{$LINK.URL}" class="phpld-button">Find Out More</a>
        </div>

    </div>
    <div class="phpld-clearfix"></div>
    {$LISTING_SUBMIT_ITEMS}
    {if $add_links}

        <div>
            <div class="phpld-label float-left">{l}Deep Links{/l}:</div>
            <div class="smallDesc float-left">
                {foreach from=$add_links item=add_link name=add_links}
                    <a href="{$add_link.URL}" target="_blank" title="{$add_link.TITLE}">{$add_link.TITLE}</a>{if !$smarty.foreach.add_links.last}, {/if}
                {/foreach}
            </div>
        </div>
    {/if}
    {$PAGERANK}
    {$ADDRESS}
    {$LISTING_TELL_FRIEND}
    {$LISTING_RATING}
    {$LISTING_COMMENTS}
</div>