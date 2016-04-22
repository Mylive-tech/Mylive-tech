<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:42
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/pagetitle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4983758295654ac9605f089-85616505%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e20a30efa7574b3cbdf604cb2a480bb635037b7' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/pagetitle.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4983758295654ac9605f089-85616505',
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
  'unifunc' => 'content_5654ac96066429_79588700',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac96066429_79588700')) {function content_5654ac96066429_79588700($_smarty_tpl) {?><h<?php echo $_smarty_tpl->tpl_vars['titleheading']->value;?>
 class="page-title"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['PAGETITLE']->value, ENT_QUOTES, 'UTF-8', true));?>
</h<?php echo $_smarty_tpl->tpl_vars['titleheading']->value;?>
>
<?php }} ?>