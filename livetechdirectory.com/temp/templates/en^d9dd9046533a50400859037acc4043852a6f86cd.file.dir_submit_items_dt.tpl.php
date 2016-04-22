<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:03:41
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_submit_items_dt.tpl" */ ?>
<?php /*%%SmartyHeaderCode:555756243535ab18d05f5a7-34461802%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd9dd9046533a50400859037acc4043852a6f86cd' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_submit_items_dt.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '555756243535ab18d05f5a7-34461802',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ltype' => 0,
    'search' => 0,
    'error' => 0,
    'sql_error' => 0,
    'columns' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535ab18d253002_22025527',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab18d253002_22025527')) {function content_535ab18d253002_22025527($_smarty_tpl) {?><?php if (!is_callable('smarty_block_escapejs')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/block.escapejs.php';
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
			 return confirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to delete this submit item? All the coresponding data will be lost! The process is ireversible!! You might want to simply inactivate this item.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
		});

		function single_action(action, id) {
      	$.ajax({
  					url: "<?php echo @DOC_ROOT;?>
/dir_submit_items_action_ajax.php",
  					type: "post",
  					data: ({
  								submitAction: action,
  								tid: <?php if ($_smarty_tpl->tpl_vars['ltype']->value){?><?php echo $_smarty_tpl->tpl_vars['ltype']->value;?>
<?php }else{ ?>''<?php }?>,
  								multiselect_links: id
  					}),
  					cache: false,
 					success: function(response){
 						if (response == '1') {
                            oTable.fnStandingRedraw();
                        } else {
 							alert('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
An errror occured while saving.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
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
			"oSearch": { "sSearch": "<?php echo $_smarty_tpl->tpl_vars['search']->value;?>
" },
			"sAjaxSource": "dir_submit_items_ajax.php?type=<?php echo $_smarty_tpl->tpl_vars['ltype']->value;?>
",
			
			"fnDrawCallback": function() {
			},

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
			
				
				var action_reorder = '<a style="border: none; " href="dir_submit_items_edit.php?action=U:' + aData[0] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/arrow-up.png" title="Move Up" border="0"></a>&nbsp;&nbsp;<a style="border: none; " href="dir_submit_items_edit.php?action=D:' + aData[0] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/arrow-down.png" title="Move Down" border="0"></a>';
				
				
				
				var action_edit = '<a href="<?php echo @DOC_ROOT;?>
/dir_submit_items_edit.php?action=E:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit Submit Item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[2] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/pencil.png" border="0"/></a>';
				
				
				var action_del = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
[Cannot be removed]<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
				if (aData[3] != 'TITLE' && aData[3] != 'CATEGORY_ID' && aData[3] != 'META_KEYWORDS' && aData[3] != 'META_DESCRIPTION' && aData[3] != 'ADDRESS' && aData[3] != 'RECPR_URL' && aData[3] != 'CITY' && aData[3] != 'STATE' && aData[3] != 'ZIP') {
					
						action_del = '<a id="remove_btn" href="<?php echo @DOC_ROOT;?>
/dir_submit_items_edit.php?action=R:' + aData[0] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/cross.png" border="0"/></a>';
					
				}
				
				<?php if (($_smarty_tpl->tpl_vars['ltype']->value=='')){?>
                  
                      $('td:eq(3)', nRow).html(action_reorder + '&nbsp;&nbsp;' + action_edit + '&nbsp;&nbsp;' + action_del);
                  
                <?php }?>
				
				var stats = '';
				
				<?php if (($_smarty_tpl->tpl_vars['ltype']->value!='')){?>
                  
                      var stats = '';
                      if (aData[3] != 'TITLE' && aData[3] != 'CATEGORY_ID' ) {
                          
                          if (aData[6] != 'Inactive')
                              stats += '<a href="" id="status_inactive" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Deactivate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_red.png" border="0"/></a>';
                          if (aData[6] != 'Active')
							stats += '<a href="" id="status_active" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Activate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_green.png" border="0"/></a>';
                          $('td:eq(4)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[6] + '</span>' + '<span style="float: right;">'+stats+ '</span></div>');
						  
					  } else {
						if (aData[3] == 'META_DESCRIPTION' ||  aData[3] == 'META_KEYWORDS') {
							
							$('td:eq(<?php if (($_smarty_tpl->tpl_vars['ltype']->value!='')){?>4<?php }else{ ?>4<?php }?>)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[6] + '<font style="color: red; font-size: 8px;">&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No Changes Allowed *<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;</font></span></div>');
							
						} else {
							
							$('td:eq(<?php if (($_smarty_tpl->tpl_vars['ltype']->value!='')){?>4<?php }else{ ?>4<?php }?>)', nRow).html('<div style="width: 100px;"><span style="float: left;">'+aData[6] + '<font style="color: red; font-size: 8px;">&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
No Changes Allowed<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;</font></span></div>');
                                                        
							
						}                                                                                                                 
					}
                  
                  var required = '';
                  if (aData[5] != '0')
                     required += '<a href="" id="status_required_inactivate" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Deactivate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_red.png" border="0"/></a>';
                  else
                     required += '<a href="" id="status_required_activate" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Activate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_green.png" border="0"/></a>';
                  $('td:eq(<?php if (($_smarty_tpl->tpl_vars['ltype']->value!='')){?>3<?php }else{ ?>4<?php }?>)', nRow).html('<div style="width: 100px;"><span style="float: left;"></span>' + '<span style="float: right;">'+required+'</span></div>');
                  var isdetail = '';
                  if (aData[4] != '0')
                     isdetail += '<a href="" id="status_isdetail_inactivate" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Deactivate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_red.png" border="0"/></a>';
                  else
                     isdetail += '<a href="" id="status_isdetail_activate" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Activate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" onclick="return false;" style="float:right;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_green.png" border="0"/></a>';
                  $('td:eq(<?php if (($_smarty_tpl->tpl_vars['ltype']->value!='')){?>2<?php }else{ ?>3<?php }?>)', nRow).html('<div style="width: 100px;"><span style="float: left;"></span>' + '<span style="float: right;">'+isdetail+'</span></div>');
                  
				
				
				<?php }?>
				                                 
                return nRow;
			}
		});
		oTable.fnSort( [ [1,'asc'] ] );
		
		
		<?php if (($_smarty_tpl->tpl_vars['ltype']->value!='')){?>
			oTable.fnSetColumnVis(1, false);
		<?php }?>
		

		oTable.fnSetColumnVis(0, false);
	});
});
</script>

<?php if (($_smarty_tpl->tpl_vars['ltype']->value=='')){?><div class="block"><!-- Action Links --><ul class="page-action-list"><li><a href="<?php echo @DOC_ROOT;?>
/dir_submit_items_edit.php?action=N" title="Create new submit item" class="button"><span>New Submit Item</span></a></li></ul><!-- /Action Links --></div><?php }?><?php if ($_smarty_tpl->tpl_vars['error']->value){?><div class="block"><!-- Error --><div class="error"><h2>Error</h2><p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
 error(s) occured while processing.</p><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p class="sql_error"><?php echo $_smarty_tpl->tpl_vars['sql_error']->value;?>
</p><?php }?></div><!-- /Error --></div><?php }?><br /><br /><style type="text/css" media="screen">@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/datatable_jui.css";@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/jquery-ui-1.7.2.custom.css";</style><div class="block"><form action="<?php echo @DOC_ROOT;?>
/dir_submit_items.php" method="post" id="multiselect_list" name="multiselect_list"><input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" /><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%; " id="example" name="example" class="display"><thead><tr><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><th><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</th><?php } ?></tr></thead><tbody><tr><td colspan="9" class="dataTables_empty">Loading data from server</td></tr></tbody></table></form></div><p align="center">* To enable / disable META_KEYWORDS and META_DESCRIPTION use the following option: Settings > Search Engine Optimization > Enable META tags</p><?php }} ?>