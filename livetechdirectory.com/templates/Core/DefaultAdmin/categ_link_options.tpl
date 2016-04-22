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
   <thead>
      <tr>
         <th>{$categInfo.TITLE|escape|trim}</th>
      </tr>
   </thead>
   <tbody>
      <tr class="{cycle values="odd,even"}"><td><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=M:{$categInfo.ID}" title="{l}Quick add subcategories to this category{/l}" class="button"><span class="new-categ">{l}Add subcategories{/l}</span></a></td></tr>

      <tr class="{cycle values="odd,even"}"><td><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={$categInfo.ID}" title="{l}Browse subcategories of category{/l}: {$categInfo.TITLE|escape|trim}">{l}Primary subcategories{/l}</a></td></tr>

      <tr class="{cycle values="odd,even"}"><td><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}" title="{l}Browse all subcategories of category{/l}: {$categInfo.TITLE|escape|trim}">{l}All subcategories{/l}</a></td></tr>

      <tr class="{cycle values="odd,even"}"><td><a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={$categInfo.ID}" title="{l}Browse links of category{/l}: {$categInfo.TITLE|escape|trim}">{l}Links{/l}</a></td></tr>

      <tr class="{cycle values="odd,even"}"><td><a href="{$smarty.const.DOC_ROOT}/dir_links.php?category={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}" title="{l}Browse all links of category{/l}: {$categInfo.TITLE|escape|trim}">{l}All Links{/l}</a></td></tr>

      <tr class="{cycle values="odd,even"}"><td><a href="{$smarty.const.DOC_ROOT}/article_list.php?category={$categInfo.ID}" title="{l}Browse articles of category{/l}: {$categInfo.TITLE|escape|trim}">{l}Articles{/l}</a></td></tr>

      <tr class="{cycle values="odd,even"}"><td><a href="{$smarty.const.DOC_ROOT}/article_list.php?category={php}global $categInfo; echo implode(',', $categInfo['TOTAL_SUBCATEGS']);{/php}" title="{l}Browse all articles of category{/l}: {$categInfo.TITLE|escape|trim}">{l}All Articles{/l}</a></td></tr>
   </tbody>
   </table>
</div>
{/if}
{/strip}