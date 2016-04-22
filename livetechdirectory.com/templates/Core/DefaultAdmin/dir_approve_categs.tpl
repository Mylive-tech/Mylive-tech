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
   <!-- Category List -->
   <form action="{$smarty.const.DOC_ROOT}/dir_approve_categs.php" method="post" id="multiselect_list" name="multiselect_list">
      <input type="hidden" name="submitAction" id="submitAction" value="" />
      <table class="list">
      <thead>
         <tr>
            <th id="select" class="first-child"></th>
            {foreach from=$columns key=col item=name}
               {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_approve_categs.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th colspan="3" class="last-child">{l}Action{/l}</th>
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
                  {elseif $col eq 'TITLE'}
                     <a id="category-{$id}" href="{$smarty.const.DOC_ROOT}/dir_links.php?category={if $row.SYMBOLIC ne 1}{$id}{else}{$row.SYMBOLIC_ID}{/if}" title="{$row.CACHE_TITLE|escape|trim}" class="category-link" onclick="return false;">{$row.$col|wordwrap:20:" ":1|escape|trim}</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id={$id}" title="{l}View full info of this item{/l}" id="more-info-categ-{$id}" class="more-info-categ" onclick="return false;"><span>{l}More info{/l}</span></a>
                  {elseif $col eq 'TITLE_URL'}
                     {$row.$col|wordwrap:20:" ":1|escape|trim}
                  {elseif $col eq 'SYMBOLIC'}
                     {assign var="sy" value=$row.$col}
                     {$symb[$sy]}
                  {elseif $col eq 'PARENT'}
                     {if $row.PARENT_ID eq '-1'}<span class="orphan">{l}Orphan{/l}</span>{else}<a id="parent-category-{$row.PARENT_ID}" href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={$row.PARENT_ID}" title="{$row.PARENT|escape|trim}" class="category-link" onclick="return false;">{$row.$col|wordwrap:20:" ":1|escape|trim}</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id={$row.PARENT_ID}" title="{l}View full info of this item{/l}" id="more-info-pcateg-{$row.PARENT_ID}" class="more-info-categ" onclick="return false;"><span>{l}More info{/l}</span></a>{/if}
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
            <td class="noborder"><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=A:{$id}" title="{l}Approve Category{/l}: {$row.TITLE}" class="action approve"><span>{l}Approve{/l}</span></a></td>
            <td class="noborder"><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?s={$row.SYMBOLIC}&amp;action=E:{$id}" title="{l}Edit Category{/l}: {$row.TITLE|escape|trim}" class="action edit"><span>{l}Edit{/l}</span></a></td>
            <td class="noborder last-child"><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=D:{$id}" onclick="return categ_rm_confirm('{l}Are you sure you want to remove this category?{/l}\n{l}Note: categories can not be restored after removal!{/l}');" title="{l}Remove Category{/l}: {$row.TITLE|escape|trim}" class="action delete"><span>{l}Delete{/l}</span></a></td>
         </tr>
      {foreachelse}
         <tr>
            <td colspan="{if $ENABLE_REWRITE}11{else}10{/if}" class="norec">{l}No records found.{/l}</td>
         </tr>
      {/foreach}
      </tbody>
      </table>

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/categ_action_btn.tpl"}
   </form>
   <!-- /Category List -->
</div>
{/strip}