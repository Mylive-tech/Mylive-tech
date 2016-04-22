<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:02:18
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/pagetitle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14423866535a951a70ea27-84766643%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f0a50ebac02c228cc7a10df0cedf9ae46eb92a07' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/pagetitle.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14423866535a951a70ea27-84766643',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'titleheading' => 0,
    'PAGETITLE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a951a721031_04144459',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a951a721031_04144459')) {function content_535a951a721031_04144459($_smarty_tpl) {?><h<?php echo $_smarty_tpl->tpl_vars['titleheading']->value;?>
 class="page-title"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['PAGETITLE']->value, ENT_QUOTES, 'UTF-8', true));?>
</h<?php echo $_smarty_tpl->tpl_vars['titleheading']->value;?>
>
<?php }} ?>