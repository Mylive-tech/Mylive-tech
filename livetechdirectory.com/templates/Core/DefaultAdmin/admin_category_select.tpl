{* Build the category selection *}
{* Error and confirmation messages *}
{include file="messages.tpl"}
{if $selected}
	{assign var='selected_cat' value=$selected}
	{assign var='selected_parent' value=$selected_parent}
{else}
	{assign var='selected_cat' value=$CATEGORY_ID}
	{assign var='selected_parent' value=$PARENT_ID}
{/if}
{strip}
{if $smarty.const.ADMIN_CAT_SELECTION_METHOD eq 1 && !$additional_categs && $link_type_details.MULTIPLE_CATEGORIES<=1 && $symbolic != 1}
   <div id="catTitle">
      {if !empty ($CategoryTitle)}{$CategoryTitle}{else}{l}Please select a category!{/l}{/if}
   </div>
   <span id="toggleCategTree">{l}Change category{/l}</span>
   {if isset ($parent) and $parent eq 1}
      <input type="hidden" id="PARENT_ID" name="PARENT_ID" value="{$selected_parent}" class="hidden" />
   {else}
      <input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID" value="{$selected_cat}" class="hidden" />
   {/if}
   <div id="categtreebox"></div>

{else}
   {if isset ($parent) and $parent eq 1}
      {html_options options=$categs selected=$PARENT_ID name="PARENT_ID" id="PARENT_ID"}
   {else}
   	{if $additional_categs}
      	{html_options options=$categs selected=$categ_id name="ADD_CATEGORY_ID[]" id="0"|rand:"100"}
      {else}
      	{html_options options=$categs selected=$selected_cat name="CATEGORY_ID" id="CATEGORY_ID"}
      {/if}
   {/if}
{/if}
{/strip}