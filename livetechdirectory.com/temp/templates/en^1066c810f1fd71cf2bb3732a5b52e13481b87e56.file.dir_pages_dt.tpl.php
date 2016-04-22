<?php /* Smarty version Smarty-3.1.12, created on 2014-04-28 07:38:41
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_pages_dt.tpl" */ ?>
<?php /*%%SmartyHeaderCode:375583774535e058191a957-85437036%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1066c810f1fd71cf2bb3732a5b52e13481b87e56' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_pages_dt.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '375583774535e058191a957-85437036',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'search' => 0,
    'category' => 0,
    'expired' => 0,
    'status' => 0,
    'featured' => 0,
    'owner_id' => 0,
    'rights' => 0,
    'error' => 0,
    'sql_error' => 0,
    'columns' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535e0581a7af06_84738411',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535e0581a7af06_84738411')) {function content_535e0581a7af06_84738411($_smarty_tpl) {?><?php if (!is_callable('smarty_block_escapejs')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/block.escapejs.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



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
                   return link_rm_confirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to remove this page?\nNote: pages can not be restored after removal!<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                });

		function multiple_action(action) {
      	hash = dt_get_selected();
      	$.ajax({
  					url: "<?php echo @DOC_ROOT;?>
/dir_pages_action_ajax.php",
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
 							alert('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
An errror occured while saving.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
  					}
			});
		}
		
		function single_action(action, id) {
      	$.ajax({
  					url: "<?php echo @DOC_ROOT;?>
/dir_pages_action_ajax.php",
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
 							alert('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
An errror occured while saving.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
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
			"oSearch": { "sSearch": "<?php echo $_smarty_tpl->tpl_vars['search']->value;?>
" },
			"sAjaxSource": "dir_pages_ajax.php?category=<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
&expired=<?php echo $_smarty_tpl->tpl_vars['expired']->value;?>
&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
&f=<?php echo $_smarty_tpl->tpl_vars['featured']->value;?>
&owner_id=<?php echo $_smarty_tpl->tpl_vars['owner_id']->value;?>
",
			
			"fnDrawCallback": function() {
				$('#example tbody tr td:nth-child(2) span').editable('<?php echo @DOC_ROOT;?>
/dir_pages_action_ajax.php', {
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
				
				$('td:eq(2)', nRow).html('<a href="<?php echo @SITE_URL;?>
page/' + aData[2] + '" target="blank" title="' + aData[1] + '"><?php echo @SITE_URL;?>
page/' + aData[2] + '</a>');
				var stats = '';
				
				if (aData[3] != 'Inactive')
					stats += '<a href="" id="status_inactive" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_red.png" border="0"/></a>';
				if (aData[3] != 'Active')
					stats += '<a href="" id="status_active" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_green.png" border="0"/></a>';			

				$('td:eq(3)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[3] + '</span>' + '<span style="float: right;">'+stats+ '</span></div>');
				
				
				var action_edit = '';
				var action_del = '';
				
				<?php if ($_smarty_tpl->tpl_vars['rights']->value['editPage']==0&&$_smarty_tpl->tpl_vars['rights']->value['delPage']==0){?>
					action_edit = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<b>None allowed</b><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
				<?php }elseif($_smarty_tpl->tpl_vars['rights']->value['editPage']==1){?>
					action_edit = '<a href="<?php echo @DOC_ROOT;?>
/dir_pages_edit.php?action=E:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Page<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[1] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/pencil.png" border="0"/></a>';
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['rights']->value['delPage']==1){?>
					action_del = '<a id="action_del" href="<?php echo @DOC_ROOT;?>
/dir_pages_edit.php?action=D:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove Page: <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' + aData[1] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/cross.png" border="0"/></a>';
				<?php }?>
				
				
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


<div class="block"><!-- Action Links --><ul class="page-action-list"><?php if ($_smarty_tpl->tpl_vars['rights']->value['addPage']==1){?><li><a href="<?php echo @DOC_ROOT;?>
/dir_pages_edit.php?action=N" title="New Page" class="button"><span class="new-image">New Page</span></a></li><?php }?></ul><!-- /Action Links --></div><?php if ($_smarty_tpl->tpl_vars['error']->value){?><div class="block"><!-- Error --><div class="error"><h2>Error</h2><p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
 error(s) occured while processing.</p><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p class="sql_error"><?php echo $_smarty_tpl->tpl_vars['sql_error']->value;?>
</p><?php }?></div><!-- /Error --></div><?php }?><br /><br /><style type="text/css" media="screen">@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/datatable_jui.css";@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/jquery-ui-1.7.2.custom.css";</style><div class="block"><!-- Links List --><form action="<?php echo @DOC_ROOT;?>
/dir_pages.php" method="post" id="multiselect_list" name="multiselect_list"><input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" /><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%; " id="example" name="example" class="display"><thead><tr><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><th><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</th><?php } ?></tr></thead><tbody><tr><td colspan="9" class="dataTables_empty">Loading data from server</td></tr></tbody></table><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/page_action_ajax.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('linkTypeButtons'=>"1",'linkNotifButtons'=>$_smarty_tpl->tpl_vars['expired']->value), 0);?>
</form><!-- /pages List --></div>
<?php }} ?>