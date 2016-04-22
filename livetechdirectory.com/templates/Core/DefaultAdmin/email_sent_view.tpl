{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
<form method="get" action="">

<div class="block">
   <table class="formPage">
   <thead>
      <tr><td colspan="2">{l}Filter sent emails view{/l}</td></tr>
   </thead>

   <tbody>
      <tr>
         <td class="label"><label>{l}Start Date{/l}</label></td>
         <td class="smallDesc">
            {html_select_date prefix="SD" time=$SD start_year="-5" end_year="+1"}
         </td>
      </tr>
      <tr>
         <td class="label"><label>{l}End Date{/l}</label></td>
         <td class="smallDesc">
            {html_select_date prefix="ED" time=$ED start_year="-5" end_year="+1"}
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-filter-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-filter-submit" name="filter" value="{l}Filter{/l}" alt="{l}Filter form{/l}" title="{l}Filter sent emails view{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
</div>

<div class="block">
   <table class="list">
   <thead>
      <tr>
         {foreach from=$columns key=col item=name}
            <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/email_sent_view.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
         {/foreach}
         </tr>
   </thead>

   <tbody>
   {foreach from=$list item=row key=id}
      <tr class="{cycle values="odd,even"}">
         {foreach from=$columns key=col item=name}
         <td>
            {if $col eq 'DATE_SENT'}
               {$row.$col|date_format:$date_format}
            {elseif $col eq 'URL'}
               {assign var="s" value=$row.VALID}
               <a class="http" id="URL-{$id}" href="{$row.$col|escape|trim}" target="_blank">{$row.$col|urlwrap:15:15:"..."|escape|trim}</a>
            {else}
               {$row.$col|escape|trim}
            {/if}
         </td>
         {/foreach}
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
</form>
{/strip}