<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImageWithUrl.tpl" */ ?>
<?php /*%%SmartyHeaderCode:855855356535d748cd285e2-42882309%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '357d71c15fa58a973257b6247a6214e13d796a9c' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImageWithUrl.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '855855356535d748cd285e2-42882309',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535d748cd489b9_89081891',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748cd489b9_89081891')) {function content_535d748cd489b9_89081891($_smarty_tpl) {?><div class="full-image">
    <a href="<?php echo $_smarty_tpl->tpl_vars['LINK']->value['URL'];?>
"><img src="<?php echo @DOC_ROOT;?>
/thumbnail.php?pic=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['IMAGE'];?>
&amp;width=800" /></a>
</div><?php }} ?>