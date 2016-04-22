{* Error and confirmation messages *}
{include file="messages.tpl"}
{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="spider_form" validators=$validators}
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

{if !$showImport or !$action or empty($action)}
<div class="block">
   <form method="post" action="" name="spider-google" id="spider_form">
   <table class="formPage">
   <thead>
      <tr>
         <th colspan="2">{l}Google import{/l}</th>
      </tr>
   </thead>

   <tbody>
     <tr>
	<td colspan="2">{l}The spider only works for addiing Regular Links and may not work with other link types{/l}!</td>
     </tr>
      <tr>
         <td class="label required"><label for="host">{l}Search in{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$langChoice selected=$gdata.lr name="lr" id="lr"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="as_q">{l}Keywords{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="as_q" name="as_q" value="{$gdata.as_q|escape|trim}" class="text" />
         </td>
      </tr>
      
      <tr>
         <td class="label required"><label for="num">{l}Number of links to request{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$googleResultsCount selected=$gdata.num name="num" id="num"}
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-gspider-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-gspider-submit" name="submit" value="{l}Continue{/l}" alt="{l}Continue{/l}" title="{l}Continue{/l}" class="button" /></td>
      </tr>
   </tfoot>

   </table>

       <input type="hidden" name="formSubmitted" value="1" />
   
   </form>
</div>

<div class="block">
   <form method="post" action="">
   <table class="formPage">
   <thead>
      <tr>
         <th colspan="2">{l}Dmoz import{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr>
         <td class="label required"><label for="dmozurl">{l}Category URL from{/l} <a href="http://www.dmoz.org/" title="Browse Open Directory Project homepage" target="_blank">dmoz.org</a>:</label></td>
         <td class="smallDesc">
            <input type="text" id="dmozurl" name="dmozurl" value="{$ddata.dmozurl|escape|trim}" class="text" />
            <p class="info">{l}Example{/l}: <code>http://www.dmoz.org/category/subcategory/</code></p>
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="dimpdomain">{l}Import domain only{/l}:</label></td>
         <td class="smallDesc">
            <input type="checkbox" name="impdomain" id="dimpdomain" value="{$ddata.impdomain}"{if $ddata.impdomain eq '1'} checked="checked"{/if} />
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-dspider-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-dspider-submit" name="dsubmit" value="{l}Continue{/l}" alt="{l}Continue{/l}" title="{l}Continue{/l}" class="button" /></td>
      </tr>
   </tfoot>

   </table>
   
   <input type="hidden" name="action" value="dmoz" />
   </form>
</div>

{else}
	  {if isset($importSuccess) and $importSuccess gt 0}
	     <div class="success block"><p>{$importSuccess} {l}links successfully imported{/l}!</p></div>
	  {/if}
	  {if isset($importSkipped) and $importSkipped gt 0}
	     <div class="notice block"><p>{$importSkipped} {l}links skipped from import{/l}! {l}Already available{/l}!</p></div>
	  {/if}
	  {if isset($importErrors) and $importErrors gt 0}
	     <div class="error block"><p>{l}Importing errors{/l}: {$importErrors}</p></div>
	  {/if}
	
   {if !is_array($importResults) or empty ($importResults)}
      <div id="noreserror" {if $for_dmoz neq 1}style="display: none;"{/if} class="error block"><p>{l}No results found{/l}.</p></div>
   {else}
      <div class="block">
      <form action="" method="post">
         <input type="hidden" id="start" name="start" value="{$nextStart}" class="hidden" />
         <input type="hidden" id="prevStart" name="prevStart" value="{$start}" class="hidden" />

         {if !empty($googleAddress)}
            <div class="notice">
               <h2><label for="googleURL">{l}Fetched Google URL{/l}</label></h2>
               <p><input type="text" id="googleURL" name="googleURL" value="{$googleAddress|trim}" class="text" /> <a href="{$googleAddress|escape|trim}" title="{l}Browse fetched Google URL{/l}" target="_blank">{l}Open{/l}</a></p></div>
            </div>
         {/if}

         <table class="formPage">
         <tbody>
            <tr>
               <td class="label required"><label for="CATEGORY_ID">{l}Category{/l}:</label></td>
               <td class="smallDesc">
                  {* Load category selection *}
                  {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl"}
               </td>
            </tr>
            <tr>
                <td class="label required"><label for="LINK_TYPE">{l}Link Type{/l}:</label></td>
                <td class="smallDesc">
                        <select name="LINK_TYPE" id="LINK_TYPE">
                        {foreach from=$link_types key=k item=v}
                        <option value="{$k}" {if $linktypeid eq $k} selected="selected" {/if}>{$v.NAME}</option>
                        {/foreach}
                        </select>
    		</td>
            </tr>
            <tr>
               <td><input type="reset" id="reset-import-submit-top" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" />{formtool_checkall name="toimport[]" checkall_text="{l}Check All{/l}" uncheckall_text="{l}Uncheck All{/l}" class="btn" id="checkallButton"}</td>
               <td><input type="submit" id="import-top" name="import" value="{l}Continue{/l}" alt="{l}Continue{/l}" title="{l}Continue{/l}" class="button" /></td>
            </tr>
         {foreach from=$importResults key=key item=result}
            <tr class="thead">
               <th><label><input type="checkbox" name="toimport[]" value="{$key}" title="{l}Check box to select this item.{/l}" checked="checked" /></label></th><th>{$result.TITLE|trim}</th>
            </tr>
            <tr>
               <td class="label required"><label for="TITLE-{$key}">{l}Title{/l}:</label></td>
               <td class="smallDesc">
                  <input type="text" id="TITLE-{$key}" name="TITLE[{$key}]" value="{$result.TITLE|trim}" class="text" />
               </td>
            </tr>
            {if $has_url eq 1}
            <tr>
               <td class="label required"><label for="URL-{$key}">{l}URL{/l}:</label></td>
               <td class="smallDesc">
                  <input type="text" id="URL-{$key}" name="URL[{$key}]" value="{$result.URL|escape|trim}" class="text" /> <a href="{$result.URL|escape|trim}" title="{$result.TITLE|escape|trim}" target="_blank">{l}Browse{/l}</a>
               </td>
            </tr>
            {/if}
            {if $has_description eq 1}
            <tr>
               <td class="label"><label for="DESCRIPTION-[{$key}]">{l}Description{/l}:</label></td>
               <td class="smallDesc">
                  <textarea id="DESCRIPTION-[{$key}]" name="DESCRIPTION[{$key}]" rows="6" cols="50" class="text">{$result.DESCRIPTION|trim}</textarea>
               </td>
            </tr>
            {/if}
            <tr>
               <td class="label"><label for="OWNER_NAME-[{$key}]">{l}Owner Name{/l}:</label></td>
               <td class="smallDesc">
                  <input type="text" id="OWNER_NAME-[{$key}]" name="OWNER_NAME[{$key}]" value="{$result.OWNER_NAME|trim}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
               </td>
            </tr>
            <tr>
               <td class="label"><label for="OWNER_EMAIL-[{$key}]">{l}Owner Email{/l}:</label></td>
               <td class="smallDesc">
                  <input type="text" id="OWNER_EMAIL-[{$key}]" name="OWNER_EMAIL[{$key}]" value="{$result.OWNER_EMAIL|trim}" maxlength="255" class="text" />
               </td>
            </tr>
         {/foreach}
         </tbody>

      <tfoot>
         <tr>
            <td><input type="reset" id="reset-import-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" />{formtool_checkall name="toimport[]" checkall_text="{l}Check All{/l}" uncheckall_text="{l}Uncheck All{/l}" class="btn" id="checkallButton2"}</td>
            <td><input type="submit" id="import" name="import" value="{l}Continue{/l}" alt="{l}Continue{/l}" title="{l}Continue{/l}" class="button" /></td>
         </tr>
      </tfoot>

   </table>
      </form>
      </div>
   {/if}
{/if}

{/strip}