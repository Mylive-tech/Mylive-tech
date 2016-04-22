<?php /* Smarty version Smarty-3.1.12, created on 2015-11-26 17:56:31
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/readMoreLink.tpl" */ ?>
<?php /*%%SmartyHeaderCode:433059698565747cfc912f5-22862552%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5403276d19c53328947ae762907523d9a2ac3279' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/readMoreLink.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '433059698565747cfc912f5-22862552',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
    'regular_user_details' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_565747cfca3ce0_45866100',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_565747cfca3ce0_45866100')) {function content_565747cfca3ce0_45866100($_smarty_tpl) {?><a class="readMore" href="<?php echo $_smarty_tpl->tpl_vars['LINK']->value->getUrl();?>
" title="Read more about: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
">Read&nbsp;more</a>


<?php if (!empty($_smarty_tpl->tpl_vars['regular_user_details']->value)&&($_smarty_tpl->tpl_vars['regular_user_details']->value['ID']==$_smarty_tpl->tpl_vars['LINK']->value['OWNER_ID'])){?>
    ,&nbsp;<a class="readMore" href="<?php echo @DOC_ROOT;?>
/submit?linkid=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" title="Edit or Remove your link">Review</a>
<?php }?><?php }} ?>