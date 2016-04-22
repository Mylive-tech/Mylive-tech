{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$form_validators}

{literal}
<script type="text/javascript">
	jQuery(function($) {
		$(document).ready(function() {
			$("#TYPE").change(function() {
                var type = $(this).val();
                $('.additionalOption').hide();
                $('div[data-field="'+type+'"]').show();
//				if ($(this).val() == 'DROPDOWN') {
//					$("#DROPDOWN_VALUE").show("fast");
//				} else {
//					$("#DROPDOWN_VALUE").hide("fast");
//				}
			});
		});
	});
</script>
{/literal}

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
      {l}Submit item saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="" id="submit_form">
   {if $tid}
   	<input type="hidden" name="LINK_TYPE_ID" value="{$tid}" />
   {/if}
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {l}Create new submit item{/l}
            {elseif $action eq 'E'}
               {l}Edit Submit Item{/l}
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
      {if $special_field neq 1}
      <tr>
         <td class="label required"><label for="FIELD_NAME">{l}Field Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="FIELD_NAME" name="FIELD_NAME" value="{$FIELD_NAME|escape|trim}" class="text" />
         	<span class="errForm" id="warning_field" name="warning_field">{l}To add this field, you will be inserting a new field into the links table.{/l}</span>   
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="TYPE">{l}Type{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$types selected=$TYPE name="TYPE" id="TYPE"}
            <br />

             <div {if $TYPE ne DROPDOWN} style="display: none;" {/if} class="additionalOption" data-field="DROPDOWN">
	            <input type="text" id="DROPDOWN_VALUE" name="DROPDOWN_VALUE" class="text" value="{$DROPDOWN_VALUE}" />
                 <div class="description">Enter your choices here separated by comma</div>
             </div>

             <div {if $TYPE ne MULTICHECKBOX} style="display: none;" {/if} class="additionalOption" data-field="MULTICHECKBOX">
                 <input type="text" id="MULTICHECKBOX_VALUE" name="MULTICHECKBOX_VALUE" class="text" value="{$MULTICHECKBOX_VALUE}" />
                 <div class="description">Enter your choices here separated by comma</div>
             </div>
         </td>
      </tr>
      {else}
       <input type="hidden" id="FIELD_NAME" name="FIELD_NAME" value="{$FIELD_NAME|escape|trim}" class="text" />
       <input type="hidden" id="TYPE" name="TYPE" value="{$TYPE|escape|trim}" class="text" />
      {/if}
      <tr>
         <td class="label"><label for="DESCRIPTION">{l}Description{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="DESCRIPTION" name="DESCRIPTION" value="{$DESCRIPTION|escape|trim}" class="text" />
         </td>
      </tr>
      {if $special_field neq 1 || $FIELD_NAME == 'RECPR_URL'}
      <tr>
         <td class="label required"><label>{l}Basic Validators{/l}:</label></td>
         <td class="smallDesc">
         	{section name=i loop=$validators}
				{if $special_field neq 1 || ($FIELD_NAME == 'RECPR_URL' && $validators[i].ID == 1)} {* "is not empty" validator for reciprocal URLs *}
					{if $validators[i].IS_REMOTE eq 0}
					<input type="checkbox" name="VALIDATORS[]" value="{$validators[i].ID}" {if $validators[i].SELECTED eq '1'}checked="1"{/if}>&nbsp;{$validators[i].TITLE}&nbsp;
					{/if}
				{/if}
         	{/section}
         </td>
      </tr>
      {/if}
      {if $special_field neq 1}
      <tr>
         <td class="label required"><label>{l}Advanced Validator{/l}:</label></td>
         <td class="smallDesc">
         	<select name="ADV_VALIDATOR">
         		<option value="" selected="selected">None</option>
	         	{section name=i loop=$validators}
	         		{if $validators[i].IS_REMOTE eq 1}
	         		<option value="{$validators[i].ID}" {if $validators[i].SELECTED eq '1'}selected="selected"{/if}>{$validators[i].TITLE}</option>
	         		{/if}
	         	{/section}
         	</select>
         </td>
      </tr>
      {/if}
   </tbody>

   <tfoot>
      <tr>
      	<!-- <input type="hidden" name="action" value="{$action}"/>-->
      	 <input type="hidden" name="id" value="{$id}"/>
         <td><input type="reset" id="reset-link-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-link-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save link{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{/strip}
