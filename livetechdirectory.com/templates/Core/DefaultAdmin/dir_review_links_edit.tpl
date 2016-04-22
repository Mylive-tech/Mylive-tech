{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="edit_link_form" validators=$validators}

{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
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
   <form method="post" action="" id="edit_link_form" onSubmit="return false;">
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {l}Link review{/l}
            {elseif $action eq 'E'}
               {l}Link review{/l}
            {/if}
         </th>
      </tr>
  </thead>
  {/if}

   <tbody>
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
						{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" selected=$data.CATEGORY_ID  selected_parent=$data.PARENT_ID}
						{if $link_type_details.MULTIPLE_CATEGORIES != ''}
							{foreach from=$add_categs item=categ}
								<div>
									{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" categ_id=$categ.CATEGORY_ID additional_categs=true}&nbsp;
									<a href="#" onclick="removeCateg(this); return false;">{l}remove{/l}</a>
									<div style="float: none; clear: both; height: 8px;"></div>
								</div>
							{/foreach}
						{/if}
						<div id="categ" style="display: none;">
							<br />
							{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" additional_categs=true}&nbsp;
						</div>
						{* editing disabled here
						<a href="" class="formDelCateg" id="fileDelCateg" onclick="removeCateg(this); return false;" style="display: none">{l}remove{/l}</a>
  						<div class="clear"></div>
  						<br />
	   					<a onclick="plusCateg();return false;" href="" id="plusCategLink" class="formSmall" style="border-width: 0;">+ {l}additional categ{/l}</a>
	   					*}
					</td>
      			</tr>
			{elseif $item.FIELD_NAME == 'OWNER_NAME'}
				<tr>
					<td class="label"><label for="OWNER_NAME">{l}Owner Name{/l}:</label></td>
					<td class="smallDesc">
						<input type="text" id="OWNER_NAME"  name="OWNER_NAME" value="{$data.OWNER_NAME|escape|trim}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
					</td>
				</tr>
			{elseif $item.FIELD_NAME == 'OWNER_EMAIL'}
				<tr>
					<td class="label"><label for="OWNER_EMAIL">{l}Owner Email{/l}:</label></td>
					<td class="smallDesc">
						<input type="text"  id="OWNER_EMAIL" name="OWNER_EMAIL" value="{$data.OWNER_EMAIL|escape|trim}" maxlength="255" class="text" />
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
						<input type="text"  id="META_KEYWORDS" name="META_KEYWORDS" value="{$data.META_KEYWORDS|escape|trim}" class="text" />
						<p class="msg notice info">{l}Separate keywords by comma.{/l}</p>
					</td>
				</tr>
				{/if}
			{elseif $item.FIELD_NAME == 'META_DESCRIPTION'}
				{if $smarty.const.ENABLE_META_TAGS eq 1}
				<tr>
					<td class="label"><label for="META_DESCRIPTION">{l}META Description{/l}:</label></td>
					<td class="smallDesc">
						<textarea id="META_DESCRIPTION"  name="META_DESCRIPTION" rows="3" cols="30" class="text" {formtool_count_chars name="META_DESCRIPTION" limit=$smarty.const.META_DESCRIPTION_MAX_LENGTH alert=true}>{$data.META_DESCRIPTION|trim|escape}</textarea>
						<p class="limitDesc">{l}Limit{/l}: <input type="text" name="META_DESCRIPTION_limit" class="limit_field" ="" value="{$MetaDescriptionLimit}" /></p>
					</td>
				</tr>
				{/if}
			{* /special submit items *}
			{else}
				<tr>
				   	<td class="label"><label for="{$item.FIELD_NAME}">{l}{$item.NAME}{/l}:</label></td>
				   	<td class="smallDesc">
						{if $item.TYPE eq 'STR'}
							<input type="text"  name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$data[$item.FIELD_NAME]|escape|trim}" size="40" class="text" />
						{elseif $item.TYPE eq 'TXT'}
						    {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/rte.tpl" NAME=$item.FIELD_NAME VALUE=$data[$item.FIELD_NAME]|escape|trim}

						{elseif $item.TYPE eq 'BOOL'}
							{html_options options=$yes_no selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME}
						{elseif $item.TYPE eq 'CAT'}
							{html_options options=$categs selected=$data[$item.FIELD_NAME] name=$item.FIELD_NAME id=$item.FIELD_NAME}
						{elseif $item.TYPE eq 'FILE'}
							{if $data[$item.FIELD_NAME] neq ''}
								<a href="../uploads/reviews/{$data[$item.FIELD_NAME]}" target="_blank">{$data[$item.FIELD_NAME]}</a>
							{/if}
						{elseif $item.TYPE eq 'IMAGE'}
							{if $data[$item.FIELD_NAME] neq ''}
								<img src="../uploads/reviews/thumb/{$data[$item.FIELD_NAME]}"/>
							{/if}
						{elseif $item.TYPE eq 'VIDEO'}
                                                            <input type="file" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" class="text"/><br/>
                                                            {l}Allowed files{/l}: .avi, .mpg, .wmv, .mov {l}Files of other types will not be uploaded at all{/l}.
                                                            <br />
                                                            {if $data[$item.FIELD_NAME] neq ''}
                                                                {l}Current{/l}: <br/><img src="../uploads/thumb/{$data[$item.FIELD_NAME]}.jpg"/> <a href="{$smarty.const.DOC_ROOT}/rm_si.php?id={$item.ID}&lid={$data.ID}">{l}Remove{/l}</a>
                                                            {/if}
							    Download by URL<br />
                                                            <input type="text" name="{$item.FIELD_NAME}_SUBMIT" id="{$item.FIELD_NAME}" class="text" />


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
						<td><input type="text"  name="ADD_LINK_TITLE[]" value="{$add_links[i].TITLE}" class="text" style="width: 130px;" /></td>
						<td><input type="text"  name="ADD_LINK_URL[]"   value="{$add_links[i].URL}"   class="text" style="width: 130px;" /></td>
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
				<input type="checkbox"  id="semail" name="semail" value="1" {if $semail}checked="checked"{/if} />
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
				<input type="text"  id="EXPIRY_DATE" name="EXPIRY_DATE" value="{$data.EXPIRY_DATE}" maxlength="40" class="text" />
			</td>
		</tr>

		{if $smarty.const.ENABLE_PAGERANK}
		<tr>
			<td class="label"><label for="PAGERANK">{l}Pagerank{/l}:</label></td>
			<td class="smallDesc">
            	<input type="text" id="PAGERANK"  name="PAGERANK" value="{$data.PAGERANK}" maxlength="2" class="text" />
            	<p class="msg notice info">{l}Leave the field blank for automatic Google Pagerank calculation or give value of "-1" for no Pagerank.{/l}</p>
			</td>
		</tr>
		{/if}

		<tr>
			<td class="label"><label for="HITS">{l}Hits{/l}:</label></td>
			<td class="smallDesc">
				<input type="text"  id="HITS" name="HITS" value="{$data.HITS}" maxlength="15" class="text" />
			</td>
		</tr>
   </tbody>

   <tfoot>
     <tr>
         <td><input type="reset" id="reset-link-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-link-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Approve review{/l}" class="button" /></td>
      </tr>
     
   </tfoot>

   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="id" value="{$LINK_ID|escape|trim}" />
   </form>
</div>
{/strip}


{literal}
<script type="text/javascript">


function removeCateg(elem)
{
   elem.parentNode.remove(elem.previousSibling);
	
   numCats--;
   if (numCats < maxCats) {
   $('plusCategLink').style.display = 'block'; 
   }
}
</script>
{/literal}
