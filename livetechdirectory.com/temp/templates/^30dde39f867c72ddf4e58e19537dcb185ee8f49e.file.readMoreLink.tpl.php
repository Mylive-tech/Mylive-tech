<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/readMoreLink.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20584862535d748cddd8f0-90037072%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '30dde39f867c72ddf4e58e19537dcb185ee8f49e' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/readMoreLink.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20584862535d748cddd8f0-90037072',
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
  'unifunc' => 'content_535d748ce40637_07995051',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748ce40637_07995051')) {function content_535d748ce40637_07995051($_smarty_tpl) {?><a class="readMore" href="<?php echo $_smarty_tpl->tpl_vars['LINK']->value->getUrl();?>
" title="Read more about: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
">Read&nbsp;more</a>


<?php if (!empty($_smarty_tpl->tpl_vars['regular_user_details']->value)&&($_smarty_tpl->tpl_vars['regular_user_details']->value['ID']==$_smarty_tpl->tpl_vars['LINK']->value['OWNER_ID'])){?>
    ,&nbsp;<a class="readMore" href="<?php echo @DOC_ROOT;?>
/submit?linkid=<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" title="Edit or Remove your link">Review</a>
<?php }?><?php }} ?>