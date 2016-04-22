<?php /* Smarty version Smarty-3.1.12, created on 2015-11-26 17:56:31
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingUrl.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1519447202565747cfc3be01-53233405%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '275aba40e2bbea709e78a37a717fff96ab525753' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingUrl.tpl',
      1 => 1386991030,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1519447202565747cfc3be01-53233405',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_565747cfc4ea15_70679833',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_565747cfc4ea15_70679833')) {function content_565747cfc4ea15_70679833($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['URL']){?>
<div class="url">
    <label>Website URL: </label><a class="link" id="id_<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value->getUrl(), ENT_QUOTES, 'UTF-8', true));?>
" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
"
    <?php if ($_smarty_tpl->tpl_vars['LINK']->value['NOFOLLOW']){?> rel="nofollow"<?php }?> target="_blank">
        <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>

    </a>
</div>
<?php }?><?php }} ?>