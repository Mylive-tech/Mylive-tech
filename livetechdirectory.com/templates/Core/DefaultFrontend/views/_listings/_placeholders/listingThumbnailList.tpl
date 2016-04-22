<div class="thumbnail">
    <a class="link" id="id_{$LINK.ID}" href="{$LINK->getUrl()}" title="{$LINK.TITLE|escape|trim}"
    {if $LINK.NOFOLLOW} rel="nofollow"{/if}>
    <img src="{$smarty.const.DOC_ROOT}/thumbnail.php?pic={$LINK.IMAGE}&amp;width={$LINK.THUMB_WIDTH}" width="{$LINK.THUMB_WIDTH}"  class="flexible bordered float-left" alt=""/>
</a>
</div>