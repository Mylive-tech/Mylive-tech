<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/rss.tpl" */ ?>
<?php /*%%SmartyHeaderCode:203314658056548c5e427295-24885818%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7e6a4f685ab9b3eb9a8fa6017859ad1c9736e16f' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/rss.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '203314658056548c5e427295-24885818',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
    'list' => 0,
    'in_page_title' => 0,
    'search' => 0,
    'p' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e44b412_77625135',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e44b412_77625135')) {function content_56548c5e44b412_77625135($_smarty_tpl) {?> <?php if (@ENABLE_RSS&&($_smarty_tpl->tpl_vars['category']->value['ID']>0||$_smarty_tpl->tpl_vars['list']->value)){?>
 <link rel="alternate" type="application/rss+xml" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['in_page_title']->value, ENT_QUOTES, 'UTF-8', true));?>
" href="<?php echo @SITE_URL;?>
rss.php?<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?>search=<?php echo urlencode($_smarty_tpl->tpl_vars['search']->value);?>
<?php }elseif($_smarty_tpl->tpl_vars['p']->value>1){?>p=<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
<?php }elseif($_smarty_tpl->tpl_vars['list']->value){?>list=<?php echo $_smarty_tpl->tpl_vars['list']->value;?>
<?php }else{ ?>c=<?php echo $_smarty_tpl->tpl_vars['category']->value['ID'];?>
<?php }?>" />
 <?php }?><?php }} ?>