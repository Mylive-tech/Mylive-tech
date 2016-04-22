<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:47:29
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/submissions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1433799419535abbd1a6f012-02643856%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b2689622e582d95cce2459553768e10831a9cb1b' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/submissions.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1433799419535abbd1a6f012-02643856',
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
  'unifunc' => 'content_535abbd1b00473_74656842',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abbd1b00473_74656842')) {function content_535abbd1b00473_74656842($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['feat_links']->value->countWithoutLimit()>0){?>
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