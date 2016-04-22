<div class="listing-list-item listing-list linearize-level-1">
        <h4>{$LISTING_VIDEO_URL_TITLE}</h4> 
        <div class="phpld-box list-headline"><span class="date"> {$LINK.DATE_ADDED|date_format} | </span><div class="listing-categories">{$LISTING_CATEGORIES}</div></div>
        {if $smarty.const.SHOW_PAGERANK}
            <div class="page-rank">{$PAGERANK}</div>
        {/if}
        <div class="phpld-clearfix"></div>
        <div class="description listing-field">
            {$VIDEO_THUMBNAIL}
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
