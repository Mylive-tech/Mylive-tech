{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="edit_link_form" validators=$validators}

{* Error and confirmation messages *}
{include file="messages.tpl"}


{if (isset($error) and $error gt 0) or !empty($sql_error)}
    <div class="error block">
        <h2>{l}Error{/l}</h2>
        <p>{l}An error occured while saving.{/l}</p>
        {if !empty($errorMsg)}
            <p>{$errorMsg|escape}</p>
        {/if}
        {if !empty($sql_error)}
            <p>{l}The database server returned the following message:{/l}</p>
            <p>{$sql_error|escape}</p>
        {/if}
    </div>
{/if}

{if $posted}
    <div class="success block">
        {l}Link saved.{/l}
    </div>
{/if}

{if isset($AllowedFeat) and $AllowedFeat ne 1}
    <div class="error block">
        <p>{l}Maximum number of featured links for this category exceeded!{/l}</p>
        <p>{l}Please review link preferences.{/l}</p>
    </div>
{/if}

<div class="block">
    <form method="post" action="" id="edit_link_form" enctype="multipart/form-data">
        <table class="formPage">
            {if isset($action) and ($action eq 'N' or $action eq 'E')}
                <thead>
                    <tr>
                        <th colspan="2">
                            {if $action eq 'N'}
                                {l}Create new link{/l}
                            {elseif $action eq 'E'}
                                {l}Edit link{/l}
                            {/if}
                        </th>
                    </tr>
                </thead>
            {/if}

            <tbody>
                <tr>
                    <td class="label"><label for="LINK_TYPE">{l}Link Type{/l}:</label></td>
                    <td class="smallDesc">
                        <select name="LINK_TYPE" id="LINK_TYPE">
                            {foreach from=$link_types key=k item=v}
                                <option value="{$k}" {if $linktypeid eq $k} selected="selected" {/if}>{$v.NAME}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        {if $submit_items}
            <div id="add_items">
                <table class="formPage">
                    {foreach from=$submit_items item=item name=submit_items}
                        {* special submit items *}
                        {if $item.FIELD_NAME == 'TITLE'}
                            <tr>
                                <td class="label required"><label for="TITLE">{l}Title{/l}:</label></td>
                                <td class="smallDesc">
                                    <input type="text" id="TITLE" name="TITLE" value="{$data.TITLE|escape|trim}" maxlength="{$smarty.const.TITLE_MAX_LENGTH}" class="text" />
                                    {if $SEO_URL != ''}
                                        <a class="admin_action" href="{if !$smarty.const.ENABLE_REWRITE}
                                           {$smarty.const.DOC_ROOT}/detail.php?id={$ID}
                                        {else}
                                        {$smarty.const.DOC_ROOT}/{$SEO_URL}{/if}" title="{l}Preview Link on the Front End{/l}" target="_blank">
                                        <img src="{$smarty.const.TEMPLATE_ROOT}/images/outbox.png"/>
                                    </a>
                                {/if}
                            </td>
                        </tr>
                        {elseif $item.FIELD_NAME == 'CATEGORY_ID'}
                            <tr>
                                <td class="label required"><label for="CATEGORY_ID">{l}Category{/l}:</label></td>
                                <td class="smallDesc">
                                    {* Load category selection *}
                                    {if $data.CATEGORY_ID}
                                        {assign var="categoryId" value=$data.CATEGORY_ID}
                                    {else}
                                        {assign var="categoryId" value=$smarty.get.category}
                                    {/if}
                                    {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" selected=$categoryId  selected_parent=$data.PARENT_ID}
                                    {if $link_type_details.MULTIPLE_CATEGORIES != ''}
                                        {foreach from=$add_categs item=categ name=additional}
                                            {if $smarty.foreach.additional.index < $link_type_details.MULTIPLE_CATEGORIES}
                                                <div>
                                                    {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" categ_id=$categ.CATEGORY_ID additional_categs=true}&nbsp;
                                                    <a href="#" onclick="removeCateg(this); return false;">{l}remove{/l}</a>
                                                    <div style="float: none; clear: both; height: 8px;"></div>
                                                </div>
                                            {/if}
                                        {/foreach}
                                    {/if}
                                    <div id="categ" style="display: none;">
                                        <br />
                                        {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" additional_categs=true}&nbsp;
                                    </div>
                                    <a href="" class="formDelCateg" id="fileDelCateg" onclick="removeCateg(this); return false;" style="display: none">{l}remove{/l}</a>
                                    <div class="clear"></div>
                                    <br />
                                    <a onclick="plusCateg();return false;" href="" id="plusCategLink" class="formSmall" style="border-width: 0;">+ {l}additional categ{/l}</a>
                                </td>
                            </tr>
                            {elseif $item.FIELD_NAME == 'OWNER_NAME'}
                                <tr>
                                    <td class="label"><label for="OWNER_NAME">{l}Owner Name{/l}:</label></td>
                                    <td class="smallDesc">
                                        <input type="text" id="OWNER_NAME" name="OWNER_NAME" value="{$data.OWNER_NAME|escape|trim}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
                                    </td>
                                </tr>
	                        {elseif $item.FIELD_NAME == 'URL' and $linktypeid eq 5} {*video*}
							<tr>
								<td class="label"><label for="URL">{l}Video Parse URL{/l}:</label></td>
								<td class="smallDesc">
									<input type="text" id="URL" name="URL" value="{$data.URL|escape|trim}" class="text" />
									<img src="../templates/Core/DefaultAdmin/images/information.png" title="The URL you place here should be the link from the video page on popular video sites including YouTube, Vimeo, MetaCafe, Hulu, Dailymotion, Viddler, Qik, CollegeHumor, TheDailyShow, etc. After submitting you will have the video embedded and a thumb created if possible, or you can use the Image upload below to specify a substitute for the thumb."/>
									<br/>
									<script type="text/javascript">
										jQuery(document).ready(function($) {ldelim}
											var checkboxLabel = "{l}Autoplay Youtube Videos{/l}";
											{literal}

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
											{/literal}
										{rdelim});
									</script>
								</td>
							</tr>
                            {elseif $item.FIELD_NAME == 'OWNER_EMAIL'}
                                    <tr>
                                        <td class="label"><label for="OWNER_EMAIL">{l}Owner Email{/l}:</label></td>
                                        <td class="smallDesc">
                                            <input type="text" id="OWNER_EMAIL" name="OWNER_EMAIL" value="{$data.OWNER_EMAIL|escape|trim}" maxlength="255" class="text" />
                                        </td>
                                    </tr>
                            {elseif $item.FIELD_NAME == 'META_KEYWORDS'}
                                        {if $smarty.const.ENABLE_META_TAGS eq 1}
                                            <tr class="thead">
                                                <th colspan="2">{l}META tags{/l}</th>
                                            </tr>
                                            <tr class="thead">
                                                <td colspan="2" class="info notice">{l}Define custom META tags for detail pages. Leave blank to use default tags defined for your directory.{/l}</td>
                                            </tr>
                                            <tr>
                                                <td class="label"><label for="META_KEYWORDS">{l}META Keywords{/l}:</label></td>
                                                <td class="smallDesc">
                                                    <input type="text" id="META_KEYWORDS" name="META_KEYWORDS" value="{$data.META_KEYWORDS|escape|trim}" class="text" />
                                                    <p class="msg notice info">{l}Separate keywords by comma.{/l}</p>
                                                </td>
                                            </tr>
                                        {/if}
                            {elseif $item.FIELD_NAME == 'META_DESCRIPTION'}
                                            {if $smarty.const.ENABLE_META_TAGS eq 1}
                                                <tr>
                                                    <td class="label"><label for="META_DESCRIPTION">{l}META Description{/l}:</label></td>
                                                    <td class="smallDesc">
                                                        <textarea id="META_DESCRIPTION" name="META_DESCRIPTION" rows="3" cols="30" class="text" {formtool_count_chars name="META_DESCRIPTION" limit=$smarty.const.META_DESCRIPTION_MAX_LENGTH alert=true}>{$data.META_DESCRIPTION|trim|escape}</textarea>
                                                        <p class="limitDesc">{l}Limit{/l}: <input type="text" name="META_DESCRIPTION_limit" class="limit_field" readonly="readonly" value="{$MetaDescriptionLimit}" /></p>
                                                    </td>
                                                </tr>
                                            {/if}
                                            {* /special submit items *}
                            {else}
                                                <tr>
                                                    <td class="label"><label for="{$item.FIELD_NAME}">{l}{$item.NAME}{/l}:</label></td>
                                                    <td class="smallDesc">
                                                        {if $item.TYPE eq 'STR'}
                                                            <input type="text" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$data[$item.FIELD_NAME]|escape|trim}" size="40" class="text" />
                                                        {elseif $item.TYPE eq 'TAGS'}
                                                            <link rel="stylesheet" type="text/css" href="{$smarty.const.FRONT_DOC_ROOT}javascripts/select2/select2.css" />
                                                            <script type="text/javascript" src="{$smarty.const.FRONT_DOC_ROOT}javascripts/select2/select2.js" ></script>
                                                            <script type="text/javascript">
                                                                    {literal}jQuery(document).ready(function(){{/literal}
                                                                    {assign var="i" value=0}
                                                                    {assign var="tagsCount" value=$allTags|count}
                                                                    jQuery("#{$item.FIELD_NAME}").select2(
                                                                        {literal}
                                                                            {
                                                                                tags: [{/literal}
                                                                                {foreach from=$allTags item="tag"}
                                                                                    {assign var="i" value=$i+1}
                                                                                    {literal}{{/literal}id:{$tag.ID}, text:'{$tag.TITLE}'{literal}}{/literal}{if $i<$tagsCount},{/if}
                                                                                {/foreach}
                                                                                {literal}]

                                                                            }
                                                                        {/literal}
                                                                    );
                                                                {literal}});{/literal}
                                                            </script>
                                                            <input type="text" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$data[$item.FIELD_NAME]|escape|trim}" size="40" class="text" />
                                                        {elseif $item.TYPE eq 'MULTICHECKBOX'}
                                                            {foreach from=$item.OPTIONS item=opt}
                                                                <input name="{$item.FIELD_NAME}[]" value="{$opt}" type="checkbox" {if in_array($opt, explode(',',$data[$item.FIELD_NAME]))}checked="checked" {/if}> {$opt} <br />
                                                            {/foreach}
                                                        {elseif $item.TYPE eq 'TXT'}
                                                            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/rte.tpl" NAME=$item.FIELD_NAME VALUE=$data[$item.FIELD_NAME]|escape|trim}
	                                                    {elseif $item.TYPE eq 'ADDRESS'}
                                                        {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/address_field.tpl" NAME=$item.FIELD_NAME VALUE=$data[$item.FIELD_NAME]}
                                                        {elseif $item.TYPE eq 'BOOL'}
                                                            {html_options options=$yes_no selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME}
                                                        {elseif $item.TYPE eq 'DROPDOWN'}
                                                            {html_options3 options=$item.OPTIONS selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME}
                                                        {elseif $item.TYPE eq 'CAT'}
                                                            {html_options options=$categs selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME}
                                                        {elseif $item.TYPE eq 'FILE'}
                                                            <input type="file" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" class="text"/><br />
                                                            {l}Allowed files{/l}: .pdf, .xls, .xlsx, .doc, .docx, .zip, .rar, .txt, .rtf, .csv. {l}Files of other types will not be uploaded at all{/l}.
                                                            <br/>
                                                            {if $data[$item.FIELD_NAME] neq ''}
                                                                {l}Current{/l}: <br/><a href="../uploads/{$data[$item.FIELD_NAME]}" target="_blank">{$data[$item.FIELD_NAME]}</a> <a href="{$smarty.const.DOC_ROOT}/rm_si.php?id={$item.ID}&lid={$data.ID}">{l}Remove{/l}</a>
                                                            {/if}
                                                        {elseif $item.TYPE eq 'IMAGE'}
                                                            Upload from disc<br />
                                                            <input type="file" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" class="text"/><br/>
                                                            {l}Allowed files{/l}: .gif, .png, .jpg, .jpeg. {l}Files of other types will not be uploaded at all{/l}.
                                                            <br />
                                                            {if $data[$item.FIELD_NAME] neq ''}
                                                                {l}Current{/l}: <br/><img src="../uploads/thumb/{$data[$item.FIELD_NAME]}"/> <a href="{$smarty.const.DOC_ROOT}/rm_si.php?id={$item.ID}&lid={$data.ID}">{l}Remove{/l}</a><br />
                                                            {/if}
                                                            <strong>OR</strong><br />
                                                            Download by URL<br />
                                                            <input type="text" name="{$item.FIELD_NAME}_SUBMIT" id="{$item.FIELD_NAME}" class="text" />


                                                        {elseif $item.TYPE eq 'VIDEO'}
                                                            <input type="file" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" class="text"/><br/>
                                                            {l}Allowed files{/l}: .avi, .mpg, .wmv, .mov {l}Files of other types will not be uploaded at all{/l}.
                                                            <br />
                                                            {if $data[$item.FIELD_NAME] neq ''}
                                                                {l}Current{/l}: <br/><img src="../uploads/thumb/{$data[$item.FIELD_NAME]}.jpg"/> <a href="{$smarty.const.DOC_ROOT}/rm_si.php?id={$item.ID}&lid={$data.ID}">{l}Remove{/l}</a>
                                                            {/if}
                                                        {elseif $item.TYPE eq 'IMAGEGROUP'}


                                                            {if $group_image_details}
                                                                <table width="100%">
                                                                    <tr><td>
                                                                            {foreach from=$group_image_details item=image}
                                                                                <div class="link-detail-image" id="{$image.IMAGEID}">
                                                                                    <div><img src="../uploads/thumb/{$image.IMAGE}"/></div>
                                                                                    <div style="clear:both;width:100%;text-align:center;margin-top:5px;"><a onclick="javascript:deleteImage({$image.IMAGEID});">Delete</a></div>
                                                                                </div>
                                                                            {/foreach}

                                                                            <div class="link-detail-image" id="imagelistcopyto" style="display: none;">
                                                                                <div class="imageinto"></div>
                                                                                <div style="clear:both;width:100%;text-align:center;margin-top:5px;" class="deletelinkcontainer"><a href="" class="deletelink">Delete</a></div>
                                                                            </div>

                                                                            <div id="imageuploadajaxcontainer">
                                                                            </div>
                                                                        </td></tr>
                                                                </table>
                                                                {literal}
                                                                    <script>
                                                                    curr_count = 0;
                                                                    function deleteImage(image_id){

                                                                            if(confirm('Are you sure you want to delete this image?')){
                                                                                    jQuery.ajax({
                                                                                    url:"{/literal}{$smarty.const.DOC_ROOT}/admin-delete-images.php{literal}",
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
                                                                {/literal}
                                                            {/if}

                                                            {php}
						                                        // assign a uniqid to this field so we can find the files on submit
						                                        $this->assign('imagegroupid',uniqid());
                                                            {/php}

                                                            <input type="hidden" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$imagegroupid}"/>
                                                            {literal}
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
                                                                {/literal}
                                                                    {if $ctn_img}
                                                                       ctn_img = parseInt('{$ctn_img}');
												
                                                                {else}
                                                                       ctn_img = 0;
                                                                {/if}

                                                                {literal}
                                                                   function createUploader(){
                                                                       var uploader = new qq.FileUploader({
                                                                           element: document.getElementById('file-uploader-admin'),
                                                                           action: '../uploadimg.php',
                                                                           onSubmit: function(id, fileName, responseJSON){
                                                                           curr_count = curr_count+1;
                                                                           if(curr_count+ctn_img>=parseInt("{/literal}{$link_types[$linktypeid].COUNT_IMAGES}{literal}"))
                                                                            jQuery(".qq-upload-button").hide();
                                                                                  },
                                                                           params: {
                                                                {/literal}
                                                                                    imagegroupid: "{$imagegroupid}",
                                                       imagegroupidnbr: "{$data.IMAGEGROUP}",
                                                                {literal}
													       
                                                                           }
                                                                       });
                                                                       if(curr_count+ctn_img>=parseInt("{/literal}{$link_types[$linktypeid].COUNT_IMAGES}{literal}") || isNaN(parseInt("{/literal}{$link_types[$linktypeid].COUNT_IMAGES}{literal}")) || parseInt("{/literal}{$link_types[$linktypeid].COUNT_IMAGES}{literal}")=='0')
                                                                            {
                                                                              jQuery(".qq-upload-button").hide();
                                                                            }
                                                                   }
                                                                    window.onload = createUploader;
												
                                                                </script>

                                                            {/literal}
                                                        {/if}
                                                        {if $item.DESCRIPTION}
                                                            <p class="msg notice info">{$item.DESCRIPTION}</p>
                                                        {/if}
                                                    </td>
                                                </tr>
                                                {/if}
                                                    {/foreach}    
                                                    </table>
                                                </div>
                                                {/if}

                                                    {if $link_type_details.DEEP_LINKS > 0}
                                                        <table class="formPage">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="label required"><label>{l}Deep Links{/l}:</label></td>
                                                                    <td class="smallDesc">
                                                                        <table id="additional_links" border="0" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td align="center">{l}Titles{/l}</td>
                                                                                <td align="center">{l}URLs{/l}</td>
                                                                            </tr>

                                                                            {section name=i start=0 loop=$link_type_details.DEEP_LINKS}
                                                                                <tr>
                                                                                    <td><input type="text" name="ADD_LINK_TITLE[]" value="{$add_links[i].TITLE}" class="text" style="width: 130px;" /></td>
                                                                                    <td><input type="text" name="ADD_LINK_URL[]"   value="{$add_links[i].URL}"   class="text" style="width: 130px;" /></td>
                                                                                </tr>
                                                                            {/section}
                                                                        </table>

                                                                        <div class="clear"></div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    {/if}


                                                    <table class="formPage">
                                                        <tbody>
                                                            {if $smarty.const.REQUIRE_REGISTERED_USER}
                                                                <tr>
                                                                    <td class="label"><label for="OWNER_ID">{l}Assign Owner{/l}:</label></td>
                                                                    <td class="smallDesc">
                                                                        {html_options options=$ActiveUsersList selected=$data.OWNER_ID name="OWNER_ID" id="OWNER_ID"}
                                                                        <p class="msg notice info">{l}Use with care!! Selected user details will overwrite link owner name, email address and id!!{/l}</p>
                                                                    </td>
                                                                </tr>
                                                            {else}
                                                                {if $data.OWNER_ID != ''}
                                                                <input type="hidden" name="OWNER_ID" value="{$data.OWNER_ID}" />
                                                            {/if}
                                                        {/if}

                                                        <tr>
                                                            <td class="label required"><label for="OWNER_EMAIL_CONFIRMED">{l}Owner Email Confirmed{/l}:</label></td>
                                                            <td class="smallDesc">
                                                                {html_options options=$econfirm selected=$data.OWNER_EMAIL_CONFIRMED name="OWNER_EMAIL_CONFIRMED" id="OWNER_EMAIL_CONFIRMED"}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="label required"><label for="semail">{l}Send Email On Edit{/l}:</label></td>
                                                            <td class="smallDesc">
                                                                <input type="checkbox" id="semail" name="semail" value="1" {if $semail}checked="checked"{/if} />
                                                                <p class="msg notice info">{l}Check this to send an email to the link owner to let them know you have edited the link.{/l}</p>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="label required"><label for="STATUS">{l}Status{/l}:</label></td>
                                                            <td class="smallDesc">
                                                                {html_options options=$stats selected=$data.STATUS name="STATUS" id="STATUS"}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="label"><label for="EXPIRY_DATE">{l}Link Expires{/l}:</label></td>
                                                            <td class="smallDesc">
                                                                <input type="text" id="EXPIRY_DATE" name="EXPIRY_DATE" value="{$data.EXPIRY_DATE}" maxlength="40" class="text" />
                                                            </td>
                                                        </tr>

                                                        {if $smarty.const.ENABLE_PAGERANK}
                                                            <tr>
                                                                <td class="label"><label for="PAGERANK">{l}Pagerank{/l}:</label></td>
                                                                <td class="smallDesc">
                                                                    <input type="text" id="PAGERANK" name="PAGERANK" value="{$data.PAGERANK}" maxlength="2" class="text" />
                                                                    <p class="msg notice info">{l}Leave the field blank for automatic Google Pagerank calculation or give value of "-1" for no Pagerank.{/l}</p>
                                                                </td>
                                                            </tr>
                                                        {/if}

                                                        <tr>
                                                            <td class="label"><label for="HITS">{l}Hits{/l}:</label></td>
                                                            <td class="smallDesc">
                                                                <input type="text" id="HITS" name="HITS" value="{$data.HITS}" maxlength="15" class="text" />
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <table class="formPage">
                                                        <tfoot>
                                                            <tr>
                                                        <input type="hidden" value="{$linktypeid}" ID="LINK_TYPE_H" name="LINK_TYPE_H"/>
                                                        <input type="hidden" value="{$data.ID}" ID="ID" name="id"/>
                                                        <input type="hidden" value="{$data.ID}" ID="ID" name="ID"/>
                                                        <td><input type="reset" id="reset-link-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
                                                        <td><input type="submit" id="send-link-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save link{/l}" class="button" /></td>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                    <input type="hidden" name="formSubmitted" value="1" />
                                                    <input type="hidden" name="submit_session" value="{$submit_session}" />
                                                </form>
                                            </div>

                                            {literal}
                                                <script type="text/javascript">
                                                jQuery(function($) {
                                                        $(document).ready(function(){
                                                                var allowed_fields 	= new Array;
		
                                                {/literal}
                                                    {foreach from=$link_types item=link_type key=link_type_id name=link_types}
                                                                    allowed_fields[{$link_type_id}] = [
                                                    {foreach from=$link_type.FIELDS item=field_name name=fields}
                                                                                        "{$field_name}"{if !$smarty.foreach.fields.last},{/if}
                                                    {/foreach}
                                                                                    ];
			
                                                {/foreach}
                                                {literal}

                                                {/literal}
                                                {* albert: seems useless, we are making a refresh when the user changes the link type
                                                ------------------------------------------------------------------------
                                                function update_form(force_id) {
                                                if (force_id == null)
                                                var type_id = $("#LINK_TYPE :selected").val();
                                                else
                                                var type_id = force_id;
                                                $("#add_items :input").each(function(n,element){
                                                if (allowed_fields[type_id].in_array($(element).attr("name"))) {
                                                $("#" + $(element).attr("name")).show();
                                                $(element).show();
                                                } else {
                                                $("#" + $(element).attr("name")).hide();
                                                $(element).hide();
                                                }
                                                });
				
                                                if (allowed_fields[type_id].length == 0) {
                                                $("#add_items_head").hide();
                                                } else if (!$("add_items_head").is(":visible")) {
                                                $("#add_items_head").show();
                                                }
                                                }
                                                update_form($("#LINK_TYPE").val());
                                                ------------------------------------------------------------------------
                                                *}
                                                {literal}
		
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

                                                var maxCats = {/literal}{if $link_type_details.MULTIPLE_CATEGORIES == ''}1{else}{$link_type_details.MULTIPLE_CATEGORIES}{/if}{literal};
                                                var numCats = {/literal}1 + {$add_categs|@count}{literal};
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
                                            {/literal}

