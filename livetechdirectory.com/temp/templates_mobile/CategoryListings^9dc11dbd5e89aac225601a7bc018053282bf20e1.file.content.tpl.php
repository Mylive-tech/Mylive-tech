<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:18:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/application/widgets/CategoryListings/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:170756122535ab4fc419567-95860755%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9dc11dbd5e89aac225601a7bc018053282bf20e1' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/application/widgets/CategoryListings/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '170756122535ab4fc419567-95860755',
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
  'unifunc' => 'content_535ab4fc431822_92073666',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab4fc431822_92073666')) {function content_535ab4fc431822_92073666($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['show_title']->value==1){?>
<h3><?php echo $_smarty_tpl->tpl_vars['TITLE']->value;?>
</h3>
<?php }?>
<div class="listingsList">
<?php echo $_smarty_tpl->tpl_vars['LISTINGS']->value;?>

<?php echo $_smarty_tpl->tpl_vars['PAGINATOR']->value;?>

</div><?php }} ?>