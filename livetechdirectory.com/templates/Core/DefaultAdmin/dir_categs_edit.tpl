{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="edit_categ_form" validators=$validators}

{literal}
<script type="text/javascript">

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


function plusCateg()
{
	var div = jQuery('#categ');
	var newDiv = div.clone(true);
	newDiv.attr('id', '');
    newDiv.show();

	var delLink = jQuery('#fileDelCateg');
	var a = delLink.clone(true);
    delLink.attr('id', '');
	delLink.show();

	//newDiv.append(a);
    newDiv.insertAfter(div);
	//delLink.parent().insertBefore(newDiv, delLink);

}

function removeCateg(elem)
{
	elem.parentNode.remove(elem.previousSibling);

}

function removeSize(elem)
{
	elem.parentNode.parentNode.removeChild(elem.parentNode);
}

</script>
{/literal}


{if $symbolic eq 1}
    {literal}
    <script type="text/javascript">
    jQuery(function($) {
        $(document).ready(function(){
            $('#PARENT_ID').change(function() {
               valid_obj.edit_categ_form.rules.SYMBOLIC_ID.remote.data.PARENT_ID = $('#PARENT_ID').val();
            });
        });
    });
    </script>
    {/literal}
{else}
    {literal}
    <script type="text/javascript">
    jQuery(function($) {
        $(document).ready(function(){
            $('#PARENT_ID').change(function() {
               valid_obj.edit_categ_form.rules.TITLE.remote.data.PARENT_ID = $('#PARENT_ID').val();
            });
        });
    });
    </script>
    {/literal}
{/if}

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

{if not $id}
   {if $posted}
      <div class="success block">
         {l}Category saved.{/l}
      </div>
   {elseif isset($quickAdded) and $quickAdded gt 0}
      <div class="success block">{$quickAdded} {l}subcategories saved{/l}!</div>
   {/if}

