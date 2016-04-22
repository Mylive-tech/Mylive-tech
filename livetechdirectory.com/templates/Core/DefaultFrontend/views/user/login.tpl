{strip} {*Please enter your username and password to log in.*}
<form method="post" action="" id="submit_form" class="phpld-form">
{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="User Login" titleheading=$titleheading}
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
                <input type="text" name="user" value="{$user}" maxlength="{$smarty.const.USER_LOGIN_MAX_LENGTH}" class="text" />
             </div>
          </div>
          <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Password{/l}</div>
            <div class="phpld-fbox-text float-left">
                <input type="password" name="pass" value="" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
           </div>
          </div>
          <div class="phpld-columnar phpld-equalize">
             <div class="phpld-fbox-button">
                 <div class="">
                    <input type="submit" name="login" value="Login" class="button" />
                 </div>
             </div>
          </div>
          <div class="forgot-pass-label">
                  <a href="{$smarty.const.DOC_ROOT}/user/register" title="{l}Register{/l}">{l}Register{/l}</a>
                  <a href="{$smarty.const.DOC_ROOT}/user/sendpassword" title="{l}Recover your password{/l}">{l}I forgot my password{/l}</a>
          </div>

      </div>
      <input type="hidden" name="formSubmitted" value="1" />
</form>
{/strip}