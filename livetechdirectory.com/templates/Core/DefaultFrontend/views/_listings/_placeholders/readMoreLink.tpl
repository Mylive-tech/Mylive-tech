<a class="readMore" href="{$LINK->getUrl()}" title="{l}Read more about{/l}: {$LINK.TITLE|escape|trim}">{l}Read&nbsp;more{/l}</a>


{if !empty ($regular_user_details) and ($regular_user_details.ID == $LINK.OWNER_ID)}
    ,&nbsp;<a class="readMore" href="{$smarty.const.DOC_ROOT}/submit?linkid={$LINK.ID}" title="{l}Edit or Remove your link{/l}">{l}Review{/l}</a>
{/if}