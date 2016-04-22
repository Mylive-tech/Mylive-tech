<div class="listingsList">
    <ul>
    {foreach from=$LISTINGS item='LISTING'}
        <li>
            <a href="{$LISTING->getUrl()}" title="{$LISTING.TITLE}">{$LISTING.TITLE}</a>
        </li>
    {/foreach}
    </ul>
</div>