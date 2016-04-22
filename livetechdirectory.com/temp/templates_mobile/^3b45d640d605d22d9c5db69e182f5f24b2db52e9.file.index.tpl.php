<?php /* Smarty version Smarty-3.1.12, created on 2014-05-03 02:37:00
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/submit/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18507690475364564c8e1bd9-57990216%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b45d640d605d22d9c5db69e182f5f24b2db52e9' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/submit/index.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18507690475364564c8e1bd9-57990216',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'is_admin' => 0,
    'disablereason' => 0,
    'confirmed' => 0,
    'regular_user_details' => 0,
    'catid' => 0,
    'linktypeid' => 0,
    'data' => 0,
    'submit_items' => 0,
    'item' => 0,
    'valid' => 0,
    'FIELD_NAME' => 0,
    'error_list' => 0,
    'review_link' => 0,
    'CategoryTitle' => 0,
    'CATEGORY_ID' => 0,
    'link_type_details' => 0,
    'add_categs' => 0,
    'categ' => 0,
    'allTags' => 0,
    'i' => 0,
    'tag' => 0,
    'tagsCount' => 0,
    'MetaDescriptionLimit' => 0,
    'user_level' => 0,
    'add_links' => 0,
    'remove_link' => 0,
    'errorKey' => 0,
    'errorItem' => 0,
    'DO_MATH_N1' => 0,
    'DO_MATH_N2' => 0,
    'DO_MATH' => 0,
    'dont_show_captch' => 0,
    'imagehash' => 0,
    'AGREERULES' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5364564cd8d087_95125493',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5364564cd8d087_95125493')) {function content_5364564cd8d087_95125493($_smarty_tpl) {?><?php if (!is_callable('smarty_function_formtool_count_chars')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.formtool_count_chars.php';
?><?php if (@DISABLE_SUBMIT==1&&$_smarty_tpl->tpl_vars['is_admin']->value!=1){?><?php echo $_smarty_tpl->getSubTemplate ("views/submit/closed.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('disablereason'=>$_smarty_tpl->tpl_vars['disablereason']->value), 0);?>
<?php }else{ ?><?php if (($_smarty_tpl->tpl_vars['confirmed']->value)){?><div class="box info">The link's email address has been confirmed.<br />The link will be listed as soon as it is approved by a site administrator.</div><?php }?><?php if (@REQUIRE_REGISTERED_USER==1&&empty($_smarty_tpl->tpl_vars['regular_user_details']->value['ID'])){?><li class="group">Information</li><li>You must be logged in to submit a new link.<br />No account yet? <a href="<?php echo @DOC_ROOT;?>
/user/register" title="Register new user">Create one</a></li><?php }else{ ?>                            <li id="categ" style="display: none;"><br /><?php echo $_smarty_tpl->getSubTemplate ("views/submit/add_categs.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
&nbsp;</li><?php if ($_smarty_tpl->tpl_vars['catid']->value==0){?><li class="group">Step One Choose a Category</li><?php }elseif($_smarty_tpl->tpl_vars['linktypeid']->value==0){?><li class="group">Step Two Choose a Link Type:</li><?php }?><li><form method="post" action="" class="cmxform phpld-form" id="submitForm" name="submitForm" enctype="multipart/form-data" ><?php if ($_smarty_tpl->tpl_vars['catid']->value==0){?><?php echo $_smarty_tpl->getSubTemplate ("views/submit/category.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('categoryID'=>$_smarty_tpl->tpl_vars['data']->value['CATEGORY_ID'],'parentID'=>$_smarty_tpl->tpl_vars['data']->value['PARENT_ID']), 0);?>
<?php }elseif($_smarty_tpl->tpl_vars['linktypeid']->value==0){?><?php echo $_smarty_tpl->getSubTemplate ("views/submit/linktype.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }else{ ?><input type="hidden" name="LINK_TYPE" value="<?php echo $_smarty_tpl->tpl_vars['linktypeid']->value;?>
" checked="true" /><?php }?><?php if ($_smarty_tpl->tpl_vars['linktypeid']->value!=0){?><?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['submit_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?><?php if (isset($_smarty_tpl->tpl_vars["FIELD_NAME"])) {$_smarty_tpl->tpl_vars["FIELD_NAME"] = clone $_smarty_tpl->tpl_vars["FIELD_NAME"];
$_smarty_tpl->tpl_vars["FIELD_NAME"]->value = $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']; $_smarty_tpl->tpl_vars["FIELD_NAME"]->nocache = null; $_smarty_tpl->tpl_vars["FIELD_NAME"]->scope = 0;
} else $_smarty_tpl->tpl_vars["FIELD_NAME"] = new Smarty_variable($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'], null, 0);?><?php if ($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='TITLE'){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><?php if ($_smarty_tpl->tpl_vars['valid']->value['rules'][$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]['required']==true){?><span class="phpld-required">*</span><?php }?>Title:</div><div class="field float-left"><div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><input type="text" id="TITLE" name="TITLE" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
" maxlength="<?php echo @TITLE_MAX_LENGTH;?>
" class="text" /></div></div></div><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='CATEGORY_ID'){?><div class="y`m-columnar phpld-equalize"><div class="phpld-label float-left" style="display: inline-block;vertical-align: top;"><?php if ($_smarty_tpl->tpl_vars['valid']->value['rules'][$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]['required']==true){?><span class="phpld-required">*</span><?php }?>Category: &nbsp;</div><div class="field float-left" style="display: inline-block;"><?php if (!$_smarty_tpl->tpl_vars['review_link']->value){?><div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>" ><?php echo $_smarty_tpl->tpl_vars['CategoryTitle']->value;?>
</div><div id="inlineCategSelection"><input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID" value="<?php echo $_smarty_tpl->tpl_vars['CATEGORY_ID']->value;?>
" /></div><?php }else{ ?><?php echo $_smarty_tpl->getSubTemplate ("views/submit/review_categ.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('categ_id'=>$_smarty_tpl->tpl_vars['CATEGORY_ID']->value), 0);?>
&nbsp;<?php }?><?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']>0){?><?php  $_smarty_tpl->tpl_vars['categ'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['categ']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['add_categs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['additional']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['categ']->key => $_smarty_tpl->tpl_vars['categ']->value){
$_smarty_tpl->tpl_vars['categ']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['additional']['index']++;
?><?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['additional']['index']<$_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']){?><div><br /><?php echo $_smarty_tpl->getSubTemplate ("views/submit/add_categs.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('categ_id'=>$_smarty_tpl->tpl_vars['categ']->value['CATEGORY_ID'],'additional_categs'=>true), 0);?>
&nbsp;</div><?php }?><?php } ?><a href="#" class="formDelCateg" id="fileDelCateg" onclick="return false;" style="<?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']>'1'&&!empty($_smarty_tpl->tpl_vars['add_categs']->value)){?><?php }else{ ?>display: none<?php }?>">remove</a><div class="clear"></div><br /><a href="#" id="plusCategLink" class="formSmall" style="border-width: 0;">+ additional categ</a><?php }?></div></div><?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='TAGS'){?><link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
javascripts/select2/select2.css" /><script type="text/javascript" src="<?php echo @DOC_ROOT;?>
javascripts/select2/select2.js" ></script><script type="text/javascript">jQuery(document).ready(function(){<?php if (isset($_smarty_tpl->tpl_vars["i"])) {$_smarty_tpl->tpl_vars["i"] = clone $_smarty_tpl->tpl_vars["i"];
$_smarty_tpl->tpl_vars["i"]->value = 0; $_smarty_tpl->tpl_vars["i"]->nocache = null; $_smarty_tpl->tpl_vars["i"]->scope = 0;
} else $_smarty_tpl->tpl_vars["i"] = new Smarty_variable(0, null, 0);?><?php if (isset($_smarty_tpl->tpl_vars["tagsCount"])) {$_smarty_tpl->tpl_vars["tagsCount"] = clone $_smarty_tpl->tpl_vars["tagsCount"];
$_smarty_tpl->tpl_vars["tagsCount"]->value = count($_smarty_tpl->tpl_vars['allTags']->value); $_smarty_tpl->tpl_vars["tagsCount"]->nocache = null; $_smarty_tpl->tpl_vars["tagsCount"]->scope = 0;
} else $_smarty_tpl->tpl_vars["tagsCount"] = new Smarty_variable(count($_smarty_tpl->tpl_vars['allTags']->value), null, 0);?>jQuery("#<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
").select2(
                                        {
                                        tags: [<?php  $_smarty_tpl->tpl_vars["tag"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["tag"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['allTags']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["tag"]->key => $_smarty_tpl->tpl_vars["tag"]->value){
$_smarty_tpl->tpl_vars["tag"]->_loop = true;
?><?php if (isset($_smarty_tpl->tpl_vars["i"])) {$_smarty_tpl->tpl_vars["i"] = clone $_smarty_tpl->tpl_vars["i"];
$_smarty_tpl->tpl_vars["i"]->value = $_smarty_tpl->tpl_vars['i']->value+1; $_smarty_tpl->tpl_vars["i"]->nocache = null; $_smarty_tpl->tpl_vars["i"]->scope = 0;
} else $_smarty_tpl->tpl_vars["i"] = new Smarty_variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);?>{id:<?php echo $_smarty_tpl->tpl_vars['tag']->value['ID'];?>
, text:'<?php echo $_smarty_tpl->tpl_vars['tag']->value['TITLE'];?>
'}<?php if ($_smarty_tpl->tpl_vars['i']->value<$_smarty_tpl->tpl_vars['tagsCount']->value){?>,<?php }?><?php } ?>]

                                        }
                                        );});</script><div class="phpld-columnar phpld-equalize select2-submit"><div class="phpld-label float-left"><?php if ($_smarty_tpl->tpl_vars['valid']->value['rules']['OWNER_EMAIL']['required']==true){?><span class="phpld-required">*</span><?php }?>Tags:</div><div class="field float-left"><div class="phpld-fbox-text  <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']], ENT_QUOTES, 'UTF-8', true));?>
" size="40" /></div></div></div><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='OWNER_NAME'){?><?php if ($_smarty_tpl->tpl_vars['regular_user_details']->value){?><input type="hidden" id="OWNER_NAME" name="OWNER_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['OWNER_NAME'], ENT_QUOTES, 'UTF-8', true));?>
" maxlength="<?php echo @USER_NAME_MAX_LENGTH;?>
" /><?php }else{ ?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><?php if ($_smarty_tpl->tpl_vars['valid']->value['rules']['OWNER_NAME']['required']==true){?><span class="phpld-required">*</span><?php }?>Owner Name:</div><div class="field float-left"><div class="phpld-fbox-text  <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><input type="text" id="OWNER_NAME" name="OWNER_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['OWNER_NAME'], ENT_QUOTES, 'UTF-8', true));?>
" maxlength="<?php echo @USER_NAME_MAX_LENGTH;?>
" class="text" /></div></div></div><?php }?><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='OWNER_EMAIL'){?><?php if ($_smarty_tpl->tpl_vars['regular_user_details']->value){?><input type="hidden" id="OWNER_EMAIL" name="OWNER_EMAIL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['OWNER_EMAIL'], ENT_QUOTES, 'UTF-8', true));?>
" /><?php }else{ ?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><?php if ($_smarty_tpl->tpl_vars['valid']->value['rules']['OWNER_EMAIL']['required']==true){?><span class="phpld-required">*</span><?php }?>Owner Email:</div><div class="field float-left"><div class="phpld-fbox-text  <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><input type="text" id="OWNER_EMAIL" name="OWNER_EMAIL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['OWNER_EMAIL'], ENT_QUOTES, 'UTF-8', true));?>
" maxlength="255" class="text" /></div><div class="phpld-clearfix"></div><p class="small"><span style="display:inline-block; float: left;width:20px;"><input type="checkbox" name="OWNER_NEWSLETTER_ALLOW" checked="checked" /></span>&nbsp;<span style="display: block; float: left; margin: 2px 0 0 0;">Allow site administrator to send me newsletters.</span><div style="clear: both;"></div></p></div></div><?php }?><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='META_KEYWORDS'){?><?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['SHOW_META']==1){?><?php if (@ENABLE_META_TAGS==1){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">META Keywords:</div><div class="field float-left"><div class="phpld-fbox-text  <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><input type="text" id="META_KEYWORDS" name="META_KEYWORDS" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['META_KEYWORDS'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></div><div class="phpld-clearfix"></div><p class="msg notice info">Separate keywords by comma.</p></div></div><?php }?><?php }?><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='META_DESCRIPTION'){?><?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['SHOW_META']==1){?><?php if (@ENABLE_META_TAGS==1){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">META Description:</div><div class="smallDesc float-left"><div class="phpld-fbox-text  <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><textarea id="META_DESCRIPTION" name="META_DESCRIPTION" rows="3" cols="30" class="text" <?php echo smarty_function_formtool_count_chars(array('name'=>"META_DESCRIPTION",'limit'=>@META_DESCRIPTION_MAX_LENGTH,'alert'=>true),$_smarty_tpl);?>
><?php echo htmlspecialchars(trim($_smarty_tpl->tpl_vars['data']->value['META_DESCRIPTION']), ENT_QUOTES, 'UTF-8', true);?>
</textarea></div><div class="phpld-clearfix"></div><p class="limitDesc float-left">Limit:<div class="phpld-fbox-text"><input type="text" name="META_DESCRIPTION_limit" class="limit_field" readonly="readonly" value="<?php echo $_smarty_tpl->tpl_vars['MetaDescriptionLimit']->value;?>
" /></p></div></div></div><?php }?><?php }?><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='RELEASE_DATE'){?><?php if ($_smarty_tpl->tpl_vars['user_level']->value==1){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><?php if ($_smarty_tpl->tpl_vars['valid']->value['rules'][$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]['required']==true){?><span class="phpld-required">*</span><?php }?><?php echo $_smarty_tpl->tpl_vars['item']->value['NAME'];?>
:</div><div class="smallDesc float-left"><div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']], ENT_QUOTES, 'UTF-8', true));?>
" size="40" class="text" /></div><div class="phpld-clearfix"></div></div></div><?php }?><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='THUMBNAIL_WIDTH_GRID'){?><input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['link_type_details']->value['DEFAULT_THUMBNAIL_GRID'];?>
" /><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='THUMBNAIL_WIDTH_LIST'){?><input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['link_type_details']->value['DEFAULT_THUMBNAIL_LIST'];?>
" /><?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='RECPR_URL'){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><?php if ($_smarty_tpl->tpl_vars['valid']->value['rules'][$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]['required']==true){?><span class="phpld-required">*</span><?php }?><?php echo $_smarty_tpl->tpl_vars['item']->value['NAME'];?>
:</div><div class="field float-left"><div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="" size="40" maxlength="255" class="text" /></div><?php if (@SAME_DOMAIN_RECPR==1){?><p class="small">We require the Reciprocal to be on the same domain as the link you are submitting.</p><?php }?><div class="phpld-clearfix"></div><p class="small">To validate the reciprocal link please include the<br />following HTML code in the page at the URL<br />specified above, before submiting this form:</p><div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>"><textarea name="RECPR_TEXT" rows="2" readonly="readonly" cols="50" class="text">&lt;a href="<?php echo @DEFAULT_RECPR_URL;?>
"&gt;<?php echo @DEFAULT_RECPR_TITLE;?>
&lt;/a&gt;</textarea></div></div></div><?php }else{ ?><?php echo $_smarty_tpl->getSubTemplate ("views/submit/fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('item'=>$_smarty_tpl->tpl_vars['item']->value,'valid'=>$_smarty_tpl->tpl_vars['valid']->value,'data'=>$_smarty_tpl->tpl_vars['data']->value), 0);?>
<?php }?><?php } ?><?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['DEEP_LINKS']>0){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label required"><label>Deep Links:</label></div><div class="field float-left"><div id="additional_links"><div ><div  style="width:49%;float: left;">Titles</div><div  style="width:49%;float: left;">URLs</div></div><?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = (int)0;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['link_type_details']->value['DEEP_LINKS']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?><div><div class="phpld-fbox-text" style="float: left;width: 45%;"><input type="text" name="ADD_LINK_TITLE[]" class="text" style="width: 160px;" value="<?php echo $_smarty_tpl->tpl_vars['add_links']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['TITLE'];?>
" /></div><div class="phpld-fbox-text" style="float: left;width: 45%;"><input type="text" name="ADD_LINK_URL[]"   class="text" style="width: 160px;" value="<?php echo $_smarty_tpl->tpl_vars['add_links']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['URL'];?>
" /></div></div><?php endfor; endif; ?></div><div class="clear"></div></div></div><?php }?><?php if ($_smarty_tpl->tpl_vars['remove_link']->value){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left">Mark for removal:</div><div class="phpld-field float-left"><input type="checkbox" id="MARK_REMOVE" name="MARK_REMOVE" /><span class="small">Use with caution</span></div></div><?php }?><?php if (@VISUAL_CONFIRM==2){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>DO THE MATH:</div><div class="field float-left"><span style="color: red;"><?php  $_smarty_tpl->tpl_vars['errorItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['errorItem']->_loop = false;
 $_smarty_tpl->tpl_vars['errorKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['error_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['errorItem']->key => $_smarty_tpl->tpl_vars['errorItem']->value){
$_smarty_tpl->tpl_vars['errorItem']->_loop = true;
 $_smarty_tpl->tpl_vars['errorKey']->value = $_smarty_tpl->tpl_vars['errorItem']->key;
?><?php if ($_smarty_tpl->tpl_vars['errorKey']->value=='DO_MATH'){?><?php if (is_array($_smarty_tpl->tpl_vars['errorItem']->value)){?><?php echo $_smarty_tpl->tpl_vars['errorItem']->value['remote'];?>
<br/><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['errorItem']->value;?>
<br/><?php }?><?php }?><?php } ?></span><font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;"><?php echo $_smarty_tpl->tpl_vars['DO_MATH_N1']->value;?>
 + <?php echo $_smarty_tpl->tpl_vars['DO_MATH_N2']->value;?>
 = </font><input type="text" id="DO_MATH" name="DO_MATH" value='<?php echo $_smarty_tpl->tpl_vars['DO_MATH']->value;?>
' class="text" style="width: 60px;"/></div></div><?php }?><?php if (@VISUAL_CONFIRM==1){?><?php if ($_smarty_tpl->tpl_vars['dont_show_captch']->value!=1){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Enter the code shown:</div><div class="field float-left"><div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value['CAPTCHA']){?>phpld-error<?php }?>"><?php if ($_smarty_tpl->tpl_vars['error_list']->value['CAPTCHA']){?><p class="phpld-message"><?php echo $_smarty_tpl->tpl_vars['error_list']->value['CAPTCHA'];?>
</p><?php }?><input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" /><input class="required text" id="CAPTCHA" name="CAPTCHA" style="width:80px;" type="text" value="" size="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" maxlength="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" class="text" /><label for="CAPTCHA" id="captcha_validation" style="float: none; color: red; padding-left: .5em; "></label><div style="clear: both;"></div><p class="small">This helps prevent automated registrations.</p><img src="<?php echo @DOC_ROOT;?>
/captcha.php?imagehash=<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" class="captcha" alt="Visual Confirmation Security Code" title="Visual Confirmation Security Code" /></div></div></div><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['regular_user_details']->value&&$_smarty_tpl->tpl_vars['regular_user_details']->value['ACTIVE']&&$_smarty_tpl->tpl_vars['regular_user_details']->value['EMAIL_CONFIRMED']){?><input type="hidden" id="AGREERULES" name="AGREERULES" value="1" checked="checked" /><?php }else{ ?><div class="phpld-columnar phpld-equalize"><div id="AGREERULES"><div class="phpld-label float-left" ><span class="phpld-required">*</span><a href="<?php echo @DOC_ROOT;?>
/rules" title="Submission Rules" target='_blank'>Submission Rules Agreement</a>:</div><div class="field float-left" style="width:70%"><div class="phpld-fbox-check float-left <?php if ($_smarty_tpl->tpl_vars['error_list']->value['AGREERULES']){?>phpld-error<?php }?>"><?php if ($_smarty_tpl->tpl_vars['error_list']->value['AGREERULES']){?><p class="phpld-message"><?php echo $_smarty_tpl->tpl_vars['error_list']->value['AGREERULES'];?>
</p><?php }?><input type="checkbox" id="AGREERULES" name="AGREERULES"<?php if ($_smarty_tpl->tpl_vars['AGREERULES']->value=='on'){?> checked="checked"<?php }?> /></div><div class="phpld-label float-left">I AGREE with the <a href="<?php echo @DOC_ROOT;?>
/rules" title="Submission Rules" target='_blank'>submission rules</a></div></div></div></div><?php }?><div class="phpld-fbox-button"><?php if (!$_smarty_tpl->tpl_vars['review_link']->value&&!$_smarty_tpl->tpl_vars['remove_link']->value){?><div class="phpld-fbox-button"><input type="submit" name="continue" value="Continue" onclick="this.value='Please Wait'"  class="button" /></div><?php }else{ ?><input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['ID'];?>
"/><input type="submit" name="edit" value="Continue" class="button" /><?php }?></div><?php }?><input type="hidden" name="formSubmitted" value="1" /></form></li><?php }?><?php }?><div class="phpld-clearfix"></div>
    <script type="text/javascript">
    //<![CDATA[

            jQuery(document).ready(function(){

                jQuery("input[name=choicemade]").click(function(){

                   // alert('asdasd');
                    //update_form(); // albert: seems useless, we are making a refresh when the user changes the link type
                    var ts = jQuery('input[name=LINK_TYPE]:checked').val()
                    var lt = getUrlVars()["LINK_TYPE"];
                    if (lt) {
                        var url = location.href.replace("LINK_TYPE="+lt, "LINK_TYPE=" + ts);
                    } else {
                        if (location.href.search(/\?/) == -1) {
                            var url = location.href + "?LINK_TYPE="+ts;
                        } else {
                            var url = location.href + "&LINK_TYPE="+ts;
                        }
                    }
                    location.href= url;
                });

                function getUrlVars()
                {
                    var vars = [], hash;
                    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                    for(var i = 0; i < hashes.length; i++)
                    {
                        hash = hashes[i].split('=');
                        vars.push(hash[0]);
                        vars[hash[0]] = hash[1];
                    }
                    return vars;
                }

                var maxCats = <?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']==''){?>1<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES'];?>
<?php }?>;
                var numCats = 1 + <?php echo count($_smarty_tpl->tpl_vars['add_categs']->value);?>
;

                jQuery("#addCateg").click(function(){
                    if (numCats >= maxCats){
                        jQuery('#plusCategLink').hide();
                    }
                });
                
               
               $("#plusCategLink").unbind('click');

                jQuery("#plusCategLink").click(function(){

                    if (numCats < maxCats) {
                       
                        jQuery("#categ").clone(true).insertBefore("#fileDelCateg").attr('id','').show();
                         
                       
                        jQuery("#fileDelCateg").show();
                        numCats++;
                    }
                    if (numCats == maxCats)
                        jQuery(this).hide();

                    return false;
                });
                
                $("#fileDelCateg").unbind('click');

                jQuery("#fileDelCateg").click(function(){

                    if (numCats > 1) {
                        jQuery(this).prev().remove();
                        numCats--;
                    }
                    if (numCats < maxCats) {
                        jQuery("#plusCategLink").show();
                    }
                    if (numCats == 1)
                        jQuery(this).hide();

                    return false;
                });
            });


            Array.prototype.in_array = function(p_val) {
                for(var i = 0, l = this.length; i < l; i++) {
                    if(this[i] == p_val) {
                        return true;
                    }
                }
                return false;
            }
        //]]>
        </script>
    
<?php }} ?>