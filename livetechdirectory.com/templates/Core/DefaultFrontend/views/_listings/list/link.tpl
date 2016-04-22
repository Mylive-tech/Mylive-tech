<div class="listing-link listing-list-item listing-list linearize-level-1">
        {$LISTING_URL_TITLE}
        <div class="phpld-clearfix"></div>
        <div class="description listing-field">
            <p>{$ANNOUNCE} - {$READ_MORE_LINK}</p>
        </div>

		{if $LINK.URL}
			<div class="link-url-not-linked">
				<span class="link">{$LINK.URL|escape|trim}</span>
			</div>
		{/if}
        <div class="phpld-clearfix"></div>
        {if $smarty.const.SHOW_PAGERANK}
            <div class="page-rank">{$PAGERANK}</div>
        {/if}
        {if $ratings_on == 1 && $smarty.const.LINK_RATING_DISPLAY == 'image'}
            <div class="link_rating convo">
                {$RATING_STARS}
            </div>
        {/if}
    <div class="phpld-clearfix"></div>
</div>