<div class="block">
   <form method="post" action="" id="edit_categ_form">
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E' or $action eq 'M')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {if $symbolic eq 1}
                  {l}Create new symbolic category{/l}
               {else}
                  {l}Create new{/l}{if $symbolic eq 1}{l} symbolic{/l}{/if}{l} category{/l}
               {/if}
            {elseif $action eq 'E'}
               {l}Edit Category{/l}
            {elseif $action eq 'M'}
               {l}Add Subcategories to{/l}: {$currentCategory.TITLE|escape|trim}
            {/if}
         </th>
      </tr>
   </thead>
   {/if}

   <tbody>
      {if $action eq 'M'}
      <tr>
         <td class="label"><label for="multicategs">{l}Title{/l}:</label></td>
         <td class="smallDesc">
            <textarea name="multicategs" id="multicategs" rows="10" cols="50" class="text">{$multicategs|trim|escape}</textarea>
            <p class="info">{l}Place a line break between each category title{/l}.</p>
            <p class="info">{l}Duplicate entries are auto skipped{/l}.</p>
         </td>
      </tr>
      {else}
      <tr>
         <td class="label{if $symbolic ne 1} required{/if}"><label for="TITLE">{l}Title{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="TITLE" name="TITLE" value="{$TITLE|escape|trim}" maxlength="{$smarty.const.CAT_TITLE_MAX_LENGTH}" class="text" />
            {if $symbolic eq 1}
               <p class="notice msg">{l}Leave blank to follow the title of the category that you're creating a symbolic link for{/l}.</p>
            {/if}
         </td>
      </tr>

     {if $URL eq ""}
  	<tr>
     		<td class="label required"><label for="TITLE_URL">{l}Custom SEO Url{/l}:</label></td>
     		<td class="smallDesc">
        	<input type="text" id="TITLE_URL" name="TITLE_URL" value="{$TITLE_URL|escape|trim}" maxlength="255" class="text" />
     		</td>
  	</tr>
   {/if}

  <tr>
          <td class="label required"><label for="URL">{l}URL{/l}:</label></td>
          <td class="smallDesc">
              <input type="text" id="URL" name="URL" value="{$URL|escape|trim}" class="text" />
              <p class="limitDesc">{l}Fill this if you want to make this category link to be just a link on some URL{/l}</p>
          </td>
      </tr>

      <tr>
          <td class="label required"><label for="NEW_WINDOW">{l}Open in new window{/l}:</label></td>
          <td class="smallDesc">
              <input type="hidden" name="NEW_WINDOW" value="0"/>
              <input type="checkbox" id="NEW_WINDOW" name="NEW_WINDOW" value="1" {if $NEW_WINDOW eq 1}checked="checked"{/if}/>
              <p class="limitDesc">{l}If checked category page or custom url page will be opened in a new window{/l}</p>
          </td>
      {if $symbolic ne 1}
      <tr>
         <td class="label"><label for="DESCRIPTION">{l}Description{/l}:</label></td>
         <td class="smallDesc">
            <textarea name="DESCRIPTION" id="DESCRIPTION" rows="6" cols="50" class="text" {formtool_count_chars name="DESCRIPTION" limit=$smarty.const.CAT_DESCRIPTION_MAX_LENGTH alert=true}>{$DESCRIPTION|trim|escape}</textarea>
            <p class="limitDesc">{l}Limit{/l}: <input type="text" name="DESCRIPTION_limit" class="limit_field" readonly="readonly" value="{$DescriptionLimit}" /></p>
         </td>
      </tr>
      {/if}

      <tr>
         <td class="label required"><label for="PARENT_ID">{l}Parent{/l}:</label></td>
         <td class="smallDesc">
            {* Load category selection *}
            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" parent="1"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="SORT_ORDER">{l}Sort Order{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="SORT_ORDER" name="SORT_ORDER" value="{$SORT_ORDER|escape|trim}" maxlength="255" class="text" />
         </td>
      </tr>
      
      <tr>
      	<td class="label"><label for="RSS_URL">{l}RSS feed:{/l}</label></td>
      	<td class="smallDesc">
      		<input type="text" id="RSS_URL" name="RSS_URL" value="{$RSS_URL|escape|trim}" maxlength="255" class="text" />
      		<p class="limitDesc">{l}For an automatic update from the RSS feed set {/l}<strong>rss_import_feeds.php</strong>{l} to run as a cron job.{/l}</p>
      	</td>
      </tr>

      {if $symbolic eq 1}
      <tr>
         <td class="label required"><label for="SYMBOLIC_ID">{l}Symbolic category for{/l}:</label></td>
         <td class="smallDesc">
            {*html_options options=$categs selected=$SYMBOLIC_ID name="SYMBOLIC_ID" id="SYMBOLIC_ID"*}
            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select_symbolic.tpl" parent="1"}
         </td>
      </tr>
      {/if}
      
      <tr>
         <td class="label required"><label for="STATUS">{l}Link Type{/l}:</label></td>
         <td class="smallDesc">
						{* Link Type selection *}
						{*html_options options=$link_types selected=$LINK_TYPE name="LINK_TYPE" id="LINK_TYPE"*}
							{foreach from=$LINK_TYPES item=link_type name=additional}
									<div>
										{html_options options=$link_types selected=$link_type.LINK_TYPE name="LINK_TYPES[]" id="LINK_TYPES[]"}&nbsp;
										{*include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" categ_id=$categ.CATEGORY_ID additional_categs=true*}
										<a href="#" onclick="removeCateg(this); return false;">{l}remove{/l}</a>
										<div style="float: none; clear: both; height: 8px;"></div>
									</div>
							{foreachelse}
								<div>
								{html_options options=$link_types name="LINK_TYPES[]" id="LINK_TYPES[]"}&nbsp;
						</div>
							{/foreach}
						<div id="categ" style="display: none;">
							<br />
							{html_options options=$link_types name="LINK_TYPES[]" id="LINK_TYPES[]"}&nbsp;
                            <a href="#" class="formDelCateg" id="fileDelCateg" onclick="removeCateg(this); return false;">{l}remove{/l}</a>
                            <div style="float: none; clear: both; height: 8px;"></div>
						</div>

  						<div class="clear"></div>
  						<br />
	   					<a onclick="plusCateg();return false;" href="" id="plusCategLink" class="formSmall" style="border-width: 0;">+ {l}additional link type{/l}</a>
						<p class="limitDesc">By default all link types are allowed for the category, by setting a link type it will then becomes only link types you set here.</p>
					</td>
      </tr>

      <tr>
         <td class="label required"><label for="STATUS">{l}Columns{/l}:</label></td>
         <td class="smallDesc">
             <select name="COLS">
                 <option value>-- As defined in listing widget --</option>
                 <option value="1" {if $COLS==1}selected="selected"{/if}>1</option>
                 <option value="2" {if $COLS==2}selected="selected"{/if}>2</option>
                 <option value="3" {if $COLS==3}selected="selected"{/if}>3</option>
                 <option value="4" {if $COLS==4}selected="selected"{/if}>4</option>
             </select>
             <p class="limitDesc">This setting overrides widget's setting. Leave default value to let widget define columns number</p>
         </td>
      </tr>

      <tr>
         <td class="label required"><label for="STATUS">{l}Style{/l}:</label></td>
         <td class="smallDesc">
             <select name="STYLE">
                 <option value="">-- As defined in listing widget --</option>
                 <option value="list" {if $STYLE=='list'}selected="selected"{/if}>List</option>
                 <option value="grid" {if $STYLE=='grid'}selected="selected"{/if}>Grid</option>
             </select>
             <p class="limitDesc">This setting overrides widget's setting. Leave default value to let widget define columns number</p>
         </td>
      </tr>

      <tr>
         <td class="label required"><label for="STATUS">{l}Status{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$stats selected=$STATUS name="STATUS" id="STATUS"}
         </td>
      </tr>

      {if $symbolic ne 1 and $smarty.const.ENABLE_META_TAGS eq 1}
      <tr class="thead">
         <th colspan="2">{l}META tags{/l}</th>
      </tr>
      <tr>
         <th class="msg notice info" colspan="2">{l}Define custom META tags. Leave blank to use default tags defined for your directory.{/l}</th>
      </tr>

      <tr>
         <td class="label"><label for="META_KEYWORDS">{l}META Keywords{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="META_KEYWORDS" name="META_KEYWORDS" value="{$META_KEYWORDS|escape|trim}" class="text" />
            <p class="msg notice info">{l}Separate keywords by comma.{/l}</p>
         </td>
      </tr>
      <tr>
         <td class="label"><label for="META_DESCRIPTION">{l}META Description{/l}:</label></td>
         <td class="smallDesc">
            <textarea name="META_DESCRIPTION" id="META_DESCRIPTION" rows="3" cols="30" class="text" {formtool_count_chars name="META_DESCRIPTION" limit=$smarty.const.META_DESCRIPTION_MAX_LENGTH alert=true}>{$META_DESCRIPTION|trim|escape}</textarea>
            <p class="limitDesc">{l}Limit{/l}: <input type="text" name="META_DESCRIPTION_limit" class="limit_field" readonly="readonly" value="{$MetaDescriptionLimit}" /></p>
         </td>
      </tr>
      {/if}
 
   {if $symbolic ne 1}
<tr>
<td class="label"><label for="TDESCRIPTION">{l}Page Title{/l}:</label></td>
<td class="smallDesc">
<textarea name="TDESCRIPTION" id="TDESCRIPTION" rows="3" cols="50" class="text" >{$TDESCRIPTION|trim|escape}</textarea>
</td>
</tr>
{/if}
   {if $symbolic ne 1}
<tr>
<td class="label"><label for="CATCONTENT">{l}Main area Content for the Category{/l}:</label></td>
<td class="smallDesc">
    {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/rte.tpl" NAME="CATCONTENT" VALUE=$CATCONTENT}
</td>
</tr>
{/if}
  {/if}
   </tbody>
   <tfoot>
      <tr>
         <td><input type="reset" id="reset-categ-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-categ-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save category{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="id" class="hidden" value="{$ID}" />
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
</form>
</div>
{else}
<div class="block">
   <form method="post" action="" name="delete">
   <input type="hidden" name="action" class="hidden" value="D:{$id}" />
   <input type="hidden" name="id" class="hidden" value="{$id}" />
   <table class="formPage">
      <tbody>
         <tr>
            <td colspan="2">{l}The category contains {/l}{$count_categs}{l} subcategorie(s) {/l}{$count_links}{l} link(s) and {/l}{$count_articles}{l} article(s){/l}.<br />{l}Cannot proceed with delete until further action is taken{/l}:</td>
         </tr>
         <tr>
            <td class="label"><label for="do-categ-select">{l}Move all to{/l}</label> <input type="radio" id="do-categ-select" name="do" value="move"{if $do eq "move"} checked="checked"{/if} /></td>
            <td class="smallDesc">
               {* Load category selection *}
               {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl"}

               {if $error}
                  <span class="errForm">{l}Please select another category.{/l}</span>
               {/if}
            </td>
         </tr>
         <tr>
            <td class="label" colspan="2"><label for="do-delete-all">{l}Delete all{/l}</label> <input type="radio" id="do-delete-all" name="do" value="delete"{if $do eq "delete"} checked="1"{/if} /></td>
         </tr>
      </tbody>
      <tfoot>
         <tr>
            <td>
               <input type="submit" name="delete" value="{l}Delete{/l}" onclick="return categ_rm_confirm('{escapejs}{l}Are you sure you want to remove this category?{/l}\n{l}Note: categories can not be restored after removal!{/l}{/escapejs}');" title="{l}Remove Category{/l}" class="action delete button" />
            </td>
            <td>
               <input type="submit" name="cancel" value="{l}Cancel{/l}" title="{l}Cancel category removal{/l}" class="button" />
            </td>
         </tr>
      </tfoot>
   </table>
   </form>
</div>
{/if}
{/strip}