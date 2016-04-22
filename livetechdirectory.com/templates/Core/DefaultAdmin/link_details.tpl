{strip}
{if $noLinkID}
<div class="block">
   <!-- Error -->
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{l}No, or invalid link ID!{/l}</p>
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
      <tr class="{cycle values="odd,even"}"><td class="label">{l}Link ID{/l}:</td><td class="smallDesc">{$linkInfo.ID}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Title{/l}:</td><td class="smallDesc">{$linkInfo.TITLE|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}URL{/l}:</td><td class="smallDesc">{$linkInfo.URL|escape|trim} {if {$linkInfo.URL|escape|trim}}<a href="{$linkInfo.URL|escape|trim}" target='_blank'>Visit</a>{/if}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Description{/l}:</td><td class="smallDesc">{$linkInfo.DESCRIPTION|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Category{/l}:</td><td class="smallDesc">{if $row.CATEGORY_ID eq '-1'}<span class="orphan">{l}Orphan{/l}</span>{else}{$linkInfo.CATEGORY|escape|trim} ({$linkInfo.CATEGORY_ID}){/if}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Status{/l}:</td><td class="smallDesc">{$stats[$linkInfo.STATUS]|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Type{/l}:</td><td class="smallDesc">{$link_type_str[$linkInfo.LINK_TYPE]}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}PageRank{/l}:</td><td class="smallDesc">{if $linkInfo.PAGERANK eq -1}<em>N/A</em>{else}{$linkInfo.PAGERANK|escape|trim}{/if}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Hits{/l}:</td><td class="smallDesc">{$linkInfo.HITS}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Date Added{/l}:</td><td class="smallDesc">{$linkInfo.DATE_ADDED|date_format:$date_format}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Valid{/l}:</td><td class="smallDesc">{$valid[$linkInfo.VALID]} ({$linkInfo.LAST_CHECKED|date_format:$date_format})</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Recpr. Link URL{/l}:</td><td class="smallDesc">{$linkInfo.RECPR_URL|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Recpr. PageRank{/l}:</td><td class="smallDesc">{if $linkInfo.RECPR_PAGERANK eq -1}N/A{else}{$linkInfo.RECPR_PAGERANK}{/if}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Recpr. Valid{/l}:</td><td class="smallDesc">{$valid[$linkInfo.RECPR_VALID]}{if !empty($linkInfo.RECPR_LAST_CHECKED)} ({$linkInfo.RECPR_LAST_CHECKED|date_format:$date_format}){/if}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Owner ID{/l}:</td><td class="smallDesc">{$linkInfo.OWNER_ID|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Owner Name{/l}:</td><td class="smallDesc">{$linkInfo.OWNER_NAME|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Owner Email{/l}:</td><td class="smallDesc">{$linkInfo.OWNER_EMAIL|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Owner IP{/l}:</td><td class="smallDesc">{$linkInfo.IPADDRESS|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Meta Keywords{/l}:</td><td class="smallDesc">{$linkInfo.META_KEYWORDS|escape|trim}</td></tr>

      <tr class="{cycle values="odd,even"}"><td class="label">{l}Meta Description{/l}:</td><td class="smallDesc">{$linkInfo.META_DESCRIPTION|escape|trim}</td></tr>
   </tbody>
   </table>
</div>
{/if}
{/strip}