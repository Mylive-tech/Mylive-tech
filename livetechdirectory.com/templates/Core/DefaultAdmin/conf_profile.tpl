{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}

{strip}
{if $posted}
   <div class="success block">
      {l}Profile updated.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="" id="submit_form">
   <table border="0" class="formPage">

   <thead>
      <tr>
         <th colspan="2">{l}Update profile{/l}</th>
      </tr>
  </thead>

   <tbody>
      <tr>
         <td class="label required"><label for="NAME">{l}Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="NAME" name="NAME" value="{$NAME}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label"><label for="PASSWORD">{l}Password{/l}:</label></td>
         <td class="smallDesc">
            <input type="password" id="PASSWORD" name="PASSWORD" value="" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label"><label for="PASSWORDC">{l}Confirm Password{/l}:</label></td>
         <td class="smallDesc">
            <input type="password" id="PASSWORDC" name="PASSWORDC" value="" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label"><label for="LANGUAGE">{l}Preffered Language{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$languages selected=$LANGUAGE name="LANGUAGE" id="LANGUAGE"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="EMAIL">{l}Email{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="EMAIL" name="EMAIL" value="{$EMAIL}" maxlength="255" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label"><label for="SUBMIT_NOTIF">{l}Link Submit Notification{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" id="SUBMIT_NOTIF" name="SUBMIT_NOTIF" value="1"{if $SUBMIT_NOTIF} checked="checked"{/if} />
         </td>
      </tr>
      <tr>
         <td class="label"><label for="PAYMENT_NOTIF">{l}Link Payment Notification{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" id="PAYMENT_NOTIF" name="PAYMENT_NOTIF" value="1"{if $PAYMENT_NOTIF} checked="checked"{/if} />
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-profile-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-profile-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save profile{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{/strip}