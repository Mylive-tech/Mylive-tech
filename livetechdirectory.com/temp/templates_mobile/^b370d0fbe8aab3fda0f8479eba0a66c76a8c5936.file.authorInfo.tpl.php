<?php /* Smarty version Smarty-3.1.12, created on 2014-05-11 17:09:38
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/authorInfo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:30215984536faed279c524-14399634%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b370d0fbe8aab3fda0f8479eba0a66c76a8c5936' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/authorInfo.tpl',
      1 => 1392430848,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30215984536faed279c524-14399634',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_536faed27bc6a8_81477250',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_536faed27bc6a8_81477250')) {function content_536faed27bc6a8_81477250($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['LINK']->value['NAME'])&&@ALLOW_AUTHOR_INFO){?><div style="clear: both;">Author: <b><?php echo trim($_smarty_tpl->tpl_vars['LINK']->value['NAME']);?>
</b></div><?php }?><?php }} ?>