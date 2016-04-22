{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}

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
      {l}Page saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="" enctype="multipart/form-data" id="submit_form">
   <table class="formPage">

   <thead>
      <tr>
         <th colspan="2">
               {if $action eq 'N'}
                  {l}New Page{/l}
               {else}
                  {l}Edit Page{/l}
               {/if}
         </th>
      </tr>
  </thead>

   <tbody>
      <tr>
         <td class="label required"><label for="TITLE">{l}Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" name="NAME" id="NAME" class="text" value="{$NAME}" />
         </td>
      </tr>
         {*<tr>*}
         {*<td class="label required"><label for="TITLE">{l}SEO URL{/l}:</label></td>*}
         {*<td class="smallDesc">*}
            {*<input type="text" name="SEO_NAME" id="SEO_NAME" class="text" value="{$SEO_NAME}" />*}
         {*</td>*}
      {*</tr>*}
      <tr>
         <td class="label required"><label for="INFORMATION">{l}Information{/l}:</label></td>
         <td class="smallDesc">{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/rte.tpl" NAME="CONTENT" VALUE=$CONTENT}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="STATUS">{l}Status{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$status selected=$STATUS name="STATUS" id="STATUS"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="Privacy">{l}Privacy{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$privacy selected=$PRIVACY name="PRIVACY" id="PRIVACY"}
         </td> </tr>
	  <tr>
         <td class="label required"><label for="Placement">{l}Order{/l}:</label></td>
        <td class="smallDesc">
            <input type="text" id="PLACEMENT" name="PLACEMENT" value="{$PLACEMENT|escape|trim}"  class="text" />
	     <p class="msg notice info">{l}This is the order in the menu that it will appear. Numbers Only. The smaller the number the higher it appears in menu order.{/l}</p>
           
         </td>
     
      </tr>
       <tr>
<td class="label required"><label for="SHOW_IN_MENU">{l}Show In Menu{/l}:</label></td>
<td class="smallDesc">
<input type="checkbox" id="SHOW_IN_MENU" name="SHOW_IN_MENU" value="1" {if $SHOW_IN_MENU}checked="checked"{/if} />
 <p class="msg notice info">{l}Check this to list the page in the menu.{/l}</p>        
</td>
</tr>
      <tr class="thead">
         <th colspan="2">{l}META tags{/l}</th>
      </tr>
      <tr class="thead">
         <td colspan="2" class="info notice">{l}Define custom META tags for pages. Leave blank to use default tags defined for your directory.{/l}</td>
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
            <textarea id="META_DESCRIPTION" name="META_DESCRIPTION" rows="3" cols="30" class="text" {formtool_count_chars name="META_DESCRIPTION" limit=$smarty.const.META_DESCRIPTION_MAX_LENGTH alert=true}>{$META_DESCRIPTION|trim|escape}</textarea>
            <p class="limitDesc">{l}Limit{/l}: <input type="text" name="META_DESCRIPTION_limit" class="limit_field" readonly="readonly" value="{$MetaDescriptionLimit}" /></p>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-page-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset page{/l}" title="{l}Reset page{/l}" class="button" /></td>
         <td><input type="submit" id="send-page-submit" name="save" value="{l}Save{/l}" alt="{l}Save page{/l}" title="{l}Save page{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{/strip}
