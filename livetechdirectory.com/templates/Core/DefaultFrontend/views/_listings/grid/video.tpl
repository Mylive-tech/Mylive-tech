<div class="listing-list-item listing-grid">
      <h4>{$LISTING_VIDEO_URL_TITLE}</h4> 
    {$VIDEO_THUMBNAIL}
    {$ANNOUNCE}

    <div class="phpld-clearfix"></div>
    <div class="convo-continer">
        {if $ratings_on == 1 && $smarty.const.LINK_RATING_DISPLAY == 'image'}
            <div class="link_rating">
                {$RATING_STARS}
            </div>
        {/if}
        <div class="listing-categories convo"><span>{l}Categories{/l}</span>:{$LISTING_CATEGORIES}</div>
        {$LISTING_STATS}
        <div class="convo">
            {$READ_MORE_LINK}
        </div>

    </div>
</div>