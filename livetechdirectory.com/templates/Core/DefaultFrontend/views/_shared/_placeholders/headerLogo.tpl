{if $smarty.const.FRONT_LOGO != '' AND $smarty.const.FRONT_LOGO != 'FRONT_LOGO'}
    <a href="{$smarty.const.DOC_ROOT}/" title="{$smarty.const.SITE_NAME}">
        <img src="{$smarty.const.DOC_ROOT}/logo_thumbnail.php?pic=/{$smarty.const.FRONT_LOGO}&amp;width={$LOGO_OPTIONS.widthValue}" style="{$LOGO_STYLES}" alt="{$smarty.const.SITE_NAME}" />
    </a>
{else}
    <h1><a href="{$smarty.const.DOC_ROOT}/">{$SITE_NAME}</a></h1>
{/if}