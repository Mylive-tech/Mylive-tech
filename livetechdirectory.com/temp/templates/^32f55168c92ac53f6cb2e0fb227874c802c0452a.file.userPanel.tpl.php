<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/userPanel.tpl" */ ?>
<?php /*%%SmartyHeaderCode:125443265756548c5e4715a9-48716447%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32f55168c92ac53f6cb2e0fb227874c802c0452a' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/userPanel.tpl',
      1 => 1395445276,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '125443265756548c5e4715a9-48716447',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'regular_user_details' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e486d97_98675156',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e486d97_98675156')) {function content_56548c5e486d97_98675156($_smarty_tpl) {?>
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