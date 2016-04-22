{if $show_title eq 1}
{$TITLE}
<br/>
{/if}
<form method="post" action="{$smarty.const.DOC_ROOT}/login" onsubmit="ajaxFunction();">

 {if ($smarty.const.REQUIRE_REGISTERED_USER == 1 || $smarty.const.REQUIRE_REGISTERED_USER_ARTICLE == 1) && empty($user_details)}
<table border="0" align="center" width="40%" style="padding-top: 10px; z-index: -1; height: 0px">
	<tr>
   	<td>{l}User{/l}:</td>
      <td>
       	<input type="text" name="user" value="{$user}" size="10" maxlength="{$smarty.const.USER_LOGIN_MAX_LENGTH}" class="text" />
      </td>
   </tr>
   <tr>
   	<td>{l}Password{/l}:</td>
      <td>
      	<input type="password" name="pass" value="" size="10" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
      </td>
   </tr>
   <tr>
       <td colspan="2" align="center"><input type="submit" name="submit" value="Login" class="btn" /></td>
   </tr>
   <tr>
   	<td colspan="2" style="text-align: left;">
   		<input type="checkbox" name="rememberMe">&nbsp;&nbsp; Keep me logged in.
      </td>        
   </tr>
</table>
<ul class="boxPopCats">
	<li><a href="{$smarty.const.DOC_ROOT}/user/register" title="{l}Register{/l}">{l}Register{/l}</a></li>
	<li><a href="{$smarty.const.DOC_ROOT}/user/sendpassword" title="{l}Recover your password{/l}">{l}I forgot my password{/l}</a></li>
</ul>
{elseif ($smarty.const.REQUIRE_REGISTERED_USER == 1 || $smarty.const.REQUIRE_REGISTERED_USER_ARTICLE == 1)}
	<br />
	<div align="center" style="font-weight: bold; ">{l}Welcome{/l} {$user_details.NAME}!</div>
	<ul class="boxPopCats">
		<li><a href="{$smarty.const.DOC_ROOT}/user" title="{l}Edit your account settings{/l}">{l}My Account{/l}</a></li>
		<li><a href="{$smarty.const.DOC_ROOT}/logout" title="{l}Log out of this account{/l}">{l}Sign Out{/l}</a></li>
	</ul>
{/if}
</form>