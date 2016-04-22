<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:30:36
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/latest.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3534699555654accc67d688-28364362%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '82e344355c4536a878c6617673568893582bd399' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/latest.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3534699555654accc67d688-28364362',
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
  'unifunc' => 'content_5654accc690ac1_73496413',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654accc690ac1_73496413')) {function content_5654accc690ac1_73496413($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"Latest",'titleheading'=>"3"), 0);?>

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
</div><?php }} ?>