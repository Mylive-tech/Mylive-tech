{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
{if $op_status eq 1}
<div class="success block">
	{l}Operation successful.{/l}
</div>
{elseif $op_status eq -1}
<div class="block">
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{l}Some errors occured during the operation.{/l}</p>
   </div>
</div>
{/if}

<div class="block">
	<h2 style="font-size: 12px;">{l}You are installing the{/l} {$widname} {l}widget.{/l}</h2>
   <form action="" method="post">
      <table class="list" style="width: 50%; margin-bottom: 20px;">
      <thead>
         <tr>
         	<th class="listHeader" colspan="{$columns|@count}"  colspan="2">{l}WIDGET ZONES{/l}</th>
         </tr>
      </thead>
      <tbody>
      	<tr>
      		<td  colspan="2">
      		<p style="padding: 10px;">{l}Your widget is now successfully installed. Please select the zone(s) where you want it visible. These are the areas where it will actually show up on the site's front end.{/l}</p>
      		</td>
      	</tr>
     	 {foreach from=$list item=row key=NAME}
         <tr class="{cycle values="odd,even"}">
            <td  colspan="2">
            	<input type="checkbox" id="{$row.NAME}" name="zones[]" value="{$row.NAME}" />
            	&nbsp;&nbsp;{$row.NAME}
            </td>
         </tr>
         {foreachelse}
        <tr>
           <td class="norec"  colspan="2">{l}No records found.{/l}</td>
        </tr>
         {/foreach}
      </tbody>
      <tfoot>
      <tr>
         <td><input type="submit" name="save" value="{l}Save{/l}" alt="{l}Save{/l}" title="{l}Save{/l}" class="button" /></td>
         <td><input type="submit" name="cancel" value="{l}Cancel{/l}" alt="{l}Cancel{/l}" title="{l}Cancel{/l}" class="button" /></td>
      </tr>
   </tfoot>
      </table>
      <input type="hidden" name="formSubmitted" value="1" />
   </form>
</div>
{/strip}
