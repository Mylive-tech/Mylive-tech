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

		/*
		$("#status_pending").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('pending', id);
		});
		*/
		
		$("#status_inactive").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('inactive', id);
		});

		function multiple_action(action) {
      	hash = dt_get_selected();
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/submit_items_action_ajax.php",
  					type: "post",
  					data: ({
  								tid: {/literal}{$tid}{literal},
  								submitAction: action,
  								multiselect_links: hash
  					}),
  					cache: false,
 					success: function(response){
 						if (response == '1')
 							oTable.fnStandingRedraw();
 						else
 							alert('An errror occured while saving.');
  					}
			});
		}
		
		function single_action(action, id) {
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/submit_items_action_ajax.php",
  					type: "post",
  					data: ({
  								tid: {/literal}{$tid}{literal},
  								submitAction: action,
  								multiselect_links: id
  					}),
  					cache: false,
 					success: function(response){
 						if (response == '1')
 							oTable.fnStandingRedraw();
 						else
 							alert('An errror occured while saving.' + response);
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
			hash = hash.slice(0, - 1);
			return hash;
		}


oTable = $('#submit_items').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                        "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "submit_items_ajax.php?id={/literal}{$tid}{literal}",

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
				
				{/literal}
				var action_html = '<a style="border: none; " href="dir_submit_items_edit.php?action=U:' + id + ':{$tid}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-up.png" title="Move Up" border="0"></a>&nbsp;&nbsp;<a style="border: none; " href="dir_submit_items_edit.php?action=E:' + id + ':{$tid}" title="Edit Item"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" title="Edit Item" /></a>&nbsp;&nbsp;<a style="border: none; " href="dir_submit_items_edit.php?action=D:' + id + ':{$tid}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-down.png" title="Move Down" border="0"></a>';			
				var status_html = '';
				if (aData[3] != 'Inactive')
					status_html += '<a href="" id="status_inactive" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
				/*
				if (aData[3] != 'Pending')
					status_html += '<a href="" id="status_pending" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_yellow.png" border="0"/></a>';
				*/
				if (aData[3] != 'Active')
					status_html += '<a href="" id="status_active" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';
				{literal}
				
				$('td:eq(2)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[3] + '</span>' + '<span style="float: right;">'+status_html+ '</span></div>');
				$('td:eq(4)', nRow).html(action_html);
				
				return nRow;
			},

			"aoColumns": [null, null, null, null, null, { "bSortable": false }]
			
		});
		
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
<a href="dir_submit_items_edit.php?action=N:{$tid}" title="New Submit Item">New Submit Item</a>
<div class="block">
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="submit_items" name="submit_items" class="display">
      <thead>
         <tr>
            {foreach from=$columns key=col item=name}
					<th>{$name|escape|trim}</th>
				{/foreach}
         </tr>
      </thead>

		<tbody>
			<tr>
				<td colspan="4" class="dataTables_empty">Loading data from server</td>
			</tr>
		</tbody>
	</table>
</div>
{/strip}
