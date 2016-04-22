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
      {l}Media saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="" id="submit_form" enctype="multipart/form-data">
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {l}Create new Media{/l}
            {elseif $action eq 'E'}
               {l}Edit Media Unit{/l}
            {/if}
         </th>
      </tr>
  </thead>
  {/if}

   <tbody>
      <tr>
         <td class="label required"><label for="FILE">{l}File{/l}:</label></td>
         <td class="smallDesc">
            <input type="file" id="FILE" name="FILE" value="" class="text" />
	    <br/>
	    {if $TYPE eq "image" and !empty($FILE_NAME) and $action neq 'N'}
	    <img src="/uploads/media/{$USER_ID}/{$FILE_NAME}">
	    {/if}
         </td>
      </tr>
       <tr>
         <td class="label required"><label for="TYPE">{l}TYPE{/l}:</label></td>
         <td class="smallDesc">
	 <select name="TYPE">
	    <option value="image" {if $TYPE eq 'image'}selected="selected"{/if}>image</option>
	 </select>
         </td>
      </tr>
       <tr>
         <td class="label"><label for="OWNER_ID">{l}Assign Owner{/l}:</label></td>
         <td class="smallDesc">
             {html_options options=$ActiveUsersList selected=$USER_ID name="USER_ID" id="USER_ID"}
            
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-link-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset Form{/l}" class="button" /></td>
         <td><input type="submit" id="send-link-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save AdUnit{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   
   <input type="hidden" name="id" value="{$id}" />
   <input type="hidden" name="formSubmitted" value="1" />
   
   </form>
</div>
{/strip}
