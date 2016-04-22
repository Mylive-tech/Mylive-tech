{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
<div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
   	  {if $rights.addLink eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=N{if $featured}&amp;f=1{/if}" title="{l}Create new link{/l}" class="button"><span class="new-link">{l}New Link{/l}</span></a></li>
	  {/if}
      {if $HaveExpiredRecpr gt 0}
         <li><a href="{$smarty.const.DOC_ROOT}/dir_links.php?expired=1{if $feat_link}&amp;f=1{/if}{if $categ gt 0}&amp;category={$categ}{/if}" title="{l}Browser listings with expired reciprocal URL{/l}" class="button"><span class="expired-link">{l}Expired Reciprocal{/l}</span></a></li>
      {/if}
      {if $rights.editLink eq 1}
      {if $rss_link eq true}
         <li><a href="{$smarty.const.DOC_ROOT}/dir_links_importrss.php?category={$rsscategory}" title="{l}Import from RSS{/l}" class="button"><span class="import-rss-link">{l}Import RSS{/l}</span></a></li>
      {/if}
      {/if}
      {if $rights.delLink eq 1}
      {if $HaveExpiredEmail gt 0}
         <li>
            <a href="{$smarty.const.DOC_ROOT}/dir_links.php?action=del_expired_emails" title="{l}Delete links with unconfirmed emails older than {/l}{$smarty.const.WAIT_FOR_EMAIL_CONF}{l} days{/l}" class="button" onclick="return link_rm_confirm('{l}Are you sure you want to remove {/l}{$HaveExpiredEmail}{l} links?{/l}\n{l}Note: links can not be restored after removal!{/l}');">
         <span class="expired-link">{l}Delete Expired{/l}</span></a></li>
      {/if}
      {/if}
      
   </ul>
   <!-- /Action Links -->
</div>

<div class="block">
   <!-- Links Search Form -->
   <form action="{$smarty.const.DOC_ROOT}/dir_links.php" method="get">
      <table class="list search-box">
      <thead>
         <tr>
            <th class="listHeader" colspan="5">{l}Search Links{/l}</th>
         </tr>
      </thead>
      <tbody>
         <tr class="{cycle values="odd,even"}">
            <td>
               <input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" />
               {if $feat_link eq 1}<input type="hidden" name="f" value="1" readonly="true" />{/if}
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
   <form action="{$smarty.const.DOC_ROOT}/dir_links.php" method="post" id="multiselect_list" name="multiselect_list">
      <input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" />
      <table class="list">
      <thead>
         <tr>
            <th id="select" class="first-child"></th>
            {foreach from=$columns key=col item=name}
               {if $col ne 'TITLE_URL'}
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_links.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th colspan="2" class="last-child">{l}Action{/l}</th>
         </tr>
      </thead>

      <tbody>
      {foreach from=$list item=row key=id}
         <tr id="link-row-{$id}" class="{cycle values="odd,even"}">
             <!--  {if $row.FEATURED eq '1'}featured{/if} -->
            <td class="first-child">
               <label><input type="checkbox" name="multiselect_checkbox[]" value="{$id}" title="{l}Check box to select this item.{/l}" /></label>
            </td>
            {foreach from=$columns key=col item=name}
               {assign var="val" value=$row.$col}
               {if $col eq 'STATUS'}
                  <td class="status-{$val}">
                     <span id="current-status-{$id}" class="link-status pop">{$stats[$val]|escape|trim}</span>
                     {if $rights.editLink eq 1}
                     <div class="pop-status" id="pS{$id}">
                        <h3 id="chgStat-{$id}" class="chgStatTitle">{l}Change status{/l}</h3>
                        <ul id="list-status-{$id}" class="list-status">
                        {foreach from=$stats item=v key=k}
                           {if $k ne $val}
                              <li><a href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=S:{$id}:{$k}" class="new-status-{$k}" title="{l}Click to change status to{/l}: {$stats[$k]|escape|trim}">{$stats[$k]|escape|trim}</a></li>
                           {/if}
                        {/foreach}
                        </ul>
                     </div>
                     {/if}
               {elseif $col eq 'LINK_TYPE'}
                  <td>{$link_type_str.$val}
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
                     <a class="http" id="URL-{$id}" href="{$row.$col|escape|trim}" target="_blank">{$row.$col|urlwrap:15:15:"..."|escape|trim}</a>
               {elseif $col eq 'PAGERANK'}
                  <td>{if $row.$col eq -1}N/A{else}{$row.$col}{/if}
               {elseif $col eq 'DATE_ADDED'}
                  <td>{$row.$col|date_format:$date_format}
               {elseif $col eq 'CATEGORY'}
                  <td>{if $row.CATEGORY_ID eq '-1'}<span class="orphan">{l}Orphan{/l}</span>{else}<a id="category-{$row.CATEGORY_ID}" href="{$smarty.const.DOC_ROOT}/dir_links.php?category={$row.CATEGORY_ID}" title="{l}Browse links of category{/l}: {$row.$col|escape|trim}" class="category-link" onclick="return false;">{$row.$col|escape|trim}</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id={$row.CATEGORY_ID}" title="{l}View full info of this item{/l}" id="more-info-categ-{$row.CATEGORY_ID}" class="more-info-categ" onclick="return false;"><span>{l}More info{/l}</span></a>{/if}
               {elseif $col eq 'OWNER_EMAIL_CONFIRMED'}
                  <td>{if $row.OWNER_EMAIL_CONFIRMED eq '1'}<span style="color: green;">{l}YES{/l}</span>{else}<span style="color: red;">NO</span>{/if}
               {else}
                  <td>{$row.$col|escape|trim}
               {/if}
               </td>
            {/foreach}
            {if $rights.editLink eq 0 && $rights.delLink eq 0}
   	  		<td class="noborder last-child" colspan="2">None allowed</td>
   	  		{else}
            <td class="noborder">
            {if $rights.editLink eq 1}
            <a id="edit-link-{$id}" href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=E:{$id}" title="{l}Edit Link{/l}: {$row.TITLE|escape|trim}" class="action edit"><span>{l}Edit{/l}</span></a>
            {/if}
            </td>
            <td class="noborder last-child">
            {if $rights.delLink eq 1}
            <a id="remove-link-{$id}" href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=D:{$id}" onclick="return link_rm_confirm('{l}Are you sure you want to remove this link?{/l}\n{l}Note: links can not be restored after removal!{/l}');" title="{l}Remove Link{/l}: {$row.TITLE|escape|trim}" class="action delete"><span>{l}Delete{/l}</span></a>
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
{if $rights.editLink neq 0 || $rights.delLink neq 0}
      {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/link_action_ajax.tpl" linkTypeButtons="1" linkNotifButtons=$expired}
{/if}
   </form>
   <!-- /Links List -->
</div>
{/strip}
