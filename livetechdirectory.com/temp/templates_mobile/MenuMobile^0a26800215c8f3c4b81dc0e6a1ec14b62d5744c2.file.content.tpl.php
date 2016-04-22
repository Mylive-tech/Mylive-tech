<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:19:03
         compiled from "/home/mylive5/public_html/livetechdirectory.com/application/widgets/MenuMobile/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:75272596535a9907bec060-44727091%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0a26800215c8f3c4b81dc0e6a1ec14b62d5744c2' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/application/widgets/MenuMobile/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '75272596535a9907bec060-44727091',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'show_title' => 0,
    'TITLE' => 0,
    'menuList' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a9907c1d504_48377500',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a9907c1d504_48377500')) {function content_535a9907c1d504_48377500($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['show_title']->value==1){?>
    <?php echo $_smarty_tpl->tpl_vars['TITLE']->value;?>

<?php }?>
<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menuList']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
    <li><a href="<?php if (preg_match("/http:/",$_smarty_tpl->tpl_vars['item']->value['URL'])){?><?php }else{ ?><?php echo @SITE_URL;?>
<?php }?><?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->tpl_vars['item']->value['URL'], $_smarty_tpl->smarty, $_smarty_tpl);echo $_template->fetch(); ?>"><?php echo $_smarty_tpl->tpl_vars['item']->value['LABEL'];?>
</a></li>
  <?php } ?><?php }} ?>