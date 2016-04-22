<?php /* Smarty version Smarty-3.1.12, created on 2015-11-26 17:56:31
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/authorInfo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1875534570565747cfd9f9a7-30277347%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a073c81c7eb01f016914d1e8d903a784593b8f82' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/authorInfo.tpl',
      1 => 1392430848,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1875534570565747cfd9f9a7-30277347',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_565747cfda9c86_19357356',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_565747cfda9c86_19357356')) {function content_565747cfda9c86_19357356($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['LINK']->value['NAME'])&&@ALLOW_AUTHOR_INFO){?><div style="clear: both;">Author: <b><?php echo trim($_smarty_tpl->tpl_vars['LINK']->value['NAME']);?>
</b></div><?php }?><?php }} ?>