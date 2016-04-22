{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="edit_widget_zone_form" validators=$validators}


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
         {l}Widget Zone Saved.{/l}
      </div>
   {/if}

<div class="block">
   <form method="post" action="" id="edit_widget_zone_form">
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E' or $action eq 'M')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
		{l}Create Widget Zone{/l}
            {elseif $action eq 'E'}
                {l}Edit Widget Zone{/l}
            {/if}
         </th>
      </tr>
   </thead>
   {/if}

   <tbody>
      <tr>
         <td class="label required"><label for="NAME">{l}Widget Zone Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="NAME" name="NAME" value="{$NAME|escape|trim}" maxlength="255" class="text" />
           
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="TYPE">{l}Zone Type{/l}:</label></td>
         <td class="smallDesc">
           {html_options options=$zone_types selected=$TYPE name="TYPE" id="TYPE"}
           
         </td>
      </tr>
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
{/strip}