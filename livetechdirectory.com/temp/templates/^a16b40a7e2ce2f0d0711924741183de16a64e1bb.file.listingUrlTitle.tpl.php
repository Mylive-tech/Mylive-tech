<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:20:43
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingUrlTitle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21594209956548e5b182570-89695970%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a16b40a7e2ce2f0d0711924741183de16a64e1bb' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingUrlTitle.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21594209956548e5b182570-89695970',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548e5b197f71_84188397',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548e5b197f71_84188397')) {function content_56548e5b197f71_84188397($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['TITLE']){?>
    		<a class="listing-title" id="ID_<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" href="<?php if ($_smarty_tpl->tpl_vars['LINK']->value['URL']){?><?php echo $_smarty_tpl->tpl_vars['LINK']->value['URL'];?>
<?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value->getUrl(), ENT_QUOTES, 'UTF-8', true));?>
<?php }?>"
    <?php if ($_smarty_tpl->tpl_vars['LINK']->value['NOFOLLOW']){?> rel="nofollow"<?php }?>>
    <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>

</a>
<?php }?><?php }} ?>