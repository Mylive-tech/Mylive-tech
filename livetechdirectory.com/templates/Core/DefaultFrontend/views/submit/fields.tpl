{if $item.TYPE neq 'CAT'}
<div class="phpld-columnar phpld-equalize">
    <div class="phpld-label float-left">

        {if $valid.rules[$item.FIELD_NAME].required==true}
            <span class="phpld-required">*</span>
        {/if}
        {l}{$item.NAME}{/l}:
    </div>
    <div class="field float-left">
        {assign var="FIELD_NAME" value=$item.FIELD_NAME}
        {if $item.TYPE eq 'STR'}
            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                <input type="text" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$data[$item.FIELD_NAME]|escape|trim}" size="40" class="text" />
            </div>
        {elseif $item.TYPE eq 'ADDRESS'}
			<div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
			{include file="views/_shared/address_field.tpl" NAME=$item.FIELD_NAME VALUE=$data[$item.FIELD_NAME]}
			</div>
        {elseif $item.TYPE eq 'TXT'}
            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                {include file="views/_shared/rte.tpl" NAME=$item.FIELD_NAME VALUE=$data[$item.FIELD_NAME]}
            </div>
        {elseif $item.TYPE eq 'BOOL'}
            {html_options options=$yes_no selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME}
        {elseif $item.TYPE eq 'DROPDOWN'}
            {html_options3 options=$item.OPTIONS selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME}
        {elseif $item.TYPE eq 'CAT'}
            {*html_options options=$categsNormal selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME*}
        {elseif $item.TYPE eq 'FILE'}
            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                <input type="file" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" class="text"/><br />
                {l}Allowed files{/l}: .pdf, .xls, .xlsx, .doc, .docx, .zip, .rar, .txt, .rtf, .csv. {l}Files of other types will not be uploaded at all{/l}.
                <br/>
                {if $data[$item.FIELD_NAME] neq ''}
                    {l}Current{/l}: <br/><a href="{$smarty.const.SITE_URL}uploads/{$data[$item.FIELD_NAME]}" target="_blank">{$data[$item.FIELD_NAME]}</a>
                    &nbsp;<input type="checkbox" name="MARK_REMOVE_{$item.FIELD_NAME}" value="1"/>&nbsp;{l}Mark for removal{/l}
                {/if}
            </div>
	    {elseif $item.TYPE eq 'MULTICHECKBOX'}                                                                             
                     <div class="smallDesc" >           <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                 {foreach from=$item.OPTIONS item=opt}
                                              <div style='float:left;padding:5px;'>  <input name="{$item.FIELD_NAME}[]" value="{$opt}" type="checkbox" {if in_array($opt, explode(',',$data[$item.FIELD_NAME]))}checked="checked" {/if}> {$opt} </div>
                                                           &nbsp;&nbsp;&nbsp; {/foreach}
                                </div></div>
                         
        {elseif $item.TYPE eq 'VIDEO'}
            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                <input type="file" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" class="text"/><br/>
                {l}Allowed files{/l}: .avi, .mpg, .wmv, .mov {l}Files of other types will not be uploaded at all{/l}.
                <br />
                {if $data[$item.FIELD_NAME] neq ''}
                    {l}Current{/l}: <br/><img src="{$smarty.const.SITE_URL}uploads/thumb/{$data[$item.FIELD_NAME]}.jpg"/> <a href="{$smarty.const.DOC_ROOT}/rm_si.php?id={$item.ID}&lid={$data.ID}">{l}Remove{/l}</a>
                {/if}
            </div>
        {elseif $item.TYPE eq 'IMAGE'}
            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                <input type="file" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" class="text"/><br/>
		 <strong>OR</strong><br />
                   Download by Image Url. Paste the link to the image.<br />
                 <input type="text" name="{$item.FIELD_NAME}_SUBMIT" id="{$item.FIELD_NAME}" class="text" /><br />
                {l}Allowed files{/l}: .gif, .png, .jpg, .jpeg. {l}Files of other types will not be uploaded at all{/l}.
                <br />
                {if $data[$item.FIELD_NAME] neq ''}
                    {l}Current{/l}: <br/>
                    <a href="{$smarty.const.SITE_URL}uploads/{$data[$item.FIELD_NAME]}?detail=1&TB_iframe=true&height=400&width=400" onclick="return showThickbox(this);" class="thickbox">
                        <img src="{$smarty.const.SITE_URL}uploads/thumb/{$data[$item.FIELD_NAME]}"/></a>
                    &nbsp;<input type="checkbox" name="MARK_REMOVE_{$item.FIELD_NAME}" value="1"/>&nbsp;{l}Mark for removal{/l}
                {/if}
            </div>
        {elseif $item.TYPE eq 'IMAGEGROUP'}
	{if $group_image_details}
                                                                <table width="100%">
                                                                    <tr><td>
                                                                            {foreach from=$group_image_details item=image}
                                                                                <div class="link-detail-image" id="{$image.IMAGEID}">
                                                                                    <div><img src="../uploads/thumb/{$image.IMAGE}"/></div>
                                                                                    <div style="clear:both;width:100%;text-align:center;margin-top:5px;"></div>
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
                                                                
                                                            {/if}

            <input type="hidden" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{if !empty($data[$item.FIELD_NAME])}{$data[$item.FIELD_NAME]}{else}{$imagegroupid}{/if}"/>
            {literal}
                <div id="file-uploader">
                    <noscript>
                    <p>Please enable JavaScript to use file uploader.</p>
                    <!-- or put a simple form for upload here -->
                    </noscript>
                </div>
                <script src="javascripts/fileuploader.js" type="text/javascript"></script>
                <script type="text/javascript">
		    {/literal}
		    {if !empty($group_image_details)}
			curr_count = {$group_image_details|count};
		    {else}
		     curr_count = 0;
		     {/if}{literal}
		     
		     
                
                   function createUploader(){
                       var uploader = new qq.FileUploader({
                           element: document.getElementById('file-uploader'),
                           action: 'uploadimg.php',
                           onSubmit: function(id, fileName, responseJSON){
                                                                           curr_count = curr_count+1;
                                                                           if(curr_count>=parseInt("{/literal}{$link_types[$linktypeid].COUNT_IMAGES}{literal}"))
                                                                            jQuery(".qq-upload-button").hide();
                                                                                  },
                           params: {
                                imagegroupid: "{/literal}{if !empty($data[$item.FIELD_NAME])}{$data[$item.FIELD_NAME]}{else}{$imagegroupid}{/if}{literal}"
                           }
                       });
		       if(curr_count>=parseInt("{/literal}{$link_types[$linktypeid].COUNT_IMAGES}{literal}"))
			    jQuery(".qq-upload-button").hide();
                   }
                   window.onload = createUploader;
                </script>

            {/literal}
        {/if}
        {if $item.DESCRIPTION or $item.FIELD_NAME eq 'URL'}
            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if} small">{$item.DESCRIPTION}
                {if $item.FIELD_NAME eq 'URL' and $video}
                    {l}Enter links from sites www.youtube.com, www.flickr.com, www.vimeo.com, www.viddler.com, www.dailymotion.com, www.hulu.com, www.qik.com, www.revision3.com, www.scribd.com, www.wordpress.tv, www.funnyordie.com, www.thedailyshow.com, www.collegehumor.com{/l}
                {/if}
            </div>
        {/if}
    </div>
</div>
{/if}