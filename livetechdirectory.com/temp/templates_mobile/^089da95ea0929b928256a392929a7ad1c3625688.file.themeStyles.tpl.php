<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/themeStyles.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4020100895654ac8c7ceff2-02157000%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '089da95ea0929b928256a392929a7ad1c3625688' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/themeStyles.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4020100895654ac8c7ceff2-02157000',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'fontsEnabled' => 0,
    'font_faces' => 0,
    'content_font' => 0,
    'header_font' => 0,
    'site_name_font' => 0,
    'background_pattern' => 0,
    'background_color' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654ac8c7e9271_69340708',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac8c7e9271_69340708')) {function content_5654ac8c7e9271_69340708($_smarty_tpl) {?>
<style type="text/css">
<?php if ($_smarty_tpl->tpl_vars['fontsEnabled']->value==true){?>
<?php echo $_smarty_tpl->tpl_vars['font_faces']->value;?>


body, body * {
    font-family: <?php echo $_smarty_tpl->tpl_vars['content_font']->value;?>
;
}
h1,h2,h3,h4,h5,h6 {
    font-family: <?php echo $_smarty_tpl->tpl_vars['header_font']->value;?>
;
}

.headerLogo a {
    font-family: <?php echo $_smarty_tpl->tpl_vars['site_name_font']->value;?>
;
}

<?php }?>
body {

<?php if (!empty($_smarty_tpl->tpl_vars['background_pattern']->value)){?>
    background-image: url(' <?php echo $_smarty_tpl->tpl_vars['background_pattern']->value;?>
');
<?php }?>
<?php if (!empty($_smarty_tpl->tpl_vars['background_pattern']->value)){?>
    background-color: <?php echo $_smarty_tpl->tpl_vars['background_color']->value;?>
;
<?php }?>

}
</style>
<?php }} ?>