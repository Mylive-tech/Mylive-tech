<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/application/widgets/MenuMobile/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1934209845654ac8c6b8ab0-19571826%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4142de4a660a4535a5378fa7a77198600689e0c9' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/application/widgets/MenuMobile/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1934209845654ac8c6b8ab0-19571826',
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
  'unifunc' => 'content_5654ac8c6cf433_53071532',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac8c6cf433_53071532')) {function content_5654ac8c6cf433_53071532($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['show_title']->value==1){?>
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