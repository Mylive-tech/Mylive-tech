<?php /* Smarty version Smarty-3.1.12, created on 2014-05-07 00:24:25
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/search/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:124238237353697d39e20ee9-76762765%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '04f8cd254bb14be67efaf0f49db8c8599c1703c7' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/search/index.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '124238237353697d39e20ee9-76762765',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'totalCount' => 0,
    'search' => 0,
    'links' => 0,
    'link' => 0,
    'regular_user_details' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53697d39f03d10_18582533',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53697d39f03d10_18582533')) {function content_53697d39f03d10_18582533($_smarty_tpl) {?><?php if (!is_callable('smarty_mb_wordwrap')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/shared.mb_wordwrap.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.truncate.php';
?><?php if ($_smarty_tpl->tpl_vars['totalCount']->value==0){?><li><p>Sorry, no records found that match your keyword(s)<?php if ($_smarty_tpl->tpl_vars['search']->value){?>: "<?php echo smarty_mb_wordwrap(htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true),200,"\n",true);?>
"<?php }?></p><p><strong>Suggestions</strong>:</p><p><ul><li>Make sure all words are spelled correctly.</li><li>Try different keywords.</li><li>Try more general keywords.</li></ul></p></li><?php }else{ ?><?php if (!empty($_smarty_tpl->tpl_vars['links']->value)){?><li class="group">Search Results - "<?php echo $_REQUEST['search'];?>
"</li><?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
?><li><a class="link" id="id_<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
" href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
"<?php if ($_smarty_tpl->tpl_vars['link']->value['NOFOLLOW']){?> rel="nofollow"<?php }?> target="_blank"><?php echo smarty_modifier_truncate(trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true)),45,"...",true);?>
</a><div style="width:95%"><?php if ($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION']){?><p id="description<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
" style="font-size:15px;font-weight:normal;"><span id="editdescrip_<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
"><?php if (!empty($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION'])){?><?php echo smarty_modifier_truncate(trim($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION']),230,"...",true);?>
<?php }else{ ?>[No Description]<?php }?></span></p><?php }?><a class="readMore" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getUrl();?>
" title="Read more about: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
" target="_self">Read&nbsp;more</a><?php if ((@REQUIRE_REGISTERED_USER==1||@REQUIRE_REGISTERED_USER_ARTICLE==1)&&!empty($_smarty_tpl->tpl_vars['regular_user_details']->value)&&($_smarty_tpl->tpl_vars['regular_user_details']->value['ID']==$_smarty_tpl->tpl_vars['link']->value['OWNER_ID'])){?>,&nbsp;<a class="readMore" href="<?php echo @DOC_ROOT;?>
/submit?linkid=<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
" title="Edit or Remove your link">Review</a><?php }?></div></li><?php } ?><?php }?><?php }?><?php }} ?>