<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:20:43
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImage.tpl" */ ?>
<?php /*%%SmartyHeaderCode:77456930656548e5b1f3040-61504245%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3bc5d8c56c3a7caca7b9dab9708fef7263370c95' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingImage.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '77456930656548e5b1f3040-61504245',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548e5b1fcb79_88627725',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548e5b1fcb79_88627725')) {function content_56548e5b1fcb79_88627725($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['LINK']->value['IMAGE'])){?>
    <div class="full-image">
        <img src="<?php echo @DOC_ROOT;?>
/thumbnail.php?pic=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['IMAGE'];?>
&amp;width=800" />
    </div>
<?php }?><?php }} ?>