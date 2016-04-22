<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:05:50
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/thankyou.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1555059111535ab20eebc273-26957730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aefbe1c4bb886befcb4e702087d32246c4b8361d' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/thankyou.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1555059111535ab20eebc273-26957730',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'titleheading' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535ab20eed6df3_86362403',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab20eed6df3_86362403')) {function content_535ab20eed6df3_86362403($_smarty_tpl) {?><div class="box success">
    <?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Information",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>

   <p>Thank you for registering. Your account has been created. <?php if (@EMAIL_CONFIRMATION){?>Check your e-mail to activate your account.<?php }?></p>

</div><?php }} ?>