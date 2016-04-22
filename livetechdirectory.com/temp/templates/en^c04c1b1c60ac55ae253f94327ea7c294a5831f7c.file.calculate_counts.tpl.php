<?php /* Smarty version Smarty-3.1.12, created on 2014-05-12 13:03:42
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/calculate_counts.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9717445905370c6ae6f0942-33932562%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c04c1b1c60ac55ae253f94327ea7c294a5831f7c' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/calculate_counts.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9717445905370c6ae6f0942-33932562',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'rebuild' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5370c6ae746114_37603114',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5370c6ae746114_37603114')) {function content_5370c6ae746114_37603114($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['rebuild']->value!='rebuild_counts'){?>
<h1>Rebuild All Counts</h1><p>Press "Rebuild Now" in order to update all categoryes listings number cache(number in round braces right after category name)</p><form>   
<input type="hidden" name="rebuild" value="1">   
<input type="submit" value="Rebuild Now"></form>
<?php }?><?php }} ?>