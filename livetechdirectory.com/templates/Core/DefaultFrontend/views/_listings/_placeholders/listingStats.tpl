{if $comments_on == 1 OR $ratings_on == 1}
    <div class="stats convo">
        <span class="review">{if $comments_on == 1}{if $LINK.COMMENT_COUNT !=''}{$LINK.COMMENT_COUNT}{else}0{/if} Reviews.{/if}{if $ratings_on == 1} Rating: {$LINK.RATING/$LINK.VOTES|truncate:4:""} Total Votes: {if !$LINK.VOTES}0{else}{$LINK.VOTES}{/if}{/if}</span>
    </div>
{/if}