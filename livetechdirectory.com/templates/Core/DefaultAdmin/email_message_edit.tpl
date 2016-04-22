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
      {l}Email message template saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="" id="submit_form">
   <table class="formPage">
   {if isset($action) and ($action eq 'N' or $action eq 'E')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {l}Create new email template{/l}
            {elseif $action eq 'E'}
               {l}Edit email template{/l}
            {/if}
         </th>
      </tr>
   </thead>
   {/if}

   <tbody>
      <tr>
         <td class="label required"><label for="TITLE">{l}Title{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="TITLE" name="TITLE" value="{$TITLE|escape|trim}" maxlength="255" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="TPL_TYPE">{l}Template Type{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$tpl_types selected=$TPL_TYPE name="TPL_TYPE" id="TPL_TYPE"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="SUBJECT">{l}Subject{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="SUBJECT" name="SUBJECT" value="{$SUBJECT|escape|trim}" maxlength="255" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="BODY">{l}Body{/l}:</label></td>
         <td class="smallDesc">
            <textarea id="BODY" name="BODY" rows="6" cols="50" class="text">{$BODY|escape|trim}</textarea>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-email-tpl-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-email-tpl-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save email template{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>

<div class="info block">
   <table class="list info-tbl">
   <thead>
      <tr>
         <th colspan="2">{l}Available variables for {/l}<span class="important">{l}Subject{/l}</span>{l} and {/l}<span class="important">{l}Body{/l}</span>:</th>
      </tr>
   </thead>
   <tbody>

      <tr>
         <td colspan="2" class="notice">{l}From PHP Link Directory configuration{/l}:</td>
      </tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{MY_SITE_NAME}{/literal}</td><td class="smallDesc">{l}Site Name{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{MY_SITE_URL}{/literal}</td><td class="smallDesc">{l}Site URL{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{MY_SITE_DESC}{/literal}</td><td class="smallDesc">{l}Site Description{/l}</td></tr>

      <tr class="notice">
         <td colspan="2">{l}From {/l}<span class="important">{l}Send Email{/l}</span>{l} form{/l}:</td>
      </tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_TITLE}{/literal}</td><td class="smallDesc">{l}Site Name{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_OWNER_NAME}{/literal}</td><td class="smallDesc">{l}Site Owner Name{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_URL}{/literal}</td><td class="smallDesc">{l}Link URL{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_RECIPR_URL}{/literal}</td><td class="smallDesc">{l}Reciprocal URL{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_RECIPR_TITLE}{/literal}</td><td class="smallDesc">{l}Reciprocal Title{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_CATEGORY_TITLE}{/literal}</td><td class="smallDesc">{l}Category title where link is available{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_CATEGORY_URL}{/literal}</td><td class="smallDesc">{l}Full URL to category where link is available{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{LINK_PAYMENT_URL}{/literal}</td><td class="smallDesc">{l}Full URL to payment page{/l}</td></tr>

      <tr class="notice">
         <td colspan="2">{l}From {/l}<span class="important">{l}Send Email and Add Link{/l}</span>{l} form{/l}:</td>
      </tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{EMAIL_TITLE}{/literal}</td><td class="smallDesc">{l}Site Name{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{EMAIL_NAME}{/literal}</td><td class="smallDesc">{l}Site Owner Name{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{EMAIL_URL}{/literal}</td><td class="smallDesc">{l}Link URL{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{EMAIL_DESCRIPTION}{/literal}</td><td class="smallDesc">{l}Site Description{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{EMAIL_ADD_RECIPROCAL_URL}{/literal}</td><td class="smallDesc">{l}URL to enter reciprocal link{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{EMAIL_LINK_URL}{/literal}</td><td class="smallDesc">{l}Directory page where link is added{/l}</td></tr>

      <tr class="notice">
         <td colspan="2">{l}From {/l}<span class="important">{l}Send User Profile Details{/l}</span>{l} form{/l}:</td>
      </tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{USER_LOGIN}{/literal}</td><td class="smallDesc">{l}User's login name or ID{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{USER_NAME}{/literal}</td><td class="smallDesc">{l}User's full name{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{USER_PASSWORD}{/literal}</td><td class="smallDesc">{l}User's password{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{USER_EMAIL}{/literal}</td><td class="smallDesc">{l}User's email address{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{USER_LANGUAGE}{/literal}</td><td class="smallDesc">{l}User's language preference{/l}</td></tr>
      <tr class="{cycle values="odd,even"}"><td class="label">{literal}{USER_LOGIN_PAGE}{/literal}</td><td class="smallDesc">{l}Full URL to your directory user login page{/l}</td></tr>

   </tbody>
   </table>
</div>
{/strip}