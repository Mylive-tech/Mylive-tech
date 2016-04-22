<?php /* Smarty version Smarty-3.1.12, created on 2014-05-02 13:06:33
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/unauthorized.tpl" */ ?>
<?php /*%%SmartyHeaderCode:29189128353639859f1c8d8-60477180%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '71162938b38560108eb422730c000176f9500603' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/unauthorized.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29189128353639859f1c8d8-60477180',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'unauthorizedReason' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5363985a052dc3_76451352',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5363985a052dc3_76451352')) {function content_5363985a052dc3_76451352($_smarty_tpl) {?><h2>Unauthorized</h2>
<p>Sorry, you are not allowed to access this page.</p>

<?php if (isset($_smarty_tpl->tpl_vars['unauthorizedReason']->value)&&!empty($_smarty_tpl->tpl_vars['unauthorizedReason']->value)){?>
<h2>Reason</h2>
<p><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['unauthorizedReason']->value, ENT_QUOTES, 'UTF-8', true));?>
</p>
<?php }?>
<?php }} ?>