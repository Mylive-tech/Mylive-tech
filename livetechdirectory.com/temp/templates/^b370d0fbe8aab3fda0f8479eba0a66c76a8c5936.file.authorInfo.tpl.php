<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:13
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/authorInfo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:649114487535d748d452511-39266941%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
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
  'nocache_hash' => '649114487535d748d452511-39266941',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535d748d48f993_57687075',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748d48f993_57687075')) {function content_535d748d48f993_57687075($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['LINK']->value['NAME'])&&@ALLOW_AUTHOR_INFO){?><div style="clear: both;">Author: <b><?php echo trim($_smarty_tpl->tpl_vars['LINK']->value['NAME']);?>
</b></div><?php }?><?php }} ?>