<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/mainMenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13645150356548c5e3b69a1-61177150%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '35941419f7a2bf00dd353aeff286ccae48b73412' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/mainMenu.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13645150356548c5e3b69a1-61177150',
  'function' => 
  array (
    'render_menu' => 
    array (
      'parameter' => 
      array (
        'level' => 0,
      ),
      'compiled' => '',
    ),
  ),
  'variables' => 
  array (
    'items' => 0,
    'item' => 0,
    'level' => 0,
    'menuList' => 0,
  ),
  'has_nocache_code' => 0,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e40dc03_92124767',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e40dc03_92124767')) {function content_56548c5e40dc03_92124767($_smarty_tpl) {?><?php if (!function_exists('smarty_template_function_render_menu')) {
    function smarty_template_function_render_menu($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['render_menu']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?><ul><?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?><li><a href="<?php if (preg_match("/http:/",$_smarty_tpl->tpl_vars['item']->value['URL'])){?><?php }else{ ?><?php echo @SITE_URL;?>
<?php }?><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->tpl_vars['item']->value['URL'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->fetch(); ?>"><?php echo $_smarty_tpl->tpl_vars['item']->value['LABEL'];?>
</a><?php if (count($_smarty_tpl->tpl_vars['item']->value['pages'])>0){?><?php smarty_template_function_render_menu($_smarty_tpl,array('items'=>$_smarty_tpl->tpl_vars['item']->value['pages'],'level'=>$_smarty_tpl->tpl_vars['level']->value+1));?>
<?php }?></li><?php } ?></ul><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
<?php smarty_template_function_render_menu($_smarty_tpl,array('items'=>$_smarty_tpl->tpl_vars['menuList']->value));?>
<?php }} ?>