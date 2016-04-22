<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 18:53:16
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_links_dt.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1940095321535aaf1ca7f3f0-20018962%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79fee1247c17e2b6f143a524cbcb9be1937beae3' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_links_dt.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1940095321535aaf1ca7f3f0-20018962',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'search' => 0,
    'category' => 0,
    'expired' => 0,
    'status' => 0,
    'link_type' => 0,
    'featured' => 0,
    'owner_id' => 0,
    'rights' => 0,
    'HaveExpiredRecpr' => 0,
    'feat_link' => 0,
    'categ' => 0,
    'rss_link' => 0,
    'rsscategory' => 0,
    'HaveExpiredEmail' => 0,
    'error' => 0,
    'sql_error' => 0,
    'columns' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535aaf1cc9e0e9_75021701',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535aaf1cc9e0e9_75021701')) {function content_535aaf1cc9e0e9_75021701($_smarty_tpl) {?><?php if (!is_callable('smarty_block_escapejs')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/block.escapejs.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



<script type="text/javascript">

var oTable;

function multiple_action(action) {
    hash = dt_get_selected();
        jQuery.ajax({
url: "<?php echo @DOC_ROOT;?>
/dir_links_action_ajax.php",
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
        alert('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
An errror occured while saving.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
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
                   return link_rm_confirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to remove this link?\nNote: links can not be restored after removal!<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
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
  					url: "<?php echo @DOC_ROOT;?>
/dir_links_action_ajax.php",
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
 							alert('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
An errror occured while saving.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
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
			"oSearch": { "sSearch": "<?php echo $_smarty_tpl->tpl_vars['search']->value;?>
" },
                        "aaSorting": [[ 6, "desc" ]],
			"sAjaxSource": "dir_links_ajax.php?category=<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
&expired=<?php echo $_smarty_tpl->tpl_vars['expired']->value;?>
&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
&link_type=<?php echo $_smarty_tpl->tpl_vars['link_type']->value;?>
&f=<?php echo $_smarty_tpl->tpl_vars['featured']->value;?>
&owner_id=<?php echo $_smarty_tpl->tpl_vars['owner_id']->value;?>
",
			
			"fnDrawCallback": function() {
			
				jQuery('#example tbody tr td:nth-child(2) span').editable('<?php echo @DOC_ROOT;?>
/dir_links_action_ajax.php', {
					"height": "14px",
					submitdata : {
						submitAction: "title"
					},
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});
				
				jQuery('#example tbody tr td:nth-child(6)').editable('<?php echo @DOC_ROOT;?>
/dir_links_action_ajax.php', {
					"height": "14px",
					submitdata : {
						submitAction: "pr"
					},
					callback: function(value, settings) {
						oTable.fnStandingRedraw();
					}
				});
				
				jQuery('#example tbody tr td:nth-child(7)').editable('<?php echo @DOC_ROOT;?>
/dir_links_action_ajax.php', {
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
				
				jQuery('td:eq(1)', nRow).html('<span id="' + aData[0] + '">' + aData[1] + '</span><a class="visit-site" href="' + aData[10] + '" target="_blank" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Open site in new window<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><span>Visit</span></a><a href="<?php echo @DOC_ROOT;?>
/link_details.php?id=' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View full info of this item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" id="more-info-' + aData[0] + '" class="more-info" onclick="return false;"><span><?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
More info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>');
				
				jQuery('td:eq(1) #more-info-' + aData[0], nRow).click(function() {
					ajaxLinkDetailsRequest(aData[0]);
				});
				
				
				var stats = '';
				
				if (aData[3] != 'Inactive')
					stats += '<a href="" id="status_inactive" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_red.png" border="0"/></a>';
				if (aData[3] != 'Pending')
					stats += '<a href="" id="status_pending" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_yellow.png" border="0"/></a>';
				if (aData[3] != 'Active')
					stats += '<a href="" id="status_active" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_green.png" border="0"/></a>';			

				jQuery('td:eq(3)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[3] + '</span>' + '<span style="float: right;">'+stats+ '</span></div>');
				
				
				var action_edit = '';
				var action_del = '';
				
				<?php if ($_smarty_tpl->tpl_vars['rights']->value['editLink']==0&&$_smarty_tpl->tpl_vars['rights']->value['delLink']==0){?>
					action_edit = '<b><?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
None allowed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>';
				<?php }elseif($_smarty_tpl->tpl_vars['rights']->value['editLink']==1){?>
					action_edit = '<a href="<?php echo @DOC_ROOT;?>
/dir_links_edit.php?action=E:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Link<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[1] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/pencil.png" border="0"/></a>';
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['rights']->value['delLink']==1){?>
					action_del = '<a id="action_del" href="<?php echo @DOC_ROOT;?>
/dir_links_edit.php?action=D:' + aData[0] + '"  title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove Link<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[1] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/cross.png" border="0"/></a>';
				<?php }?>
				
				
				jQuery('td:eq(8)', nRow).html(action_edit + '&nbsp;' + action_del);
				
				
				if (aData[8] == '-1')
					categ_html = '<span class="orphan"><?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Orphan<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>';
				else
					categ_html = '<a id="category-' + aData[8] + '" href="<?php echo @DOC_ROOT;?>
/dir_links.php?category=' + aData[8] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Browse links of category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[2] + '" class="category-link" onclick="return false;">' + aData[2] + '</a><a href="<?php echo @DOC_ROOT;?>
/categ_details.php?id=' + aData[8] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
View full info of this item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" id="more-info-categ-' + aData[8] + '" class="more-info-categ" onclick="return false;"><span><?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
More info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></a>';
				
				
				jQuery('td:eq(2)', nRow).html(categ_html);
				
				jQuery('td:eq(2) #category-' + aData[8], nRow).click(function() {
					ajaxCategLinkRequest(aData[8]);
				});
				
				jQuery('td:eq(2) #more-info-categ-' + aData[8], nRow).click(function() {
					ajaxCategDetailsRequest(aData[8]);
				});
				
				
				link_type_html = '<a id="link-type-' + aData[9] + '" href="<?php echo @DOC_ROOT;?>
/dir_links.php?status=2&link_type=' + aData[9] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Browse links<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[7] + '" class="link-type" >' + aData[7] + '</a>';
				
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



<div class="block"><!-- Action Links --><ul class="page-action-list"><?php if ($_smarty_tpl->tpl_vars['rights']->value['addLink']==1){?><li><a href="<?php echo @DOC_ROOT;?>
/dir_links_edit.php?action=N<?php if ($_smarty_tpl->tpl_vars['featured']->value){?>&amp;f=1<?php }?><?php if ($_GET['category']){?>&amp;category=<?php echo $_GET['category'];?>
<?php }?>" title="Create new link" class="button"><span class="new-link">New Link</span></a></li><?php }?><?php if ($_smarty_tpl->tpl_vars['HaveExpiredRecpr']->value>0){?><li><a href="<?php echo @DOC_ROOT;?>
/dir_links.php?expired=1<?php if ($_smarty_tpl->tpl_vars['feat_link']->value){?>&amp;f=1<?php }?><?php if ($_smarty_tpl->tpl_vars['categ']->value>0){?>&amp;category=<?php echo $_smarty_tpl->tpl_vars['categ']->value;?>
<?php }?>" title="Browser listings with expired reciprocal URL" class="button"><span class="expired-link">Expired Reciprocal</span></a></li><?php }?><?php if ($_smarty_tpl->tpl_vars['rights']->value['editLink']==1){?><?php if ($_smarty_tpl->tpl_vars['rss_link']->value==true){?><li><a href="<?php echo @DOC_ROOT;?>
/dir_links_importrss.php?category=<?php echo $_smarty_tpl->tpl_vars['rsscategory']->value;?>
" title="Import from RSS" class="button"><span class="import-rss-link">Import RSS</span></a></li><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['rights']->value['delLink']==1){?><?php if ($_smarty_tpl->tpl_vars['HaveExpiredEmail']->value>0){?><li><a href="<?php echo @DOC_ROOT;?>
/dir_links.php?action=del_expired_emails" title="Delete links with unconfirmed emails older than <?php echo @WAIT_FOR_EMAIL_CONF;?>
 days" class="button" onclick="return link_rm_confirm('Are you sure you want to remove <?php echo $_smarty_tpl->tpl_vars['HaveExpiredEmail']->value;?>
 links?\nNote: links can not be restored after removal!');"><span class="expired-link">Delete Expired</span></a></li><?php }?><?php }?></ul><!-- /Action Links --></div><?php if ($_smarty_tpl->tpl_vars['error']->value){?><div class="block"><!-- Error --><div class="error"><h2>Error</h2><p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
 error(s) occured while processing.</p><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p class="sql_error"><?php echo $_smarty_tpl->tpl_vars['sql_error']->value;?>
</p><?php }?></div><!-- /Error --></div><?php }?><br /><br /><style type="text/css" media="screen">@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/datatable_jui.css";@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/jquery-ui-1.7.2.custom.css";</style><div class="block"><form action="<?php echo @DOC_ROOT;?>
/dir_links.php" method="post" id="multiselect_list" name="multiselect_list"><input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" /><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%; " id="example" name="example" class="display"><thead><tr><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><th><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</th><?php } ?></tr></thead><tbody><tr><td colspan="9" class="dataTables_empty">Loading data from server</td></tr></tbody></table><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/link_action_ajax.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('linkTypeButtons'=>"1",'linkNotifButtons'=>$_smarty_tpl->tpl_vars['expired']->value), 0);?>
</form></div><?php }} ?>