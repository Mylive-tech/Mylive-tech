{* Error and confirmation messages *}
{include file="messages.tpl"}

<style type="text/css" media="screen">
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/datatable_jui.css";
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/jquery-ui-1.7.2.custom.css";
</style>

{literal}
<script type="text/javascript">
var oTable;
jQuery(function($) {

$(document).ready(function() {
	$("#multiple_controls input").click(function() {
			ctrl_name = $(this).attr('name');
			if (ctrl_name != 'check_all' && ctrl_name != 'uncheck_all')
				multiple_action(ctrl_name);
		});
	
		$("#check_all").click(function() {
			$("#example tbody tr").each(function(n,element){
				var chbox = '#categ_' + $(element).attr('id');
				$(element).addClass('row_selected');
				$(chbox).attr('checked', true);
			});
		});
		
		$("#uncheck_all").click(function() {
			$("#example tbody tr").each(function(n,element){
				var chbox = '#categ_' + $(element).attr('id');
				$(element).removeClass('row_selected');
				$(chbox).attr('checked', false);
			});
		});
		
		$("#status_active").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('active', id);
		});

		$("#status_inactive").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('inactive', id);
		});

		$("#remove_btn").live("click", function () {
			{/literal}
			return confirm('{escapejs}{l}Are you sure you want to delete this link type? All links in this link type will no longer display correctly, you will have to assign them a new link type manually! It's an irreversible process!!{/l}{/escapejs}');
			{literal}
		});

		function multiple_action(action) {
      	hash = dt_get_selected();
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_link_types_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								multiselect_links: hash
  					}),
  					cache: false,
 					success: function(response){
 						if (response == '1')
 							oTable.fnStandingRedraw();
 						else
 							alert('{/literal}{escapejs}{l}An errror occured while saving.{/l}{/escapejs}{literal}');
  					}
			});
		}
		
		function single_action(action, id) {
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_link_types_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								multiselect_links: id
  					}),
  					cache: false,
 					success: function(response){
 						if (response == '1')
 							oTable.fnStandingRedraw();
 						else
 							alert('{/literal}{escapejs}{l}An errror occured while saving.{/l}{/escapejs}{literal}' + response);
  					}
			});
		}

		$('#example tbody tr').live("click", function () {
			var chbox = '#categ_' + $(this).attr('id');
			if ($(this).hasClass('row_selected')) {
				$(this).removeClass('row_selected');
				$(chbox).attr('checked', false);
			} else {
				$(this).addClass('row_selected');
				$(chbox).attr('checked', true);
			}
		});
		
		function dt_get_selected() {
			var hash = '';
			var trs = oTable.fnGetNodes();
			for (var i=0 ; i<trs.length ; i++) {
				if ( $(trs[i]).hasClass('row_selected'))
					hash += trs[i].getAttribute("id") + ',';
			}
			hash = hash.slice(0, -1);
			return hash;
		}

oTable = $('#link_types').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                        "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "dir_link_types_ajax.php",

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
                                
                                $(nRow)
				
				{/literal}
				var action_html = '<a style="border: none; " href="dir_link_types_edit.php?action=U:' + id + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-up.png" title="Move Up" border="0"></a>&nbsp;&nbsp;<a style="border: none; " href="dir_link_types_edit.php?action=D:' + id + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-down.png" title="Move Down" border="0"></a>&nbsp;&nbsp;<a style="border: none; " href="dir_link_types_edit.php?action=E:' + id + '" title="Edit Available Submit Items"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" title="Edit Available Submit Items" /></a>';
                                if(aData[8] != 1 )
                                    action_html +='&nbsp;&nbsp;<a id="remove_btn" style="border: none; " href="dir_link_types_edit.php?action=R:' + id + '"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" title="Remove Link Type" /></a>';
                                else
                                    action_html +='&nbsp;&nbsp;<div style="border: none;width:32px;display:inline-block;height:13px; "></div>';
                                action_html += '&nbsp;&nbsp;<a style="border: none; " href="dir_links.php?status=2&link_type=' + id + '" title="{escapejs}{l}Browse links{/l}{/escapejs}: ' + aData[2] + '"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/folder_link.png" title="{escapejs}{l}Browse links{/l}{/escapejs}: ' + aData[2] + '" /></a>';
                                
				
				action_html += '&nbsp;&nbsp;<a style="border: none; " href="dir_submit_items.php?type=' + id + '" title="Configure Link Type"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/edit_link_type.png" title="Configure Link Type" /></a>';
                                
                                var status_html = '';
				if (aData[4] != 'Inactive')
					status_html += '<a href="" id="status_inactive" title="{l}Deactivate{/l}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
				if (aData[4] != 'Active')
					status_html += '<a href="" id="status_active" title="{l}Activate{/l}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';
				{literal}
				
				$('td:eq(3)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[4] + '</span>' + '<span style="float: right;">'+status_html+ '</span></div>');
				$('td:eq(6)', nRow).html(action_html);
                                $('td:eq(7)', nRow).hide();
				return nRow;
			},

			"aoColumns": [null, null, null, null, null, null, null, { "bSortable": false }]
			
		});
		oTable.fnSort( [ [1,'asc'] ] );
		oTable.fnSetColumnVis(0, false);
	});
	
});

</script>
{/literal}


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
<div class="block">
   <!-- Action Links -->
   
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/dir_link_types_edit.php?action=N" title="{l}Create new link type{/l}" class="button"><span>{l}New Link Type{/l}</span></a></li>
   </ul>
   <!-- /Action Links -->
</div>
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

<div class="block" style="padding-top: 30px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="link_types" name="link_types" class="display">
      <thead>
         <tr>
            {foreach from=$columns key=col item=name}
					<th>{$name|escape|trim}</th>
				{/foreach}
         </tr>
      </thead>

		<tbody>
			<tr>
				<td colspan="{$columns|count}" class="dataTables_empty">Loading data from server</td>
			</tr>
		</tbody>
	</table>
</div>

{/strip}