<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:20:43
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingThumbnailList.tpl" */ ?>
<?php /*%%SmartyHeaderCode:135918766656548e5b1d9fa6-25977236%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8f6c32f08ff49bb6fbaa64c0ba9cfa34afaa17e5' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingThumbnailList.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '135918766656548e5b1d9fa6-25977236',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548e5b1eefa1_56913036',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548e5b1eefa1_56913036')) {function content_56548e5b1eefa1_56913036($_smarty_tpl) {?><div class="thumbnail">
    <a class="link" id="id_<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['LINK']->value->getUrl();?>
" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
"
    <?php if ($_smarty_tpl->tpl_vars['LINK']->value['NOFOLLOW']){?> rel="nofollow"<?php }?>>
    <img src="<?php echo @DOC_ROOT;?>
/thumbnail.php?pic=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['IMAGE'];?>
&amp;width=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['THUMB_WIDTH'];?>
" width="<?php echo $_smarty_tpl->tpl_vars['LINK']->value['THUMB_WIDTH'];?>
"  class="flexible bordered float-left" alt=""/>
</a>
</div><?php }} ?>