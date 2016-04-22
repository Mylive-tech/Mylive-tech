{* Error and confirmation messages *}
{include file="messages.tpl"}

{literal}
<script type="text/javascript">
function status_hide(id) {
	var display = document.getElementById('list-status-'+id).style.display;
	if (display == 'none') {
		document.getElementById('list-status-'+id).style.display = 'block';
	} else {
		document.getElementById('list-status-'+id).style.display = 'none';
	}
	return false;
}
function getPosition(e, id) {
    e = e || window.event;
    var cursor = {x:0, y:0};
    if (e.pageX || e.pageY) {
        cursor.x = e.pageX;
        cursor.y = e.pageY;
    } 
    else {
        var de = document.documentElement;
        var b = document.body;
        cursor.x = e.clientX + 
            (de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
        cursor.y = e.clientY + 
            (de.scrollTop || b.scrollTop) - (de.clientTop || 0);
    }

    document.getElementById(id).style.top = eval(cursor.y+1)+'px';
	document.getElementById(id).style.left = eval(cursor.x)+'px';
}

function select_all(key,all) {

	if (document.getElementsByClassName == undefined) {
	document.getElementsByClassName = function(className)
	{
		var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
		var allElements = document.getElementsByTagName("*");
		var results = [];

		var element;
		for (var i = 0; (element = allElements[i]) != null; i++) {
			var elementClass = element.className;
			if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
				results.push(element);
		}

		return results;
	}
	}	

	var elem = document.getElementsByClassName(key);
	if (all == 1) {
		for (var i in elem) {
			elem[i].checked=true;
		}
	} else {
		for (var i in elem) {
			elem[i].checked=false;
		}
	}
}
</script>
{/literal}
{strip}

{if $wid_message neq ''}
<div class="success block">
	{$wid_message}
</div>
{/if}
{if $wid_error neq ''}
<div class="block">
   <div class="error">
      {$wid_error}
   </div>
</div>
{/if}


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
 <form action="{$smarty.const.DOC_ROOT}/dir_widgets_edit.php" method="post">
{foreach from=$list item=type key=key}
     <table class="list availWidgets" style="margin-bottom: 30px;">
      <thead>
      	 <tr>
      	 	<th class="listHeader" colspan="{$col_count}">{$key|escape|trim} {l}WIDGETS{/l} : {l}Can be placed on the following zones{/l}:&nbsp; 
      	 		{section name=i loop=$available[$key]}
      	 			<a style="display: inline;" href="{$smarty.const.DOC_ROOT}/dir_widgets_per_zone.php?Z={$available[$key][i].NAME}&T={$key}">
      	 				{$available[$key][i].NAME}
      	 			</a>
      	 			{if !$smarty.section.i.last},&nbsp;{/if}
      	 		{/section} 
      	 	</th>
      	 </tr>
         <tr>
         	<th class="listHeader">&nbsp;</th>
            {foreach from=$columns key=col item=name}
               {if $col ne 'TITLE_URL'}
                  <th class="listHeader">{$name|escape|trim}</th>
               {/if}
            {/foreach}
         </tr>
      </thead>
      <tbody>
      	 {foreach from=$type item=row key=NAME name=widgets}
         <tr class="{cycle values="odd,even"}">
			<td class="first-child" style="width: 25px;">
				<label>
					<input type="checkbox" name="multi-{$row.NAME}" class="{$key}" id="multi-{$row.NAME}" title="{l}Check box to select this item.{/l}" />
				</label>
			</td>         
            {foreach from=$columns key=col item=name}
               {assign var="val" value=$row.$col}
				{if $col eq 'INSTALLED'}<td {if $row.$col eq '1'}class="status-2"{else}class="status-0"{/if}>
					{if $row.$col eq '1'}
					<span class="link-status pop">{l}Installed{/l}</span>
					<h3 id="chgStat-{$row.NAME}" class="chgStatTitle" onclick="{literal}return status_hide('{/literal}{$row.NAME}{literal}');{/literal}">{l}Change status{/l}</h3>
                    <ul id="list-status-{$row.NAME}" style="display: none;">
                    	<li><a class="new-status-0" href="{$smarty.const.DOC_ROOT}/dir_widgets_edit.php?action=U:{$row.NAME}" title="{l}Uninstall widget{/l}: {$row.NAME|escape|trim}">{l}Uninstall{/l}</a></li>
                    </ul>
                 	{else}
                 	<span class="link-status pop">{l}Not Installed{/l}</span>
					<h3 id="chgStat-{$row.NAME}" class="chgStatTitle" onclick="{literal}return status_hide('{/literal}{$row.NAME}{literal}');{/literal}">{l}Change status{/l}</h3>
                    <ul id="list-status-{$row.NAME}" style="display: none;">
                    	<li><a class="new-status-2" href="{$smarty.const.DOC_ROOT}/dir_widgets_edit.php?action=I:{$row.NAME}" title="{l}Install widget{/l}: {$row.NAME|escape|trim}">{l}Install{/l}</a></li>
                    </ul>
					{/if}
				</td>
                {elseif $col eq 'ACTION'}
                    <td style="width: 40px;">
                        <a href="{$smarty.const.DOC_ROOT}/dir_widgets_pick_zones.php?id={$row.NAME}" title="{l}New widget for{/l}: {$row.NAME|escape|trim}" class="action newWidget"><span>{l}NEW WIDGET{/l}</span></a>
		    </td>
		{elseif $col eq 'NAME'}
			<td>
				<a href="" style="border: none; text-decoration: underline;" onmouseover="{literal}document.getElementById('wid_details_{/literal}{$row.$col}{literal}').style.display='block'; getPosition(event, 'wid_details_{/literal}{$row.$col}{literal}');{/literal}" onmouseout="{literal}document.getElementById('wid_details_{/literal}{$row.$col}{literal}').style.display='none';{/literal}" onclick="return false;">{$row.$col|escape|trim}</a>
				<span id="wid_details_{$row.$col}" class="wid_details" style="display: none;">
					{if $row.DESCRIPTION|escape|trim neq ''}{$row.DESCRIPTION|escape|trim}{else}No description.{/if}
				</span>
			</td>
				
		{elseif $col eq 'DESCRIPTION'}
		<td style="padding: 0px 5px;">
				
				{if $row.DESCRIPTION|escape|trim neq ''}{$row.DESCRIPTION|escape|trim|truncate:250}{else}No description.{/if}
				
		</td>         
		{else}
			<td>
				{$row.$col|escape|trim}
			</td>
		{/if}
            {/foreach}
         </tr>
         {foreachelse}
        <tr>
           <td colspan="{$col_count}" class="norec">{l}No records found.{/l}</td>
        </tr>
         {/foreach}
      </tbody>
      </table>
	  <table class="list">
		<tr>
			<td colspan="2" style="font-weight: bold; text-align: center;">{l}Manage multiple selections{/l}</td>
  		</tr>
      	<tr>
         	<td>
	            <fieldset class="link_action">
	               <legend>{l}Select{/l}</legend>
	               <input type="button" name="all" id="allButton" value="{l}All{/l}" title="{l}Select All{/l}" class="button" onclick="{literal}select_all('{/literal}{$key}{literal}', 1);{/literal}" />
				   <input type="button" name="none" id="noneButton" value="{l}None{/l}" title="{l}Select None{/l}" class="button" onclick="{literal}select_all('{/literal}{$key}{literal}', 0);{/literal}" />	
	            </fieldset>
				<fieldset class="link_action">
	               <legend>{l}Change Status{/l}</legend>
	               <input type="submit" name="install" id="installButton" value="{l}Install{/l}" title="{l}Install selected widgets{/l}" class="button"/>
	               <input type="submit" name="uninstall" id="uninstallButton" value="{l}Uninstall{/l}" title="{l}Uninstall selected widgets{/l}" class="button"/>
				   <input type="hidden" name="action" value="multi"/>
				   <input type="hidden" name="type" value="{$key}"/>
	            </fieldset>
         	</td>
      	</tr>
	</table>
{/foreach}
</form>
</div>
{/strip}
