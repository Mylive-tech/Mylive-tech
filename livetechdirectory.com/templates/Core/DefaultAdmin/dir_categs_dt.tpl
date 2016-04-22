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
			var id = $(this).parents('td').attr('id');
			single_action('active', id);
		});				
		
		$("#status_inactive").live("click", function () {
			var id = $(this).parents('td').attr('id');
			single_action('inactive', id);
		});
		
                $("#action_del").live("click", function() {
                   return link_rm_confirm("{/literal}{escapejs}{l}Are you sure you want to remove this category?{/l}\n{l}Note: categories can not be restored after removal!{/l}{/escapejs}{literal}");
                });

		$("#remove").live("click", function () {
			var id = $(this).parent('td').attr('id');
			multiple_action('remove', id);
		});
		
		$("#removecomplete").live("click", function () {
			var id = $(this).parent('td').attr('id');
			multiple_action('removecomplete', id);
		});
		
		$("#changeparent").live("click", function () {
			var id = $(this).parent('td').attr('id');
			multiple_action('changeparent', id);
		});

		function multiple_action(action) {
      	hash = dt_get_selected();
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_categs_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								parent_id: function() { return $("#PARENT_ID").val(); },
  								multiselect_categs: hash
  					}),
  					cache: false,
 					success: function(response){
 						if (response != '1')
 							alert('{/literal}{escapejs}{l}An errror occured while saving.{/l}{/escapejs}{literal}' + response);
  					}
			});
			oTable.fnStandingRedraw();
		}
		
		function single_action(action, id) {
      	$.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_categs_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								category_id: function() { return $("#CATEGORY_ID").val(); },
  								multiselect_categs: id
  					}),
  					cache: false,
 					success: function(response){
 						if (response != '1')
 							alert('{/literal}{escapejs}{l}An errror occured while saving. {/l}{/escapejs}{literal}' + response);
  					}
			});
			oTable.fnStandingRedraw();
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
			
			"sAjaxSource": "{/literal}dir_categs_ajax.php?category={$category}&parent={$parent}&status={$status}{literal}",
			
			"fnDrawCallback": function() {
				$('#example tbody tr td:nth-child(0)').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php', {
					"height": "14px",
					submitdata : {
						submitAction: "title"
					},
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});
			},

			"fnRowCallback": function(nRow, aData, iDisplayIndex) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
				
				$('td:eq(0)', nRow).html('<input type="checkbox" name="categ_' + aData[0] + '" id="categ_' + aData[0] + '"> ' + aData[0]);
				
				if (aData[1].length >= 15)
					url_title = aData[1].slice(0, 15) + '...';
				else
					url_title = aData[1];
				
				//$('td:eq(1)', nRow).html('<a href="' + aData[1] + '" target="blank" title="' + aData[1] + '">' + url_title + '</a>');

				var symb = '';
				
				if (aData[4].length >= 4) {
				
					$('td:eq(3)', nRow).html('<div style="width: 100%; text-align: center;"><span style="color: green;">{/literal}{escapejs}{l}YES{/l}{/escapejs}{literal}</span></div>');
				}
				else {
					$('td:eq(3)', nRow).html('<div style="width: 100%; text-align: center;"><span style="color: red;">{/literal}{escapejs}{l}NO{/l}{/escapejs}{literal}</span></div>');
				}	
					
				
				var stats = '';
				{/literal}

				if (aData[5] != 'Inactive')
					stats += '<a href="" id="status_inactive" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
				if (aData[5] != 'Pending')
					stats += '<a href="" id="status_pending" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_yellow.png" border="0"/></a>';
				if (aData[5] != 'Active')
					stats += '<a href="" id="status_active" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';			

				$('td:eq(4)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[5] + '</span><span style="float: right;">' + stats+ '</span></div>');
				{literal}
				
				var action_edit = '';
				var action_del = '';
                                var action_close = '';
                                
				{/literal}
				{if $rights.editLink eq 0 && $rights.delLink eq 0}
					action_edit = '<b>{escapejs}{l}None allowed{/l}{/escapejs}</b>';
				{elseif $rights.editLink eq 1}
                                        if (aData[4] != '1')
                                            action_edit = '<a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit Category{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0" alt="{escapejs}{l}Edit{/l}{/escapejs}" /></a>';
                                        else
                                           action_edit = '<a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=E:' + aData[0] + '&s=1" title="{escapejs}{l}Edit Category{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0" alt="{escapejs}{l}Edit{/l}{/escapejs}" /></a>';
                                       action_close = '<a href="{$smarty.const.DOC_ROOT}/dir_categs_close.php?id='+ aData[0] +'" title="{escapejs}{l}Close Category to Activities{/l}{/escapejs}: '+ aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/key.png" border="0" alt="{escapejs}{l}Close{/l}{/escapejs}" /></a>';
                                {/if}
				{if $rights.delLink eq 1}
					action_del = '<a id="action_del" href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=D:' + aData[0] + '"  title="{escapejs}{l}Remove Category{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0" alt="{escapejs}{l}Delete{/l}{/escapejs}" /></a>';
				{/if}
				{literal}
				
				$('td:eq(7)', nRow).html('<div style="width: 100%; text-align: center;">'+action_edit + '&nbsp;' + action_close + '&nbsp;' + action_del+'</div>');
			
				if (aData[3] != '1')
					link_categ_id = aData[0];
				else
					link_categ_id = aData[3];
					
				$('td:eq(1)', nRow).html({/literal}'<a id="category-' + aData[0] + '" href="{$smarty.const.DOC_ROOT}/dir_links.php?category=' + link_categ_id + '" title="' + aData[1] + '" class="category-link" onclick="return false;">' + aData[1] + '</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id=' + aData[0] + '" title="{escapejs}{l}View full info of this item{/l}{/escapejs}" id="more-info-categ-' + aData[0] + '" class="more-info-categ" onclick="return false;"><span>{escapejs}{l}More info{/l}{/escapejs}</span></a>'{literal});
				
				$('td:eq(1) #more-info-categ-' + aData[0], nRow).click(function() {
					ajaxCategDetailsRequest(aData[0]);
				});
				
				$('td:eq(1) #category-' + aData[0], nRow).click(function() {
					ajaxCategLinkRequest(aData[0]);
				});
				
				{/literal}
				if (aData[9] == '-1')
					parent_html = '<span class="orphan">{escapejs}{l}Orphan{/l}{/escapejs}</span>';
				else
					parent_html = '<a id="parent-category-' + aData[9] + '" href="{$smarty.const.DOC_ROOT}/dir_categs.php?parent=' + aData[9] + '" title="' + aData[3] + '" class="category-link" onclick="return false;">' + aData[3] + '</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id=' + aData[9] + '" title="{escapejs}{l}View full info of this item{/l}{/escapejs}" id="more-info-pcateg-' + aData[9] + '" class="more-info-categ" onclick="return false;"><span>{escapejs}{l}More info{/l}{/escapejs}</span></a>';
				{literal}
				
				$('td:eq(2)', nRow).html(parent_html);
				
				
				$('td:eq(2) #more-info-pcateg-' + aData[9], nRow).click(function() {
					ajaxCategDetailsRequest(aData[9]);
				});
				$('td:eq(2) #parent-category-' + aData[9], nRow).click(function() {
					ajaxCategLinkRequest(aData[9]);
				});
				
				return nRow;
			},
			
			"aoColumns": [
				null,
				{ "sType": "html" },
				null,
				null,
				null,
				null,
				null,
				null,
				{ "bSortable": false },
				null
				
			]


		});
		
		oTable.fnSetColumnVis(2, false);
		oTable.fnSetColumnVis(9, false);

	});
	
});



