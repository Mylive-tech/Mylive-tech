{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}

<div class="block">
   <p>{l}Importing into:{/l}

      {assign var="current_path" value=""}
      <span class="important">
      {foreach from=$path item=cat name=path}
         {assign var="current_path" value="`$current_path``$cat.TITLE_URL`/"}
         {if not $smarty.foreach.path.first} &raquo; {/if}
         {if not $smarty.foreach.path.last}
            {$cat.TITLE|escape}
         {else}
            {$cat.TITLE|escape}
         {/if}
      {/foreach}
      </span>
   </p>
</div>

{if $error}
   <div class="block error">
      <p>{l}An error occured while parsing the RSS feed. Please make sure that the specified URL is a valid RSS feed.{/l}</p>
      <p>{l}The RSS parser returned the following message:{/l}</p>
      <p>{$error|escape}</p>
   </div>
{elseif $link_count eq 0}
   <div class="block error">
      <p>{l}No links were found in the RSS feed.{/l}</p>
   </div>
{/if}
<form name="form1" method="post" action="{$smarty.const.DOC_ROOT}/dir_links_importrss.php?c={$cid}" id="submit_form">
<div class="block">
<table class="formPage">
   <thead>
      <tr>
         <th colspan="2">
            {l}Import links from RSS feed{/l}
         </th>
      </tr>
   </thead>

   <tbody>
   <tr>
      <td class="label required"><label for="rss_url">{l}RSS feed URL{/l}:</label></td>
      <td class="smallDesc">
         <input id="rss_url" name="rss_url" type="text" class="text" value="{$rss_url}" />
      </td>
   </tr>
   <tr>
      <td class="label required"><label>{l}Import as{/l}:</label></td>
      <td class="smallDesc">
         <p><label for="status-active"><input id="status-active" name="status" type="radio" value="2" checked="checked" />{l}Active{/l}</label></p>
         <p><label for="status-inactive"><input id="status-inactive" name="status" type="radio" value="0" />{l}Inactive{/l}</label></p>
      </td>
   </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-import-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-import-submit" name="import" value="{l}Import{/l}" alt="{l}Import links{/l}" title="{l}Import links{/l}" class="button" /></td>
      </tr>
   </tfoot>
</table>
</div>
<input type="hidden" name="formSubmitted" value="1" />
</form>


{if $list}
<div class="block">
<table class="list">
   <tr>
      <td class="listHeader">{l}Title{/l}</td>
      <td class="listHeader">{l}URL{/l}</td>
      <td class="listHeader">{l}Result{/l}</td>
   </tr>
   {foreach from=$list item=row key=id}
   <tr class="{cycle values="odd,even"}">
      <td>{$row.TITLE|escape}</td>
      <td>{$row.URL|escape}</td>
      <td>
         {if $row.ERROR.TITLE}{l}Title already exists{/l}{elseif $row.ERROR.URL}{l}URL already exists{/l}{elseif $row.ERROR.SQL}{l}SQL error while inserting{/l}{else}Link imported{/if}
      </td>
   </tr>
   {/foreach}
</table>
</div>
{/if}
