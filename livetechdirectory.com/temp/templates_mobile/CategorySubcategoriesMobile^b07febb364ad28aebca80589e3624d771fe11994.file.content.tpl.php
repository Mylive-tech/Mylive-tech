<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/application/widgets/CategorySubcategoriesMobile/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13546252915654ac8c6fb847-26055082%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b07febb364ad28aebca80589e3624d771fe11994' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/application/widgets/CategorySubcategoriesMobile/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13546252915654ac8c6fb847-26055082',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'subcategories' => 0,
    'cat' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654ac8c70e3a4_47394766',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac8c70e3a4_47394766')) {function content_5654ac8c70e3a4_47394766($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['subcategories']->value->countWithoutLimit()>0){?>
<?php  $_smarty_tpl->tpl_vars['cat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['subcategories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cat']->key => $_smarty_tpl->tpl_vars['cat']->value){
$_smarty_tpl->tpl_vars['cat']->_loop = true;
?>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['cat']->value->getUrl();?>
" target="_self" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat']->value['TITLE'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat']->value['TITLE'], ENT_QUOTES, 'UTF-8', true);?>
</a></li>
	<?php } ?>
<?php }?>

<?php }} ?>