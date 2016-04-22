<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:47:41
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/profile.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1491396127535abbdd1effd1-60480182%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a4588c9cf7bc8686b12e8e8a61ed5a1acdae7978' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/profile.tpl',
      1 => 1392437106,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1491396127535abbdd1effd1-60480182',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'titleheading' => 0,
    'profileUpdate' => 0,
    'user_registration' => 0,
    'user' => 0,
    'languages' => 0,
    'ALLOW_AVATARS' => 0,
    'hasLinks' => 0,
    'OWNER_NEWSLETTER_ALLOW' => 0,
    'InfoLimit' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535abbdd2aaff6_47607844',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abbdd2aaff6_47607844')) {function content_535abbdd2aaff6_47607844($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
if (!is_callable('smarty_function_formtool_count_chars')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.formtool_count_chars.php';
?><form method="post" action="" enctype="multipart/form-data" name="profile_form" id="submit_form" class="phpld-form"><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Registration Information",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>
<div class="formPage"><?php if ($_smarty_tpl->tpl_vars['profileUpdate']->value){?><div><h3>Profile updated</h3></div><?php }?><?php if ($_smarty_tpl->tpl_vars['user_registration']->value==1){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Username:</div><div class="phpld-fbox-text float-left"><input type="text" name="LOGIN" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['LOGIN'];?>
" size="20" maxlength="<?php echo @USER_LOGIN_MAX_LENGTH;?>
" class="text" /></div></div><?php }?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Name:</div><div class="phpld-fbox-text float-left"><input type="text" name="NAME" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['NAME'];?>
" size="20" maxlength="<?php echo @USER_NAME_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Password:</div><div class="phpld-fbox-text float-left"><input type="password" name="PASSWORD" id="PASSWORD" value="" size="20" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Confirm Password:</div><div class="phpld-fbox-text float-left"><input type="password" name="PASSWORDC" id="PASSWORDC" value="" size="20" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Language:</div><div class="phpld-fbox-select float-left"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['languages']->value,'selected'=>$_smarty_tpl->tpl_vars['user']->value['LANGUAGE'],'name'=>"LANGUAGE"),$_smarty_tpl);?>
</div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Email:</div><div class="phpld-fbox-text float-left"><input type="text" name="EMAIL" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value['EMAIL'], ENT_QUOTES, 'UTF-8', true);?>
" size="20" maxlength="255" class="text" /></div></div><?php if ($_smarty_tpl->tpl_vars['user_registration']->value==0&&@ALLOW_AUTHOR_INFO==1){?><div><h3>Optional Info For Author Page</h3></div><!-- author avatar related --><?php if ($_smarty_tpl->tpl_vars['ALLOW_AVATARS']->value==1){?><?php }?><!-- end --><?php if ($_smarty_tpl->tpl_vars['hasLinks']->value>0){?><div class="phpld-columnar phpld-equalize"><div class="phpld-fbox-check float-left"><input type="checkbox" name="OWNER_NEWSLETTER_ALLOW" <?php if ($_smarty_tpl->tpl_vars['OWNER_NEWSLETTER_ALLOW']->value>=1){?>checked="checked"<?php }?> /></div><div class="phpld-label float-left"><p class="small">Allow site administrator to send me newsletters.</p></div></div><?php }?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Info:</div><div class="phpld-fbox-text float-left"><textarea name="INFO" id="INFO" rows="3" cols="10" class="text" <?php echo smarty_function_formtool_count_chars(array('name'=>"INFO",'limit'=>"255",'alert'=>true),$_smarty_tpl);?>
><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['user']->value['INFO'], ENT_QUOTES, 'UTF-8', true));?>
</textarea><div class="limitDesc float-left">Limit:<input type="text" name="INFO_limit" cols="4" class="limit_field" readonly="readonly" value="<?php echo trim($_smarty_tpl->tpl_vars['InfoLimit']->value);?>
" /></div></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Website Name:</div><div class="phpld-fbox-text float-left"><input type="text" name="WEBSITE_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['user']->value['WEBSITE_NAME'], ENT_QUOTES, 'UTF-8', true));?>
" size="40" maxlength="255" class="text"/></div></div><div class="phpld-clearfix"></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Website URL:</div><div class="phpld-fbox-text float-left"><input type="text" name="WEBSITE" value="<?php if (!$_smarty_tpl->tpl_vars['user']->value['WEBSITE']){?>http://<?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['user']->value['WEBSITE'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?>" size="40" maxlength="255" class="text"/></div></div><?php }?><div class="phpld-columnar phpld-equalize"><div class="phpld-fbox-button"><input type="submit" name="send" value="Submit" class="button" /></div></div></div><input type="hidden" name="formSubmitted" value="1" /></form><?php }} ?>