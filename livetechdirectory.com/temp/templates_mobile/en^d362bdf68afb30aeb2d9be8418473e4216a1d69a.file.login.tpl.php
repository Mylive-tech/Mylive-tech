<?php /* Smarty version Smarty-3.1.12, created on 2014-12-19 07:56:05
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8470792815493da15705925-15841343%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd362bdf68afb30aeb2d9be8418473e4216a1d69a' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/login.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8470792815493da15705925-15841343',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validators' => 0,
    'action' => 0,
    'error' => 0,
    'sql_error' => 0,
    'errorMsg' => 0,
    'email_status' => 0,
    'LOGIN' => 0,
    'EMAIL' => 0,
    'password_recovered' => 0,
    'failed' => 0,
    'invalid' => 0,
    'no_permission' => 0,
    'user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5493da15842dc2_46432094',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5493da15842dc2_46432094')) {function content_5493da15842dc2_46432094($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>PHP Link Directory v<?php echo @CURRENT_VERSION;?>
 Admin - Login</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta name="robots" content="noindex,nofollow" />
   <link rel="stylesheet" type="text/css" href="<?php echo @TEMPLATE_ROOT;?>
/files/style.css" />
   <script type="text/javascript">
   
      var DOC_ROOT = '{$smarty.const.DOC_ROOT}';
   
   </script>
   <script src="../javascripts/jquery-1.3.2.min.js"></script>
   
   <script src="../javascripts/jquery.validate.js"></script>
   <script src="../javascripts/jquery/jquery.field.min.js"></script>
   
   <script type="text/javascript">
   	jQuery.noConflict();
   </script>
   <?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"login-form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>

</head>
<body>

<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<!-- Main --><div id="wrap" class="login-page"><!-- Header --><div id="header"><div id="header-title"><a href="http://www.phplinkdirectory.com/" title="Visit PHP Link Directory homepage" class="phpldBackLink" target="_blank"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/phpldlogo.png" alt="PHP Link Directory" id="logo" /></a></div></div><!-- /Header --><?php if ($_smarty_tpl->tpl_vars['action']->value=='sendpassword'){?><div class="block"><form id="login-form" name="login-form" method="post" action="<?php echo @DOC_ROOT;?>
/login.php?action=sendpassword"><div id="admin-login"><div class="admin-login-title">Administrator Password Recovery @ <?php echo @SITE_NAME;?>
</div><div class="admin-login-content"><?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while recovering your password!</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if ($_smarty_tpl->tpl_vars['email_status']->value=='1'){?><div class="success block"><p>Please check your email to confirm your new password request.</p></div><?php }?><ul><li><label for="admin-input" class="label">Username</label><input type="text" id="admin-input" name="LOGIN" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LOGIN']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></li><li><label for="email-input" class="label">Email</label><input type="text" id="email-input" name="EMAIL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['EMAIL']->value, ENT_QUOTES, 'UTF-8', true));?>
" maxlength="255" class="text" /></li></ul><div class="admin_btn"><label for="pass-input" class="label">&nbsp;</label><div class="adm_btn_left"></div><input type="submit" name="proceed" value="Proceed" alt="Recover password" title="Recover your lost password" class="adm_btn_center" /><div class="adm_btn_right"></div></div><div style="clear: both;"></div><br/><p><a href="<?php echo @DOC_ROOT;?>
/login.php" title="Login to your administrator control panel">Login</a></p></div><div class="admin-login-bot"></div></div><input type="hidden" name="formSubmitted" value="1" /></form></div><?php }elseif($_smarty_tpl->tpl_vars['action']->value=='confirm'){?><div class="block"><div id="admin-login"><div class="admin-login-title">Administrator Password Recovery @ <?php echo @SITE_NAME;?>
</div><div class="admin-login-content"><?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while recovering your password!</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if ($_smarty_tpl->tpl_vars['password_recovered']->value){?><div class="success block"><p>You can now login with your new password.</p></div><?php }?><div style="clear: both;"></div><br/><p><a href="<?php echo @DOC_ROOT;?>
/login.php" title="Login to your administrator control panel">Login</a></p></div><div class="admin-login-bot"></div></div><?php }else{ ?><div class="block"><form id="login-form" name="login-form" method="post" action="<?php echo @DOC_ROOT;?>
/login.php"><div id="admin-login"><div class="admin-login-title">Administrator Login @ <?php echo @SITE_NAME;?>
</div><div class="admin-login-content"><!-- <div class="error" id="login_failed">Invalid username or password.</div> --><?php if ($_smarty_tpl->tpl_vars['failed']->value){?><div class="error">Invalid username or password.</div><?php }?><?php if ($_smarty_tpl->tpl_vars['invalid']->value){?><div class="error">Some validation went wrong, please try again.</div><?php }?><?php if ($_smarty_tpl->tpl_vars['no_permission']->value){?><div class="error">No permissions set for this user.</div><?php }?><ul><li><label for="admin-input" class="label">Username</label><input type="text" id="admin-input" name="user" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['user']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></li><li><label for="pass-input" class="label">Password</label><input type="password" id="pass-input" name="pass" value="" class="text" /><div class="clear: both;"></div></li></ul><div class="admin_btn"><label for="pass-input" class="label">&nbsp;</label><div class="adm_btn_left"></div><input type="submit" name="login" value="Login" alt="Login" title="Login to your administrator control panel" class="adm_btn_center" /><div class="adm_btn_right"></div></div><div style="clear: both;"></div><br/><p><a href="<?php echo @DOC_ROOT;?>
/login.php?action=sendpassword" title="Recover your password">I forgot my password</a></p></div><div class="admin-login-bot"></div></div><input type="hidden" name="formSubmitted" value="1" /></form></div><?php }?><!-- Footer --><div id="footer">PHP Link Directory Phoenix v<?php echo @CURRENT_VERSION;?>
, Copyright &copy; 2004-<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo smarty_php_tag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
echo date('Y');<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_php_tag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 NetCreated.</div><!-- /Footer --></div><!-- /Main -->
</body>
</html><?php }} ?>