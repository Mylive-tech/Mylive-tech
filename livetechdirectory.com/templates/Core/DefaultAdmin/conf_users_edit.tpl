{* Error and confirmation messages *}
{include file="messages.tpl"}


{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="edit_user_form" validators=$validators}

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
      {l}User saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="" id="edit_user_form">
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {l}Create new user{/l}
            {elseif $action eq 'E'}
               {l}Edit user{/l}
            {/if}
         </th>
      </tr>
  </thead>
  {/if}

   <tbody>
      <tr>
         <td class="label required"><label for="LOGIN">{l}Login{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="LOGIN" name="LOGIN" value="{$LOGIN|trim}" maxlength="{$smarty.const.USER_LOGIN_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="NAME">{l}Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="NAME" name="NAME" value="{$NAME|trim}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label{if $action eq 'N'} required{/if}"><label for="PASSWORD">{l}Password{/l}:</label></td>
         <td class="smallDesc">
            <input type="password" id="PASSWORD" name="PASSWORD" value="" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label{if $action eq 'N'} required{/if}"><label for="PASSWORDC">{l}Confirm Password{/l}:</label></td>
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
            <input type="text" id="EMAIL" name="EMAIL" value="{$EMAIL|trim}" maxlength="255" class="text" />
         </td>
      </tr>
   <td class="label required"><label for="cemail">{l}Confirm Email{/l}:</label></td>
<td class="smallDesc">
<input type="checkbox" id="cemail" name="cemail" value="1" {if $cemail}checked="checked"{/if} />
 <p class="msg notice info">{l}Check this to confirm an users email and allow their links to show.{/l}</p>        
</td>


      <!-- user avatar related -->
      <!--
      <tr>
       <td class="label">{l}Current Photo{/l}:</td>
       <td class="smallDesc" align="left"><img src="{$AUTH_IMG}"></td>
      </tr>
      -->
      <!-- end -->
      {if $smarty.const.ALLOW_AUTHOR_INFO eq 1}
      <tr>
         <td class="label required"><label for="INFO">{l}Info{/l}:</label></td>
         <td class="smallDesc">
            <textarea name="INFO" id="INFO" rows="3" cols="10" class="text" >{$INFO|escape|trim}</textarea>
                   
         </td>
      </tr>
       <tr>
         <td class="label required"><label for="WEBSITE_NAME">{l}Website Name{/l}:</label></td>
         <td class="smallDesc">
             <input type="text" name="WEBSITE_NAME" value="{$WEBSITE_NAME|escape|trim}" maxlength="255" class="text"/>
                   
         </td>
      </tr>
        <tr>
         <td class="label required"><label for="WEBSITE">{l}Website {/l}:</label></td>
         <td class="smallDesc">
             <input type="text" name="WEBSITE" value="{$WEBSITE|escape|trim}" maxlength="255" class="text"/>
                   
         </td>
      </tr>
       
       
      {/if}
      
      <tr>
         <td class="label required"><label for="LEVEL">{l}User Type{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$admin_user selected=$LEVEL name="LEVEL" id="LEVEL"}
         </td>
      </tr>
      <tr>
     	<td class="label required"><label for="ACTIVE">{l}Active{/l}:</label></td>
     	<td class="smallDesc">
        	{html_options options=$yes_no selected=$ACTIVE name="ACTIVE" id="ACTIVE"}
     	</td>
  	</tr>
      <tr>
         <td class="label"><label for="SUBMIT_NOTIF">{l}Link Submit Notification{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" id="SUBMIT_NOTIF" name="SUBMIT_NOTIF" value="1" {if $SUBMIT_NOTIF}checked="checked"{/if}/>
         </td>
      </tr>
      <tr>
         <td class="label"><label for="PAYMENT_NOTIF">{l}Link Payment Notification{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" id="PAYMENT_NOTIF" name="PAYMENT_NOTIF" value="1" {if $PAYMENT_NOTIF}checked="checked"{/if}/>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-user-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-user-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save user{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="id" value="{$ID}" />
   <input type="hidden" name="exclude_id" value="{$ID}" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{/strip}