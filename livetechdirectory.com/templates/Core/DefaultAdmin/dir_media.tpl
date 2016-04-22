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

	
		$("#check_all").click(function() {
			$("#media tbody tr").each(function(n,element){
				var chbox = '#categ_' + $(element).attr('id');
				$(element).addClass('row_selected');
				$(chbox).attr('checked', true);
			});
		});
		
		$("#uncheck_all").click(function() {
			$("#media tbody tr").each(function(n,element){
				var chbox = '#categ_' + $(element).attr('id');
				$(element).removeClass('row_selected');
				$(chbox).attr('checked', false);
			});
		});
		

		$("#remove_btn").live("click", function () {
			{/literal}
			return confirm('{escapejs}{l}Are you sure you want to delete this media? It's an irreversible process!{/l}{/escapejs}');
			{literal}
		});


		$('#media tbody tr').live("click", function () {
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

oTable = $('#media').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                        "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "dir_media_ajax.php",

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);

				if(aData[2] == 'image')
				$('td:eq(1)', nRow).html($('td:eq(1)', nRow).html()+' <img src="/uploads/media/'+aData[5]+'/'+aData[1]+'" height="20px;" style="float:right;">');
				$('td:eq(3)', nRow).html('<a href="conf_users_edit.php?action=E:'+aData[5]+'" title="Edit User" target="_blank">'+aData[3]+'</a>');
				
				{/literal}
				var action_html = '<a style="border: none; " href="dir_media_edit.php?action=E:' + id + '" title="Edit Media"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" title="Edit Media" /></a>&nbsp;&nbsp;<a id="remove_btn" style="border: none; " href="dir_media_edit.php?action=D:' + id + '"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" title="Remove Media" /></a>';
				{literal}
				
				$('td:eq(5)', nRow).html(action_html);
				return nRow;
			},

			"aoColumns": [null, null, null, null, null, { "bSortable": false }]
			
		});
		oTable.fnSort( [ [1,'asc'] ] );
	});
	
});

</script>
{/literal}


{literal}
<script type="text/javascript">

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
      <li><a href="{$smarty.const.DOC_ROOT}/dir_media_edit.php?action=N" title="{l}Add New Media{/l}" class="button"><span>{l}New Media{/l}</span></a></li>
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
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="media" name="link_types" class="display">
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
