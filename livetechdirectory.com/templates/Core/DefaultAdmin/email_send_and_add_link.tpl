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
            <td class="label">{l}Description{/l}</td>
            <td class="smallDesc">{$sent.DESCRIPTION|escape}</td>
         </tr>
         <tr class="{cycle values="odd,even"}">
            <td class="label">{l}Category{/l}</td>
            <td class="smallDesc">{$sent.CATEGORY|escape}</td>
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
      <tr><th colspan="2">{l}Send new email and add link{/l}</th></tr>
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
         <td class="label"><label for="DESCRIPTION">{l}Description{/l}:</label></td>
         <td class="smallDesc">
            <textarea id="DESCRIPTION" name="DESCRIPTION" rows="6" cols="50" class="text" {formtool_count_chars name="DESCRIPTION" limit=$smarty.const.DESCRIPTION_MAX_LENGTH alert=true}>{$DESCRIPTION|trim|escape}</textarea>
            <p class="limitDesc">{l}Limit{/l}: <input type="text" name="DESCRIPTION_limit" class="limit_field" readonly="readonly" value="{$DescriptionLimit}" /></p>
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="EMAIL">{l}Email{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="EMAIL" name="EMAIL" value="{$EMAIL}" maxlength="255" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="CATEGORY_ID">{l}Category{/l}:</label></td>
         <td class="smallDesc">
            {* Load category selection *}
            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl"}
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
            <input type="text" id="META_KEYWORDS" name="META_KEYWORDS" value="{$META_KEYWORDS|escape|trim}" class="text" />
            <p class="msg notice info">{l}Separate keywords by comma.{/l}</p>
         </td>
      </tr>
      <tr>
         <td class="label"><label for="META_DESCRIPTION">{l}META Description{/l}:</label></td>
         <td class="smallDesc">
            <textarea id="META_DESCRIPTION" name="META_DESCRIPTION" rows="3" cols="30" class="text" {formtool_count_chars name="META_DESCRIPTION" limit=$smarty.const.DESCRIPTION_MAX_LENGTH alert=true}>{$META_DESCRIPTION|trim|escape}</textarea>
            <p class="limitDesc">{l}Limit{/l}: <input type="text" name="META_DESCRIPTION_limit" class="limit_field" readonly="readonly" value="{$MetaDescriptionLimit}" /></p>
         </td>
      </tr>
      {/if}
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-email-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td>{if isset($tpls) and !empty($tpls)}<input type="submit" id="send-email-submit" name="send" value="{l}Send{/l}" alt="{l}Send form{/l}" title="{l}Send email{/l}" class="button" />{/if}</td>
      </tr>
   </tfoot>
   </table>
</div>
<input type="hidden" name="formSubmitted" value="1" />
</form>
{/strip}