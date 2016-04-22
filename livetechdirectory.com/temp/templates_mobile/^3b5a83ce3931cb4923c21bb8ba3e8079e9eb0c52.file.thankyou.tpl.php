<?php /* Smarty version Smarty-3.1.12, created on 2015-06-22 06:13:55
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/thankyou.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20124933415587a7a39b4250-82789144%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b5a83ce3931cb4923c21bb8ba3e8079e9eb0c52' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/thankyou.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20124933415587a7a39b4250-82789144',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'titleheading' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5587a7a3a16d52_33561767',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5587a7a3a16d52_33561767')) {function content_5587a7a3a16d52_33561767($_smarty_tpl) {?><div class="box success">
    <?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Information",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>

   <p>Thank you for registering. Your account has been created. <?php if (@EMAIL_CONFIRMATION){?>Check your e-mail to activate your account.<?php }?></p>

</div><?php }} ?>