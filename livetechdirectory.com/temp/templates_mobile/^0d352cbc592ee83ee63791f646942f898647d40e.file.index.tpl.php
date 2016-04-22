<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 13:43:52
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/index/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6947715995655bb18afe167-51516220%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0d352cbc592ee83ee63791f646942f898647d40e' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/index/index.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6947715995655bb18afe167-51516220',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categs' => 0,
    'cat' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5655bb18b14d90_83937949',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5655bb18b14d90_83937949')) {function content_5655bb18b14d90_83937949($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['categs']->value)){?>
	<li class="group">Categories</li>        
	<?php  $_smarty_tpl->tpl_vars['cat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cat']->key => $_smarty_tpl->tpl_vars['cat']->value){
$_smarty_tpl->tpl_vars['cat']->_loop = true;
?>
    	<li><a href="<?php echo @DOC_ROOT;?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat']->value['CACHE_URL'], ENT_QUOTES, 'UTF-8', true);?>
" target="_self"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat']->value['TITLE'], ENT_QUOTES, 'UTF-8', true);?>
 (<?php echo $_smarty_tpl->tpl_vars['cat']->value['COUNT'];?>
)</a></li>
    <?php } ?>
<?php }?><?php }} ?>