</script>
{/literal}

{strip}
{if $action ne 'rebuild_cache'}
{if $err eq 1}
	<div class="success block">
		{$succesMsg}
	</div>
{elseif isset($err)}
	<div class="error block">
		{$errMsg}
	</div>
{/if}
<div class="block">
   <!-- Category Action Links -->
   <ul class="page-action-list">
   	  {if $rights.addCat eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=N" title="{l}Create a new category{/l}" class="button"><span class="new-categ">{l}New Category{/l}</span></a></li>
    <!--     <li><a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=N&amp;s=1" title="{l}Create a new symbolic category{/l}" class="button"><span class="new-symb-categ">{l}New Symbolic Category{/l}</span></a></li> -->
      {/if}
      {if $rights.addCat eq 1 || $rights.editCat eq 1 || $rights.delCat eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?action=rebuild_cache" title="{l}Rebuild Category Cache{/l}" class="button"><span class="rebuild-categ-cache">{l}Rebuild Category Cache{/l}</span></a></li>
      {/if}
      {if $rights.addCat eq 1 || $rights.editCat eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_categs.php?action=update_RSS" title="{l}Update RSS feeds for all categories{/l}" class="button"><span class="update-rss">{l}Update RSS feeds{/l}</span></a></li>
      {/if}
   </ul>
   <!-- /Category Action Links -->
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

<form action="{$smarty.const.DOC_ROOT}/dir_categs.php" method="post" id="multiselect_list" name="multiselect_list">
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
 {if $rights.editCat eq 1 || $rights.delCat eq 1}
   {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/categ_action_ajax.tpl"}
 {/if}
   </form>
   <!-- /Category List -->
</div>

{/if}
{/strip}