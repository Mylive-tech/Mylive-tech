<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/_shared/listingRender.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13902830865654ac8c746a59-35962272%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd5195146c44b1b16fdfbbce985e7d9da79f6acdf' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/MobileFormat/views/_shared/listingRender.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13902830865654ac8c746a59-35962272',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'PAGETITLE' => 0,
    'links' => 0,
    'edit_link' => 0,
    'link' => 0,
    'regular_user_details' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654ac8c7adeb2_31268551',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac8c7adeb2_31268551')) {function content_5654ac8c7adeb2_31268551($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.truncate.php';
?>
<?php if (!empty($_smarty_tpl->tpl_vars['PAGETITLE']->value)){?>
<li class="group"><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
</li>
<?php }else{ ?>
<li class="group">Links</li>
<?php }?>
<?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
?>
 <li>      

<?php if ($_smarty_tpl->tpl_vars['edit_link']->value){?>
	<span id="T_<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
</span>
	<?php if (!empty($_smarty_tpl->tpl_vars['link']->value['URL'])){?>
		&nbsp;&nbsp;
		<a class="link" id="id_<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
" href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
"
		<?php if ($_smarty_tpl->tpl_vars['link']->value['NOFOLLOW']){?> rel="nofollow"<?php }?>
		target="_blank">
		<span class="link"><img src="<?php echo @DOC_ROOT;?>
/images/external_link.png" alt="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
" border="0"/></span>
		</a>
	<?php }?>
<?php }else{ ?>
<a class="link" id="id_<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
" href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
"
<?php if ($_smarty_tpl->tpl_vars['link']->value['NOFOLLOW']){?> rel="nofollow"<?php }?>
target="_blank"><?php echo smarty_modifier_truncate(trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true)),45,"...",true);?>
</a>   
<?php }?>

<div style="width:95%">	   
	<?php if ($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION']){?>
	<p id="description<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
" style="font-size:15px;font-weight:normal;">
	   <span id="editdescrip_<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
"><?php if (!empty($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION'])){?><?php echo smarty_modifier_truncate(trim($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION']),230,"...",true);?>
<?php }else{ ?>[No Description]<?php }?></span>
	</p>	
	<?php }?>
	<a class="readMore" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getUrl();?>
" title="Read more about: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
" target="_self">Read&nbsp;more</a>
	<?php if ((@REQUIRE_REGISTERED_USER==1||@REQUIRE_REGISTERED_USER_ARTICLE==1)&&!empty($_smarty_tpl->tpl_vars['regular_user_details']->value)&&($_smarty_tpl->tpl_vars['regular_user_details']->value['ID']==$_smarty_tpl->tpl_vars['link']->value['OWNER_ID'])){?>
	,&nbsp;<a class="readMore" href="<?php echo @DOC_ROOT;?>
/submit?linkid=<?php echo $_smarty_tpl->tpl_vars['link']->value['ID'];?>
" title="Edit or Remove your link">Review</a>
	<?php }?>
</div>
</li>
<?php } ?>

<?php }} ?>