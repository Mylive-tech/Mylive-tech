{* Error and confirmation messages *}
{include file="messages.tpl"}
{*
{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}
*}
{strip}

<div class="block">
   <!-- Maintenance Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/conf_maintenance.php?r=1&amp;action=clean_all_temp" title="{l}Clean cached and temporary files{/l}" class="button"><span class="clean-temp">{l}Clear Cache{/l}</span></a></li>
   </ul>
   <!-- /Maintenance Action Links -->
</div>

{if $clean_temp_msg}
   <div class="block">
      <div class="success">{l}Cached and compiled template files where removed.{/l}</div>
   </div>
{/if}

<div id="buildmeta" class="block">
   <form method="post" name="build_meta" id="submit_form" action="{$smarty.const.DOC_ROOT}/conf_maintenance.php">
   <table class="list" border="0">
   <thead>
      <tr>
         <th colspan="2">{l}Build META tags.{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="notice">
         <td colspan="2" class="notice">
            <p>{l}This feature will (re)build custom META tags to every category and detail link pages.{/l}</p>
            <p>{l}META keywords for categories are the titles of it's subcategories, if no subcategories are present the category title is used and default META keywords for the directory are appended. For detail link pages keywords will be link title, category title, owner name.{/l}</p>
            <p>{l}META description for categories will be the category description itself and for link detail pages the link description. If the description is not present the field is left empty for default directory META description.{/l}</p>
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="meta_range">{l}Number of categories/links to process per cycle{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="meta_range" name="range" value="{$range}" maxlength="10" class="text" />
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Build option{/l}:</label></td>
         <td class="smallDesc">
            {html_radios name="meta_options" options=$build_meta_options_radios selected=$build_meta_options_checked separator="<br />"}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Build option{/l}:</label></td>
         <td class="smallDesc">
            {html_radios name="meta_overwrite" options=$meta_overwrite_radios selected=$meta_overwrite_checked separator="<br />"}
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-metabuild-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td>
             <input type="submit" id="send-metabuild-submit" name="meta-build-submit" value="{l}Build{/l}" alt="{l}Build META tags{/l}" title="{l}Build META tags{/l}" class="button" />
         </td>
      </tr>
   </tfoot>
   </table>
   </form>
</div>
{/strip}