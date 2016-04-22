<?php /* Smarty version Smarty-3.1.12, created on 2014-05-11 17:09:38
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/categories2.tpl" */ ?>
<?php /*%%SmartyHeaderCode:942720570536faed24bf450-76901067%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79d182d59edbf1050bff35c23b1dfaaa135df13a' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/categories2.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '942720570536faed24bf450-76901067',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTING_CATEGORIES_LIST' => 0,
    'category' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_536faed24d7fe5_83279959',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_536faed24d7fe5_83279959')) {function content_536faed24d7fe5_83279959($_smarty_tpl) {?>
    <?php  $_smarty_tpl->tpl_vars["category"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["category"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTING_CATEGORIES_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["category"]->key => $_smarty_tpl->tpl_vars["category"]->value){
$_smarty_tpl->tpl_vars["category"]->_loop = true;
?>
      <a class="link-category-url" href="<?php echo $_smarty_tpl->tpl_vars['category']->value->getUrl();?>
"><?php echo $_smarty_tpl->tpl_vars['category']->value['TITLE'];?>
</a> &nbsp;
    <?php } ?>

<?php }} ?>