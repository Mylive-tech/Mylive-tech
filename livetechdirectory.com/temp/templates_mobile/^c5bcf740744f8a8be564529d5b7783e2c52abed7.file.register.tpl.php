<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:41
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/user/register.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20578965065654ac95f30413-76794849%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c5bcf740744f8a8be564529d5b7783e2c52abed7' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/user/register.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20578965065654ac95f30413-76794849',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'titleheading' => 0,
    'profileUpdate' => 0,
    'LOGIN' => 0,
    'languages' => 0,
    'LANGUAGE' => 0,
    'EMAIL' => 0,
    'user_registration' => 0,
    'ALLOW_AUTHOR_INFO' => 0,
    'ALLOW_AVATARS' => 0,
    'hasLinks' => 0,
    'OWNER_NEWSLETTER_ALLOW' => 0,
    'INFO' => 0,
    'InfoLimit' => 0,
    'WEBSITE_NAME' => 0,
    'WEBSITE' => 0,
    'error_list' => 0,
    'errorKey' => 0,
    'errorItem' => 0,
    'DO_MATH_N1' => 0,
    'DO_MATH_N2' => 0,
    'DO_MATH' => 0,
    'imagehash' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654ac9605ade1_82964280',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac9605ade1_82964280')) {function content_5654ac9605ade1_82964280($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
if (!is_callable('smarty_function_formtool_count_chars')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.formtool_count_chars.php';
?><li class="group"><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Registration Information",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>
</li><li><form method="post" action="" enctype="multipart/form-data" name="profile_form" id="submit_form" class="phpld-form"><input type="hidden" name="ANONYMOUS" value="0" /><div class="formPage"><?php if ($_smarty_tpl->tpl_vars['profileUpdate']->value){?><div><h3>Profile updated</h3></div><?php }?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Login:</div><div class="phpld-fbox-text float-left"><input type="text" name="LOGIN" value="<?php echo $_smarty_tpl->tpl_vars['LOGIN']->value;?>
" size="20" maxlength="<?php echo @USER_LOGIN_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Password:</div><div class="phpld-fbox-text float-left"><input type="password" name="PASSWORD" id="PASSWORD" value="" size="20" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Confirm Password:</div><div class="phpld-fbox-text float-left"><input type="password" name="PASSWORDC" id="PASSWORDC" value="" size="20" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Language:</div><div class="phpld-fbox-select float-left"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['languages']->value,'selected'=>$_smarty_tpl->tpl_vars['LANGUAGE']->value,'name'=>"LANGUAGE"),$_smarty_tpl);?>
</div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Email:</div><div class="phpld-fbox-text float-left"><input type="text" name="EMAIL" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['EMAIL']->value, ENT_QUOTES, 'UTF-8', true);?>
" size="20" maxlength="255" class="text" /></div></div><?php if (@ALLOW_ANONYMOUS){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span>*</span>Anonymous:</div><div class="phpld-fbox-check float-left"><input type="checkbox" name="ANONYMOUS" value="1" /></div></div><?php }?><?php if ($_smarty_tpl->tpl_vars['user_registration']->value==0&&$_smarty_tpl->tpl_vars['ALLOW_AUTHOR_INFO']->value==1){?><div><h3>Optional Info For About Page</h3></div><!-- author avatar related --><?php if ($_smarty_tpl->tpl_vars['ALLOW_AVATARS']->value==1){?><?php }?><!-- end --><?php if ($_smarty_tpl->tpl_vars['hasLinks']->value>0){?><div class="phpld-columnar phpld-equalize"><div class="phpld-fbox-check float-left"><input type="checkbox" name="OWNER_NEWSLETTER_ALLOW" <?php if ($_smarty_tpl->tpl_vars['OWNER_NEWSLETTER_ALLOW']->value>=1){?>checked="checked"<?php }?> /></div><div class="phpld-label float-left"><p class="small">Allow site administrator to send me newsletters.</p></div></div><?php }?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Info:</div><div class="phpld-fbox-text float-left"><textarea name="INFO" rows="3" cols="37" class="text" <?php echo smarty_function_formtool_count_chars(array('name'=>"INFO",'limit'=>"255",'alert'=>true),$_smarty_tpl);?>
><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['INFO']->value, ENT_QUOTES, 'UTF-8', true));?>
</textarea></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Limit:</div><div class="phpld-fbox-text float-left"><input type="text" name="INFO_limit" size="4" class="limit_field" readonly="readonly" value="<?php echo trim($_smarty_tpl->tpl_vars['InfoLimit']->value);?>
" /></div></div><div class="phpld-clearfix"></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Website Name:</div><div class="phpld-fbox-text float-left"><input type="text" name="WEBSITE_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['WEBSITE_NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
" size="40" maxlength="255" class="text"/></div></div><div class="phpld-clearfix"></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Website URL:</div><div class="phpld-fbox-text float-left"><input type="text" name="WEBSITE" value="<?php if (!$_smarty_tpl->tpl_vars['WEBSITE']->value){?>http://<?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['WEBSITE']->value, ENT_QUOTES, 'UTF-8', true));?>
<?php }?>" size="40" maxlength="255" class="text"/></div></div><div><h3></h3></div><?php }?><?php if (@VISUAL_CONFIRM==2){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>DO THE MATH:</div><div class="phpld-error float-left"><span style="color: red;"><?php  $_smarty_tpl->tpl_vars['errorItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['errorItem']->_loop = false;
 $_smarty_tpl->tpl_vars['errorKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['error_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['errorItem']->key => $_smarty_tpl->tpl_vars['errorItem']->value){
$_smarty_tpl->tpl_vars['errorItem']->_loop = true;
 $_smarty_tpl->tpl_vars['errorKey']->value = $_smarty_tpl->tpl_vars['errorItem']->key;
?><?php if ($_smarty_tpl->tpl_vars['errorKey']->value=='DO_MATH'){?><?php if (is_array($_smarty_tpl->tpl_vars['errorItem']->value)){?><?php echo $_smarty_tpl->tpl_vars['errorItem']->value['remote'];?>
<br/><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['errorItem']->value;?>
<br/><?php }?><?php }?><?php } ?></span><font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;"><?php echo $_smarty_tpl->tpl_vars['DO_MATH_N1']->value;?>
 + <?php echo $_smarty_tpl->tpl_vars['DO_MATH_N2']->value;?>
 = </font><input type="text" id="DO_MATH" name="DO_MATH" value='<?php echo $_smarty_tpl->tpl_vars['DO_MATH']->value;?>
' class="text" style="width: 60px;"/><br/><br/></div></div><?php }?><?php if (@VISUAL_CONFIRM==1){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Confirmation code<p class="small">This helps prevent automated registrations.</p></div><div class="phpld-fbox-text float-left"><input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" /><input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" maxlength="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" class="text" /></div><div class="phpld-fbox-text float-left"><img src="<?php echo @DOC_ROOT;?>
/captcha.php?imagehash=<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" class="captcha" alt="Visual Confirmation Security Code" title="Visual Confirmation Security Code" /></div></div><?php }?><div>&nbsp;</div><div class="phpld-columnar phpld-equalize"><div class="phpld-fbox-button"><input type="submit" name="send" value="Register" class="button" /></div></div></div><input type="hidden" name="formSubmitted" value="1" /></form></li><?php }} ?>