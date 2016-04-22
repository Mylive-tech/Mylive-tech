<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 17:51:10
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/search/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6727428495654a38eb1e7e4-29407010%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '47fafa5bf1b2382be180f523098c3c8bd95882b6' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/search/index.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6727428495654a38eb1e7e4-29407010',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'totalCount' => 0,
    'search' => 0,
    'searchquery' => 0,
    'links' => 0,
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654a38eb472e8_31492568',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654a38eb472e8_31492568')) {function content_5654a38eb472e8_31492568($_smarty_tpl) {?><?php if (!is_callable('smarty_mb_wordwrap')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/shared.mb_wordwrap.php';
?><?php if ($_smarty_tpl->tpl_vars['totalCount']->value==0){?><div><p>Sorry, no records found that match your keyword(s)<?php if ($_smarty_tpl->tpl_vars['search']->value){?>: "<?php echo smarty_mb_wordwrap(htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true),200,"\n",true);?>
"<?php }?></p><p><strong>Suggestions</strong>:</p><p><ul><li>Make sure all words are spelled correctly.</li><li>Try different keywords.</li><li>Try more general keywords.</li></ul></p></div><?php }else{ ?><?php if (!empty($_smarty_tpl->tpl_vars['searchquery']->value)){?><p>Search results for: <strong><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['searchquery']->value, ENT_QUOTES, 'UTF-8', true);?>
</strong></p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['links']->value)){?><h3>Search Results</h3><div class="listing-style-list"><?php  $_smarty_tpl->tpl_vars['LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINK']->key => $_smarty_tpl->tpl_vars['LINK']->value){
$_smarty_tpl->tpl_vars['LINK']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['LINK']->value->listing();?>
<?php } ?></div><?php }?><?php }?><?php }} ?>