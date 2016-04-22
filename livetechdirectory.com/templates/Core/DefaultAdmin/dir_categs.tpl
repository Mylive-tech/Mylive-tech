{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}

{if $action ne 'rebuild_cache'}
{if $err eq 1}
	<div class="success block">
		{$succesMsg}
	</div>
{elseif isset($err)}
	<div class="error block">
		{$errMsg}
	</div>
{/if}
<div class="block">
   <!-- Category Action Links -->
   <ul class="page-action-list">
   	  {if $rights.addCat eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=N" title="{l}Create a new category{/l}" class="button"><span class="new-categ">{l}New Category{/l}</span></a></li>
  <!--    <li><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=N&amp;s=1" title="{l}Create a new symbolic category{/l}" class="button"><span class="new-symb-categ">{l}New Symbolic Category{/l}</span></a></li> -->
      {/if}
      {if $rights.addCat eq 1 || $rights.editCat eq 1 || $rights.delCat eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?action=rebuild_cache" title="{l}Rebuild Category Cache{/l}" class="button"><span class="rebuild-categ-cache">{l}Rebuild Category Cache{/l}</span></a></li>
      {/if}
      {if $rights.addCat eq 1 || $rights.editCat eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?action=update_RSS" title="{l}Update RSS feeds for all categories{/l}" class="button"><span class="update-rss">{l}Update RSS feeds{/l}</a></li></span>
      {/if}
   </ul>
   <!-- /Category Action Links -->
</div>

<div class="block">
   <!-- Category Search Form -->
   <form action="{$smarty.const.DOC_ROOT}/dir_categs.php" method="get">
      <table class="list search-box">
      <thead>
         <tr>
            <th class="listHeader" colspan="4">{l}Search Categories{/l}</th>
         </tr>
      </thead>
      <tbody>
         <tr>
         	<!-- 
            <td id="searchbyid" {if empty($searchbyid)}class="hidden"{/if}>
               {l}ID{/l}:<input type="text" id="searchitemid" name="searchbyid" maxlength="255" value="{if !empty($searchbyid)}{$searchbyid|escape|trim}{/if}" class="text searchinput searchid" title="{l}Add ID to search for{/l}" />
            </td>
            -->
            <td>
               <input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" />
               <!-- <p><a href="{$smarty.const.DOC_ROOT}/dir_categs.php" title="{l}Search items for ID{/l}" onclick="togglesearchbyid(); return false;"><span>{l}Search by ID{/l}</span></a></p>-->
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
   <!-- /Category Search Form -->
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
   <!-- Category List -->
   <form action="{$smarty.const.DOC_ROOT}/dir_categs.php" method="post" id="multiselect_list" name="multiselect_list">
      <input type="hidden" name="submitAction" id="submitAction" value="" />
      <table class="list">
      <thead>
         <tr>
            <th id="select" class="first-child"></th>
            {foreach from=$columns key=col item=name}
               {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
                  <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_categs.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
            {/foreach}
            <th colspan="4" class="last-child">{l}Action{/l}</th>
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
                  {elseif $col eq 'PARENT'}
                     {if $row.PARENT_ID eq '-1'}<span class="orphan">{l}Orphan{/l}</span>{else}<a id="parent-category-{$row.PARENT_ID}" href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={$row.PARENT_ID}" title="{$row.PARENT|escape|trim}" class="category-link" onclick="return false;">{$row.$col|wordwrap:20:" ":1|escape|trim}</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id={$row.PARENT_ID}" title="{l}View full info of this item{/l}" id="more-info-pcateg-{$row.PARENT_ID}" class="more-info-categ" onclick="return false;"><span>{l}More info{/l}</span></a>{/if}
                  {elseif $col eq 'SYMBOLIC'}
                     {assign var="sy" value=$row.$col}
                     {$symb[$sy]}
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
            <td class="noborder">
            	{if $rights.editCat eq 1}
            	<a id="edit-categ-{$id}" href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?s={$row.SYMBOLIC}&amp;action=E:{$id}" title="{l}Edit Category{/l}: {$row.TITLE|escape|trim}" class="action edit">
            	<span>{l}Edit{/l}</span>
            	</a>
            	{/if}
            </td>
             <td class="noborder">
             	{if $rights.editCat eq 1}
            	<a id="close-categ-{$id}" href="{$smarty.const.DOC_ROOT}/dir_categs_close.php?id={$id}" title="{l}Close Category to Activities{/l}: {$row.TITLE|escape|trim}" class="action close">
            	<span>{l}Close to activities{/l}</span>
            	</a>
            	{/if}
            </td>
            <td class="noborder">
            	<a id="view-categ-{$id}" href="{$smarty.const.DIRECTORY_ROOT}/{$row.CACHE_URL|escape}" title="{l}View Category in Frontend{/l}: {$row.TITLE|escape|trim}" class="action view" target="_blank">
            	<span>{l}View in Frontend{/l}</span>
            	</a>
            </td>
            <td class="noborder last-child">
            	{if $rights.delCat eq 1}
            	<a id="remove-categ-{$id}" href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=D:{$id}" onclick="return categ_rm_confirm('{l}Are you sure you want to remove this category?{/l}\n{l}Note: categories can not be restored after removal!{/l}');" title="{l}Remove Category{/l}: {$row.TITLE|escape|trim}" class="action delete">
            	<span>{l}Delete{/l}</span>
            	</a>
            	{/if}
            </td>
         </tr>
      {foreachelse}
         <tr>
            <td colspan="{if $ENABLE_REWRITE}11{else}10{/if}" class="norec">{l}No records found.{/l}</td>
         </tr>
      {/foreach}
      </tbody>
      </table>

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}
 {if $rights.editCat eq 1 || $rights.delCat eq 1}
   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/categ_action_btn.tpl"}
 {/if}
   </form>
   <!-- /Category List -->
</div>

{/if}
{/strip}