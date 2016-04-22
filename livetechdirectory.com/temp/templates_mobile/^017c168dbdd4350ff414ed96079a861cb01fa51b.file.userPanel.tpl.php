<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:19:03
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/userPanel.tpl" */ ?>
<?php /*%%SmartyHeaderCode:505437495535a9907aee720-39647001%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '017c168dbdd4350ff414ed96079a861cb01fa51b' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/userPanel.tpl',
      1 => 1395445276,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '505437495535a9907aee720-39647001',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'regular_user_details' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a9907b1ec39_71707012',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a9907b1ec39_71707012')) {function content_535a9907b1ec39_71707012($_smarty_tpl) {?>
<?php if (1==1){?>
<div class="userPanel">
    <?php if (empty($_smarty_tpl->tpl_vars['regular_user_details']->value)){?>
        <a href="<?php echo @DOC_ROOT;?>
/login" class="btn-slide">Login</a>
        &nbsp;|&nbsp;
        <a href="<?php echo @DOC_ROOT;?>
/user/register" title="Register new user">Register</a>
        <?php }else{ ?>
        Welcome: <strong><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['regular_user_details']->value['NAME'], ENT_QUOTES, 'UTF-8', true);?>
</strong> [<a href="<?php echo @DOC_ROOT;?>
/logout" title="Log out of this account">Sign Out</a>, <a href="<?php echo @DOC_ROOT;?>
/user" title="Edit your account settings">My Account</a>]
    <?php }?>
</div>
<?php }?><?php }} ?>