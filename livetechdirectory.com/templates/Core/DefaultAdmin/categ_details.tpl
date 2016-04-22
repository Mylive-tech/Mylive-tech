{strip}
{if $noCategID}
<div class="block">
   <!-- Error -->
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{l}No, or invalid category ID!{/l}</p>
   </div>
   <!-- /Error -->
</div>
{elseif $error gt '1'}
<div class="block">
   <!-- Error -->
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{l}An occured while processing.{/l}</p>
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p class="sql_error">{$sql_error}</p>
      {/if}
   </div>
   <!-- /Error -->
</div>
{else}
<div class="tooltip block" id="tooltip-{$id}">
   <table class="tooltip-table list">
   <tbody>
      <tr class="{cycle values="odd,even"}"><td class="label">{l}ID{/l}:</td><td class="smallDesc">{$categInfo.ID}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Title{/l}:</td><td class="smallDesc">{$categInfo.TITLE|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Parent{/l}:</td><td class="smallDesc">{if $categInfo.PARENT_ID eq '-1'}<span class="orphan">{l}Orphan{/l}</span>{else}{$categInfo.PARENT|escape|trim} ({$categInfo.PARENT_ID}){/if}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Description{/l}:</td><td class="smallDesc">{$categInfo.DESCRIPTION|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Category{/l}:</td><td class="smallDesc">{$categInfo.CATEGORY|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Status{/l}:</td><td class="smallDesc">{$stats[$categInfo.STATUS]|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Hits{/l}:</td><td class="smallDesc">{$categInfo.HITS}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Date Added{/l}:</td><td class="smallDesc">{$categInfo.DATE_ADDED|date_format:$date_format}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Meta Keywords{/l}:</td><td class="smallDesc">{$categInfo.META_KEYWORDS|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Meta Description{/l}:</td><td class="smallDesc">{$categInfo.META_DESCRIPTION|escape|trim}</td></tr>

      <tr>
         <th class="msg notice" colspan="2">{l}Category statistics{/l}</th>
      </tr>

      {* SUBCATEGORIES *}
      <tr class="{cycle values="odd,even"}"><td class="label">{l}Primary Subcategories{/l}:</td><td class="smallDesc"><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={$categInfo.ID}" title="{l}Browse subcategories of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_SUBCATEGS_COUNT}</a> ({l}Active{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={$categInfo.ID}&amp;status=2" title="{l}Browse active subcategories of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_ACTIVE_SUBCATEGS_COUNT}</a>, {l}Pending{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={$categInfo.ID}&amp;status=1" title="{l}Browse pending subcategories of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_PENDING_SUBCATEGS_COUNT}</a>, {l}Inactive{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={$categInfo.ID}&amp;status=0" title="{l}Browse inactive subcategories of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_INACTIVE_SUBCATEGS_COUNT}</a>)</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Total Subcategories{/l}:</td><td class="smallDesc"><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}" title="{l}Browse all subcategories of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.TOTAL_SUBCATEGS_COUNT}</a> <span class="notice info">({l}Including subcategories of subcategories{/l})</span></td></tr>

      {* LINKS *}
      <tr class="{cycle values="odd,even"}"><td class="label">{l}Primary Links{/l}:</td><td class="smallDesc"><a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={$categInfo.ID}" title="{l}Browse links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_LINKS_COUNT}</a> ({l}Active{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={$categInfo.ID}&amp;status=2" title="{l}Browse active links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_ACTIVE_LINKS_COUNT}</a>, {l}Pending{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={$categInfo.ID}&amp;status=1" title="{l}Browse pending links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_PENDING_LINKS_COUNT}</a>, {l}Inactive{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={$categInfo.ID}&amp;status=0" title="{l}Browse inactive links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.PRIMARY_INACTIVE_LINKS_COUNT}</a>)</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}All Links{/l}:</td><td class="smallDesc"><a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}" title="{l}Browse all links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.TOTAL_LINKS_COUNT}</a> ({l}Active{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}&amp;status=2" title="{l}Browse all active links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.TOTAL_ACTIVE_LINKS_COUNT}</a>, {l}Pending{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}&amp;status=1" title="{l}Browse all pending links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.TOTAL_PENDING_LINKS_COUNT}</a>, {l}Inactive{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}&amp;status=0" title="{l}Browse all inactive links of category{/l}: {$categInfo.TITLE|escape|trim}">{$categInfo.TOTAL_INACTIVE_LINKS_COUNT}</a>) <span class="notice info">({l}Including links of subcategories{/l})</span></td></tr>

   </tbody>
   </table>
</div>
{/if}
{/strip}