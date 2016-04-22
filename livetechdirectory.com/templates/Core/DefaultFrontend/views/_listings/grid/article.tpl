<div class="listing-list-item listing-grid">
    <h4>{$LISTING_ARTICLE_TITLE}</h4>

    <div class="description listing-field">
        {$ANNOUNCE}
    </div>
    <div class="phpld-clearfix"></div>
    <div class="convo-continer">
        <div class="listing-categories convo"><span>{l}Categories{/l}</span>:{$LISTING_CATEGORIES}</div>
        {if $ratings_on == 1 && $smarty.const.LINK_RATING_DISPLAY == 'image'}
            <div class="link_rating">
                {$RATING_STARS}
            </div>
        {/if}
        {$LISTING_STATS}
        <div class="convo">
            {$READ_MORE_LINK}
        </div>

    </div>
</div>