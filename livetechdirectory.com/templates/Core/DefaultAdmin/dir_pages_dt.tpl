{* Error and confirmation messages *}
{include file="messages.tpl"}

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
		
		$("#status_pending").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('pending', id);
		});
		
		$("#status_inactive").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('inactive', id);
		});

                $("#action_del").live("click", function() {
                   return link_rm_confirm('{/literal}{escapejs}{l}Are you sure you want to remove this page?{/l}\n{l}Note: pages can not be restored after removal!{/l}{/escapejs}{literal}');
                });

		function multiple_action(action) {
      	hash = dt_get_selected();
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_pages_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								category_id: function() { return $("#CATEGORY_ID").val(); },
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
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_pages_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								category_id: function() { return $("#CATEGORY_ID").val(); },
  								multiselect_links: id
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
			"sAjaxSource": "{/literal}dir_pages_ajax.php?category={$category}&expired={$expired}&status={$status}&f={$featured}&owner_id={$owner_id}{literal}",
			
			"fnDrawCallback": function() {
				$('#example tbody tr td:nth-child(2) span').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_pages_action_ajax.php', {
					"height": "14px",
					submitdata : {
						submitAction: "title"
					},
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});
			},

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);

				$('td:eq(0)', nRow).html('<input type="checkbox" name="categ_' + aData[0] + '" id="categ_' + aData[0] + '"> ' + aData[0]);
				
				$('td:eq(1)', nRow).html('<span id="' + aData[0] + '">' + aData[1] + '</span>');
				
				$('td:eq(2)', nRow).html('<a href="{/literal}{$smarty.const.SITE_URL}{literal}page/' + aData[2] + '" target="blank" title="' + aData[1] + '">{/literal}{$smarty.const.SITE_URL}{literal}page/' + aData[2] + '</a>');
				var stats = '';
				{/literal}
				if (aData[3] != 'Inactive')
					stats += '<a href="" id="status_inactive" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
				if (aData[3] != 'Active')
					stats += '<a href="" id="status_active" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';			

				$('td:eq(3)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[3] + '</span>' + '<span style="float: right;">'+stats+ '</span></div>');
				{literal}
				
				var action_edit = '';
				var action_del = '';
				{/literal}
				{if $rights.editPage eq 0 && $rights.delPage eq 0}
					action_edit = '{escapejs}{l}<b>None allowed</b>{/l}{/escapejs}';
				{elseif $rights.editPage eq 1}
					action_edit = '<a href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit Page{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0"/></a>';
				{/if}
				{if $rights.delPage eq 1}
					action_del = '<a id="action_del" href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=D:' + aData[0] + '" title="{escapejs}{l}Remove Page{/l}: {/escapejs}' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0"/></a>';
				{/if}
				{literal}
				
				$('td:eq(7)', nRow).html(action_edit + '&nbsp;' + action_del);
								
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
				{ "bSortable": false }
			]
		});
		oTable.fnSetColumnVis(8, false);
	});
});



</script>
{/literal}

{strip}
<div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
   {if $rights.addPage eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=N" title="{l}New Page{/l}" class="button"><span class="new-image">{l}New Page{/l}</span></a></li>
   {/if}
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
   <!-- Links List -->
   <form action="{$smarty.const.DOC_ROOT}/dir_pages.php" method="post" id="multiselect_list" name="multiselect_list">
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
	{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/page_action_ajax.tpl" linkTypeButtons="1" linkNotifButtons=$expired}
   </form>
   <!-- /pages List -->
</div>
{/strip}
