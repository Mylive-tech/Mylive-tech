{* Build the category tree or category title with AJAX *}
{strip}
{if $action eq 'titleupdate'}
   {$CategoryTitle|escape|trim}
{else}
   <div id="categtree1">
      <h2>{l}Select category{/l} <span id="close_tree" onclick="{literal}catTreeClose1(); return false;{/literal}" title="{l}Close category tree{/l}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-up.png" alt="{l}Close category tree{/l}" width="12" height="12" border="0" /></span></h2>

      {if $categID gt 0}
      <div class="categ-item {cycle values="odd,even"}" title="{l}Reload category list from top.{/l}" onclick="reload_categ_tree1(0); return false;">
         <img src="{$smarty.const.TEMPLATE_ROOT}/images/refresh.png" align="top" alt="{l}Reload category tree from top{/l}" border="0" height="12" width="12" />
         {l}Reload{/l}
      </div>

      <div class="categ-item {cycle values="odd,even"}" title="{l}Go one step back{/l}" onclick="update_categ_selection1({$parentID}, 0, 1); return false;">
         <img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-diag.png" align="top" alt="{l}Go one step back{/l}" border="0" height="12" width="12" />
         ..
      </div>
      {/if}

      {if !$error_cat_tree}
      {foreach from=$categoryList key=key item=cat name=categs}
         <div class="categ-item {cycle values="odd,even"}" title="{$cat.TITLE|escape|trim}" onclick="update_categ_selection1({$cat.ID}, {$parentID}, {$cat.SUBCATEGS}); return false;">
            {if $cat.SUBCATEGS eq 1}
               <img src="{$smarty.const.TEMPLATE_ROOT}/images/folderopen.png" align="top" alt="{l}Select subcategories{/l}" border="0" height="12" width="12" />
            {else}
               <img src="{$smarty.const.TEMPLATE_ROOT}/images/folderclose.png" align="top" alt="{l}No subcategories available{/l}" border="0" height="12" width="12" />
            {/if}
            {$cat.TITLE|escape|trim}
         </div>
      {/foreach}
      {/if}
   </div>
{/if}

{/strip}