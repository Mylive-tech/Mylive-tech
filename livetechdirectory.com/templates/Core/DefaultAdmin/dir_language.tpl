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

oTable = $('#example').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                        "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "dir_language_ajax.php",
			"fnDrawCallback": function() {
			
				$('#example tbody tr td:nth-child(3)').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_language_edit.php', {
					"height": "14px",
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});
			},
			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
				return nRow;
			}
		});
	

	});
	
});

</script>
{/literal}


{strip}

<div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/" title="{l}Create new link{/l}" class="button"><span class="new-link">{l}New Link{/l}</span></a></li>
   </ul>
   {html_options options=$languages selected=$smarty.const.FRONTEND_LANG name="LANGUAGE" id="LANGUAGE"}
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


<div class="block">
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="example" name="example" class="display">
      <thead>
         <tr>
            <th class="listHeader">ID</th>
            <th class="listHeader">Language</th>
            <th class="listHeader" width="100%">Text</th>
         </tr>
      </thead>

		<tbody>
			<tr>
				<td colspan="3" class="dataTables_empty">Loading data from server</td>
			</tr>
		</tbody>
	</table>
   </form>
</div>

{/strip}