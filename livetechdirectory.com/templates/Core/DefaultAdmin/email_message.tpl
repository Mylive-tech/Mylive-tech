{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
<div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/email_message_edit.php?action=N" title="{l}Create new email template{/l}" class="button"><span class="new-email-tpl">{l}New Email Template{/l}</span></a></li>
   </ul>
   <!-- /Action Links -->
</div>

<div class="block">
   <table class="list">
      <thead>
         <tr>
            {foreach from=$columns key=col item=name}
               <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/email_message.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
            {/foreach}
            <th colspan="2" class="last-child">{l}Action{/l}</th>
         </tr>
      </thead>

      <tbody>
         {foreach from=$list item=row key=id}
         <tr class="{cycle values="odd,even"}">
            {foreach from=$columns key=col item=name}
               {assign var="val" value=$row.$col}
               <td>
                  {if $col eq 'TPL_TYPE'}
                     {$tpl_types.$val|escape|trim}
                  {else}
                     {$row.$col|escape|trim}
                  {/if}
               </td>
            {/foreach}

            <td><a href="{$smarty.const.DOC_ROOT}/email_message_edit.php?action=E:{$id}" title="{l}Edit email template{/l}: {$row.TITLE|escape|trim}" class="action edit"><span>{l}Edit{/l}</span></a></td>
            <td class="last-child"><a href="{$smarty.const.DOC_ROOT}/email_message_edit.php?action=D:{$id}" onclick="return email_tpl_rm_confirm('{escapejs}{l}Are you sure you want to remove this email template?{/l}\n{l}Note: email templates can not be restored after removal!{/l}{/escapejs}');" title="{l}Remove email template{/l}: {$row.TITLE|escape|trim}" class="action delete"><span>{l}Delete{/l}</span></a></td>
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