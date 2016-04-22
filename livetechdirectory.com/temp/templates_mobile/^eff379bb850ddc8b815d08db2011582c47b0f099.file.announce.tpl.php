<?php /* Smarty version Smarty-3.1.12, created on 2014-05-11 17:09:38
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/announce.tpl" */ ?>
<?php /*%%SmartyHeaderCode:479798754536faed25aa561-27176749%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eff379bb850ddc8b815d08db2011582c47b0f099' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/announce.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '479798754536faed25aa561-27176749',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_536faed25ce2f8_73765556',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_536faed25ce2f8_73765556')) {function content_536faed25ce2f8_73765556($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.truncate.php';
?><?php if (empty($_smarty_tpl->tpl_vars['LINK']->value['ANNOUNCE'])){?>
    <?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['LINK']->value['DESCRIPTION']),200,'',false);?>

<?php }else{ ?>
    <?php echo $_smarty_tpl->tpl_vars['LINK']->value['ANNOUNCE'];?>

<?php }?><?php }} ?>