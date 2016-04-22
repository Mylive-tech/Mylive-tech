{* Build the category selection *}
{* Error and confirmation messages *}

{if $selected}
	{assign var='selected_cat' value=$selected}
	{assign var='selected_parent' value=$selected_parent}
{else}
	{assign var='selected_cat' value=$CATEGORY_ID}
	{assign var='selected_parent' value=$PARENT_ID}
{/if}
{strip}
<select name="CATEGORY_ID" id="{math equation='rand(10,100)'}">
{foreach from=$categs_tree key=k item=v}
<option value="{$k}" {if (($categ_id eq $k) && ($categ_id neq 0))} selected="selected" {/if} {if $v.closed eq 1}disabled = "disabled" {/if}>{$v.val}</option>
{/foreach}
</select>
{/strip}