{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}

<!--  
<div class="block">
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/conf_templates_edit.php?r=1" title="{l}Edit current template files{/l}" class="button"><span class="edit-tpl">{l}Edit current template files{/l}</span></a></li>
   </ul>
</div>
-->

{if $posted}
   <div class="success block">
      {l}Template updated.{/l}
   </div>
{/if}

<div class="block">
   <table class="list active-template">
   <thead>
      <tr>
         <th colspan="2">{l}Current template{/l}</th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <td class="label">{l}Title{/l}:</td>
         <td class="smallDesc title">{if !empty ($current_template.theme_uri)}<a href="{$current_template.theme_uri}" title="{l}Browse template homepage{/l}" target="_blank">{$current_template.theme_name|escape|trim}</a>{else}{$current_template.theme_name|escape|trim}{/if}</td>
      </tr>
      <!--  
      <tr>
         <td class="label">{l}Version{/l}:</td>
         <td class="smallDesc">{$current_template.theme_version|escape|trim}</td>
      </tr>
      <tr>
         <td class="label">{l}Author{/l}:</td>
         <td class="smallDesc">{if !empty ($current_template.theme_author_uri)}<a href="{$current_template.theme_author_uri}" title="{l}Browse template author homepage{/l}" target="_blank">{$current_template.theme_author|escape|trim}</a>{else}{$current_template.theme_author|escape|trim}{/if}</td>
      </tr>
      -->
      <tr>
         <td class="label">{l}Description{/l}:</td>
         <td class="smallDesc">{$current_template.theme_description|escape|trim}</td>
      </tr>

      <tr>
         <td class="label">{l}Preview{/l}:</td>
         <td class="smallDesc preview">{if !empty($current_template.theme_screenshot_file) and $showPreview eq '1'}{thumb file=$current_template.theme_screenshot_file width="250" link="true" type=$thumbType cache="../temp/cache/"}{else}{l}No preview available{/l}{/if}</td>
      </tr>
   </tbody>
   </table>
</div>

{if is_array($available_templates) and !empty($available_templates)}
<div class="block">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Available templates{/l}</th>
      </tr>
   </thead>
   <tbody>
   {foreach from=$available_templates item=template key=key}
      {cycle assign='zebra' values="odd,even"}
      <tr class="{$zebra}">
         <td class="label">{l}Title{/l}:</td>
         <td class="smallDesc title">{if !empty ($template.theme_uri)}<a href="{$template.theme_uri}" title="{l}Browse template homepage{/l}" target="_blank">{$template.theme_name|escape|trim}</a>{else}{$template.theme_name|escape|trim}{/if}</td>
      </tr>
      <tr class="{$zebra}">
         <td class="label">{l}Action{/l}:</td>
         <td class="smallDesc title"><a href="{$smarty.const.DOC_ROOT}/conf_admin_templates.php?r=1&amp;action=activate&amp;template={$template.theme_path|escape|trim}" class="button activate"><span>{l}Activate{/l}</span></a></td>
      </tr>
      <!--  
      <tr class="{$zebra}">
         <td class="label">{l}Version{/l}:</td>
         <td class="smallDesc">{$template.theme_version|escape|trim}</td>
      </tr>
      <tr class="{$zebra}">
         <td class="label">{l}Author{/l}:</td>
         <td class="smallDesc">{if !empty ($template.theme_author_uri)}<a href="{$template.theme_author_uri}" title="{l}Browse template author homepage{/l}" target="_blank">{$template.theme_author|escape|trim}</a>{else}{$template.theme_author|escape|trim}{/if}</td>
      </tr>
      -->
      <tr class="{$zebra}">
         <td class="label">{l}Description{/l}:</td>
         <td class="smallDesc">{$template.theme_description|escape|trim}</td>
      </tr>

      <tr class="{$zebra}">
         <td class="label">{l}Preview{/l}:</td>
         <td class="smallDesc preview">{if !empty($template.theme_screenshot_file) and $showPreview eq '1'}{thumb file=$template.theme_screenshot_file width="250" link="true" type="3" cache="../temp/cache/"}{else}{l}No preview available{/l}{/if}</td>
      </tr>
   {/foreach}
   </tbody>
</table>
</div>
{/if}
{/strip}