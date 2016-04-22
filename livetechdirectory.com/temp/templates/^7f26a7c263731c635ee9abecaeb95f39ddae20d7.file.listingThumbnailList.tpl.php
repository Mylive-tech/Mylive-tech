<?php /* Smarty version Smarty-3.1.12, created on 2014-05-06 13:51:43
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingThumbnailList.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1515304645368e8efd4e3a8-22389287%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f26a7c263731c635ee9abecaeb95f39ddae20d7' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingThumbnailList.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1515304645368e8efd4e3a8-22389287',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5368e8efd91b81_22996137',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5368e8efd91b81_22996137')) {function content_5368e8efd91b81_22996137($_smarty_tpl) {?><div class="thumbnail">
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