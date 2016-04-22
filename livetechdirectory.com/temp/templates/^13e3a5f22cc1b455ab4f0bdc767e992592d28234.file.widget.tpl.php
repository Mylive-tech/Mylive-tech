<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:26
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/DevXBlue/views/_shared/widget.tpl" */ ?>
<?php /*%%SmartyHeaderCode:159232527535a93baa2f408-87796610%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '13e3a5f22cc1b455ab4f0bdc767e992592d28234' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/DevXBlue/views/_shared/widget.tpl',
      1 => 1398444917,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '159232527535a93baa2f408-87796610',
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
  'unifunc' => 'content_535a93baa51823_26942164',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93baa51823_26942164')) {function content_535a93baa51823_26942164($_smarty_tpl) {?><div class="phpld-grid phpld-full phpld-widget" id="widget_<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
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