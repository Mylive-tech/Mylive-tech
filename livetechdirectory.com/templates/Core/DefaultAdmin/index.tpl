{* Error and confirmation messages *}
{include file="messages.tpl"}
{strip}
<!-- Version Update Info -->
<div class="block {if $update_available ne 1}success{else}warning{/if}">{$version|escape|trim}</div>
<!-- /Version Update Info -->

{if is_array($security_warnings) and !empty($security_warnings)}
   <!-- Security Warnings -->
   {foreach from=$security_warnings item=warning}
      <div class="error block">{$warning|trim}</div>
   {/foreach}
   <!-- /Security Warnings -->
{/if}

{if is_array($directory_warnings) and !empty($directory_warnings)}
   <!-- Directory Warnings -->
   {foreach from=$directory_warnings item=warning}
      <div class="error block">{$warning|trim}</div>
   {/foreach}
   <!-- /Directory Warnings -->
{/if}

{* Statistics *}
<div class="block">
   <!-- Statistics -->
   <table class="half_list" cellspacing="0">
   <thead>
      <tr>
         <th class="listHeader">{l}Directory Searches{/l}</th>
      </tr>
   </thead>
   <tbody>
     <tr>
        <td style="padding: 16px 0 16px 0 !important;">
        	<form action="{$smarty.const.DOC_ROOT}/dir_categs.php" method="get">
                <table style="border: none;" border="0">
                  <tr>
                 	 <td style="border: none; padding:0px !important;" class="label" width="55%">
                 	 	{l}Categories{/l}:
                 	 	<br/><span class="smallDesc">({l}type ID or keywords{/l})</span>
                 	 </td>
                     <td style="border: none; padding:0px !important;" width="35%">
                     	<input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" style="width: 80%;" />
                     </td>
                     <td style="border: none; padding:0px !important;" width="10%">
                     	<input type="submit" value="{l}Search{/l}" title="{l}Click to start search{/l}" class="button" />
					 </td>
				   </tr>
                 </table>
        	 </form>
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 0 16px 0 !important;">
        	<form action="{$smarty.const.DOC_ROOT}/dir_links.php" method="get">
                <table style="border: none;" border="0">
                  <tr>
                 	 <td style="border: none; padding:0px !important;" class="label" width="55%">
                 	 	{l}Links{/l}:
                 	 	<br/><span class="smallDesc">({l}type ID or keywords{/l})</span>
                 	 </td>
                     <td style="border: none; padding:0px !important;" width="35%">
                     	<input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" style="width: 80%;" />
                     </td>
                     <td style="border: none; padding:0px !important;" width="10%">
                     	<input type="submit" value="{l}Search{/l}" title="{l}Click to start search{/l}" class="button" />
					 </td>
				   </tr>
                 </table>
        	 </form>
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 0 16px 0 !important;">
        	<form action="{$smarty.const.DOC_ROOT}/dir_link_comments.php" method="get">
                <table style="border: none;" border="0">
                  <tr>
                 	 <td style="border: none; padding:0px !important;" class="label" width="55%">
                 	 	{l}Link Comments{/l}:
                 	 	<br/><span class="smallDesc">({l}type ID or keywords{/l})</span>
                 	 </td>
                     <td style="border: none; padding:0px !important;" width="35%">
                     	<input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" style="width: 80%;" />
                     </td>
                     <td style="border: none; padding:0px !important;" width="10%">
                     	<input type="submit" value="{l}Search{/l}" title="{l}Click to start search{/l}" class="button" />
					 </td>
				   </tr>
                 </table>
        	 </form>
        </td>
    </tr>
    <tr>
        <td style="padding: 16px 0 16px 0 !important;">
        	<form action="{$smarty.const.DOC_ROOT}/conf_users.php" method="get">
                <table style="border: none;" border="0">
                  <tr>
                 	 <td style="border: none; padding:0px !important;" class="label" width="55%">
                 	 	{l}Users{/l}:
                 	 	<br/><span class="smallDesc">({l}type ID or keywords{/l})</span>
                 	 </td>
                     <td style="border: none; padding:0px !important;" width="35%">
                     	<input type="text" id="searchinput" name="search" maxlength="255" value="{if !empty($search)}{$search|escape}{/if}" class="text searchinput" title="{l}Add your search keywords{/l}" style="width: 80%;" />
                     </td>
                     <td style="border: none; padding:0px !important;" width="10%">
                     	<input type="submit" value="{l}Search{/l}" title="{l}Click to start search{/l}" class="button" />
					 </td>
				   </tr>
                 </table>
        	 </form>
        </td>
    </tr>
   </tbody>
   </table>
   <table class="half_list" style="float: right;">
   <thead>
      <tr>
         <th class="listHeader" style="width: 40%;" style="text-align: left;">{l}Statistic{/l}</th>
         <th class="listHeader" style="text-align: left;">{l}Value{/l}</th>
      </tr>
   </thead>
   <tbody>
      <tr class="odd">
         <td class="label">{l}Active Links{/l}</td>
         <td class="smallDesc">{$stats[0]}</td>
      </tr>
       <tr class="even">
         <td class="label"><a href="{$smarty.const.DOC_ROOT}/dir_links.php?status=1&r=1">{l}Pending Links{/l}</a></td>
         <td class="smallDesc">{$stats[1]}</td>
      </tr>
      <tr class="odd">
         <td class="label"><a href="{$smarty.const.DOC_ROOT}/dir_links.php?status=0&r=1">{l}Inactive Links{/l}</a></td>
         <td class="smallDesc">{$stats[2]}</td>
      </tr>
      <tr class="even">
         <td class="label">{l}Active Link Comments{/l}</td>
         <td class="smallDesc">{$stats[3]}</td>
      </tr>
      <tr class="odd">
         <td class="label"><a href="{$smarty.const.DOC_ROOT}/dir_approve_link_comments.php">{l}Pending Link Comments{/l}</a></td>
         <td class="smallDesc">{$stats[4]}</td>
      </tr>
      <tr class="odd">
         <td class="label"><a href="{$smarty.const.DOC_ROOT}/dir_categs.php">{l}Categories{/l}</a></td>
         <td class="smallDesc">{$stats[10]}</td>
      </tr>
   </tbody>
   </table>
   <!-- /Statistics -->
</div>

<div style="clear: both;"></div>

{if isset($news) and is_array($news) and !empty($news)}
   <!-- News -->
   <div class="block">
   {foreach from=$news item=item}
      <div class="news">
         <h2 class="title">{$item.title|escape|trim}</h2>
         <span class="date">{$item.date|date_format:"%b %e %Y"}</span>
         <p class="body">{$item.body|trim}</p>
      </div>
   {/foreach}
   </div>
   <!-- /News -->
{/if}
{/strip}