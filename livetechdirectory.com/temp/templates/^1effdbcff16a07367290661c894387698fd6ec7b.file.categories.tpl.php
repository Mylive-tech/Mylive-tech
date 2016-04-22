<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:20:43
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/categories.tpl" */ ?>
<?php /*%%SmartyHeaderCode:189449329956548e5b208231-17487457%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1effdbcff16a07367290661c894387698fd6ec7b' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/categories.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '189449329956548e5b208231-17487457',
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
  'unifunc' => 'content_56548e5b211956_05553604',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548e5b211956_05553604')) {function content_56548e5b211956_05553604($_smarty_tpl) {?><ul>
    <?php  $_smarty_tpl->tpl_vars["category"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["category"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTING_CATEGORIES_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["category"]->key => $_smarty_tpl->tpl_vars["category"]->value){
$_smarty_tpl->tpl_vars["category"]->_loop = true;
?>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['category']->value->getUrl();?>
"><?php echo $_smarty_tpl->tpl_vars['category']->value['TITLE'];?>
</a></li>
    <?php } ?>
</ul>
<?php }} ?>