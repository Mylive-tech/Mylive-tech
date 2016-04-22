{* Build the category selection *}
{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
{if $smarty.const.AJAX_CAT_SELECTION_METHOD eq 1 && $symbolic neq 1}
   <div id="catTitle1">
      {if !empty ($CategoryTitle)}{$CategoryTitle}{else}{l}Please select a category!{/l}{/if}
   </div>
   <span id="toggleCategTree1">{l}Change category{/l}</span>
   {if isset ($parent) and $parent eq 1}
      <input type="hidden" id="SYMBOLIC_ID" name="SYMBOLIC_ID" value="{$SYMBOLIC_ID}" class="hidden" />
   {else}
      <input type="hidden" id="CATEGORY_SYMBOLIC_ID" name="CATEGORY_SYMBOLIC_ID" value="{$CATEGORY_SYMBOLIC_ID}" class="hidden" />
   {/if}
   <div id="categtreebox1"></div>

{else}
   {if isset ($parent) and $parent eq 1}
      {html_options options=$categs selected=$SYMBOLIC_ID name="SYMBOLIC_ID" id="SYMBOLIC_ID"}
   {else}
      {html_options options=$categs selected=$CATEGORY_SYMBOLIC_ID name="CATEGORY_SYMBOLIC_ID" id="CATEGORY_ID"}
   {/if}
{/if}
{/strip}