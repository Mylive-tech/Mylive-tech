{include file="messages.tpl"}

{literal}
<script type="text/javascript">
var oTable;
jQuery(function($) {

	$(document).ready(function() {

                $("#action_del").live("click", function() {
                   return link_rm_confirm("{/literal}{escapejs}{l}Are you sure you want to remove this widget zone?{/l}\n{l}Note: zones can not be restored after removal!{/l}{/escapejs}{literal}");
                });
		

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

	
		oTable = $('#example').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                       "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"oSearch": { "sSearch": "{/literal}{$search}{literal}" },
			
			"sAjaxSource": "{/literal}dir_widget_zones_ajax.php{literal}",

			"fnRowCallback": function(nRow, aData, iDisplayIndex) {
				var name = aData[0];
				$(nRow).attr("id",name);
				$(nRow).find("td").attr("id",name);
				
				$('td:eq(0)', nRow).html('<a href="{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_widgets_per_zone.php?Z='+aData[0]+'">'+aData[0]+'</a>');
				
				var action_edit = '';
				var action_del = '';
                               
                                if(aData[1] != 'CENTRAL' && aData[1] != 'VERTICAL'){
					{/literal}
					action_edit = '<a href="{$smarty.const.DOC_ROOT}/dir_widget_zones_edit.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit Zone{/l}{/escapejs}: ' + aData[0] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0" alt="{escapejs}{l}Edit{/l}{/escapejs}" /></a>';
					action_del = '<a id="action_del" href="{$smarty.const.DOC_ROOT}/dir_widget_zones_edit.php?action=D:' + aData[0] + '"  title="{escapejs}{l}Remove Zone{/l}{/escapejs}: ' + aData[0] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0" alt="{escapejs}{l}Delete{/l}{/escapejs}" /></a>';
					{literal}	
				}
				else action_edit = "<b><span style='color:#FF0000'>Default Zone</span></b>";
				
				
				$('td:eq(2)', nRow).html('<div style="width: 100%; text-align: center;">'+action_edit + '&nbsp;' + action_del+'</div>');
			
	
				return nRow;
			},
			
			"aoColumns": [
				null,
				null,
				{ "bSortable": false },
			]
		});
		oTable.fnSort( [ [2,'desc'] ] );
	});
	
	
	
});




</script>
{/literal}
{strip}

<div class="block">
   <!-- Widget Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/dir_widget_zones_edit.php?action=N" title="{l}Create a new Custom Widget Zone{/l}" class="button"><span class="new-categ">{l}New Custom Widget Zone{/l}</span></a></li>
   </ul>
   <div style="clear: both;">
   To insert Custom Widget Zone in Template file - use shortcode <b>{literal}{widget_zone name="Widget_Zone_Name"}{/literal}</b>
   </div>
   <!-- /Widget Action Links -->
</div>

{if $error}
<div class="block">
   <!-- Error -->
   <div class="error">
      <h2>{l}Error{/l}</h2>
      <p>{$error} {l}error(s) occured while processing.{/l}</p>
      {if !empty ($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p class="sql_error">{$sql_error}</p>
      {/if}
   </div>
</div>
{/if}

<style type="text/css" media="screen">
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/datatable_jui.css";
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/jquery-ui-1.7.2.custom.css";
</style>
<div class="block">

<form action="{$smarty.const.DOC_ROOT}/dir_widget_zones.php" method="post" id="multiselect_list" name="multiselect_list">
      <input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" />
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%; " id="example" name="example" class="display">
	<thead>
		<tr>
		{foreach from=$columns key=col item=name}
			<th>{$name|escape|trim}</th>
		{/foreach}
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="9" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
</table>

   </form>
   <!-- /Widget Zone List -->
</div>

{/strip}
