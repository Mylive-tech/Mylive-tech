<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 17:38:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1328643145654a0982a4a53-62273453%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '007171a6a45e1d5d0d01fc453ef992e745d6f69c' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/login.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1328643145654a0982a4a53-62273453',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'titleheading' => 0,
    'failed' => 0,
    'no_permission' => 0,
    'user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654a0982da575_85340983',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654a0982da575_85340983')) {function content_5654a0982da575_85340983($_smarty_tpl) {?> <form method="post" action="" id="submit_form" class="phpld-form"><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"User Login",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>
<div class="formPage"><div class="phpld-columnar"><?php if ($_smarty_tpl->tpl_vars['failed']->value){?><div class="box error">Invalid username or password.</div><?php }?><?php if ($_smarty_tpl->tpl_vars['no_permission']->value){?><div class="box error">No permissions set for this user.</div><?php }?></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">User</div><div class="phpld-fbox-text float-left"><input type="text" name="user" value="<?php echo $_smarty_tpl->tpl_vars['user']->value;?>
" maxlength="<?php echo @USER_LOGIN_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Password</div><div class="phpld-fbox-text float-left"><input type="password" name="pass" value="" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-fbox-button"><div class=""><input type="submit" name="login" value="Login" class="button" /></div></div></div><div class="forgot-pass-label"><a href="<?php echo @DOC_ROOT;?>
/user/register" title="Register">Register</a><a href="<?php echo @DOC_ROOT;?>
/user/sendpassword" title="Recover your password">I forgot my password</a></div></div><input type="hidden" name="formSubmitted" value="1" /></form><?php }} ?>