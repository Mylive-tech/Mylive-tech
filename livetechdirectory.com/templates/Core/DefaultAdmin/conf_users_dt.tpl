{* Error and confirmation messages *}
{include file="messages.tpl"}

{literal}
<script type="text/javascript">
var oTable;
jQuery(function($) {

	$(document).ready(function() {
		$("#status_active").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('active', id);
		});
		
		$("#status_pending").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('pending', id);
		});
		
		$("#status_inactive").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('inactive', id);
		});


                $("#action_del").live("click", function() {
                   return link_rm_confirm("{/literal}{escapejs}{l}Are you sure you want to remove this user?{/l}\n{l}Note: users can not be restored after removal!{/l}{/escapejs}{literal}");
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
	
		oTable = $('#example').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                        "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"oSearch": { "sSearch": "{/literal}{$search}{literal}" },
			"sAjaxSource": "{/literal}conf_users_ajax.php?level={$level}&category={$category}&expired={$expired}&status={$status}&f={$featured}&owner_id={$owner_id}{literal}",
			
			"fnDrawCallback": function() {
			},

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
				
				$('td:eq(2)', nRow).html('<br/>'+aData[2] + '<br/><br/>');
				
				var action_edit = '';
				var action_del = '';
				{/literal}
				{if $current_user_is_admin neq 1 and $id neq $current_user_id}
					action_edit = '{escapejs}{l}<b>None allowed</b>{/l}{/escapejs}';
				{elseif  $current_user_is_admin eq 1 or $id eq $current_user_id}
					action_edit_act = '<a href="{$smarty.const.DOC_ROOT}/conf_users_actions.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit user Actions{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/actions.png" border="0"/></a>'
					action_edit = '<a href="{$smarty.const.DOC_ROOT}/conf_users_edit.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit User{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0"/></a>';
				{/if}
				{if  $current_user_is_admin eq 1 or $id eq $current_user_id}
					action_del = '<a id="action_del" href="{$smarty.const.DOC_ROOT}/conf_users_edit.php?action=D:' + aData[0] + '" title="{escapejs}{l}Remove User{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0"/></a>';
				{/if}
				{literal}
				
				$('td:eq(8)', nRow).html(action_edit_act + '&nbsp;' + action_edit + '&nbsp;' + action_del);
				
				return nRow;
			},
			
			"aoColumns": [
				null,
				null,
				{ "sType": "html" },
				null,
				null,
				null,
				null,
				null,
				{ "bSortable": false }
			]


		});
		
		oTable.fnSetColumnVis(9, false);

	});
	
});



</script>
{/literal}


{strip}
<div class="block">
    <!-- Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/conf_users_edit.php?action=N" title="{l}Create new user{/l}" class="btn"><span class="new-user">{l}New User{/l}</span></a></li>
   </ul>
   <!-- /Action Links -->
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
   <!-- /Error -->
</div>
{/if}

<br /><br />
<style type="text/css" media="screen">
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/datatable_jui.css";
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/jquery-ui-1.7.2.custom.css";
</style>
<div class="block">
<form action="" method="post" id="multiselect_list" name="multiselect_list">
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
</div>

{/strip}