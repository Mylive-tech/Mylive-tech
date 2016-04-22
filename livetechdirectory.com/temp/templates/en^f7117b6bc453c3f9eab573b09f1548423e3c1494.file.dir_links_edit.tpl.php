<?php /* Smarty version Smarty-3.1.12, created on 2014-04-28 10:42:53
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_links_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1192355425535e30ad0a6eb5-62514152%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f7117b6bc453c3f9eab573b09f1548423e3c1494' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_links_edit.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1192355425535e30ad0a6eb5-62514152',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validators' => 0,
    'error' => 0,
    'sql_error' => 0,
    'errorMsg' => 0,
    'posted' => 0,
    'AllowedFeat' => 0,
    'action' => 0,
    'link_types' => 0,
    'k' => 0,
    'linktypeid' => 0,
    'v' => 0,
    'submit_items' => 0,
    'item' => 0,
    'data' => 0,
    'SEO_URL' => 0,
    'ID' => 0,
    'categoryId' => 0,
    'link_type_details' => 0,
    'add_categs' => 0,
    'categ' => 0,
    'MetaDescriptionLimit' => 0,
    'allTags' => 0,
    'i' => 0,
    'tag' => 0,
    'tagsCount' => 0,
    'opt' => 0,
    'yes_no' => 0,
    'categs' => 0,
    'group_image_details' => 0,
    'image' => 0,
    'imagegroupid' => 0,
    'ctn_img' => 0,
    'add_links' => 0,
    'ActiveUsersList' => 0,
    'econfirm' => 0,
    'semail' => 0,
    'stats' => 0,
    'submit_session' => 0,
    'link_type_id' => 0,
    'link_type' => 0,
    'field_name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535e30ad69a672_19797171',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535e30ad69a672_19797171')) {function content_535e30ad69a672_19797171($_smarty_tpl) {?><?php if (!is_callable('smarty_function_formtool_count_chars')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.formtool_count_chars.php';
if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
if (!is_callable('smarty_function_html_options3')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options3.php';
?><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"edit_link_form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>



<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



<?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?>
    <div class="error block">
        <h2>Error</h2>
        <p>An error occured while saving.</p>
        <?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?>
            <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
        <?php }?>
        <?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?>
            <p>The database server returned the following message:</p>
            <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
        <?php }?>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['posted']->value){?>
    <div class="success block">
        Link saved.
    </div>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['AllowedFeat']->value)&&$_smarty_tpl->tpl_vars['AllowedFeat']->value!=1){?>
    <div class="error block">
        <p>Maximum number of featured links for this category exceeded!</p>
        <p>Please review link preferences.</p>
    </div>
<?php }?>

<div class="block">
    <form method="post" action="" id="edit_link_form" enctype="multipart/form-data">
        <table class="formPage">
            <?php if (isset($_smarty_tpl->tpl_vars['action']->value)&&($_smarty_tpl->tpl_vars['action']->value=='N'||$_smarty_tpl->tpl_vars['action']->value=='E')){?>
                <thead>
                    <tr>
                        <th colspan="2">
                            <?php if ($_smarty_tpl->tpl_vars['action']->value=='N'){?>
                                Create new link
                            <?php }elseif($_smarty_tpl->tpl_vars['action']->value=='E'){?>
                                Edit link
                            <?php }?>
                        </th>
                    </tr>
                </thead>
            <?php }?>

            <tbody>
                <tr>
                    <td class="label"><label for="LINK_TYPE">Link Type:</label></td>
                    <td class="smallDesc">
                        <select name="LINK_TYPE" id="LINK_TYPE">
                            <?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['link_types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['linktypeid']->value==$_smarty_tpl->tpl_vars['k']->value){?> selected="selected" <?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['NAME'];?>
</option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if ($_smarty_tpl->tpl_vars['submit_items']->value){?>
            <div id="add_items">
                <table class="formPage">
                    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['submit_items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
                        
                        <?php if ($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='TITLE'){?>
                            <tr>
                                <td class="label required"><label for="TITLE">Title:</label></td>
                                <td class="smallDesc">
                                    <input type="text" id="TITLE" name="TITLE" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
" maxlength="<?php echo @TITLE_MAX_LENGTH;?>
" class="text" />
                                    <?php if ($_smarty_tpl->tpl_vars['SEO_URL']->value!=''){?>
                                        <a class="admin_action" href="<?php if (!@ENABLE_REWRITE){?>
                                           <?php echo @DOC_ROOT;?>
/detail.php?id=<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>

                                        <?php }else{ ?>
                                        <?php echo @DOC_ROOT;?>
/<?php echo $_smarty_tpl->tpl_vars['SEO_URL']->value;?>
<?php }?>" title="Preview Link on the Front End" target="_blank">
                                        <img src="<?php echo @TEMPLATE_ROOT;?>
/images/outbox.png"/>
                                    </a>
                                <?php }?>
                            </td>
                        </tr>
                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='CATEGORY_ID'){?>
                            <tr>
                                <td class="label required"><label for="CATEGORY_ID">Category:</label></td>
                                <td class="smallDesc">
                                    
                                    <?php if ($_smarty_tpl->tpl_vars['data']->value['CATEGORY_ID']){?>
                                        <?php if (isset($_smarty_tpl->tpl_vars["categoryId"])) {$_smarty_tpl->tpl_vars["categoryId"] = clone $_smarty_tpl->tpl_vars["categoryId"];
$_smarty_tpl->tpl_vars["categoryId"]->value = $_smarty_tpl->tpl_vars['data']->value['CATEGORY_ID']; $_smarty_tpl->tpl_vars["categoryId"]->nocache = null; $_smarty_tpl->tpl_vars["categoryId"]->scope = 0;
} else $_smarty_tpl->tpl_vars["categoryId"] = new Smarty_variable($_smarty_tpl->tpl_vars['data']->value['CATEGORY_ID'], null, 0);?>
                                    <?php }else{ ?>
                                        <?php if (isset($_smarty_tpl->tpl_vars["categoryId"])) {$_smarty_tpl->tpl_vars["categoryId"] = clone $_smarty_tpl->tpl_vars["categoryId"];
$_smarty_tpl->tpl_vars["categoryId"]->value = $_GET['category']; $_smarty_tpl->tpl_vars["categoryId"]->nocache = null; $_smarty_tpl->tpl_vars["categoryId"]->scope = 0;
} else $_smarty_tpl->tpl_vars["categoryId"] = new Smarty_variable($_GET['category'], null, 0);?>
                                    <?php }?>
                                    <?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/admin_category_select.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('selected'=>$_smarty_tpl->tpl_vars['categoryId']->value,'selected_parent'=>$_smarty_tpl->tpl_vars['data']->value['PARENT_ID']), 0);?>

                                    <?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']!=''){?>
                                        <?php  $_smarty_tpl->tpl_vars['categ'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['categ']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['add_categs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['additional']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['categ']->key => $_smarty_tpl->tpl_vars['categ']->value){
$_smarty_tpl->tpl_vars['categ']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['additional']['index']++;
?>
                                            <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['additional']['index']<$_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']){?>
                                                <div>
                                                    <?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/admin_category_select.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('categ_id'=>$_smarty_tpl->tpl_vars['categ']->value['CATEGORY_ID'],'additional_categs'=>true), 0);?>
&nbsp;
                                                    <a href="#" onclick="removeCateg(this); return false;">remove</a>
                                                    <div style="float: none; clear: both; height: 8px;"></div>
                                                </div>
                                            <?php }?>
                                        <?php } ?>
                                    <?php }?>
                                    <div id="categ" style="display: none;">
                                        <br />
                                        <?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/admin_category_select.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('additional_categs'=>true), 0);?>
&nbsp;
                                    </div>
                                    <a href="" class="formDelCateg" id="fileDelCateg" onclick="removeCateg(this); return false;" style="display: none">remove</a>
                                    <div class="clear"></div>
                                    <br />
                                    <a onclick="plusCateg();return false;" href="" id="plusCategLink" class="formSmall" style="border-width: 0;">+ additional categ</a>
                                </td>
                            </tr>
                            <?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='OWNER_NAME'){?>
                                <tr>
                                    <td class="label"><label for="OWNER_NAME">Owner Name:</label></td>
                                    <td class="smallDesc">
                                        <input type="text" id="OWNER_NAME" name="OWNER_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['OWNER_NAME'], ENT_QUOTES, 'UTF-8', true));?>
" maxlength="<?php echo @USER_NAME_MAX_LENGTH;?>
" class="text" />
                                    </td>
                                </tr>
	                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='URL'&&$_smarty_tpl->tpl_vars['linktypeid']->value==5){?> 
							<tr>
								<td class="label"><label for="URL">Video Parse URL:</label></td>
								<td class="smallDesc">
									<input type="text" id="URL" name="URL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" />
									<img src="../templates/Core/DefaultAdmin/images/information.png" title="The URL you place here should be the link from the video page on popular video sites including YouTube, Vimeo, MetaCafe, Hulu, Dailymotion, Viddler, Qik, CollegeHumor, TheDailyShow, etc. After submitting you will have the video embedded and a thumb created if possible, or you can use the Image upload below to specify a substitute for the thumb."/>
									<br/>
									<script type="text/javascript">
										jQuery(document).ready(function($) {
											var checkboxLabel = "Autoplay Youtube Videos";
											

											var urlInput = $("#URL");
											var checkboxContainer = $(document.createElement("div")).appendTo(urlInput.parent());
											urlInput.change(urlInputChange);

											function determineAutoplay(url)
											{
												return url.match(/autoplay=1/i)!=null;
											}

											function urlInputChange()
											{
												var newValue = urlInput.val();

												//determine if youtube link
												if(newValue.match(/youtu\.?be/i))
												{
													var checkbox = $(document.createElement("input")).attr("type","checkbox")
															.change(checkboxChange)
															.appendTo(checkboxContainer);
													$(document.createElement("span")).html(" "+checkboxLabel).appendTo(checkboxContainer);
													checkbox.attr("checked", determineAutoplay(newValue));
												}
												else
												{
													checkboxContainer.empty();
												}
											}

											function checkboxChange()
											{
												var url = urlInput.val();
												var state = this.checked;
												if(state && url.match(/\?/))
												{
													url+="&autoplay=1";
												}
												else if(state)
												{
													url+="?autoplay=1";
												}
												else
												{
													url=url.replace(/&?autoplay=1/i, "");
												}
												urlInput.unbind("change", urlInputChange);
												urlInput.val(url);
												urlInput.bind("change", urlInputChange);
											}
											
										});
									</script>
								</td>
							</tr>
                            <?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='OWNER_EMAIL'){?>
                                    <tr>
                                        <td class="label"><label for="OWNER_EMAIL">Owner Email:</label></td>
                                        <td class="smallDesc">
                                            <input type="text" id="OWNER_EMAIL" name="OWNER_EMAIL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['OWNER_EMAIL'], ENT_QUOTES, 'UTF-8', true));?>
" maxlength="255" class="text" />
                                        </td>
                                    </tr>
                            <?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='META_KEYWORDS'){?>
                                        <?php if (@ENABLE_META_TAGS==1){?>
                                            <tr class="thead">
                                                <th colspan="2">META tags</th>
                                            </tr>
                                            <tr class="thead">
                                                <td colspan="2" class="info notice">Define custom META tags for detail pages. Leave blank to use default tags defined for your directory.</td>
                                            </tr>
                                            <tr>
                                                <td class="label"><label for="META_KEYWORDS">META Keywords:</label></td>
                                                <td class="smallDesc">
                                                    <input type="text" id="META_KEYWORDS" name="META_KEYWORDS" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value['META_KEYWORDS'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" />
                                                    <p class="msg notice info">Separate keywords by comma.</p>
                                                </td>
                                            </tr>
                                        <?php }?>
                            <?php }elseif($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='META_DESCRIPTION'){?>
                                            <?php if (@ENABLE_META_TAGS==1){?>
                                                <tr>
                                                    <td class="label"><label for="META_DESCRIPTION">META Description:</label></td>
                                                    <td class="smallDesc">
                                                        <textarea id="META_DESCRIPTION" name="META_DESCRIPTION" rows="3" cols="30" class="text" <?php echo smarty_function_formtool_count_chars(array('name'=>"META_DESCRIPTION",'limit'=>@META_DESCRIPTION_MAX_LENGTH,'alert'=>true),$_smarty_tpl);?>
><?php echo htmlspecialchars(trim($_smarty_tpl->tpl_vars['data']->value['META_DESCRIPTION']), ENT_QUOTES, 'UTF-8', true);?>
</textarea>
                                                        <p class="limitDesc">Limit: <input type="text" name="META_DESCRIPTION_limit" class="limit_field" readonly="readonly" value="<?php echo $_smarty_tpl->tpl_vars['MetaDescriptionLimit']->value;?>
" /></p>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                            
                            <?php }else{ ?>
                                                <tr>
                                                    <td class="label"><label for="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['NAME'];?>
:</label></td>
                                                    <td class="smallDesc">
                                                        <?php if ($_smarty_tpl->tpl_vars['item']->value['TYPE']=='STR'){?>
                                                            <input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']], ENT_QUOTES, 'UTF-8', true));?>
" size="40" class="text" />
                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='TAGS'){?>
                                                            <link rel="stylesheet" type="text/css" href="<?php echo @FRONT_DOC_ROOT;?>
javascripts/select2/select2.css" />
                                                            <script type="text/javascript" src="<?php echo @FRONT_DOC_ROOT;?>
javascripts/select2/select2.js" ></script>
                                                            <script type="text/javascript">
                                                                    jQuery(document).ready(function(){
                                                                    <?php if (isset($_smarty_tpl->tpl_vars["i"])) {$_smarty_tpl->tpl_vars["i"] = clone $_smarty_tpl->tpl_vars["i"];
$_smarty_tpl->tpl_vars["i"]->value = 0; $_smarty_tpl->tpl_vars["i"]->nocache = null; $_smarty_tpl->tpl_vars["i"]->scope = 0;
} else $_smarty_tpl->tpl_vars["i"] = new Smarty_variable(0, null, 0);?>
                                                                    <?php if (isset($_smarty_tpl->tpl_vars["tagsCount"])) {$_smarty_tpl->tpl_vars["tagsCount"] = clone $_smarty_tpl->tpl_vars["tagsCount"];
$_smarty_tpl->tpl_vars["tagsCount"]->value = count($_smarty_tpl->tpl_vars['allTags']->value); $_smarty_tpl->tpl_vars["tagsCount"]->nocache = null; $_smarty_tpl->tpl_vars["tagsCount"]->scope = 0;
} else $_smarty_tpl->tpl_vars["tagsCount"] = new Smarty_variable(count($_smarty_tpl->tpl_vars['allTags']->value), null, 0);?>
                                                                    jQuery("#<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
").select2(
                                                                        
                                                                            {
                                                                                tags: [
                                                                                <?php  $_smarty_tpl->tpl_vars["tag"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["tag"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['allTags']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["tag"]->key => $_smarty_tpl->tpl_vars["tag"]->value){
$_smarty_tpl->tpl_vars["tag"]->_loop = true;
?>
                                                                                    <?php if (isset($_smarty_tpl->tpl_vars["i"])) {$_smarty_tpl->tpl_vars["i"] = clone $_smarty_tpl->tpl_vars["i"];
$_smarty_tpl->tpl_vars["i"]->value = $_smarty_tpl->tpl_vars['i']->value+1; $_smarty_tpl->tpl_vars["i"]->nocache = null; $_smarty_tpl->tpl_vars["i"]->scope = 0;
} else $_smarty_tpl->tpl_vars["i"] = new Smarty_variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);?>
                                                                                    {id:<?php echo $_smarty_tpl->tpl_vars['tag']->value['ID'];?>
, text:'<?php echo $_smarty_tpl->tpl_vars['tag']->value['TITLE'];?>
'}<?php if ($_smarty_tpl->tpl_vars['i']->value<$_smarty_tpl->tpl_vars['tagsCount']->value){?>,<?php }?>
                                                                                <?php } ?>
                                                                                ]

                                                                            }
                                                                        
                                                                    );
                                                                });
                                                            </script>
                                                            <input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']], ENT_QUOTES, 'UTF-8', true));?>
" size="40" class="text" />
                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='MULTICHECKBOX'){?>
                                                            <?php  $_smarty_tpl->tpl_vars['opt'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['opt']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['item']->value['OPTIONS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['opt']->key => $_smarty_tpl->tpl_vars['opt']->value){
$_smarty_tpl->tpl_vars['opt']->_loop = true;
?>
                                                                <input name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
[]" value="<?php echo $_smarty_tpl->tpl_vars['opt']->value;?>
" type="checkbox" <?php if (in_array($_smarty_tpl->tpl_vars['opt']->value,explode(',',$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]))){?>checked="checked" <?php }?>> <?php echo $_smarty_tpl->tpl_vars['opt']->value;?>
 <br />
                                                            <?php } ?>
                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='TXT'){?>
                                                            <?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/rte.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('NAME'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'VALUE'=>trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']], ENT_QUOTES, 'UTF-8', true))), 0);?>

	                                                    <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='ADDRESS'){?>
                                                        <?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/address_field.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('NAME'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'VALUE'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]), 0);?>

                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='BOOL'){?>
                                                            <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['yes_no']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']],'name'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'id'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']),$_smarty_tpl);?>

                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='DROPDOWN'){?>
                                                            <?php echo smarty_function_html_options3(array('options'=>$_smarty_tpl->tpl_vars['item']->value['OPTIONS'],'selected'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']],'name'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'id'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']),$_smarty_tpl);?>

                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='CAT'){?>
                                                            <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['categs']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']],'name'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'id'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']),$_smarty_tpl);?>

                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='FILE'){?>
                                                            <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text"/><br />
                                                            Allowed files: .pdf, .xls, .xlsx, .doc, .docx, .zip, .rar, .txt, .rtf, .csv. Files of other types will not be uploaded at all.
                                                            <br/>
                                                            <?php if ($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]!=''){?>
                                                                Current: <br/><a href="../uploads/<?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
</a> <a href="<?php echo @DOC_ROOT;?>
/rm_si.php?id=<?php echo $_smarty_tpl->tpl_vars['item']->value['ID'];?>
&lid=<?php echo $_smarty_tpl->tpl_vars['data']->value['ID'];?>
">Remove</a>
                                                            <?php }?>
                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='IMAGE'){?>
                                                            Upload from disc<br />
                                                            <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text"/><br/>
                                                            Allowed files: .gif, .png, .jpg, .jpeg. Files of other types will not be uploaded at all.
                                                            <br />
                                                            <?php if ($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]!=''){?>
                                                                Current: <br/><img src="../uploads/thumb/<?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
"/> <a href="<?php echo @DOC_ROOT;?>
/rm_si.php?id=<?php echo $_smarty_tpl->tpl_vars['item']->value['ID'];?>
&lid=<?php echo $_smarty_tpl->tpl_vars['data']->value['ID'];?>
">Remove</a><br />
                                                            <?php }?>
                                                            <strong>OR</strong><br />
                                                            Download by URL<br />
                                                            <input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
_SUBMIT" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text" />


                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='VIDEO'){?>
                                                            <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text"/><br/>
                                                            Allowed files: .avi, .mpg, .wmv, .mov Files of other types will not be uploaded at all.
                                                            <br />
                                                            <?php if ($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]!=''){?>
                                                                Current: <br/><img src="../uploads/thumb/<?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
.jpg"/> <a href="<?php echo @DOC_ROOT;?>
/rm_si.php?id=<?php echo $_smarty_tpl->tpl_vars['item']->value['ID'];?>
&lid=<?php echo $_smarty_tpl->tpl_vars['data']->value['ID'];?>
">Remove</a>
                                                            <?php }?>
                                                        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='IMAGEGROUP'){?>


                                                            <?php if ($_smarty_tpl->tpl_vars['group_image_details']->value){?>
                                                                <table width="100%">
                                                                    <tr><td>
                                                                            <?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['group_image_details']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
?>
                                                                                <div class="link-detail-image" id="<?php echo $_smarty_tpl->tpl_vars['image']->value['IMAGEID'];?>
">
                                                                                    <div><img src="../uploads/thumb/<?php echo $_smarty_tpl->tpl_vars['image']->value['IMAGE'];?>
"/></div>
                                                                                    <div style="clear:both;width:100%;text-align:center;margin-top:5px;"><a onclick="javascript:deleteImage(<?php echo $_smarty_tpl->tpl_vars['image']->value['IMAGEID'];?>
);">Delete</a></div>
                                                                                </div>
                                                                            <?php } ?>

                                                                            <div class="link-detail-image" id="imagelistcopyto" style="display: none;">
                                                                                <div class="imageinto"></div>
                                                                                <div style="clear:both;width:100%;text-align:center;margin-top:5px;" class="deletelinkcontainer"><a href="" class="deletelink">Delete</a></div>
                                                                            </div>

                                                                            <div id="imageuploadajaxcontainer">
                                                                            </div>
                                                                        </td></tr>
                                                                </table>
                                                                
                                                                    <script>
                                                                    curr_count = 0;
                                                                    function deleteImage(image_id){

                                                                            if(confirm('Are you sure you want to delete this image?')){
                                                                                    jQuery.ajax({
                                                                                    url:"<?php echo @DOC_ROOT;?>
/admin-delete-images.php",
                                                                                    type:"get",
                                                                                    dataType:"json",
                                                                                    success:imgDeleteSuccess,
                                                                                    error:imgDeleteError,
                                                                                    data:{
                                                                                            "image_id":image_id

                                                                                            }
                                                                                    });
                                                                            }
                                                                    }
                                                                    function imgDeleteSuccess(Obj){

                                                                            //remove the image's parent div
                                                                            var id = '#'+Obj.image_id;
                                                                            jQuery(id).empty().remove();
                                                                            curr_count = curr_count-1;
                                                                            jQuery(".qq-upload-button").show();
                                                                    }
                                                                    function imgDeleteError(Obj, message){
                                                                            alert(message);
                                                                    }
                                                                    </script>
                                                                
                                                            <?php }?>

                                                            <?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo smarty_php_tag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

						                                        // assign a uniqid to this field so we can find the files on submit
						                                        $this->assign('imagegroupid',uniqid());
                                                            <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_php_tag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>


                                                            <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['imagegroupid']->value;?>
"/>
                                                            
                                                                <div id="file-uploader-admin">
                                                                    <noscript>
                                                                    <p>Please enable JavaScript to use file uploader.</p>
                                                                    <!-- or put a simple form for upload here -->
                                                                    </noscript>
                                                                </div>
                                                                <input type="hidden" id="curr_count" value="1" />
                                                                <script src="../javascripts/fileuploader.js" type="text/javascript"></script>
                                                                <script>
                                                                   curr_count = 0;
                                                                
                                                                    <?php if ($_smarty_tpl->tpl_vars['ctn_img']->value){?>
                                                                       ctn_img = parseInt('<?php echo $_smarty_tpl->tpl_vars['ctn_img']->value;?>
');
												
                                                                <?php }else{ ?>
                                                                       ctn_img = 0;
                                                                <?php }?>

                                                                
                                                                   function createUploader(){
                                                                       var uploader = new qq.FileUploader({
                                                                           element: document.getElementById('file-uploader-admin'),
                                                                           action: '../uploadimg.php',
                                                                           onSubmit: function(id, fileName, responseJSON){
                                                                           curr_count = curr_count+1;
                                                                           if(curr_count+ctn_img>=parseInt("<?php echo $_smarty_tpl->tpl_vars['link_types']->value[$_smarty_tpl->tpl_vars['linktypeid']->value]['COUNT_IMAGES'];?>
"))
                                                                            jQuery(".qq-upload-button").hide();
                                                                                  },
                                                                           params: {
                                                                
                                                                                    imagegroupid: "<?php echo $_smarty_tpl->tpl_vars['imagegroupid']->value;?>
",
                                                       imagegroupidnbr: "<?php echo $_smarty_tpl->tpl_vars['data']->value['IMAGEGROUP'];?>
",
                                                                
													       
                                                                           }
                                                                       });
                                                                       if(curr_count+ctn_img>=parseInt("<?php echo $_smarty_tpl->tpl_vars['link_types']->value[$_smarty_tpl->tpl_vars['linktypeid']->value]['COUNT_IMAGES'];?>
") || isNaN(parseInt("<?php echo $_smarty_tpl->tpl_vars['link_types']->value[$_smarty_tpl->tpl_vars['linktypeid']->value]['COUNT_IMAGES'];?>
")) || parseInt("<?php echo $_smarty_tpl->tpl_vars['link_types']->value[$_smarty_tpl->tpl_vars['linktypeid']->value]['COUNT_IMAGES'];?>
")=='0')
                                                                            {
                                                                              jQuery(".qq-upload-button").hide();
                                                                            }
                                                                   }
                                                                    window.onload = createUploader;
												
                                                                </script>

                                                            
                                                        <?php }?>
                                                        <?php if ($_smarty_tpl->tpl_vars['item']->value['DESCRIPTION']){?>
                                                            <p class="msg notice info"><?php echo $_smarty_tpl->tpl_vars['item']->value['DESCRIPTION'];?>
</p>
                                                        <?php }?>
                                                    </td>
                                                </tr>
                                                <?php }?>
                                                    <?php } ?>    
                                                    </table>
                                                </div>
                                                <?php }?>

                                                    <?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['DEEP_LINKS']>0){?>
                                                        <table class="formPage">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label required"><label>Deep Links:</label></td>
                                                                    <td class="smallDesc">
                                                                        <table id="additional_links" border="0" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td align="center">Titles</td>
                                                                                <td align="center">URLs</td>
                                                                            </tr>

                                                                            <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
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
?>
                                                                                <tr>
                                                                                    <td><input type="text" name="ADD_LINK_TITLE[]" value="<?php echo $_smarty_tpl->tpl_vars['add_links']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['TITLE'];?>
" class="text" style="width: 130px;" /></td>
                                                                                    <td><input type="text" name="ADD_LINK_URL[]"   value="<?php echo $_smarty_tpl->tpl_vars['add_links']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['URL'];?>
"   class="text" style="width: 130px;" /></td>
                                                                                </tr>
                                                                            <?php endfor; endif; ?>
                                                                        </table>

                                                                        <div class="clear"></div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    <?php }?>


                                                    <table class="formPage">
                                                        <tbody>
                                                            <?php if (@REQUIRE_REGISTERED_USER){?>
                                                                <tr>
                                                                    <td class="label"><label for="OWNER_ID">Assign Owner:</label></td>
                                                                    <td class="smallDesc">
                                                                        <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['ActiveUsersList']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['OWNER_ID'],'name'=>"OWNER_ID",'id'=>"OWNER_ID"),$_smarty_tpl);?>

                                                                        <p class="msg notice info">Use with care!! Selected user details will overwrite link owner name, email address and id!!</p>
                                                                    </td>
                                                                </tr>
                                                            <?php }else{ ?>
                                                                <?php if ($_smarty_tpl->tpl_vars['data']->value['OWNER_ID']!=''){?>
                                                                <input type="hidden" name="OWNER_ID" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['OWNER_ID'];?>
" />
                                                            <?php }?>
                                                        <?php }?>

                                                        <tr>
                                                            <td class="label required"><label for="OWNER_EMAIL_CONFIRMED">Owner Email Confirmed:</label></td>
                                                            <td class="smallDesc">
                                                                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['econfirm']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['OWNER_EMAIL_CONFIRMED'],'name'=>"OWNER_EMAIL_CONFIRMED",'id'=>"OWNER_EMAIL_CONFIRMED"),$_smarty_tpl);?>

                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="label required"><label for="semail">Send Email On Edit:</label></td>
                                                            <td class="smallDesc">
                                                                <input type="checkbox" id="semail" name="semail" value="1" <?php if ($_smarty_tpl->tpl_vars['semail']->value){?>checked="checked"<?php }?> />
                                                                <p class="msg notice info">Check this to send an email to the link owner to let them know you have edited the link.</p>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="label required"><label for="STATUS">Status:</label></td>
                                                            <td class="smallDesc">
                                                                <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['stats']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value['STATUS'],'name'=>"STATUS",'id'=>"STATUS"),$_smarty_tpl);?>

                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="label"><label for="EXPIRY_DATE">Link Expires:</label></td>
                                                            <td class="smallDesc">
                                                                <input type="text" id="EXPIRY_DATE" name="EXPIRY_DATE" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['EXPIRY_DATE'];?>
" maxlength="40" class="text" />
                                                            </td>
                                                        </tr>

                                                        <?php if (@ENABLE_PAGERANK){?>
                                                            <tr>
                                                                <td class="label"><label for="PAGERANK">Pagerank:</label></td>
                                                                <td class="smallDesc">
                                                                    <input type="text" id="PAGERANK" name="PAGERANK" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['PAGERANK'];?>
" maxlength="2" class="text" />
                                                                    <p class="msg notice info">Leave the field blank for automatic Google Pagerank calculation or give value of "-1" for no Pagerank.</p>
                                                                </td>
                                                            </tr>
                                                        <?php }?>

                                                        <tr>
                                                            <td class="label"><label for="HITS">Hits:</label></td>
                                                            <td class="smallDesc">
                                                                <input type="text" id="HITS" name="HITS" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['HITS'];?>
" maxlength="15" class="text" />
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <table class="formPage">
                                                        <tfoot>
                                                            <tr>
                                                        <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['linktypeid']->value;?>
" ID="LINK_TYPE_H" name="LINK_TYPE_H"/>
                                                        <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['ID'];?>
" ID="ID" name="id"/>
                                                        <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['ID'];?>
" ID="ID" name="ID"/>
                                                        <td><input type="reset" id="reset-link-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td>
                                                        <td><input type="submit" id="send-link-submit" name="save" value="Save" alt="Save form" title="Save link" class="button" /></td>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                    <input type="hidden" name="formSubmitted" value="1" />
                                                    <input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" />
                                                </form>
                                            </div>

                                            
                                                <script type="text/javascript">
                                                jQuery(function($) {
                                                        $(document).ready(function(){
                                                                var allowed_fields 	= new Array;
		
                                                
                                                    <?php  $_smarty_tpl->tpl_vars['link_type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link_type']->_loop = false;
 $_smarty_tpl->tpl_vars['link_type_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['link_types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link_type']->key => $_smarty_tpl->tpl_vars['link_type']->value){
$_smarty_tpl->tpl_vars['link_type']->_loop = true;
 $_smarty_tpl->tpl_vars['link_type_id']->value = $_smarty_tpl->tpl_vars['link_type']->key;
?>
                                                                    allowed_fields[<?php echo $_smarty_tpl->tpl_vars['link_type_id']->value;?>
] = [
                                                    <?php  $_smarty_tpl->tpl_vars['field_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['link_type']->value['FIELDS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['field_name']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['field_name']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['field_name']->key => $_smarty_tpl->tpl_vars['field_name']->value){
$_smarty_tpl->tpl_vars['field_name']->_loop = true;
 $_smarty_tpl->tpl_vars['field_name']->iteration++;
 $_smarty_tpl->tpl_vars['field_name']->last = $_smarty_tpl->tpl_vars['field_name']->iteration === $_smarty_tpl->tpl_vars['field_name']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['fields']['last'] = $_smarty_tpl->tpl_vars['field_name']->last;
?>
                                                                                        "<?php echo $_smarty_tpl->tpl_vars['field_name']->value;?>
"<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['fields']['last']){?>,<?php }?>
                                                    <?php } ?>
                                                                                    ];
			
                                                <?php } ?>
                                                

                                                
                                                
                                                
		
                                                                $("#LINK_TYPE").change(function(){
                                                                        //update_form(); // albert: seems useless, we are making a refresh when the user changes the link type
                                                                        var lt = getUrlVars()["LINK_TYPE"];
                                                                        if (lt) {
                                                                                var url = location.href.replace("LINK_TYPE="+lt, "LINK_TYPE="+$("#LINK_TYPE").val());
                                                                                location.href= url;
                                                                        } else {
                                                                                var url = location.href+"&LINK_TYPE="+$("#LINK_TYPE").val();
                                                                                location.href= url;
                                                                        }
                                                                });
		
                                                                $("#proccess_add_items").click(function(){
                                                                        $("#add_items").slideToggle();
                                                                });
		
                                                                $("#submitForm").submit(function() {
                                                                        $('#categ').remove();
                                                                        return true;
                                                                });
                                                        });
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

                                                Array.prototype.in_array = function(p_val) {
                                                        for(var i = 0, l = this.length; i < l; i++) {
                                                                if(this[i] == p_val) {
                                                                        return true;
                                                                }
                                                        }
                                                        return false;
                                                }

                                                function plusFile()
                                                {
                                                        var div = $('file');
                                                        var newDiv = div.cloneNode(true);
                                                        newDiv.id = ''
                                                        //newDiv.style.display = 'none';

                                                        //newDiv.find('*.smallText').val("");

                                                        var delLink = $('fileDelLink');
                                                        var a = delLink.cloneNode(true);
                                                        a.id = '';
                                                        a.style.display = "inline";
                                                        newDiv.appendChild(a);
                                                        delLink.parentNode.insertBefore(newDiv, delLink);

                                                }

                                                function removeFile(elem)
                                                {
                                                        elem.parentNode.remove(elem.previousSibling);
                                                }

                                                function removeSize(elem)
                                                {
                                                        elem.parentNode.parentNode.removeChild(elem.parentNode);
                                                }

                                                var maxCats = <?php if ($_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES']==''){?>1<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['link_type_details']->value['MULTIPLE_CATEGORIES'];?>
<?php }?>;
                                                var numCats = 1 + <?php echo count($_smarty_tpl->tpl_vars['add_categs']->value);?>
;
                                                if (numCats >= maxCats) {
                                                        $('plusCategLink').style.display = 'none'; 
                                                }

                                                function plusCateg()
                                                {
                                                        var div = $('categ');
                                                        var newDiv = div.cloneNode(true);
                                                        newDiv.id = ''
                                                        newDiv.style.display = 'block';

                                                        //newDiv.find('*.smallText').val("");

                                                        var delLink = $('fileDelCateg');
                                                        var a = delLink.cloneNode(true);
                                                        a.id = '';
                                                        a.style.display = "inline";
                                                        newDiv.appendChild(a);
                                                        delLink.parentNode.insertBefore(newDiv, delLink);

                                                        numCats++;
                                                        if (numCats >= maxCats) {
                                                                $('plusCategLink').style.display = 'none'; 
                                                        } 
                                                }

                                                function removeCateg(elem)
                                                {
                                                        elem.parentNode.remove(elem.previousSibling);
	
                                                        numCats--;
                                                        if (numCats < maxCats) {
                                                                $('plusCategLink').style.display = 'block'; 
                                                        } 
                                                }

                                                function removeSize(elem)
                                                {
                                                        elem.parentNode.parentNode.removeChild(elem.parentNode);
                                                }

                                                </script>
                                            

<?php }} ?>