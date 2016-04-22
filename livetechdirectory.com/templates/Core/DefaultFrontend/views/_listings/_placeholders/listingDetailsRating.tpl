<div>
    <fieldset>
        <b>{l}Average rating{/l}:</b>
    {if $smarty.const.LINK_RATING_DISPLAY == 'image'}
        {if $LINK.RATING >0 }
            {section name=foo loop=$LINK.RATING}
                <img src="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/images/tiny_star.gif">
            {/section}
            {if intval($LINK.RATING) < $LINK.RATING}
                <img src="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/images/tiny_star_half.gif">
            {/if}
        {/if}
        {elseif $smarty.const.LINK_RATING_DISPLAY == 'bar'}
        {if $LINK.RATING >0 }
            {section name=foo loop=$LINK.RATING}
                <img src="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/images/bar_full.png">
            {/section}
            {if intval($LINK.RATING) < $LINK.RATING}
                <img src="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/images/bar_full_half.png">
                <img src="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/images/bar_empty_half.png">
            {/if}
            {section name=foo loop=$LINK.LEFT_RATING}
                <img src="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/images/bar_empty.png">
            {/section}
        {/if}
        {else}
        {$LINK.RATING}
    {/if} ({if !$LINK.VOTES}0{else}{$LINK.VOTES}{/if} {l}votes{/l})

{if ($smarty.const.REQUIRE_REGISTERED_USER_LINK_RATING eq 1 && !empty($regular_user_details)) || $smarty.const.REQUIRE_REGISTERED_USER_LINK_RATING eq 0}
   {if $ratingError}<div class="box error">{l}Please choose your rating, and after this click Rate{/l}.</div>{/if}
    <label for="RATING">{l}Rate the link{/l}: &nbsp;</label>
    <form name="rating" action="{$smarty.const.DOC_ROOT}/listing/rate/{$LINK.ID}" method="post"  class="phpld-form">
     <div class="phpld-columnar phpld-equalize">
        <div class="phpld-fbox-select float-left">
            <select id="RATING" name="RATING" class="text" {$rating_disabled}>
                <option value=""></option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </div>
           <div class="phpld-fbox-button float-left">
              <div class="nopadding">
                <input type="submit" value="{l}Rate{/l}" class="button" {$rating_disabled}>
              </div>
           </div>
     </div>

    </form>

    {else}
        <br /><a href="{$smarty.const.DOC_ROOT}/login" class="phpld-button">{l}You must be logged in to leave a rating{/l}.</a>
    {/if}

    </fieldset>
</div>