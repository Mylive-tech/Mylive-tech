<?php /* Smarty version Smarty-3.1.12, created on 2014-07-15 11:18:24
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/activate.tpl" */ ?>
<?php /*%%SmartyHeaderCode:179168721853c50e0032e668-77875308%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5a3617cb4041e6d431bc1b4d07535506f3e6771e' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/activate.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '179168721853c50e0032e668-77875308',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'success' => 0,
    'titleheading' => 0,
    'failed' => 0,
    'no_permission' => 0,
    'user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53c50e0039f379_01017770',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53c50e0039f379_01017770')) {function content_53c50e0039f379_01017770($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['success']->value){?>
<li class="group"><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"User Login",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>
</li>

<div class="box success">
	<?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Information",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>

	<p>Your e-mail has been confirmed and the account has been activated. You may now log in and submit links.</p>
</div>

	 <li><form method="post" id="submit_form" class="phpld-form" action="<?php echo @DOC_ROOT;?>
/login"><div class="formPage"><div class="phpld-columnar"><?php if ($_smarty_tpl->tpl_vars['failed']->value){?><div class="box error">Invalid username or password.</div><?php }?><?php if ($_smarty_tpl->tpl_vars['no_permission']->value){?><div class="box error">No permissions set for this user.</div><?php }?></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">User</div><div class="phpld-fbox-text float-left"><input type="text" name="user" value="<?php echo $_smarty_tpl->tpl_vars['user']->value;?>
" size="40" maxlength="<?php echo @USER_LOGIN_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Password</div><div class="phpld-fbox-text float-left"><input type="password" name="pass" value="" size="40" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" /></div></div><div class="phpld-columnar"><div class="phpld-label float-right"><a href="<?php echo @DOC_ROOT;?>
/user/register" title="Register">Register</a></div><div class="phpld-label float-right"><a href="<?php echo @DOC_ROOT;?>
/user/sendpassword" title="Recover your password">I forgot my password</a></div></div><div style="clear: both;"></div><div class="phpld-columnar phpld-equalize"><div class="phpld-fbox-button"><div class="float-right"><input type="submit" name="login" value="Login" class="button" /></div></div></div></div><input type="hidden" name="formSubmitted" value="1" /></form></li>

<?php }else{ ?>
<div class="box error">
	<?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Error",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>

	<p>There was an error with the account activation.</p>
</div>
<?php }?><?php }} ?>