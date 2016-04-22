{if $success}
<div class="box success">
	{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="Information" titleheading=$titleheading}
	<p>{l}Your e-mail has been confirmed and the account has been activated. You may now log in and submit links.{/l}</p>
</div>

	{strip} {*Please enter your username and password to log in.*}
	{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="User Login" titleheading=$titleheading}
	<form method="post" id="submit_form" class="phpld-form" action="{$smarty.const.DOC_ROOT}/login">
		<div class="formPage">
			<div class="phpld-columnar">
				{if $failed}
					<div class="box error">{l}Invalid username or password.{/l}</div>
				{/if}
				{if $no_permission}
					<div class="box error">{l}No permissions set for this user.{/l}</div>
				{/if}
			</div>
			<div class="phpld-columnar phpld-equalize">
				<div class="phpld-label float-left">{l}User{/l}</div>
				<div class="phpld-fbox-text float-left">
					<input type="text" name="user" value="{$user}" size="40" maxlength="{$smarty.const.USER_LOGIN_MAX_LENGTH}" class="text" />
				</div>
			</div>
			<div class="phpld-columnar phpld-equalize">
				<div class="phpld-label float-left">{l}Password{/l}</div>
				<div class="phpld-fbox-text float-left">
					<input type="password" name="pass" value="" size="40" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
				</div>
			</div>
			<div class="phpld-columnar">
				<div class="phpld-label float-right">
					<a href="{$smarty.const.DOC_ROOT}/user/register" title="{l}Register{/l}">{l}Register{/l}</a>
				</div>
				<div class="phpld-label float-right">
					<a href="{$smarty.const.DOC_ROOT}/user/sendpassword" title="{l}Recover your password{/l}">{l}I forgot my password{/l}</a>
				</div>
			</div>
			<div style="clear: both;"></div>
			<div class="phpld-columnar phpld-equalize">
				<div class="phpld-fbox-button">
					<div class="float-right">
						<input type="submit" name="login" value="Login" class="button" />
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="formSubmitted" value="1" />
	</form>
	{/strip}

{else}
<div class="box error">
	{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="Error" titleheading=$titleheading}
	<p>{l}There was an error with the account activation.{/l}</p>
</div>
{/if}