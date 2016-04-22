{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}

{if $error}
<div class="block">
   <!-- Error -->
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{$error} {l}error(s) occured while processing.{/l}</p>
      {if !empty ($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p class="sql_error">{$sql_error}</p>
      {/if}
   </div>
   <!-- /Error -->
</div>
{/if}

<div class="block">
   <!-- Comment List -->
   <form action="{$smarty.const.DOC_ROOT}/dir_approve_link_comments.php" method="post" id="multiselect_list" name="multiselect_list">
      <input type="hidden" name="submitAction" id="submitAction" value="" />
      <table class="list">
      <thead>
         <tr>
            <th id="select" class="first-child"></th>
            {foreach from=$columns key=col item=name}
               {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_approve_link_comments.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th colspan="2" class="last-child">{l}Action{/l}</th>
         </tr>
      </thead>

      <tbody>
      {foreach from=$list item=row key=id}
         <tr class="{cycle values="odd,even"}">
            <td class="first-child">
               <label><input type="checkbox" name="multiselect_checkbox[]" value="{$id}" title="{l}Check box to select this item.{/l}" /></label>
            </td>
            {foreach from=$columns key=col item=name}
               {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
                  <td {if $col eq 'STATUS'}class="status-{$row.$col}"{/if}>
                  {if $col eq 'STATUS'}
                     {assign var="s" value=$row.$col}
                     {$stats[$s]}
                  {elseif $col eq 'DATE_ADDED'}
                     {$row.$col|date_format:$date_format}
                  {elseif $col eq 'COMMENT'}
                     {$row.$col|wordwrap:40:" ":1|escape|trim}
                  {else}
                     {$row.$col|escape|trim}
                  {/if}
                  </td>
               {/if}
            {/foreach}
            <td class="noborder"><a href="{$smarty.const.DOC_ROOT}/dir_link_comments_action.php?action=A:{$id}" title="{l}Approve Comment{/l}: {$row.TITLE}" class="action approve"><span>{l}Approve{/l}</span></a></td>
            <td class="noborder last-child"><a href="{$smarty.const.DOC_ROOT}/dir_link_comments_action.php?action=D:{$id}" onclick="return categ_rm_confirm('{l}Are you sure you want to remove this comment?{/l}\n{l}Note: comments can not be restored after removal!{/l}');" title="{l}Remove Comment{/l}: {$row.TITLE|escape|trim}" class="action delete"><span>{l}Delete{/l}</span></a></td>
         </tr>
      {foreachelse}
         <tr>
            <td colspan="{if $ENABLE_REWRITE}10{else}9{/if}" class="norec">{l}No records found.{/l}</td>
         </tr>
      {/foreach}
      </tbody>
      </table>

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/comment_action_btn.tpl"}
   </form>
   <!-- /Comment List -->
</div>
{/strip}