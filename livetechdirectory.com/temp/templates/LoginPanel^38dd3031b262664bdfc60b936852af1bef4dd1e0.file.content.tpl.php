<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/application/widgets/LoginPanel/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:47625910456548c5e4f3384-09471427%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '38dd3031b262664bdfc60b936852af1bef4dd1e0' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/application/widgets/LoginPanel/templates/content.tpl',
      1 => 1395445312,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '47625910456548c5e4f3384-09471427',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'show_title' => 0,
    'TITLE' => 0,
    'user_details' => 0,
    'user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e51dd93_85301774',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e51dd93_85301774')) {function content_56548c5e51dd93_85301774($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['show_title']->value==1){?>
<?php echo $_smarty_tpl->tpl_vars['TITLE']->value;?>

<br/>
<?php }?>
<form method="post" action="<?php echo @DOC_ROOT;?>
/login" onsubmit="ajaxFunction();">

 <?php if ((@REQUIRE_REGISTERED_USER==1||@REQUIRE_REGISTERED_USER_ARTICLE==1)&&empty($_smarty_tpl->tpl_vars['user_details']->value)){?>
<table border="0" align="center" width="40%" style="padding-top: 10px; z-index: -1; height: 0px">
	<tr>
   	<td>User:</td>
      <td>
       	<input type="text" name="user" value="<?php echo $_smarty_tpl->tpl_vars['user']->value;?>
" size="10" maxlength="<?php echo @USER_LOGIN_MAX_LENGTH;?>
" class="text" />
      </td>
   </tr>
   <tr>
   	<td>Password:</td>
      <td>
      	<input type="password" name="pass" value="" size="10" maxlength="<?php echo @USER_PASSWORD_MAX_LENGTH;?>
" class="text" />
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
	<li><a href="<?php echo @DOC_ROOT;?>
/user/register" title="Register">Register</a></li>
	<li><a href="<?php echo @DOC_ROOT;?>
/user/sendpassword" title="Recover your password">I forgot my password</a></li>
</ul>
<?php }elseif((@REQUIRE_REGISTERED_USER==1||@REQUIRE_REGISTERED_USER_ARTICLE==1)){?>
	<br />
	<div align="center" style="font-weight: bold; ">Welcome <?php echo $_smarty_tpl->tpl_vars['user_details']->value['NAME'];?>
!</div>
	<ul class="boxPopCats">
		<li><a href="<?php echo @DOC_ROOT;?>
/user" title="Edit your account settings">My Account</a></li>
		<li><a href="<?php echo @DOC_ROOT;?>
/logout" title="Log out of this account">Sign Out</a></li>
	</ul>
<?php }?>
</form><?php }} ?>