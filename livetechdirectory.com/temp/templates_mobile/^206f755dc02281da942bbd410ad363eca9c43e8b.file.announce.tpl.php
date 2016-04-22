<?php /* Smarty version Smarty-3.1.12, created on 2015-11-26 17:56:31
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/announce.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1266194100565747cfcdd572-48145314%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '206f755dc02281da942bbd410ad363eca9c43e8b' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/announce.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1266194100565747cfcdd572-48145314',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_565747cfceb290_01500995',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_565747cfceb290_01500995')) {function content_565747cfceb290_01500995($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.truncate.php';
?><?php if (empty($_smarty_tpl->tpl_vars['LINK']->value['ANNOUNCE'])){?>
    <?php echo smarty_modifier_truncate(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['LINK']->value['DESCRIPTION']),200,'',false);?>

<?php }else{ ?>
    <?php echo $_smarty_tpl->tpl_vars['LINK']->value['ANNOUNCE'];?>

<?php }?><?php }} ?>