{strip}
{if $email_send_errors and not $posted}
<div class="warning">
   <h2>Warning!</h2>
   {if count($email_send_errors.dir) gt 0}
      <h3>{l}Directory{/l}</h3>
      <ul>
      {foreach from=$email_send_errors.dir item=row}
         <li>{if $row eq 'URL'}{l}A link with this URL already exists in the directory;{/l}{else}{l}A link with this title already exists in the directory{/l}{/if}</li>
      {/foreach}
      </ul>
   {/if}

   {if count($email_send_errors.email) gt 0}
      <h3>{l}Emails{/l}</h3>
      <ul>
      {foreach from=$email_send_errors.email item=row}
         <li>
            {if $row.TYPE eq 'EMAIL'}
               {$email_sent_errors_EMAIL|escape|replace:'#DATE#':$row.DATE|replace:'#URL#':$row.URL|replace:'#TITLE#':$row.TITLE}
            {elseif $row.TYPE eq 'URL'}
               {$email_sent_errors_URL|escape|replace:'#DATE#':$row.DATE|replace:'#EMAIL#':$row.EMAIL|replace:'#TITLE#':$row.TITLE}
            {elseif $row.TYPE eq 'TITLE'}
               {$email_sent_errors_TITLE|escape|replace:'#DATE#':$row.DATE|replace:'#URL#':$row.URL|replace:'#EMAIL#':$row.EMAIL}
            {/if}
         </li>
      {/foreach}
      </ul>
   {/if}

   <p>{l}Ignore the warning(s)?{/l}</p>
   <p>{html_radios options=$yes_no name="IGNORE" checked=$IGNORE separator="<br />"}</p>
</div>
{/if}
{/strip}