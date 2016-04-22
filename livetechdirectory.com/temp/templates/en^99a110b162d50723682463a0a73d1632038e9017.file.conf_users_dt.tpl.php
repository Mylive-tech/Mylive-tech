<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:45:16
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_users_dt.tpl" */ ?>
<?php /*%%SmartyHeaderCode:264650816535abb4c4e6472-90615632%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '99a110b162d50723682463a0a73d1632038e9017' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_users_dt.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '264650816535abb4c4e6472-90615632',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'search' => 0,
    'level' => 0,
    'category' => 0,
    'expired' => 0,
    'status' => 0,
    'featured' => 0,
    'owner_id' => 0,
    'current_user_is_admin' => 0,
    'id' => 0,
    'current_user_id' => 0,
    'error' => 0,
    'sql_error' => 0,
    'columns' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535abb4c609445_73590145',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abb4c609445_73590145')) {function content_535abb4c609445_73590145($_smarty_tpl) {?><?php if (!is_callable('smarty_block_escapejs')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/block.escapejs.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



<script type="text/javascript">
var oTable;
jQuery(function($) {

	$(document).ready(function() {
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
                   return link_rm_confirm("<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to remove this user?\nNote: users can not be restored after removal!<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
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
			"sAjaxSource": "conf_users_ajax.php?level=<?php echo $_smarty_tpl->tpl_vars['level']->value;?>
&category=<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
&expired=<?php echo $_smarty_tpl->tpl_vars['expired']->value;?>
&status=<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
&f=<?php echo $_smarty_tpl->tpl_vars['featured']->value;?>
&owner_id=<?php echo $_smarty_tpl->tpl_vars['owner_id']->value;?>
",
			
			"fnDrawCallback": function() {
			},

			"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				var id = aData[0];
				$(nRow).attr("id",id);
				$(nRow).find("td").attr("id",id);
				
				$('td:eq(2)', nRow).html('<br/>'+aData[2] + '<br/><br/>');
				
				var action_edit = '';
				var action_del = '';
				
				<?php if ($_smarty_tpl->tpl_vars['current_user_is_admin']->value!=1&&$_smarty_tpl->tpl_vars['id']->value!=$_smarty_tpl->tpl_vars['current_user_id']->value){?>
					action_edit = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<b>None allowed</b><?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
				<?php }elseif($_smarty_tpl->tpl_vars['current_user_is_admin']->value==1||$_smarty_tpl->tpl_vars['id']->value==$_smarty_tpl->tpl_vars['current_user_id']->value){?>
					action_edit_act = '<a href="<?php echo @DOC_ROOT;?>
/conf_users_actions.php?action=E:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit user Actions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[1] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/actions.png" border="0"/></a>'
					action_edit = '<a href="<?php echo @DOC_ROOT;?>
/conf_users_edit.php?action=E:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Edit User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[1] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/pencil.png" border="0"/></a>';
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['current_user_is_admin']->value==1||$_smarty_tpl->tpl_vars['id']->value==$_smarty_tpl->tpl_vars['current_user_id']->value){?>
					action_del = '<a id="action_del" href="<?php echo @DOC_ROOT;?>
/conf_users_edit.php?action=D:' + aData[0] + '" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Remove User<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: ' + aData[1] + '"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/cross.png" border="0"/></a>';
				<?php }?>
				
				
				$('td:eq(8)', nRow).html(action_edit_act + '&nbsp;' + action_edit + '&nbsp;' + action_del);
				
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
				null,
				{ "bSortable": false }
			]


		});
		
		oTable.fnSetColumnVis(9, false);

	});
	
});



</script>



<div class="block"><!-- Action Links --><ul class="page-action-list"><li><a href="<?php echo @DOC_ROOT;?>
/conf_users_edit.php?action=N" title="Create new user" class="btn"><span class="new-user">New User</span></a></li></ul><!-- /Action Links --></div><?php if ($_smarty_tpl->tpl_vars['error']->value){?><div class="block"><!-- Error --><div class="error"><h2>Error</h2><p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
 error(s) occured while processing.</p><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p class="sql_error"><?php echo $_smarty_tpl->tpl_vars['sql_error']->value;?>
</p><?php }?></div><!-- /Error --></div><?php }?><br /><br /><style type="text/css" media="screen">@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/datatable_jui.css";@import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/jquery-ui-1.7.2.custom.css";</style><div class="block"><form action="" method="post" id="multiselect_list" name="multiselect_list"><input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" /><table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100%; " id="example" name="example" class="display"><thead><tr><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><th><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</th><?php } ?></tr></thead><tbody><tr><td colspan="9" class="dataTables_empty">Loading data from server</td></tr></tbody></table></form></div><?php }} ?>