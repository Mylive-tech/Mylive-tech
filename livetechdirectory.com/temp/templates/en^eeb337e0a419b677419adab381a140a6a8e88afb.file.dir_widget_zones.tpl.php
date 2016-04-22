<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:13:32
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widget_zones.tpl" */ ?>
<?php /*%%SmartyHeaderCode:664321249535a97bc5639a9-35333981%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eeb337e0a419b677419adab381a140a6a8e88afb' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widget_zones.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '664321249535a97bc5639a9-35333981',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'search' => 0,
    'error' => 0,
    'sql_error' => 0,
    'columns' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a97bc6454b1_00438822',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a97bc6454b1_00438822')) {function content_535a97bc6454b1_00438822($_smarty_tpl) {?><?php if (!is_callable('smarty_block_escapejs')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/block.escapejs.php';
?><?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



<script type="text/javascript">
var oTable;
jQuery(function($) {

	$(document).ready(function() {

                $("#action_del").live("click", function() {
                   return link_rm_confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to remove this widget zone?\nNote: zones can not be restored after removal!<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
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
			"oSearch": { "sSearch": "<?php echo $_smarty_tpl->tpl_vars['search']->value;?>
" },
			
			"sAjaxSource": "dir_widget_zones_ajax.php",

			"fnRowCallback": function(nRow, aData, iDisplayIndex) {
				var name = aData[0];
				$(nRow).attr("id",name);
				$(nRow).find("td").attr("id",name);
				
				$('td:eq(0)', nRow).html('<a href="<?php echo @DOC_ROOT;?>
/dir_widgets_per_zone.php?Z='+aData[0]+'">'+aData[0]+'</a>');
				
				var action_edit = '';
				var action_del = '';
                               
                                if(aData[1] != 'CENTRAL' && aData[1] != 'VERTICAL'){
					
					action_edit = '<a href="<?php echo @DOC_ROOT;?>
/dir_widget_zones_edit.php?action=E:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Zone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[0] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/pencil.png" border="0" alt="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" /></a>';
					action_del = '<a id="action_del" href="<?php echo @DOC_ROOT;?>
/dir_widget_zones_edit.php?action=D:' + aData[0] + '"  title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove Zone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[0] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/cross.png" border="0" alt="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" /></a>';
						
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

<div class="block"><!-- Widget Action Links --><ul class="page-action-list"><li><a href="<?php echo @DOC_ROOT;?>
/dir_widget_zones_edit.php?action=N" title="Create a new Custom Widget Zone" class="button"><span class="new-categ">New Custom Widget Zone</span></a></li></ul><div style="clear: both;">To insert Custom Widget Zone in Template file - use shortcode <b>{widget_zone name="Widget_Zone_Name"}</b></div><!-- /Widget Action Links --></div><?php if ($_smarty_tpl->tpl_vars['error']->value){?><div class="block"><!-- Error --><div class="error"><h2>Error</h2><p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
 error(s) occured while processing.</p><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p class="sql_error"><?php echo $_smarty_tpl->tpl_vars['sql_error']->value;?>
</p><?php }?></div></div><?php }?><style type="text/css" media="screen">@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/datatable_jui.css";@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/jquery-ui-1.7.2.custom.css";</style><div class="block"><form action="<?php echo @DOC_ROOT;?>
/dir_widget_zones.php" method="post" id="multiselect_list" name="multiselect_list"><input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" /><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%; " id="example" name="example" class="display"><thead><tr><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><th><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</th><?php } ?></tr></thead><tbody><tr><td colspan="9" class="dataTables_empty">Loading data from server</td></tr></tbody></table></form><!-- /Widget Zone List --></div>
<?php }} ?>