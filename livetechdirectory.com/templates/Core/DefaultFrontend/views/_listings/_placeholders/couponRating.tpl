<div class="thumb-rating">
    <div class="title">
        {l}Success{/l}
    </div>

    <div class="rating">
        {(($LINK.RATING/$LINK.VOTES)*100)|ceil}%
    </div>
    <div class="thumbs">
        {if $smarty.const.REQUIRE_REGISTERED_USER_LINK_RATING eq 1 && !empty($regular_user_details)}
            {if $ratingError}
                <div class="box error">{l}Please choose your rating, and after this click Rate{/l}.</div>
            {/if}


            <form action="{$smarty.const.DOC_ROOT}/listing/rate/{$LINK.ID}" method="post">
                <input type="hidden" name="RATING" value="1">
                <input type="submit" value="" class="thumb-up-icon">
            </form>
            <form action="{$smarty.const.DOC_ROOT}/listing/rate/{$LINK.ID}" method="post">
                <input type="hidden" name="RATING" value="0">
                <input type="submit" value="" class="thumb-down-icon">
            </form>
            {else}
            {*<a href="{$smarty.const.DOC_ROOT}/login" class="phpld-button">{l}You must be logged in to leave a rating{/l}.</a>*}

        {/if}
    </div>
</div>