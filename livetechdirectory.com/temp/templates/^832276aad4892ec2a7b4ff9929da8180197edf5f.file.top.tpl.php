<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 18:34:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2131778804535aaaa4356808-02254423%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '832276aad4892ec2a7b4ff9929da8180197edf5f' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/top.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2131778804535aaaa4356808-02254423',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'links' => 0,
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535aaaa439faa7_49136879',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535aaaa439faa7_49136879')) {function content_535aaaa439faa7_49136879($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Top",'titleheading'=>3), 0);?>

<div class="listing-style-list">
    <?php if ($_smarty_tpl->tpl_vars['links']->value->countWithoutLimit()>0){?>
        <?php  $_smarty_tpl->tpl_vars['LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINK']->key => $_smarty_tpl->tpl_vars['LINK']->value){
$_smarty_tpl->tpl_vars['LINK']->_loop = true;
?>
            <?php echo $_smarty_tpl->tpl_vars['LINK']->value->listing();?>

        <?php } ?>
    <?php }?>
</div>
<?php }} ?>