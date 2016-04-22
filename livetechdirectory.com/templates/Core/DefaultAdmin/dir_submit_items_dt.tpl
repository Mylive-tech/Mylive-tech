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
		
		$("#status_inactive").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('inactive', id);
		});

        $("#status_required_activate").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('required_activate', id);
		});
        
        $("#status_required_inactivate").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('required_inactivate', id);
		});
        
        $("#status_isdetail_activate").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('isdetail_activate', id);
		});
        
        $("#status_isdetail_inactivate").live("click", function () {
			var id = $(this).parent().parent().parent().attr('id');
			single_action('isdetail_inactivate', id);
		});
                
		$("#remove_btn").live("click", function () {
			 return confirm('{/literal}{escapejs}{l}Are you sure you want to delete this submit item? All the coresponding data will be lost! The process is ireversible!! You might want to simply inactivate this item.{/l}{/escapejs}{literal}');
		});

		function single_action(action, id) {
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_submit_items_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								tid: {/literal}{if $ltype}{$ltype}{else}''{/if}{literal},
  								multiselect_links: id
  					}),
  					cache: false,
 					success: function(response){
 						if (response == '1') {
                            oTable.fnStandingRedraw();
                        } else {
 							alert('{/literal}{escapejs}{l}An errror occured while saving.{/l}{/escapejs}{literal}');
                        }
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
			"bStateSave": true,
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
            "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"oSearch": { "sSearch": "{/literal}{$search}{literal}" },
			"sAjaxSource": "{/literal}dir_submit_items_ajax.php?type={$ltype}{literal}",
			
			"fnDrawCallback": function() {
			},

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
			
				{/literal}
				var action_reorder = '<a style="border: none; " href="dir_submit_items_edit.php?action=U:' + aData[0] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-up.png" title="Move Up" border="0"></a>&nbsp;&nbsp;<a style="border: none; " href="dir_submit_items_edit.php?action=D:' + aData[0] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/arrow-down.png" title="Move Down" border="0"></a>';
				{literal}
				
				{/literal}
				var action_edit = '<a href="{$smarty.const.DOC_ROOT}/dir_submit_items_edit.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit Submit Item{/l}{/escapejs}: ' + aData[2] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0"/></a>';
				{literal}
				
				var action_del = '{/literal}{escapejs}{l}[Cannot be removed]{/l}{/escapejs}{literal}';
				if (aData[3] != 'TITLE' && aData[3] != 'CATEGORY_ID' && aData[3] != 'META_KEYWORDS' && aData[3] != 'META_DESCRIPTION' && aData[3] != 'ADDRESS' && aData[3] != 'RECPR_URL' && aData[3] != 'CITY' && aData[3] != 'STATE' && aData[3] != 'ZIP') {
					{/literal}
						action_del = '<a id="remove_btn" href="{$smarty.const.DOC_ROOT}/dir_submit_items_edit.php?action=R:' + aData[0] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0"/></a>';
					{literal}
				}
				{/literal}
				{if ($ltype eq '')}
                  {literal}
                      $('td:eq(3)', nRow).html(action_reorder + '&nbsp;&nbsp;' + action_edit + '&nbsp;&nbsp;' + action_del);
                  {/literal}
                {/if}
				{literal}
				var stats = '';
				{/literal}
				{if ($ltype neq '')}
                  {literal}
                      var stats = '';
                      if (aData[3] != 'TITLE' && aData[3] != 'CATEGORY_ID' ) {
                          {/literal}
                          if (aData[6] != 'Inactive')
                              stats += '<a href="" id="status_inactive" title="{escapejs}{l}Deactivate{/l}{/escapejs}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
                          if (aData[6] != 'Active')
							stats += '<a href="" id="status_active" title="{escapejs}{l}Activate{/l}{/escapejs}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';
                          $('td:eq(4)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[6] + '</span>' + '<span style="float: right;">'+stats+ '</span></div>');
						  {literal}
					  } else {
						if (aData[3] == 'META_DESCRIPTION' ||  aData[3] == 'META_KEYWORDS') {
							{/literal}
							$('td:eq({if ($ltype neq '')}4{else}4{/if})', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[6] + '<font style="color: red; font-size: 8px;">&nbsp;{escapejs}{l}No Changes Allowed{/l} *{/escapejs}&nbsp;</font></span></div>');
							{literal}
						} else {
							{/literal}
							$('td:eq({if ($ltype neq '')}4{else}4{/if})', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[6] + '<font style="color: red; font-size: 8px;">&nbsp;{escapejs}{l}No Changes Allowed{/l}{/escapejs}&nbsp;</font></span></div>');
                                                        
							{literal}
						}                                                                                                                 
					}
                  {/literal}
                  var required = '';
                  if (aData[5] != '0')
                     required += '<a href="" id="status_required_inactivate" title="{escapejs}{l}Deactivate{/l}{/escapejs}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
                  else
                     required += '<a href="" id="status_required_activate" title="{escapejs}{l}Activate{/l}{/escapejs}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';
                  $('td:eq({if ($ltype neq '')}3{else}4{/if})', nRow).html('<div style="width: 100px;"><span style="float: left;"></span>' + '<span style="float: right;">'+required+'</span></div>');
                  var isdetail = '';
                  if (aData[4] != '0')
                     isdetail += '<a href="" id="status_isdetail_inactivate" title="{escapejs}{l}Deactivate{/l}{/escapejs}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
                  else
                     isdetail += '<a href="" id="status_isdetail_activate" title="{escapejs}{l}Activate{/l}{/escapejs}" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';
                  $('td:eq({if ($ltype neq '')}2{else}3{/if})', nRow).html('<div style="width: 100px;"><span style="float: left;"></span>' + '<span style="float: right;">'+isdetail+'</span></div>');
                  {literal}
				
				{/literal}
				{/if}
				{literal}                                 
                return nRow;
			}
		});
		oTable.fnSort( [ [1,'asc'] ] );
		
		{/literal}
		{if ($ltype neq '')}
			oTable.fnSetColumnVis(1, false);
		{/if}
		{literal}

		oTable.fnSetColumnVis(0, false);
	});
});
</script>
{/literal}
{strip}
{if ($ltype eq '')}
<div class="block">
   <!-- Action Links -->
   
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/dir_submit_items_edit.php?action=N" title="{l}Create new submit item{/l}" class="button"><span>{l}New Submit Item{/l}</span></a></li>
   </ul>
   <!-- /Action Links -->
</div>
{/if}
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
<form action="{$smarty.const.DOC_ROOT}/dir_submit_items.php" method="post" id="multiselect_list" name="multiselect_list">
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

<p align="center">* {l}To enable / disable META_KEYWORDS and META_DESCRIPTION use the following option: Settings > Search Engine Optimization > Enable META tags{/l}</p>

{/strip}