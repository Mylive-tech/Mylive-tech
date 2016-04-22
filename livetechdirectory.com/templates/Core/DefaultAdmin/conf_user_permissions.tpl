   {* Error and confirmation messages *}
   {include file="messages.tpl"}

{strip}
{if (isset($error) and $error gt 0) or !empty($sql_error)}
   <div class="error block">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while saving.{/l}</p>
      {if !empty($errorMsg)}
         <p>{$errorMsg|escape}</p>
      {/if}
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p>{$sql_error|escape}</p>
      {/if}
   </div>
{/if}

{if $posted}
   <div class="success block">
      {$posted|escape}
   </div>
{/if}

{if $WARN}
<div class="block">
   <form method="post" action="{$smarty.const.DOC_ROOT}/conf_user_permissions.php{if !empty($u)}?u={$u}{/if}" name="delete">
   <input type="hidden" id="warn" name="warn" value="1" class="hidden" />
   <input type="hidden" name="CATEGORY_ID" value="{$CATEGORY_ID}" class="hidden" />
   <table class="formPage">
   <thead>
      <tr>
         <th colspan="2">{$permsTitleMsg|escape}</th>
      </tr>
   </thead>

   <tbody>
      <tr>
         <td class="notice" colspan="2">
            <p>Category {$CATEGORY} is parent to {$CHILD_CATEGORIES} {if $CHILD_CATEGORIES eq 0}category{else}categories{/if} that this user has permission to.</p>
            <p>Proceed to grant permission to category {$CATEGORY} and delete the existing permission to the {$CHILD_CATEGORIES} {if $CHILD_CATEGORIES eq 0}category{else}categories{/if}?</p>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td>
            <input type="submit" name="cancel" value="{l}Cancel{/l}" title="{l}Cancel{/l}" class="button" />
         </td>
         <td>
            <input type="submit" name="proceed" value="{l}Proceed{/l}" title="{l}Grant permission to parent category including child categories{/l}" class="button" />
         </td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   </form>
</div>
{else}
<div class="block">
   <form method="post" action="{$smarty.const.DOC_ROOT}/conf_user_permissions.php?action=N{if !empty($u)}&amp;u={$u}{/if}">
   <table class="formPage">
   <thead>
      <tr>
         <th colspan="2">{$permsTitleMsg|escape}</th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <td class="label"><label for="CATEGORY_ID">{l}Category{/l}:</label></td>
         <td class="smallDesc">
            {* Load category selection *}
            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl"}
           
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td>
            <input type="reset" id="reset-perms-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" />
         </td>
         <td>
            <input type="submit" id="send-perms-submit" name="add" value="{l}Add permission{/l}" alt="{l}Add permission{/l}" title="{l}Add permission to selected category{/l}" class="button" />
         </td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   </form>
</div>
{/if}

<div class="block">
   <table class="list">
   <thead>
      <tr>
         {foreach from=$columns key=col item=name}
            {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
               <th class="listHeader" id="{$col}"><a href="{$smarty.const.DOC_ROOT}/dir_links.php{if isset($columnURLs) and is_array($columnURLs)}?{$columnURLs.$col}{/if}" title="{l}Sort by{/l}: {$name|escape|trim}" class="sort{if $SORT_FIELD eq $col and $requestOrder eq 1} {$smarty.const.SORT_ORDER}{/if}">{$name|escape|trim}</a></th>
               {/if}
         {/foreach}
         <th class="last-child">{l}Action{/l}</th>
      </tr>
   </thead>

   <tbody>
      {foreach from=$list item=row key=id}
         <tr class="{cycle values="odd,even"}">
            {foreach from=$columns key=col item=name}
               {if $ENABLE_REWRITE or $col ne 'TITLE_URL'}
               <td>
                  {if $col eq 'CATEGORY_PATH'}
                     {foreach from=$row.$col item=category name=path}
                        {if $smarty.foreach.path.iteration gt 2} &gt; {/if}
                        {if not $smarty.foreach.path.first}
                           {$category.TITLE|escape|trim}
                        {/if}
                     {/foreach}
                  {else}
                     {$row.$col|escape}
                  {/if}
               </td>
               {/if}
            {/foreach}
            <td class="last-child"><a id="remove-userperms-{$id}" href="{$smarty.const.DOC_ROOT}/conf_user_permissions.php?action=D:{$id}" onclick="return link_rm_confirm('{escapejs}{l}Are you sure you want to remove this permission?{/l}{/escapejs}');" title="{l}Remove Permission{/l}" class="action delete"><span>{l}Delete{/l}</span></a></td>
         </tr>
      {foreachelse}
         <tr>
            <td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
         </tr>
      {/foreach}
   </tbody>
   </table>

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}
</div>
{/strip}