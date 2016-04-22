{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}

{strip}
{if $posted}
   <div class="success block">
      {l}Email was sent.{/l}
   </div>

   <div class="block">
      <table class="list">
         <tbody>
         <tr class="{cycle values="odd,even"}">
            <td class="label">{l}Site Name{/l}</td>
            <td class="smallDesc">{$sent.TITLE|escape}</td>
         </tr>
         <tr class="{cycle values="odd,even"}">
            <td class="label">{l}Site Owner Name{/l}</td>
            <td class="smallDesc">{$sent.NAME|escape}</td>
         </tr>
         <tr class="{cycle values="odd,even"}">
            <td class="label">{l}Link URL{/l}</td>
            <td class="smallDesc">{$sent.URL|escape}</td>
         </tr>
         <tr class="{cycle values="odd,even"}">
            <td class="label">{l}Email{/l}</td>
            <td class="smallDesc">{$sent.EMAIL|escape}</td>
         </tr>
         <tr class="{cycle values="odd,even"}">
            <td class="label">{l}Message Template{/l}</td>
            <td class="smallDesc">{$tpls.$EMAIL_TPL_ID|escape}</td>
         </tr>
         </tbody>
      </table>
   </div>
{/if}

{if $send_error or !empty($sql_error)}
   <div class="error block">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while sending email.{/l}</p>
      <p>{l}The message was not sent.{/l}</p>
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p>{$sql_error|escape}</p>
      {/if}
   </div>
{/if}

<form method="post" action="" id="submit_form">
{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/email_sent_errors.tpl"}

<div class="block">

   <table class="formPage">
   <thead>
      <tr><th colspan="2">{l}Send new email{/l}</th></tr>
   </thead>

   <tbody>
      <tr>
         <td class="label required"><label for="TITLE">{l}Site Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="TITLE" name="TITLE" value="{$TITLE}" maxlength="{$smarty.const.TITLE_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label"><label for="NAME">{l}Site Owner Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="NAME" name="NAME" value="{$NAME}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="URL">{l}Link URL{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="URL" name="URL" value="{$URL}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="EMAIL">{l}Email{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="EMAIL" name="EMAIL" value="{$EMAIL}" maxlength="255" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="EMAIL_TPL_ID">{l}Message Template{/l}:</label></td>
         <td class="smallDesc">
            {if $tpls}
               {html_options options=$tpls selected=$EMAIL_TPL_ID name="EMAIL_TPL_ID" id="EMAIL_TPL_ID"}
            {else}
               <p class="norec">{l}No email templates{/l}</p>
            {/if}
         </td>
      </tr>
      </tbody>

      <tfoot>
         <tr>
            <td><input type="reset" id="reset-email-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
            <td><input type="submit" id="send-email-submit" name="send" value="{l}Send{/l}" alt="{l}Send form{/l}" title="{l}Send email{/l}" class="button" /></td>
         </tr>
      </tfoot>
   </table>
</div>
<input type="hidden" name="formSubmitted" value="1" />
</form>
{/strip}