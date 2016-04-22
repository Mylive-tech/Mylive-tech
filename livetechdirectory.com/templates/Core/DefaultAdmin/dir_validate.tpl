{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
{if $start ne 1}
   {if $error eq 1}<span class="errForm">{l}Please select at least one action to perform.{/l}</span>{/if}

   {if $finished eq 1}
      <div class="success">
         <p>{l}Validation of links finished{/l}</p>
         {if $expired_recpr gt 0}
            <p>{l}Expired reciprocal link pages{/l}: {$expired_recpr}</p>
            <p><a href="{$smarty.const.DOC_ROOT}/dir_links.php?r=1&amp;expired=1" class="button"><span class="expired-link">{l}Browse{/l}</span></a></p>
         {/if}
      </div>
   {/if}

<div class="block">
<form method="post" action="">
   <table class="formPage">
   <thead>
      <tr>
         <th colspan="2">{l}Validate Links{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label required"><label>{l}Select category{/l}:</label></td>
         <td class="smallDesc">
            {* Load category selection *}
            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" validate="1"}
         </td>
      </tr>

      <tr class="thead">
         <th colspan="2">{l}Links{/l}</th>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label required"><label for="VALIDATE_LINKS">{l}Check Links{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" id="VALIDATE_LINKS" name="VALIDATE_LINKS" value="1"{if $VALIDATE_LINKS} checked="checked"{/if} />
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Set inactive if broken{/l}:</label></td>
         <td class="smallDesc">
            {html_checkboxes name="IL" options=$stat_inactive selected=$IL separator=" "}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Set active if valid{/l}:</label></td>
         <td class="smallDesc">
            {html_checkboxes name="AL" options=$stat_active selected=$AL separator=" "}
         </td>
      </tr>

      <tr class="thead">
         <th colspan="2">{l}Reciprocal Links{/l}</th>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label required"><label for="VALIDATE_RECPR">{l}Check Reciprocal Links{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" id="VALIDATE_RECPR" name="VALIDATE_RECPR" value="1"{if $VALIDATE_RECPR} checked="checked"{/if} />
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Set inactive if broken{/l}: {l}(only if recpr. required){/l}</label></td>
         <td class="smallDesc">
            {html_checkboxes name="IR" options=$stat_inactive selected=$IR separator=" "}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Set active if valid{/l}: {l}(only if link not broken){/l}</label></td>
         <td class="smallDesc">
            {html_checkboxes name="AR" options=$stat_active selected=$AR separator=" "}
         </td>
      </tr>

      <tr class="thead">
         <th colspan="2">{l}Owner Notification{/l}</th>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Send status notifications to link owners{/l}:</label></td>
         <td class="smallDesc">
            {html_radios name="send_notification" options=$send_notif_options selected=$send_notification separator="&nbsp;"}
         </td>
      </tr>

      <tr class="thead">
         <th colspan="2">{l}Links per cycle{/l}</th>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="range">{l}Number of links to process per cycle{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="range" name="range" value="{$range}" maxlength="10" class="text" />
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="delay">{l}Delay for auto jumping to next cycle{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="delay" name="delay" value="{$delay}" maxlength="5" class="text" />
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-validation-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-validation-submit" name="start" value="{l}Start{/l}" alt="{l}Start validation{/l}" title="{l}Start validation{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
</form>
</div>

{else}

<div class="progbar"></div>
<div class="progspacer"></div>

<div class="block">
   <table class="list">
   <thead>
      <tr>
      {foreach from=$columns key=col item=name name=cols}
         <th id="{$col}">
            {$name|escape|trim}
         </th>
      {/foreach}
      </tr>
   </thead>
   <tbody>
      <!--Progressbar-->
   </tbody>
   </table>
</div>
<br /><br /><br /><br /><br /><br />
{$nextUrl}
{/if}
{/strip}