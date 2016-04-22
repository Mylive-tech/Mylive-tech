<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/headerLogo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:86452698556548c5e44dc56-59394210%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9d31d54e5bba09ba0ee0b94c30e928b0a01ef196' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/headerLogo.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '86452698556548c5e44dc56-59394210',
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
  'unifunc' => 'content_56548c5e46ef86_56709973',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e46ef86_56709973')) {function content_56548c5e46ef86_56709973($_smarty_tpl) {?><?php if (@FRONT_LOGO!=''&&@FRONT_LOGO!='FRONT_LOGO'){?>
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