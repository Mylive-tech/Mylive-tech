<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 13:43:52
         compiled from "/home/mylivete/public_html/livetechdirectory.com/application/widgets/TopCategoriesMobile/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19954134755655bb18b7d1d8-96595075%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9e084e69a5272ebcac19e0d175c65369536b0468' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/application/widgets/TopCategoriesMobile/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19954134755655bb18b7d1d8-96595075',
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
  'unifunc' => 'content_5655bb18b8d478_20946778',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5655bb18b8d478_20946778')) {function content_5655bb18b8d478_20946778($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['topCategs']->value)){?>

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