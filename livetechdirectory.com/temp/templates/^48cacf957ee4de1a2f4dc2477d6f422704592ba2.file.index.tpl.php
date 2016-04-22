<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:01:38
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/category/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:611024388535a94f2522ce8-28033170%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '48cacf957ee4de1a2f4dc2477d6f422704592ba2' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/category/index.tpl',
      1 => 1390868836,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '611024388535a94f2522ce8-28033170',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a94f252e701_32876369',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a94f252e701_32876369')) {function content_535a94f252e701_32876369($_smarty_tpl) {?><h3><?php echo $_smarty_tpl->tpl_vars['category']->value['TITLE'];?>
</h3>
<div class="category-description"><?php echo $_smarty_tpl->tpl_vars['category']->value['CATCONTENT'];?>
</div>
<?php }} ?>