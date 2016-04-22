{if $show_title eq 1}
{$TITLE}
<br/>
{/if} 
<ul class="boxPopCats">{if empty($regular_user_details)}
   <li>   <a href="{$smarty.const.DOC_ROOT}/user/login" title="{l}Log In to your account{/l}">{l}Username{/l}</a>   </li>
    <li>     <a href="{$smarty.const.DOC_ROOT}/user/register" title="{l}Register new user{/l}">{l}Register{/l}</a> </li>
   {else}
      {l}Welcome:{/l} <strong>{$regular_user_details.NAME|escape}</strong>
	 <li>     <a href="{$smarty.const.DOC_ROOT}/logout" title="{l}Log out of this account{/l}">{l}Sign Out{/l}</a> </li>
	 <li>     <a href="{$smarty.const.DOC_ROOT}/user/profile" title="{l}Edit your account settings{/l}">{l}Edit My Account{/l}</a> </li>
       <li>     <a href="{$smarty.const.DOC_ROOT}/user/submissions" title="{l}Browse personal links{/l}">{l}View My Submissions{/l}</a> </li>

   {/if}
</ul>