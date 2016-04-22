<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 18:53:16
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/admin_category_select.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1602179437535aaf1cd199e3-31199852%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eccdb31e451a4e9df21cecd873662205ebcd194e' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/admin_category_select.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1602179437535aaf1cd199e3-31199852',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'selected' => 0,
    'selected_parent' => 0,
    'CATEGORY_ID' => 0,
    'PARENT_ID' => 0,
    'additional_categs' => 0,
    'link_type_details' => 0,
    'symbolic' => 0,
    'CategoryTitle' => 0,
    'parent' => 0,
    'selected_cat' => 0,
    'categs' => 0,
    'categ_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535aaf1cdd7486_77652043',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535aaf1cdd7486_77652043')) {function content_535aaf1cdd7486_77652043($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
?>

<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php if ($_smarty_tpl->tpl_vars['selected']->value){?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_cat'])) {$_smarty_tpl->tpl_vars['selected_cat'] = clone $_smarty_tpl->tpl_vars['selected_cat'];
$_smarty_tpl->tpl_vars['selected_cat']->value = $_smarty_tpl->tpl_vars['selected']->value; $_smarty_tpl->tpl_vars['selected_cat']->nocache = null; $_smarty_tpl->tpl_vars['selected_cat']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_cat'] = new Smarty_variable($_smarty_tpl->tpl_vars['selected']->value, null, 0);?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_parent'])) {$_smarty_tpl->tpl_vars['selected_parent'] = clone $_smarty_tpl->tpl_vars['selected_parent'];
$_smarty_tpl->tpl_vars['selected_parent']->value = $_smarty_tpl->tpl_vars['selected_parent']->value; $_smarty_tpl->tpl_vars['selected_parent']->nocache = null; $_smarty_tpl->tpl_vars['selected_parent']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_parent'] = new Smarty_variable($_smarty_tpl->tpl_vars['selected_parent']->value, null, 0);?>
<?php }else{ ?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_cat'])) {$_smarty_tpl->tpl_vars['selected_cat'] = clone $_smarty_tpl->tpl_vars['selected_cat'];
$_smarty_tpl->tpl_vars['selected_cat']->value = $_smarty_tpl->tpl_vars['CATEGORY_ID']->value; $_smarty_tpl->tpl_vars['selected_cat']->nocache = null; $_smarty_tpl->tpl_vars['selected_cat']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_cat'] = new Smarty_variable($_smarty_tpl->tpl_vars['CATEGORY_ID']->value, null, 0);?>
	<?php if (isset($_smarty_tpl->tpl_vars['selected_parent'])) {$_smarty_tpl->tpl_vars['selected_parent'] = clone $_smarty_tpl->tpl_vars['selected_parent'];
$_smarty_tpl->tpl_vars['selected_parent']->value = $_smarty_tpl->tpl_vars['PARENT_ID']->value; $_smarty_tpl->tpl_vars['selected_parent']->nocache = null; $_smarty_tpl->tpl_vars['selected_parent']->scope = 0;
} else $_smarty_tpl->tpl_vars['selected_parent'] = new Smarty_variable($_smarty_tpl->tpl_vars['PARENT_ID']->value, null, 0);?>
<?php }?>
<?php if (@ADMIN_CAT_SELECTION_METHOD==1&&!$_smarty_tpl->tpl_vars['additional_categs']->value&&$_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']<=1&&$_smarty_tpl->tpl_vars['symbolic']->value!=1){?><div id="catTitle"><?php if (!empty($_smarty_tpl->tpl_vars['CategoryTitle']->value)){?><?php echo $_smarty_tpl->tpl_vars['CategoryTitle']->value;?>
<?php }else{ ?>Please select a category!<?php }?></div><span id="toggleCategTree">Change category</span><?php if (isset($_smarty_tpl->tpl_vars['parent']->value)&&$_smarty_tpl->tpl_vars['parent']->value==1){?><input type="hidden" id="PARENT_ID" name="PARENT_ID" value="<?php echo $_smarty_tpl->tpl_vars['selected_parent']->value;?>
" class="hidden" /><?php }else{ ?><input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID" value="<?php echo $_smarty_tpl->tpl_vars['selected_cat']->value;?>
" class="hidden" /><?php }?><div id="categtreebox"></div><?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['parent']->value)&&$_smarty_tpl->tpl_vars['parent']->value==1){?><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['categs']->value,'selected'=>$_smarty_tpl->tpl_vars['PARENT_ID']->value,'name'=>"PARENT_ID",'id'=>"PARENT_ID"),$_smarty_tpl);?>
<?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['additional_categs']->value){?><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['categs']->value,'selected'=>$_smarty_tpl->tpl_vars['categ_id']->value,'name'=>"ADD_CATEGORY_ID[]",'id'=>rand("0","100")),$_smarty_tpl);?>
<?php }else{ ?><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['categs']->value,'selected'=>$_smarty_tpl->tpl_vars['selected_cat']->value,'name'=>"CATEGORY_ID",'id'=>"CATEGORY_ID"),$_smarty_tpl);?>
<?php }?><?php }?><?php }?><?php }} ?>