{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="User Area" titleheading=$titleheading}

{if $profileUpdate}
<p>{l}Profile updated{/l}</p>

{/if}


<p>   <a href="{$smarty.const.DOC_ROOT}/user/submissions" title="{l}Browse personal links{/l}">{l}My Submissions{/l}</a></p>

<p>  <a href="{$smarty.const.DOC_ROOT}/user/profile" title="{l}Edit Profile{/l}">{l}Edit Profile{/l}</a>
</p>