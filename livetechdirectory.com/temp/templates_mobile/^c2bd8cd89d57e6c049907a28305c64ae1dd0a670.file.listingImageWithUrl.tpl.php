<?php /* Smarty version Smarty-3.1.12, created on 2015-11-26 17:56:31
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImageWithUrl.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1830736246565747cfc706f3-26705166%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c2bd8cd89d57e6c049907a28305c64ae1dd0a670' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImageWithUrl.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1830736246565747cfc706f3-26705166',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_565747cfc78266_16289678',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_565747cfc78266_16289678')) {function content_565747cfc78266_16289678($_smarty_tpl) {?><div class="full-image">
    <a href="<?php echo $_smarty_tpl->tpl_vars['LINK']->value['URL'];?>
"><img src="<?php echo @DOC_ROOT;?>
/thumbnail.php?pic=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['IMAGE'];?>
&amp;width=800" /></a>
</div><?php }} ?>