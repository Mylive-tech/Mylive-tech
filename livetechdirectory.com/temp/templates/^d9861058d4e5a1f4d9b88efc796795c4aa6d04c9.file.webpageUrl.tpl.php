<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:20:43
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/webpageUrl.tpl" */ ?>
<?php /*%%SmartyHeaderCode:137385458756548e5b1c6339-64107906%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd9861058d4e5a1f4d9b88efc796795c4aa6d04c9' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/webpageUrl.tpl',
      1 => 1386991034,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '137385458756548e5b1c6339-64107906',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548e5b1d7d22_22957971',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548e5b1d7d22_22957971')) {function content_56548e5b1d7d22_22957971($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['URL']){?>
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