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
      {l}AdUnit saved.{/l}
   </div>
{/if}


<div align="left">
	<a href="{$smarty.const.DOC_ROOT}/dir_inline_widgets.php" title="Back to Widgets Inline" style="font-weight: bold; font-decoration: none; "><< Back to Widgets Inline</a>
</div>

<div class="block">
   <form method="post" action="" id="submit_form">
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {l}Create new Inline Widget{/l}
            {elseif $action eq 'E'}
               {l}Edit Inline Widget{/l}
            {/if}
         </th>
      </tr>
  </thead>
  {/if}

   <tbody>
      <tr>
         <td class="label required"><label for="NAME">{l}Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="NAME" name="NAME" value="{$NAME|escape|trim}" class="text" />
         </td>
      </tr>

      <tr>
         <td class="label"><label for="TEXT">{l}Text{/l}:</label></td>
         <td class="smallDesc">
	    <textarea name="TEXT" cols="70" rows="10">{$TEXT}</textarea>
         </td>
      </tr>

      <tr>
         <td class="label required"><label for="STATUS">{l}Status{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$stats selected=$STATUS name="STATUS" id="STATUS"}
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
