<?php /* Smarty version Smarty-3.1.12, created on 2014-04-26 09:08:18
         compiled from "/home/mylive5/public_html/livetechdirectory.com/application/widgets/TopCategoriesMobile/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:242076779535b77829ffa45-59899185%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0de42c976da8ef6142b2b9fcb7d60909bd5bc726' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/application/widgets/TopCategoriesMobile/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '242076779535b77829ffa45-59899185',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'topCategs' => 0,
    'cat' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535b7782a2b341_44852870',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535b7782a2b341_44852870')) {function content_535b7782a2b341_44852870($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['topCategs']->value)){?>

	<li class="group">Categories</li>        
	<?php  $_smarty_tpl->tpl_vars['cat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['topCategs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
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