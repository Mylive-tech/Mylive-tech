<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 10:58:02
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/add_categs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4495506665655943a162f78-72533681%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '64ec7ff5e6d60c7376032e5401028783dda9f822' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/add_categs.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4495506665655943a162f78-72533681',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'selected' => 0,
    'selected_parent' => 0,
    'CATEGORY_ID' => 0,
    'PARENT_ID' => 0,
    'categs_tree' => 0,
    'k' => 0,
    'categ_id' => 0,
    'additional_categs' => 0,
    'v' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5655943a18a8e3_22478722',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5655943a18a8e3_22478722')) {function content_5655943a18a8e3_22478722($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.math.php';
?>


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
<select name="ADD_CATEGORY_ID[]" id="<?php echo smarty_function_math(array('equation'=>'rand(10,100)'),$_smarty_tpl);?>
"><?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['categs_tree']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ((($_smarty_tpl->tpl_vars['categ_id']->value==$_smarty_tpl->tpl_vars['k']->value)&&($_smarty_tpl->tpl_vars['categ_id']->value!=0)&&$_smarty_tpl->tpl_vars['additional_categs']->value)){?> selected="selected" <?php }?> <?php if ($_smarty_tpl->tpl_vars['v']->value['closed']==1){?>disabled = "disabled" <?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['val'];?>
</option><?php } ?></select><?php }} ?>