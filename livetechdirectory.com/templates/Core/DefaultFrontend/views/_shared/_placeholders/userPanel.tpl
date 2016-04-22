{*if ($smarty.const.REQUIRE_REGISTERED_USER == 1)*}
{if 1==1}
<div class="userPanel">
    {if empty($regular_user_details)}
        <a href="{$smarty.const.DOC_ROOT}/login" class="btn-slide">{l}Login{/l}</a>
        &nbsp;|&nbsp;
        <a href="{$smarty.const.DOC_ROOT}/user/register" title="{l}Register new user{/l}">{l}Register{/l}</a>
        {else}
        {l}Welcome:{/l} <strong>{$regular_user_details.NAME|escape}</strong> [<a href="{$smarty.const.DOC_ROOT}/logout" title="{l}Log out of this account{/l}">{l}Sign Out{/l}</a>, <a href="{$smarty.const.DOC_ROOT}/user" title="{l}Edit your account settings{/l}">{l}My Account{/l}</a>]
    {/if}
</div>
{/if}