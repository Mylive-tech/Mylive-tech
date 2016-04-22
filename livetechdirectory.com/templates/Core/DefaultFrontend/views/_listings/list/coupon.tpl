<div class="listing-list-item listing-list linearize-level-1">
    {$LISTING_RATING}
    <h4>{$LISTING_URL_TITLE}</h4>
    {l}Code{/l}: <a href="javascript:void(null);"  rel-data="{$LINK.COUPON_CODE}" class="clipboard_enabled coupon_code"><span>{$LINK.COUPON_CODE}</span></a>
    <div class="phpld-box list-headline">
        <span class="date"> {$LINK.DATE_ADDED|date_format} | </span>
        <div class="listing-categories">{$LISTING_CATEGORIES}</div>
    </div>
    <div class="phpld-clearfix"></div>
    <div class="description listing-field">
    {$ANNOUNCE}... {$READ_MORE_LINK}
    </div>
    <div class="phpld-clearfix"></div>
{if $ratings_on == 1 && $smarty.const.LINK_RATING_DISPLAY == 'image'}
    <div class="link_rating convo">
        {$RATING_STARS}
    </div>
{/if}
    <div class="phpld-clearfix"></div>
</div>
