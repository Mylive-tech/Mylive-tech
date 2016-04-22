<li class="group">{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="User Area" titleheading=$titleheading}</li>

{if $profileUpdate}
<li>{l}Profile updated{/l}</li>

{/if}


<li>  <a href="{$smarty.const.DOC_ROOT}/user/submissions" title="{l}Browse personal links{/l}">{l}My Submissions{/l}</a></li>

<li>  <a href="{$smarty.const.DOC_ROOT}/user/profile" title="{l}Edit Profile{/l}">{l}Edit Profile{/l}</a>
</li>