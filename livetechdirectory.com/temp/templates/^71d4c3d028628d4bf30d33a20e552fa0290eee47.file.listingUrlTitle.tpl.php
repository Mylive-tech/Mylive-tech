<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingUrlTitle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1357092633535d748cb5d356-28880285%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '71d4c3d028628d4bf30d33a20e552fa0290eee47' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingUrlTitle.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1357092633535d748cb5d356-28880285',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535d748cbd8a11_63989251',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748cbd8a11_63989251')) {function content_535d748cbd8a11_63989251($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['TITLE']){?>
    		<a class="listing-title" id="ID_<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" href="<?php if ($_smarty_tpl->tpl_vars['LINK']->value['URL']){?><?php echo $_smarty_tpl->tpl_vars['LINK']->value['URL'];?>
<?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value->getUrl(), ENT_QUOTES, 'UTF-8', true));?>
<?php }?>"
    <?php if ($_smarty_tpl->tpl_vars['LINK']->value['NOFOLLOW']){?> rel="nofollow"<?php }?>>
    <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>

</a>
<?php }?><?php }} ?>