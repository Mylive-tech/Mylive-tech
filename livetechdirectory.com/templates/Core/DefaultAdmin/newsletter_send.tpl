{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
{if $posted}
   <div class="success block">
      {l}A newsletter is being sent at the moment.{/l}
   </div>
{elseif $error}
	<div class="error block">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while sending email.{/l}</p>
      <p>{l}The message was not sent.{/l}</p>
   </div>
{/if}


<form method="post" action="">

<div class="block">
   <table class="formPage">
   <thead>
      <tr><th colspan="2">{l}Send Newsletter for all Links{/l}</th></tr>
   </thead>

   <tbody>
      <tr>
         <td class="label required"><label for="NEWSL_TPL_ID">{l}Newsletter Template{/l}:</label></td>
         <td class="smallDesc">
            {if $tpls}
               {html_options options=$tpls selected=$NEWSL_TPL_ID name="NEWSL_TPL_ID" id="NEWSL_TPL_ID"}
            {else}
               <p class="norec">{l}No newsletter templates{/l}</p>
            {/if}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="TEST_EMAIL">{l}Test Email{/l}:</label></td>
         <td class="smallDesc">
         	<input type="text" name="TEST_EMAIL" id="TEST_EMAIL" class="text" /><br />
         	Fill this in and the newsletter will be sent to only this email, not to all the subscribers.
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="NEWSL_TPL_ID">{l}Use Html?{/l}:</label></td>
         <td class="smallDesc">
               {html_options options=$yes_no selected=0 name="USE_HTML" id="USE_HTML"}
         </td>
      </tr>
      <tr>
         <td class="label required" colspan="2" align="center"><label>{l}Newsletter Statistics{/l}</label></td>
      </tr>
      <tr>
         <td class="label required"><label>{l}Total Emails{/l}:</label></td>
         <td class="smallDesc">
         	{$stats.TOTAL}
         </td>
      </tr>
      <tr>
         <td class="label required"><label>{l}Pending{/l}:</label></td>
         <td class="smallDesc">
         	{$stats.PENDING}
         </td>
      </tr>
      <tr>
         <td class="label required"><label>{l}Sent{/l}:</label></td>
         <td class="smallDesc">
         	{$stats.SENT}
         </td>
      </tr>
      <tr>
         <td class="label required"><label>{l}Failed{/l}:</label></td>
         <td class="smallDesc">
         	{$stats.FAILED}
         </td>
      </tr>
      <tr>
         <td class="label required"><label>{l}Status{/l}:</label></td>
         <td class="smallDesc">
         	{if $stats.PENDING > 0}
         		<span style="color: red;">A newsletter is being sent at the moment.</span>
         	{else}
         		There are no records of any newsletter being sent.
         	{/if}
         </td>
      </tr>
       <tr>
         <td class="label required"><label>{l}Note{/l}:</label></td>
         <td class="smallDesc">
         	
         		<span style="color: red;">A cron job must be set in order to send newsletters.<br />
			Cpanel example: ***** php -q /home/USERNAME/public_html/newsletter_queue.php
			</span>
         	
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
<input type="hidden" name="submit_session" value="{$submit_session}" />
</form>
{/strip}