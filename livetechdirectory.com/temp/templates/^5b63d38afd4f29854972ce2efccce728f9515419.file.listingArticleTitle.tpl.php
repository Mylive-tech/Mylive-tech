<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingArticleTitle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1006383968535d748cbdeb99-02411485%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b63d38afd4f29854972ce2efccce728f9515419' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingArticleTitle.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1006383968535d748cbdeb99-02411485',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535d748cc29e95_22513043',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748cc29e95_22513043')) {function content_535d748cc29e95_22513043($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['TITLE']){?><a class="listing-title" id="ID_<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value->getUrl(), ENT_QUOTES, 'UTF-8', true));?>
" ><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>

</a>
<?php }?><?php }} ?>