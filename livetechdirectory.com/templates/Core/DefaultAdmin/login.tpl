<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>PHP Link Directory v{$smarty.const.CURRENT_VERSION} Admin - Login</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta name="robots" content="noindex,nofollow" />
   <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ROOT}/files/style.css" />
   <script type="text/javascript">
   {literal}
      var DOC_ROOT = '{$smarty.const.DOC_ROOT}';
   {/literal}
   </script>
   <script src="../javascripts/jquery-1.3.2.min.js"></script>
   
   <script src="../javascripts/jquery.validate.js"></script>
   <script src="../javascripts/jquery/jquery.field.min.js"></script>
   
   <script type="text/javascript">
   	{literal}jQuery.noConflict();{/literal}
   </script>
   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="login-form" validators=$validators}
</head>
<body>
{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
<!-- Main -->
<div id="wrap" class="login-page">
   <!-- Header -->
   <div id="header">
      <div id="header-title"><a href="http://www.phplinkdirectory.com/" title="{l}Visit PHP Link Directory homepage{/l}" class="phpldBackLink" target="_blank"><img src="{$smarty.const.TEMPLATE_ROOT}/images/phpldlogo.png" alt="PHP Link Directory" id="logo" /></a></div>
   </div>
   <!-- /Header -->

   {if $action eq 'sendpassword'}
      <div class="block">
         <form id="login-form" name="login-form" method="post" action="{$smarty.const.DOC_ROOT}/login.php?action=sendpassword">
         <div id="admin-login">
            <div class="admin-login-title">
            	{l}Administrator Password Recovery{/l} @ {$smarty.const.SITE_NAME}
            </div>
			<div class="admin-login-content">
            {if (isset($error) and $error gt 0) or !empty($sql_error)}
               <div class="error block">
                  <h2>{l}Error{/l}</h2>
                  <p>{l}An error occured while recovering your password{/l}!</p>
                  {if !empty($errorMsg)}
                     <p>{$errorMsg|escape}</p>
                  {/if}
               </div>
            {/if}

            {if $email_status eq '1'}
               <div class="success block">
                  <p>{l}Please check your email to confirm your new password request.{/l}</p>
               </div>
            {/if}

            <ul>
               <li>
                  <label for="admin-input" class="label">{l}Username{/l}</label>
                  <input type="text" id="admin-input" name="LOGIN" value="{$LOGIN|escape|trim}" class="text" />
               </li>
               <li>
                  <label for="email-input" class="label">{l}Email{/l}</label>
                  <input type="text" id="email-input" name="EMAIL" value="{$EMAIL|escape|trim}" maxlength="255" class="text" />
               </li>
            </ul>
            <div class="admin_btn">
	    		<label for="pass-input" class="label">&nbsp;</label>
	    		<div class="adm_btn_left"></div>
	    		<input type="submit" name="proceed" value="{l}Proceed{/l}" alt="{l}Recover password{/l}" title="{l}Recover your lost password{/l}" class="adm_btn_center" />
	    		<div class="adm_btn_right"></div>
	    	</div>
			<div style="clear: both;"></div><br/>
            <p><a href="{$smarty.const.DOC_ROOT}/login.php" title="{l}Login to your administrator control panel{/l}">{l}Login{/l}</a></p>
          </div>
		  <div class="admin-login-bot"></div>
         </div>
         <input type="hidden" name="formSubmitted" value="1" />
         </form>
      </div>
   {elseif $action eq 'confirm'}
      <div class="block">
         <div id="admin-login">
             <div class="admin-login-title">
             	{l}Administrator Password Recovery{/l} @ {$smarty.const.SITE_NAME}
             </div>
			<div class="admin-login-content">
            {if (isset($error) and $error gt 0) or !empty($sql_error)}
               <div class="error block">
                  <h2>{l}Error{/l}</h2>
                  <p>{l}An error occured while recovering your password{/l}!</p>
                  {if !empty($errorMsg)}
                     <p>{$errorMsg|escape}</p>
                  {/if}
               </div>
            {/if}

            {if $password_recovered}
               <div class="success block">
                  <p>{l}You can now login with your new password.{/l}</p>
               </div>
            {/if}
			<div style="clear: both;"></div><br/>
            <p><a href="{$smarty.const.DOC_ROOT}/login.php" title="{l}Login to your administrator control panel{/l}">{l}Login{/l}</a></p>
         </div>
		<div class="admin-login-bot"></div>
      </div>
   {else}
      <div class="block">
         <form id="login-form" name="login-form" method="post" action="{$smarty.const.DOC_ROOT}/login.php">
         <div id="admin-login">
            <div class="admin-login-title">
            	{l}Administrator Login{/l} @ {$smarty.const.SITE_NAME}
            </div>
            <div class="admin-login-content">
            	<!-- <div class="error" id="login_failed">{l}Invalid username or password.{/l}</div> -->
	            {if $failed}
	               <div class="error">{l}Invalid username or password.{/l}</div>
	            {/if}
	            {if $invalid}
	               <div class="error">{l}Some validation went wrong, please try again.{/l}</div>
	            {/if}
	            {if $no_permission}
	               <div class="error">{l}No permissions set for this user.{/l}</div>
	            {/if}
	            <ul>
	               <li>
	                  <label for="admin-input" class="label">{l}Username{/l}</label>
	                  <input type="text" id="admin-input" name="user" value="{$user|escape|trim}" class="text" />
	               </li>
	               <li>
	                  <label for="pass-input" class="label">{l}Password{/l}</label>
	                  <input type="password" id="pass-input" name="pass" value="" class="text" />
	                  <div class="clear: both;"></div>
	               </li>
	            </ul>
            	<div class="admin_btn">
            		<label for="pass-input" class="label">&nbsp;</label>
            		<div class="adm_btn_left"></div>
            		<input type="submit" name="login" value="{l}Login{/l}" alt="{l}Login{/l}" title="{l}Login to your administrator control panel{/l}" class="adm_btn_center" />
            		<div class="adm_btn_right"></div>
            		
            	</div>
            	<div style="clear: both;"></div><br/>
            	<p><a href="{$smarty.const.DOC_ROOT}/login.php?action=sendpassword" title="{l}Recover your password{/l}">{l}I forgot my password{/l}</a></p>
            </div>
            <div class="admin-login-bot"></div>
         </div>
         <input type="hidden" name="formSubmitted" value="1" />
         </form>
      </div>
   {/if}


   <!-- Footer -->
   <div id="footer">PHP Link Directory Phoenix v{$smarty.const.CURRENT_VERSION}, Copyright &copy; 2004-{php}echo date('Y');{/php} NetCreated.</div>
   <!-- /Footer -->
</div>
<!-- /Main -->
{/strip}
</body>
</html>