<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:26
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/headerLogo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:931936019535a93ba6cbc17-88402502%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6a966ea3861ea4c970ccd6ad0d62a44a2a8a3e0d' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/headerLogo.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '931936019535a93ba6cbc17-88402502',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LOGO_OPTIONS' => 0,
    'LOGO_STYLES' => 0,
    'SITE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a93ba70c464_81986665',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93ba70c464_81986665')) {function content_535a93ba70c464_81986665($_smarty_tpl) {?><?php if (@FRONT_LOGO!=''&&@FRONT_LOGO!='FRONT_LOGO'){?>
    <a href="<?php echo @DOC_ROOT;?>
/" title="<?php echo @SITE_NAME;?>
">
        <img src="<?php echo @DOC_ROOT;?>
/logo_thumbnail.php?pic=/<?php echo @FRONT_LOGO;?>
&amp;width=<?php echo $_smarty_tpl->tpl_vars['LOGO_OPTIONS']->value['widthValue'];?>
" style="<?php echo $_smarty_tpl->tpl_vars['LOGO_STYLES']->value;?>
" alt="<?php echo @SITE_NAME;?>
" />
    </a>
<?php }else{ ?>
    <h1><a href="<?php echo @DOC_ROOT;?>
/"><?php echo $_smarty_tpl->tpl_vars['SITE_NAME']->value;?>
</a></h1>
<?php }?><?php }} ?>