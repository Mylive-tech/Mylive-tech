<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 10:57:56
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/submissions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16100582656559434c91cc0-54893351%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b352cb6096293cfd9d3c6bb2eff67c519c8ad2fe' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/submissions.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16100582656559434c91cc0-54893351',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'feat_links' => 0,
    'link' => 0,
    'links' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56559434cad305_73675187',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56559434cad305_73675187')) {function content_56559434cad305_73675187($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['feat_links']->value->countWithoutLimit()>0){?>
    <?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['feat_links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
?>
        <?php echo $_smarty_tpl->tpl_vars['link']->value->listing('list');?>

    <?php } ?>
<?php }?>
<hr/>
<?php if ($_smarty_tpl->tpl_vars['links']->value->countWithoutLimit()>0){?>
    <?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
?>
        <?php echo $_smarty_tpl->tpl_vars['link']->value->listing('list');?>

    <?php } ?>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['feat_links']->value->countWithoutLimit()==0&&$_smarty_tpl->tpl_vars['links']->value->countWithoutLimit()==0){?>
    There are currently no submissions listed for this user
<?php }?><?php }} ?>