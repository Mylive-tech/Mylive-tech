{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
<div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
   {if $rights.addPage eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=N" title="{l}New Page{/l}" class="button"><span class="new-image">{l}New Page{/l}</span></a></li>
   {/if}
   </ul>
   <!-- /Action Links -->
</div>

<div class="block">
   <!-- Image Search Form -->
   <form action="{$smarty.const.DOC_ROOT}/dir_pages.php" method="get">
      <table class="list search-box">
      <thead>
         <tr>
            <th class="listHeader" colspan="4">{l}Search in pages{/l}</th>
         </tr>
      </thead>
      <tbody>
         <tr class="{cycle values="odd,even"}">
            <td>
               <input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" />
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
   <!-- /Image Search Form -->
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
   <!-- Links List -->
   <form action="{$smarty.const.DOC_ROOT}/dir_pages.php" method="post" id="multiselect_list" name="multiselect_list">
      <input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" />
      <table class="list">
      <thead>
         <tr>
            <th id="select" class="first-child"></th>
            {foreach from=$columns key=col item=name}
               {if $col ne 'TITLE_URL'}
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_pages.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th colspan="2" class="last-child">{l}Action{/l}</th>
         </tr>
      </thead>

      <tbody>
      {foreach from=$list item=row key=id}
         <tr id="link-row-{$id}" class="{cycle values="odd,even"} {if $row.FEATURED eq '1'}featured{/if}">
            <td class="first-child">
               <label><input type="checkbox" name="multiselect_checkbox[]" value="{$id}" title="{l}Check box to select this item.{/l}" /></label>
            </td>
            {foreach from=$columns key=col item=name}
               {assign var="val" value=$row.$col}
               {if $col eq 'STATUS'}
                  <td class="status-{$val}">
                     <span id="current-status-{$id}" class="link-status pop">{$stats[$val]|escape|trim}</span>
                     {if $rights.editPage eq 1}
                     <div class="pop-status" id="pS{$id}">
                        <h3 id="chgStat-{$id}" class="chgStatTitle">{l}Change status{/l}</h3>
                        <ul id="list-status-{$id}" class="list-status">
                        {foreach from=$stats item=v key=k}
                           {if $k ne $val}
                              <li><a href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=S:{$id}:{$k}" class="new-status-{$k}" title="{l}Click to change status to{/l}: {$stats[$k]|escape|trim}">{$stats[$k]|escape|trim}</a></li>
                           {/if}
                        {/foreach}
                        </ul>
                     </div>
                     {/if}
               {elseif $col eq 'DATE_ADDED'}
                  <td>{$row.$col|date_format:$date_format}
               {elseif $col eq 'SEO_NAME'}
                  <td><a href="{$smarty.const.SITE_URL}{$row.$col}" target="_blank">{$smarty.const.SITE_URL}{$row.$col}</a>
               {elseif $col eq 'PRIVACY'}
                  <td>{$privacy[$val]|escape|trim}
               {else}
                  <td>{$row.$col|escape|trim}
               {/if}
               </td>
            {/foreach}
            {if $rights.editPage eq 0 && $rights.delPage eq 0}
   	  		<td class="noborder last-child" colspan="2">None allowed</td>
   	  		{else}
            <td class="noborder">
            {if $rights.editPage eq 1}
            <a id="edit-link-{$id}" href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=E:{$id}" title="{l}Edit Page{/l}: {$row.TITLE|escape|trim}" class="action edit"><span>{l}Edit{/l}</span></a>
            {/if}
            </td>
            <td class="noborder last-child">
            {if $rights.delPage eq 1}
            <a id="remove-link-{$id}" href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=D:{$id}" onclick="return link_rm_confirm('{l}Are you sure you want to remove this page?{/l}\n{l}Note: pagess can not be restored after removal!{/l}');" title="{l}Remove Pages{/l}: {$row.TITLE|escape|trim}" class="action delete"><span>{l}Delete{/l}</span></a>
            {/if}
            </td>
            {/if}
         </tr>
         {foreachelse}
            <tr>
               <td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
            </tr>
         {/foreach}
      </tbody>
      </table>

      {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}

   </form>
   <!-- /pages List -->
</div>
{/strip}
