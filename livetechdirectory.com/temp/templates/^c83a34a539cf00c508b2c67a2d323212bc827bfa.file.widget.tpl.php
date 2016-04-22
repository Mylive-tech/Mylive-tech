<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/DevXBlue/views/_shared/widget.tpl" */ ?>
<?php /*%%SmartyHeaderCode:35707062956548c5e59ddd9-03348958%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c83a34a539cf00c508b2c67a2d323212bc827bfa' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/DevXBlue/views/_shared/widget.tpl',
      1 => 1398444917,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '35707062956548c5e59ddd9-03348958',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ID' => 0,
    'SHOW_TITLE' => 0,
    'TITLE' => 0,
    'WIDGET_HEADING' => 0,
    'CONTENT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e5ac153_59356047',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e5ac153_59356047')) {function content_56548c5e5ac153_59356047($_smarty_tpl) {?><div class="phpld-grid phpld-full phpld-widget" id="widget_<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
">
    <div class="boxTop"></div>
    <?php if ($_smarty_tpl->tpl_vars['SHOW_TITLE']->value&&!empty($_smarty_tpl->tpl_vars['TITLE']->value)){?>
        <h<?php echo $_smarty_tpl->tpl_vars['WIDGET_HEADING']->value;?>
><?php echo $_smarty_tpl->tpl_vars['TITLE']->value;?>
</h<?php echo $_smarty_tpl->tpl_vars['WIDGET_HEADING']->value;?>
>
    <?php }?>
    <?php echo $_smarty_tpl->tpl_vars['CONTENT']->value;?>

</div>
<?php }} ?>