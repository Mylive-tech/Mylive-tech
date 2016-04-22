{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}

<div class="block">
   <!-- Comment Search Form -->
   <form action="{$smarty.const.DOC_ROOT}/dir_link_comments.php" method="get">
      <table class="list search-box">
      <thead>
         <tr>
            <th class="listHeader" colspan="4">{l}Search Comments{/l}</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td id="searchbyid" {if empty($searchbyid)}class="hidden"{/if}>
               {l}ID{/l}:<input type="text" id="searchitemid" name="searchbyid" maxlength="255" value="{if !empty($searchbyid)}{$searchbyid|escape|trim}{/if}" class="text searchinput searchid" title="{l}Add ID to search for{/l}" />
            </td>
            <td>
               <input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" />
               <p><a href="{$smarty.const.DOC_ROOT}/dir_link_comments.php" title="{l}Search items for ID{/l}" onclick="togglesearchbyid(); return false;"><span>{l}Search by ID{/l}</span></a></p>
            </td>
            <td>
               <input type="submit" value="{l}Search{/l}" title="{l}Click to start search{/l}" class="button" />
            </td>
            <td class="notice">
               {$min_keyword_length_comment}
            </td>
         </tr>
      </tbody>
      </table>
   </form>
   <!-- /Comment Search Form -->
</div>

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
   <form action="{$smarty.const.DOC_ROOT}/dir_link_comments.php" method="post" id="multiselect_list" name="multiselect_list">
      <input type="hidden" name="submitAction" id="submitAction" value="" />
      <table class="list">
      <thead>
         <tr>
            <th id="select" class="first-child"></th>
            {foreach from=$columns key=col item=name}
               {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_link_comments.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th class="last-child">{l}Action{/l}</th>
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
                  {elseif $col eq 'DESCRIPTION'}
                     {$row.$col|wordwrap:40:" ":1|escape|trim}
                  {else}
                     {$row.$col|escape|trim}
                  {/if}
                  </td>
               {/if}
            {/foreach}
            <td class="noborder last-child"><a id="remove-comment-{$id}" href="{$smarty.const.DOC_ROOT}/dir_link_comments_action.php?action=D:{$id}" onclick="return categ_rm_confirm('{escapejs}{l}Are you sure you want to remove this comment?{/l}\n{l}Note: comments can not be restored after removal!{/l}{/escapejs}');" title="{l}Remove Comment{/l}: {$row.TITLE|escape|trim}" class="action delete"><span>{l}Delete{/l}</span></a></td>
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