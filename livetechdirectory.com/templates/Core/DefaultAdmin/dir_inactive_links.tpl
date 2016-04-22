{* Error and confirmation messages *}
{include file="messages.tpl"}

<div class="block">
   <!-- Links Search Form -->
   <form action="{$smarty.const.DOC_ROOT}/dir_inactive_links.php" method="get">
      <table class="list search-box">
      <thead>
         <tr>
            <th class="listHeader" colspan="4">{l}Search Listings{/l}</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td id="searchbyid" {if empty($searchbyid)}class="hidden"{/if}>
               {l}ID{/l}:<input type="text" id="searchitemid" name="searchbyid" maxlength="255" value="{if !empty($searchbyid)}{$searchbyid|escape|trim}{/if}" class="text searchinput searchid" title="{l}Add ID to search for{/l}" />
            </td>
            <td>
               <input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" />
               <p><a href="{$smarty.const.DOC_ROOT}/dir_inactive_links.php" title="{l}Search items by ID{/l}" onclick="togglesearchbyid(); return false;"><span>{l}Search by ID{/l}</span></a></p>
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
   <!-- /Links Search Form -->
</div>

{if $error}
<div class="block">
   <!-- Error -->
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{$error} {l}error(s) occured while processing.{/l}</p>
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p class="sql_error">{$sql_error}</p>
      {/if}
   </div>
   <!-- /Error -->
</div>
{/if}

<div class="block">
   <form action="{$smarty.const.DOC_ROOT}/dir_inactive_links.php" method="post" id="multiselect_list" name="multiselect_list">
   <input type="hidden" name="submitAction" id="submitAction" value="" />
   <table class="list">
      <thead>
         <tr>
            <th id="select" class="first-child"></th>
            {foreach from=$columns key=col item=name}
               {if $col ne 'TITLE_URL' AND $col neq 'PAGERANK' }
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_inactive_links.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th colspan="2" class="last-child">{l}Action{/l}</th>
         </tr>
      </thead>

      <tbody>
      {foreach from=$list item=row key=id}
         <tr class="{cycle values="odd,even"}">
         <!-- {if $row.FEATURED eq '1'} featured{/if} -->
            <td class="first-child">
               <label><input type="checkbox" name="multiselect_checkbox[]" value="{$id}" title="{l}Check box to select this item.{/l}" /></label>
            </td>
            {foreach from=$columns key=col item=name}
               {assign var="val" value=$row.$col}
               {if $col eq 'STATUS'}
                  <td class="status-{$val}">
                     <span id="current-status-{$id}" class="pop">{$stats[$val]|escape|trim}</span>
                     <div class="pop-list" id="pS{$id}">
                        <h3>{l}Set new status{/l}:</h3>
                        <ul class="list-status">
                        {foreach from=$stats item=v key=k}
                           {if $k ne $val}
                              <li><a href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=S:{$id}:{$k}" class="status-{$k}" title="{l}Click to change status to{/l}: {$stats[$k]|escape|trim}">{$stats[$k]|escape|trim}</a></li>
                           {/if}
                        {/foreach}
                        </ul>
                     </div>
               {elseif $col eq 'LINK_TYPE'}
                  <td>{$link_type_str.$val}
               {elseif $col eq 'PAGERANK'}
                  {*<td>{if $row.$col eq -1}N/A{else}{$row.$col}{/if}*}
               {elseif $col eq 'TITLE'}
                  <td class="item-title">
					{$row.$col|escape|trim}
					{if ($row.FEATURED === '1')}
					<span class="featured_label">FEATURED</span>
					{/if}
					<a href="{$smarty.const.DOC_ROOT}/link_details.php?id={$id}" title="{l}View full info of this item{/l}" id="more-info-{$id}" class="more-info" onclick="return false;"><span>{l}More info{/l}</span></a>
               {elseif $col eq 'URL'}
                  {assign var="s" value=$row.VALID}
                  <td class="valid-{$s}">
                     <a class="http" id="URL-{$id}" href="{$row.$col|escape|trim}" target="_blank">{$row.$col|urlwrap:10:10:"..."|escape|trim}</a>
                     <span id="text-URL-{$id}" class="full-url-text">{$row.$col|escape|trim}</span>
               {elseif $col eq 'RECPR_URL'}
                  {assign var="s" value=$row.RECPR_VALID}
                  <td class="valid-URL-{$s}">
                     <a class="rhttp" id="RURL-{$id}" href="{$row.$col|escape|trim}" target="_blank">{$row.$col|urlwrap:10:10:"..."|escape|trim}</a>
                     <span id="text-rURL-{$id}" class="full-url-text">{$row.$col|escape|trim}</span>
               {elseif $col eq 'DATE_ADDED'}
                  <td>{$row.$col|date_format:$date_format}
               {elseif $col eq 'CATEGORY'}
                  <td>{if $row.CATEGORY_ID eq '-1'}<span class="orphan">{l}Orphan{/l}</span>{else}<a id="category-{$row.CATEGORY_ID}" href="{$smarty.const.DOC_ROOT}/dir_inactive_links.php?category={$row.CATEGORY_ID}" title="{l}Browse listings of category{/l}: {$row.$col|escape|trim}" class="category-link" onclick="return false;">{$row.$col|escape|trim}</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id={$row.CATEGORY_ID}" title="{l}View full info of this item{/l}" id="more-info-categ-{$row.CATEGORY_ID}" class="more-info-categ" onclick="return false;"><span>{l}More info{/l}</span></a>{/if}
               {elseif $col eq 'OWNER_EMAIL_CONFIRMED'}
                  <td>{if $row.OWNER_EMAIL_CONFIRMED eq '1'}<span style="color: green;">{l}YES{/l}</span>{else}<span style="color: red;">NO</span>{/if}
	       {elseif $col eq 'DESCRIPTION'}
                  <td style="width:100px;word-wrap:break-word;" width="100px">{$row.$col}
{else}
                  <td>{$row.$col|escape|trim}
               {/if}
               </td>
            {/foreach}
            <td class="noborder"><a id="edit-link-{$id}" href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=E:{$id}" title="{l}Edit Listing{/l}: {$row.TITLE|escape|trim}" class="action edit"><span>{l}Edit{/l}</span></a></td>
            <td class="noborder last-child"><a id="remove-link-{$id}" href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=D:{$id}" onclick="return link_rm_confirm('{l}Are you sure you want to remove this listing?{/l}\n{l}Note: listings can not be restored after removal!{/l}');" title="{l}Remove Listings{/l}: {$row.TITLE|escape|trim}" class="action delete"><span>{l}Delete{/l}</span></a></td>
         </tr>
         {foreachelse}
            <tr>
               <td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
            </tr>
         {/foreach}
      </tbody>
      </table>

      {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}

      {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/link_action_ajax.tpl" linkTypeButtons="1" linkNotifButtons=$expired}
   </form>
   <!-- /Links List -->
</div>
