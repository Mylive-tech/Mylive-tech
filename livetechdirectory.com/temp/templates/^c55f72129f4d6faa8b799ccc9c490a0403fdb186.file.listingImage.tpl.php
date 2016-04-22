<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImage.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1095228659535d748cd1de75-07983033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c55f72129f4d6faa8b799ccc9c490a0403fdb186' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImage.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1095228659535d748cd1de75-07983033',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535d748cd54065_62543338',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748cd54065_62543338')) {function content_535d748cd54065_62543338($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['LINK']->value['IMAGE'])){?>
    <div class="full-image">
        <img src="<?php echo @DOC_ROOT;?>
/thumbnail.php?pic=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['IMAGE'];?>
&amp;width=800" />
    </div>
<?php }?><?php }} ?>