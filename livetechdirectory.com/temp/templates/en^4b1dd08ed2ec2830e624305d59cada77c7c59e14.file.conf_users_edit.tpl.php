<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:46:09
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_users_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:881890617535abb81da4bb9-81237456%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4b1dd08ed2ec2830e624305d59cada77c7c59e14' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_users_edit.tpl',
      1 => 1392437650,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '881890617535abb81da4bb9-81237456',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validators' => 0,
    'error' => 0,
    'sql_error' => 0,
    'errorMsg' => 0,
    'posted' => 0,
    'action' => 0,
    'LOGIN' => 0,
    'NAME' => 0,
    'languages' => 0,
    'LANGUAGE' => 0,
    'EMAIL' => 0,
    'cemail' => 0,
    'AUTH_IMG' => 0,
    'INFO' => 0,
    'WEBSITE_NAME' => 0,
    'WEBSITE' => 0,
    'admin_user' => 0,
    'LEVEL' => 0,
    'yes_no' => 0,
    'ACTIVE' => 0,
    'SUBMIT_NOTIF' => 0,
    'PAYMENT_NOTIF' => 0,
    'ID' => 0,
    'submit_session' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535abb81f11307_53388725',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abb81f11307_53388725')) {function content_535abb81f11307_53388725($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



<?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"edit_user_form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>


<?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while saving.</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if ($_smarty_tpl->tpl_vars['posted']->value){?><div class="success block">User saved.</div><?php }?><div class="block"><form method="post" action="" id="edit_user_form"><table class="formPage"><?php if (isset($_smarty_tpl->tpl_vars['action']->value)&&($_smarty_tpl->tpl_vars['action']->value=='N'||$_smarty_tpl->tpl_vars['action']->value=='E')){?><thead><tr><th colspan="2"><?php if ($_smarty_tpl->tpl_vars['action']->value=='N'){?>Create new user<?php }elseif($_smarty_tpl->tpl_vars['action']->value=='E'){?>Edit user<?php }?></th></tr></thead><?php }?><tbody><tr><td class="label required"><label for="LOGIN">Login:</label></td><td class="smallDesc"><input type="text" id="LOGIN" name="LOGIN" value="<?php echo trim($_smarty_tpl->tpl_vars['LOGIN']->value);?>
" maxlength="<?php echo @USER_LOGIN_MAX_LENGTH;?>
" class="text" /></td></tr><tr><td class="label required"><label for="NAME">Name:</label></td><td class="smallDesc"><input type="text" id="NAME" name="NAME" value="<?php echo trim($_smarty_tpl->tpl_vars['NAME']->value);?>
" maxlength="<?php echo @USER_NAME_MAX_LENGTH;?>
" class="text" /></td></tr><tr><td class="label<?php if ($_smarty_tpl->tpl_vars['action']->value=='N'){?> required<?php }?>"><label for="PASSWORD">Password:</label></td><td class="smallDesc"><input type="password" id="PASSWORD" name="PASSWORD" value="" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></td></tr><tr><td class="label<?php if ($_smarty_tpl->tpl_vars['action']->value=='N'){?> required<?php }?>"><label for="PASSWORDC">Confirm Password:</label></td><td class="smallDesc"><input type="password" id="PASSWORDC" name="PASSWORDC" value="" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></td></tr><tr><td class="label"><label for="LANGUAGE">Preffered Language:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['languages']->value,'selected'=>$_smarty_tpl->tpl_vars['LANGUAGE']->value,'name'=>"LANGUAGE",'id'=>"LANGUAGE"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="EMAIL">Email:</label></td><td class="smallDesc"><input type="text" id="EMAIL" name="EMAIL" value="<?php echo trim($_smarty_tpl->tpl_vars['EMAIL']->value);?>
" maxlength="255" class="text" /></td></tr><td class="label required"><label for="cemail">Confirm Email:</label></td><td class="smallDesc"><input type="checkbox" id="cemail" name="cemail" value="1" <?php if ($_smarty_tpl->tpl_vars['cemail']->value){?>checked="checked"<?php }?> /><p class="msg notice info">Check this to confirm an users email and allow their links to show.</p></td><!-- user avatar related --><!--<tr><td class="label">Current Photo:</td><td class="smallDesc" align="left"><img src="<?php echo $_smarty_tpl->tpl_vars['AUTH_IMG']->value;?>
"></td></tr>--><!-- end --><?php if (@ALLOW_AUTHOR_INFO==1){?><tr><td class="label required"><label for="INFO">Info:</label></td><td class="smallDesc"><textarea name="INFO" id="INFO" rows="3" cols="10" class="text" ><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['INFO']->value, ENT_QUOTES, 'UTF-8', true));?>
</textarea></td></tr><tr><td class="label required"><label for="WEBSITE_NAME">Website Name:</label></td><td class="smallDesc"><input type="text" name="WEBSITE_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['WEBSITE_NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
" maxlength="255" class="text"/></td></tr><tr><td class="label required"><label for="WEBSITE">Website :</label></td><td class="smallDesc"><input type="text" name="WEBSITE" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['WEBSITE']->value, ENT_QUOTES, 'UTF-8', true));?>
" maxlength="255" class="text"/></td></tr><?php }?><tr><td class="label required"><label for="LEVEL">User Type:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['admin_user']->value,'selected'=>$_smarty_tpl->tpl_vars['LEVEL']->value,'name'=>"LEVEL",'id'=>"LEVEL"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="ACTIVE">Active:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['yes_no']->value,'selected'=>$_smarty_tpl->tpl_vars['ACTIVE']->value,'name'=>"ACTIVE",'id'=>"ACTIVE"),$_smarty_tpl);?>
</td></tr><tr><td class="label"><label for="SUBMIT_NOTIF">Link Submit Notification:</label></td><td class="smallDesc"><input type="checkbox" id="SUBMIT_NOTIF" name="SUBMIT_NOTIF" value="1" <?php if ($_smarty_tpl->tpl_vars['SUBMIT_NOTIF']->value){?>checked="checked"<?php }?>/></td></tr><tr><td class="label"><label for="PAYMENT_NOTIF">Link Payment Notification:</label></td><td class="smallDesc"><input type="checkbox" id="PAYMENT_NOTIF" name="PAYMENT_NOTIF" value="1" <?php if ($_smarty_tpl->tpl_vars['PAYMENT_NOTIF']->value){?>checked="checked"<?php }?>/></td></tr></tbody><tfoot><tr><td><input type="reset" id="reset-user-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-user-submit" name="save" value="Save" alt="Save form" title="Save user" class="button" /></td></tr></tfoot></table><input type="hidden" name="formSubmitted" value="1" /><input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" /><input type="hidden" name="exclude_id" value="<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" /><input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" /></form></div><?php }} ?>