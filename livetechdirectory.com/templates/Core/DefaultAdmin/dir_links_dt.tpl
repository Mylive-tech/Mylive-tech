{* Error and confirmation messages *}
{include file="messages.tpl"}

{literal}
<script type="text/javascript">

var oTable;

function multiple_action(action) {
    hash = dt_get_selected();
        jQuery.ajax({
url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php",
    type: "post",
    data: ({
        submitAction: action,
        category_id: function() { return jQuery("#CATEGORY_ID").val(); },
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


function dt_get_selected() {
    var hash = '';
    var trs = oTable.fnGetNodes();
    for (var i=0 ; i<trs.length ; i++) {
        if ( jQuery(trs[i]).hasClass('row_selected'))
            hash += trs[i].getAttribute("id") + ',';
    }
    hash = hash.slice(0, - 1);
    return hash;
}


	jQuery(document).ready(function() {

		jQuery("#multiple_controls input").click(function() {
			ctrl_name = jQuery(this).attr('name');
			isAutosubmitDisabled = jQuery(this).attr('autosubmit-disabled');
			if (ctrl_name != 'check_all' && ctrl_name != 'uncheck_all' && !isAutosubmitDisabled)
				multiple_action(ctrl_name);
		});
	
		jQuery("#check_all").click(function() {
			jQuery("#example tbody tr").each(function(n,element){
				var chbox = '#categ_' + jQuery(element).attr('id');
				jQuery(element).addClass('row_selected');
				jQuery(chbox).attr('checked', true);
			});
		});
		
		jQuery("#uncheck_all").click(function() {
			jQuery("#example tbody tr").each(function(n,element){
				var chbox = '#categ_' + jQuery(element).attr('id');
				jQuery(element).removeClass('row_selected');
				jQuery(chbox).attr('checked', false);
			});
		});
		
                jQuery("#action_del").live("click", function() {
                   return link_rm_confirm('{/literal}{escapejs}{l}Are you sure you want to remove this link?{/l}\n{l}Note: links can not be restored after removal!{/l}{/escapejs}{literal}');
                });
                

		jQuery("#status_active").live("click", function () {
			var id = jQuery(this).parent().parent().parent().attr('id');
			single_action('active', id);
		});
		
		jQuery("#status_pending").live("click", function () {
			var id = jQuery(this).parent().parent().parent().attr('id');
			single_action('pending', id);
		});
		
		jQuery("#status_inactive").live("click", function () {
			var id = jQuery(this).parent().parent().parent().attr('id');
			single_action('inactive', id);
		});


		function single_action(action, id) {
      	jQuery.ajax({
  					url: "{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								category_id: function() { return jQuery("#CATEGORY_ID").val(); },
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

		jQuery('#example tbody tr').live("click", function () {
			var chbox = '#categ_' + jQuery(this).attr('id');
			if (jQuery(this).hasClass('row_selected')) {
				jQuery(this).removeClass('row_selected');
				jQuery(chbox).attr('checked', false);
			} else {
				jQuery(this).addClass('row_selected');
				jQuery(chbox).attr('checked', true);
			}
		});

		oTable = jQuery('#example').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bJQueryUI": true,
			"bSort": true,
                        "iDisplayLength": 20,
			"sPaginationType": "full_numbers",
			"oSearch": { "sSearch": "{/literal}{$search}{literal}" },
                        "aaSorting": [[ 6, "desc" ]],
			"sAjaxSource": "{/literal}dir_links_ajax.php?category={$category}&expired={$expired}&status={$status}&link_type={$link_type}&f={$featured}&owner_id={$owner_id}{literal}",
			
			"fnDrawCallback": function() {
			
				jQuery('#example tbody tr td:nth-child(2) span').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php', {
					"height": "14px",
					submitdata : {
						submitAction: "title"
					},
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});
				
				jQuery('#example tbody tr td:nth-child(6)').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php', {
					"height": "14px",
					submitdata : {
						submitAction: "pr"
					},
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});
				
				jQuery('#example tbody tr td:nth-child(7)').editable('{/literal}{$smarty.const.DOC_ROOT}{literal}/dir_links_action_ajax.php', {
					"height": "14px",
					submitdata : {
						submitAction: "hits"
					},
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});

			},

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				jQuery(nRow).attr("id",id);
				jQuery(nRow).find("td").attr("id",id);

				jQuery('td:eq(0)', nRow).html('<input type="checkbox" name="categ_' + aData[0] + '" id="categ_' + aData[0] + '"> ' + aData[0]);
				
				jQuery('td:eq(1)', nRow).html('<span id="' + aData[0] + '">' + aData[1] + '</span>{/literal}<a class="visit-site" href="' + aData[10] + '" target="_blank" title="{escapejs}{l}Open site in new window{/l}{/escapejs}"><span>Visit</span></a><a href="{$smarty.const.DOC_ROOT}/link_details.php?id=' + aData[0] + '" title="{escapejs}{l}View full info of this item{/l}{/escapejs}" id="more-info-' + aData[0] + '" class="more-info" onclick="return false;"><span>{escapejs}{l}More info{/l}{/escapejs}</span></a>'{literal});
				
				jQuery('td:eq(1) #more-info-' + aData[0], nRow).click(function() {
					ajaxLinkDetailsRequest(aData[0]);
				});
				
				
				var stats = '';
				{/literal}
				if (aData[3] != 'Inactive')
					stats += '<a href="" id="status_inactive" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a>';
				if (aData[3] != 'Pending')
					stats += '<a href="" id="status_pending" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_yellow.png" border="0"/></a>';
				if (aData[3] != 'Active')
					stats += '<a href="" id="status_active" onclick="return false;" style="float:right;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>';			

				jQuery('td:eq(3)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[3] + '</span>' + '<span style="float: right;">'+stats+ '</span></div>');
				{literal}
				
				var action_edit = '';
				var action_del = '';
				{/literal}
				{if $rights.editLink eq 0 && $rights.delLink eq 0}
					action_edit = '<b>{escapejs}{l}None allowed{/l}{/escapejs}</b>';
				{elseif $rights.editLink eq 1}
					action_edit = '<a href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=E:' + aData[0] + '" title="{escapejs}{l}Edit Link{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/pencil.png" border="0"/></a>';
				{/if}
				{if $rights.delLink eq 1}
					action_del = '<a id="action_del" href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=D:' + aData[0] + '"  title="{escapejs}{l}Remove Link{/l}{/escapejs}: ' + aData[1] + '"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0"/></a>';
				{/if}
				{literal}
				
				jQuery('td:eq(8)', nRow).html(action_edit + '&nbsp;' + action_del);
				
				{/literal}
				if (aData[8] == '-1')
					categ_html = '<span class="orphan">{escapejs}{l}Orphan{/l}{/escapejs}</span>';
				else
					categ_html = '<a id="category-' + aData[8] + '" href="{$smarty.const.DOC_ROOT}/dir_links.php?category=' + aData[8] + '" title="{escapejs}{l}Browse links of category{/l}{/escapejs}: ' + aData[2] + '" class="category-link" onclick="return false;">' + aData[2] + '</a><a href="{$smarty.const.DOC_ROOT}/categ_details.php?id=' + aData[8] + '" title="{escapejs}{l}View full info of this item{/l}{/escapejs}" id="more-info-categ-' + aData[8] + '" class="more-info-categ" onclick="return false;"><span>{escapejs}{l}More info{/l}{/escapejs}</span></a>';
				{literal}
				
				jQuery('td:eq(2)', nRow).html(categ_html);
				
				jQuery('td:eq(2) #category-' + aData[8], nRow).click(function() {
					ajaxCategLinkRequest(aData[8]);
				});
				
				jQuery('td:eq(2) #more-info-categ-' + aData[8], nRow).click(function() {
					ajaxCategDetailsRequest(aData[8]);
				});
				
				{/literal}
				link_type_html = '<a id="link-type-' + aData[9] + '" href="{$smarty.const.DOC_ROOT}/dir_links.php?status=2&link_type=' + aData[9] + '" title="{escapejs}{l}Browse links{/l}{/escapejs}: ' + aData[7] + '" class="link-type" >' + aData[7] + '</a>';
				{literal}
				jQuery('td:eq(7)', nRow).html(link_type_html);
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
				{ "bSortable": false }
			]


		});
		
		oTable.fnSetColumnVis(10, false);

	});
	




</script>
{/literal}


{strip}
<div class="block">
   <!-- Action Links -->
   <ul class="page-action-list">
   	  {if $rights.addLink eq 1}
      <li><a href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=N{if $featured}&amp;f=1{/if}{if $smarty.get.category}&amp;category={$smarty.get.category}{/if}" title="{l}Create new link{/l}" class="button"><span class="new-link">{l}New Link{/l}</span></a></li>
	  {/if}
      {if $HaveExpiredRecpr gt 0}
         <li><a href="{$smarty.const.DOC_ROOT}/dir_links.php?expired=1{if $feat_link}&amp;f=1{/if}{if $categ gt 0}&amp;category={$categ}{/if}" title="{l}Browser listings with expired reciprocal URL{/l}" class="button"><span class="expired-link">{l}Expired Reciprocal{/l}</span></a></li>
      {/if}
      {if $rights.editLink eq 1}
      {if $rss_link eq true}
         <li><a href="{$smarty.const.DOC_ROOT}/dir_links_importrss.php?category={$rsscategory}" title="{l}Import from RSS{/l}" class="button"><span class="import-rss-link">{l}Import RSS{/l}</span></a></li>
      {/if}
      {/if}
      {if $rights.delLink eq 1}
      {if $HaveExpiredEmail gt 0}
         <li>
            <a href="{$smarty.const.DOC_ROOT}/dir_links.php?action=del_expired_emails" title="{l}Delete links with unconfirmed emails older than {/l}{$smarty.const.WAIT_FOR_EMAIL_CONF}{l} days{/l}" class="button" onclick="return link_rm_confirm('{l}Are you sure you want to remove {/l}{$HaveExpiredEmail}{l} links?{/l}\n{l}Note: links can not be restored after removal!{/l}');">
         <span class="expired-link">{l}Delete Expired{/l}</span></a></li>
      {/if}
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
<form action="{$smarty.const.DOC_ROOT}/dir_links.php" method="post" id="multiselect_list" name="multiselect_list">
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
{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/link_action_ajax.tpl" linkTypeButtons="1" linkNotifButtons=$expired}
</form>
</div>

{/strip}