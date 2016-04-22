<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:19
         compiled from "/home/mylivete/public_html/livetechdirectory.com/application/widgets/CategoryListings/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:175447991156548c639c2f86-93142646%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfd3707f00725c02117ef016c0275092f5fd2e80' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/application/widgets/CategoryListings/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '175447991156548c639c2f86-93142646',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'show_title' => 0,
    'TITLE' => 0,
    'LISTINGS' => 0,
    'PAGINATOR' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c639cbf21_16280379',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c639cbf21_16280379')) {function content_56548c639cbf21_16280379($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['show_title']->value==1){?>
<h3><?php echo $_smarty_tpl->tpl_vars['TITLE']->value;?>
</h3>
<?php }?>
<div class="listingsList">
<?php echo $_smarty_tpl->tpl_vars['LISTINGS']->value;?>

<?php echo $_smarty_tpl->tpl_vars['PAGINATOR']->value;?>

</div><?php }} ?>