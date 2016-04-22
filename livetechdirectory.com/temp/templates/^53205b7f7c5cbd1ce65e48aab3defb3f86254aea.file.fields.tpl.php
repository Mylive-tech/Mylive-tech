<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:48:08
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/fields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1972936422535abbf86c8332-66959310%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '53205b7f7c5cbd1ce65e48aab3defb3f86254aea' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/fields.tpl',
      1 => 1386917018,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1972936422535abbf86c8332-66959310',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'item' => 0,
    'valid' => 0,
    'FIELD_NAME' => 0,
    'error_list' => 0,
    'data' => 0,
    'yes_no' => 0,
    'opt' => 0,
    'group_image_details' => 0,
    'image' => 0,
    'imagegroupid' => 0,
    'linktypeid' => 0,
    'link_types' => 0,
    'video' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535abbf8955870_96209379',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abbf8955870_96209379')) {function content_535abbf8955870_96209379($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
if (!is_callable('smarty_function_html_options3')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options3.php';
?><?php if ($_smarty_tpl->tpl_vars['item']->value['TYPE']!='CAT'){?>
<div class="phpld-columnar phpld-equalize">
    <div class="phpld-label float-left">

        <?php if ($_smarty_tpl->tpl_vars['valid']->value['rules'][$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]['required']==true){?>
            <span class="phpld-required">*</span>
        <?php }?>
        <?php echo $_smarty_tpl->tpl_vars['item']->value['NAME'];?>
:
    </div>
    <div class="field float-left">
        <?php if (isset($_smarty_tpl->tpl_vars["FIELD_NAME"])) {$_smarty_tpl->tpl_vars["FIELD_NAME"] = clone $_smarty_tpl->tpl_vars["FIELD_NAME"];
$_smarty_tpl->tpl_vars["FIELD_NAME"]->value = $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']; $_smarty_tpl->tpl_vars["FIELD_NAME"]->nocache = null; $_smarty_tpl->tpl_vars["FIELD_NAME"]->scope = 0;
} else $_smarty_tpl->tpl_vars["FIELD_NAME"] = new Smarty_variable($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'], null, 0);?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value['TYPE']=='STR'){?>
            <div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>">
                <input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']], ENT_QUOTES, 'UTF-8', true));?>
" size="40" class="text" />
            </div>
        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='ADDRESS'){?>
			<div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>">
			<?php echo $_smarty_tpl->getSubTemplate ("views/_shared/address_field.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('NAME'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'VALUE'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]), 0);?>

			</div>
        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='TXT'){?>
            <div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>">
                <?php echo $_smarty_tpl->getSubTemplate ("views/_shared/rte.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('NAME'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'VALUE'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]), 0);?>

            </div>
        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='BOOL'){?>
            <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['yes_no']->value,'selected'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']],'name'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'id'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']),$_smarty_tpl);?>

        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='DROPDOWN'){?>
            <?php echo smarty_function_html_options3(array('options'=>$_smarty_tpl->tpl_vars['item']->value['OPTIONS'],'selected'=>$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']],'name'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'],'id'=>$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']),$_smarty_tpl);?>

        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='CAT'){?>
            
        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='FILE'){?>
            <div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>">
                <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text"/><br />
                Allowed files: .pdf, .xls, .xlsx, .doc, .docx, .zip, .rar, .txt, .rtf, .csv. Files of other types will not be uploaded at all.
                <br/>
                <?php if ($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]!=''){?>
                    Current: <br/><a href="<?php echo @SITE_URL;?>
uploads/<?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
</a>
                    &nbsp;<input type="checkbox" name="MARK_REMOVE_<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="1"/>&nbsp;Mark for removal
                <?php }?>
            </div>
	    <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='MULTICHECKBOX'){?>                                                                             
                     <div class="smallDesc" >           <div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>">
                                 <?php  $_smarty_tpl->tpl_vars['opt'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['opt']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['item']->value['OPTIONS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['opt']->key => $_smarty_tpl->tpl_vars['opt']->value){
$_smarty_tpl->tpl_vars['opt']->_loop = true;
?>
                                              <div style='float:left;padding:5px;'>  <input name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
[]" value="<?php echo $_smarty_tpl->tpl_vars['opt']->value;?>
" type="checkbox" <?php if (in_array($_smarty_tpl->tpl_vars['opt']->value,explode(',',$_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]))){?>checked="checked" <?php }?>> <?php echo $_smarty_tpl->tpl_vars['opt']->value;?>
 </div>
                                                           &nbsp;&nbsp;&nbsp; <?php } ?>
                                </div></div>
                         
        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='VIDEO'){?>
            <div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>">
                <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text"/><br/>
                Allowed files: .avi, .mpg, .wmv, .mov Files of other types will not be uploaded at all.
                <br />
                <?php if ($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]!=''){?>
                    Current: <br/><img src="<?php echo @SITE_URL;?>
uploads/thumb/<?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
.jpg"/> <a href="<?php echo @DOC_ROOT;?>
/rm_si.php?id=<?php echo $_smarty_tpl->tpl_vars['item']->value['ID'];?>
&lid=<?php echo $_smarty_tpl->tpl_vars['data']->value['ID'];?>
">Remove</a>
                <?php }?>
            </div>
        <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='IMAGE'){?>
            <div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?>">
                <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text"/><br/>
		 <strong>OR</strong><br />
                   Download by Image Url. Paste the link to the image.<br />
                 <input type="text" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
_SUBMIT" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" class="text" /><br />
                Allowed files: .gif, .png, .jpg, .jpeg. Files of other types will not be uploaded at all.
                <br />
                <?php if ($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]!=''){?>
                    Current: <br/>
                    <a href="<?php echo @SITE_URL;?>
uploads/<?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
?detail=1&TB_iframe=true&height=400&width=400" onclick="return showThickbox(this);" class="thickbox">
                        <img src="<?php echo @SITE_URL;?>
uploads/thumb/<?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
"/></a>
                    &nbsp;<input type="checkbox" name="MARK_REMOVE_<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="1"/>&nbsp;Mark for removal
                <?php }?>
            </div>
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
                                                                                    <div style="clear:both;width:100%;text-align:center;margin-top:5px;"></div>
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
                                                                
                                                            <?php }?>

            <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['item']->value['FIELD_NAME'];?>
" value="<?php if (!empty($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']])){?><?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['imagegroupid']->value;?>
<?php }?>"/>
            
                <div id="file-uploader">
                    <noscript>
                    <p>Please enable JavaScript to use file uploader.</p>
                    <!-- or put a simple form for upload here -->
                    </noscript>
                </div>
                <script src="javascripts/fileuploader.js" type="text/javascript"></script>
                <script type="text/javascript">
		    
		    <?php if (!empty($_smarty_tpl->tpl_vars['group_image_details']->value)){?>
			curr_count = <?php echo count($_smarty_tpl->tpl_vars['group_image_details']->value);?>
;
		    <?php }else{ ?>
		     curr_count = 0;
		     <?php }?>
		     
		     
                
                   function createUploader(){
                       var uploader = new qq.FileUploader({
                           element: document.getElementById('file-uploader'),
                           action: 'uploadimg.php',
                           onSubmit: function(id, fileName, responseJSON){
                                                                           curr_count = curr_count+1;
                                                                           if(curr_count>=parseInt("<?php echo $_smarty_tpl->tpl_vars['link_types']->value[$_smarty_tpl->tpl_vars['linktypeid']->value]['COUNT_IMAGES'];?>
"))
                                                                            jQuery(".qq-upload-button").hide();
                                                                                  },
                           params: {
                                imagegroupid: "<?php if (!empty($_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']])){?><?php echo $_smarty_tpl->tpl_vars['data']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['imagegroupid']->value;?>
<?php }?>"
                           }
                       });
		       if(curr_count>=parseInt("<?php echo $_smarty_tpl->tpl_vars['link_types']->value[$_smarty_tpl->tpl_vars['linktypeid']->value]['COUNT_IMAGES'];?>
"))
			    jQuery(".qq-upload-button").hide();
                   }
                   window.onload = createUploader;
                </script>

            
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value['DESCRIPTION']||$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='URL'){?>
            <div class="phpld-fbox-text <?php if ($_smarty_tpl->tpl_vars['error_list']->value[$_smarty_tpl->tpl_vars['FIELD_NAME']->value]){?>phpld-error<?php }?> small"><?php echo $_smarty_tpl->tpl_vars['item']->value['DESCRIPTION'];?>

                <?php if ($_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']=='URL'&&$_smarty_tpl->tpl_vars['video']->value){?>
                    Enter links from sites www.youtube.com, www.flickr.com, www.vimeo.com, www.viddler.com, www.dailymotion.com, www.hulu.com, www.qik.com, www.revision3.com, www.scribd.com, www.wordpress.tv, www.funnyordie.com, www.thedailyshow.com, www.collegehumor.com
                <?php }?>
            </div>
        <?php }?>
    </div>
</div>
<?php }?><?php }} ?>