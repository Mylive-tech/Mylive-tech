<div class="listing-details listing-details-default">
    <h1>{$LINK.TITLE}</h1>
    {$WEBPAGE_URL}
        <div class="link-category">
		{l}Category{/l}: {$LISTING_CATEGORIES_DETAILS}
	</div>
    <div class="description-detail">
        {$LINK.DESCRIPTION}
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
    {$LISTING_CONTACT_LISTING}
    {$LISTING_TELL_FRIEND}
    {$LISTING_RATING}
    {$LISTING_COMMENTS}
</div>