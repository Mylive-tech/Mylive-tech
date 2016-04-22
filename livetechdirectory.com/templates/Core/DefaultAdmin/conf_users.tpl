{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
<div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/conf_users_edit.php?action=N" title="{l}Create new user{/l}" class="btn"><span class="new-user">{l}New User{/l}</span></a></li>
   </ul>
   <!-- /Action Links -->
</div>

<style type="text/css" media="screen">
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/datatable_jui.css";
   	@import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/jquery-ui-1.7.2.custom.css";
</style>

{literal}
<script type="text/javascript">
var oTable;
jQuery(function($) {

$(document).ready(function() {

oTable = $('#users').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                        "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "conf_users_ajax.php",

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
				
				if (aData[5] != '0')
					$('td:eq(5)', nRow).html('<a href="{/literal}{$smarty.const.DOC_ROOT}{literal}/article_list.php?owner_id=' + aData[0] + '">' + aData[5] + '</a>');
				
				if (aData[6] != '0')
					$('td:eq(6)', nRow).html('<a href="{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links.php?owner_id=' + aData[0] + '">' + aData[6] + '</a>');
					
				if (aData[7] != '0')
					$('td:eq(7)', nRow).html('<a href="{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_comments.php?owner_id=' + aData[0] + '">' + aData[7] + '</a>');
			
				if (aData[10] == '1')
					email_confirmed = '<span style="color: green;">YES</span>';
				else
					email_confirmed = '<span style="color: red;">NO</span>';
					
				$('td:eq(10)', nRow).html(email_confirmed);
					
				{/literal}
				{if $current_user_is_admin eq 1 or $id eq $current_user_id}
               action_edit = '<a href="{$smarty.const.DOC_ROOT}/conf_users_actions.php?action=E:' + aData[0] + '" title="{l}Edit user Actions{/l}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/actions.png" border="0"/></a>';
            {/if}
            {if $current_user_is_admin eq 1 or $id eq $current_user_id}
               action_edit = action_edit + '<a href="{$smarty.const.DOC_ROOT}/conf_users_edit.php?action=E:' + aData[0] + '" title="{l}Edit user{/l}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0"/></a>';
            {/if}
            {if $current_user_is_admin eq 1}
               action_edit = action_edit + '<a href="{$smarty.const.DOC_ROOT}/conf_users_edit.php?action=D:' + aData[0] + '" onclick="return user_rm_confirm("{escapejs}{l}Are you sure you want to remove this user account?{/l}\n{l}Note: user accounts can not be restored after removal!{/l}{/escapejs}");" title="{l}Remove user{/l}: {$row.LOGIN|escape|trim}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0"/></a>';
            {/if}
            {literal}
					
				$('td:eq(11)', nRow).html(action_edit);
				
				return nRow;
			},
			
			"aoColumns": [
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				{ "bSortable": false }
			]
			
		});

	});
	
});

</script>
{/literal}


<div class="block">
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="users" name="users" class="display">
      <thead>
      <tr>
        {foreach from=$columns key=col item=name}
			<th>{$name|escape|trim}</th>
		{/foreach}
      </tr>
   </thead>

		<tbody>
			<tr>
				<td colspan="10" class="dataTables_empty">Loading data from server</td>
			</tr>
		</tbody>
	</table>
   </form>
</div>

   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/list_pager.tpl"}
</div>
{/strip}