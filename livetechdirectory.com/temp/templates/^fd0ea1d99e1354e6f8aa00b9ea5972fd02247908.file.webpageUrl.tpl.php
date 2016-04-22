<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/webpageUrl.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1599226432535d748ccb6035-87600492%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fd0ea1d99e1354e6f8aa00b9ea5972fd02247908' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/webpageUrl.tpl',
      1 => 1386991034,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1599226432535d748ccb6035-87600492',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535d748cd14f39_19219236',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748cd14f39_19219236')) {function content_535d748cd14f39_19219236($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['URL']){?>
<div class="url">
    <label>Website URL: </label><a class="link" id="id_<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['LINK']->value['URL'];?>
" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
"
    <?php if ($_smarty_tpl->tpl_vars['LINK']->value['NOFOLLOW']){?> rel="nofollow"<?php }?> target="_blank">
        <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>

    </a>
</div>
<?php }?><?php }} ?>