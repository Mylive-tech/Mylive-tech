{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
{if $error}
<div class="block">
   <!-- Error -->
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{$error} {l}error(s) occured while processing.{/l}</p>
      {if !empty($errorMsg)}
         <p>{$errorMsg|escape}</p>
      {/if}
      {if !empty ($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p class="sql_error">{$sql_error}</p>
      {/if}
   </div>
   <!-- /Error -->
</div>
{/if}

<div class="block">
   <!-- Send Invoices -->
   <form method="post" name="send_invoices" id="send_invoices" action="{$smarty.const.DOC_ROOT}/conf_payment.php?action=I:0:0">
      <table class="list">
      <thead>
         <tr>
            <th colspan="2">{l}Send invoices{/l}</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td class="notice">
               <h3>{l}Send invoices to all people that have status{/l}: <span class="important">Pending</span></h3>
               <p>{l}With this tool you can remeber all users that already have submitted a link but payment was not received{/l}.</p>
            </td>
            <td><input type="submit" name="send" value="{l}Send invoices{/l}" title="{l}Send invoices{/l}" class="button" /></td>
         </tr>
      </tbody>
      </table>
      <input type="hidden" name="formSubmitted" value="1" />
   </form>
   <!-- /Send Invoices-->
</div>

{if isset($invoicesSent) and $invoicesSent eq 1}
   <div class="success block">
     <p>{$countInvoices} {l}invoices successfully sent{/l}!</p>
   </div>
{/if}

<div class="block">
   <table class="list">
      <thead>
         <tr>
            {foreach from=$columns key=col item=name}
               {if $col ne 'TITLE_URL'}
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/conf_payment.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th colspan="2" class="last-child">{l}Action{/l}</th>
         </tr>
      </thead>

      <tbody>
         {foreach from=$list item=row key=payment_id}
            {assign var="id" value=$row.ID}
            <tr class="{cycle values="odd,even"}">
               {foreach from=$columns key=col item=name}
                  {assign var="val" value=$row.$col}
                  {if $col eq 'P_UM'}
                     <td>{$payment_um.$val}
                  {elseif $col eq 'STATUS'}
                  <td class="status-{$val}">
                     <span class="link-status pop">{$stats[$val]|escape|trim}</span>
                     <div class="pop-status" id="pS{$payment_id}">
                        <h3 id="chgStat-{$payment_id}" class="chgStatTitle">{l}Change status{/l}</h3>
                        <ul id="list-status-{$payment_id}" class="list-status">
                        {foreach from=$stats item=v key=k}
                           {if $k ne $val}
                              <li><a href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=S:{$id}:{$k}" class="new-status-{$k}" title="{l}Click to change status to{/l}: {$stats[$k]|escape|trim}">{$stats[$k]|escape|trim}</a></li>
                           {/if}
                        {/foreach}
                        </ul>
                     </div>
                  {elseif $col eq 'TITLE'}
                     <td class="item-title">{$row.$col|escape|trim}<a href="{$smarty.const.DOC_ROOT}/link_details.php?id={$id}" title="{l}View full info of this item{/l}" id="more-info-{$id}" class="more-info" onclick="return false;"><span>{l}More info{/l}</span></a>
                  {elseif $col eq 'P_CONFIRMED'}
                   <td class="payment-{if $val eq -1}1{elseif $val eq 1}2{elseif $val eq 3}1{else}0{/if}"><span id="current-payment-{$id}" class="payment-status pop">{if $val eq -1}{l}Pending{/l}{elseif $val eq 2}{l}Paid{/l}{elseif $val eq 3}{l}Cancelled{/l}{else}{l}Uncleared{/l}{/if}</span>

                   <div class="pop-status" id="{$payment_id}000{$payment_id}">
                        <h3 class="chgStatTitle" id="chgStat-{$payment_id}000{$payment_id}">{l}Change status{/l}</h3>
                        <ul class="list-status" id="list-status-{$payment_id}000{$payment_id}">
                        {foreach from=$paystatus item=v key=k}
                           {if $k ne $val}
                              <li><a href="{$smarty.const.DOC_ROOT}/conf_payment.php?action=S:{$payment_id}:{$k}" class="new-status-{if $k eq 1}1{elseif $k eq 2}2{elseif $k eq 3}0{else}0{/if}" title="{l}Click to change status to{/l}: {$paystatus[$k]|escape|trim}">{$paystatus[$k]|escape|trim}</a></li>
                           {/if}
                        {/foreach}
                        </ul>
                     </div>
                  {elseif $col eq 'P_AMOUNT' || $col eq 'P_QUANTITY' || $col eq 'P_TOTAL' || $col eq 'P_PAYED_TOTAL'}
                     <td>{$row.$col|escape}
                  {else}
                     <td>{$row.$col|escape}
                  {/if}
               </td>
            {/foreach}
            <td class="noborder"><a id="edit-link-{$id}-payment-{$payment_id}" href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=E:{$id}" title="{l}Edit Link{/l}: {$row.TITLE|escape|trim}" class="action edit"><span>{l}Edit{/l}</span></a></td>
            <td class="noborder last-child"><a id="remove-payment-{$payment_id}" href="{$smarty.const.DOC_ROOT}/conf_payment.php?action=D:{$payment_id}" onclick="return payment_rm_confirm('{escapejs}{l}Are you sure you want to remove this payment?{/l}\n{l}Note: payment listings can not be restored after removal!{/l}{/escapejs}');" title="{l}Remove Payment{/l}" class="action delete"><span>{l}Delete{/l}</span></a></td>
         </tr>
      {foreachelse}
            <tr>
               <td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
            </tr>
       {/foreach}
      </tbody>
   </table>

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}
</div>
{/strip}