<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/category/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18703660325654ac8c68e9f7-37910346%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fd688e771e1d429363c10d687e6593b80d7da954' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/category/index.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18703660325654ac8c68e9f7-37910346',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
    'topCategs' => 0,
    'cat' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654ac8c6a1d69_08582586',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac8c6a1d69_08582586')) {function content_5654ac8c6a1d69_08582586($_smarty_tpl) {?><li class="group"><?php echo $_smarty_tpl->tpl_vars['category']->value['TITLE'];?>
</li>   
<?php if (!empty($_smarty_tpl->tpl_vars['topCategs']->value)){?>

	     
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

<?php }?><?php }} ?>