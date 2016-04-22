<?php /* Smarty version Smarty-3.1.12, created on 2014-04-30 08:01:26
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/task_manager.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6542826615360add6d93222-91445399%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1b6cc5b42eb2a23421ee3a7bb1d5356551d26ba3' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/task_manager.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6542826615360add6d93222-91445399',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tasks' => 0,
    'task' => 0,
    'date_format' => 0,
    'reload_freq' => 0,
    'task_status' => 0,
    'actions' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5360add7018962_14649262',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5360add7018962_14649262')) {function content_5360add7018962_14649262($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.date_format.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<script src="../javascripts/progressbar/jquery.progressbar.js"></script>

<?php if ($_smarty_tpl->tpl_vars['tasks']->value){?>

<script type="text/javascript">

jQuery(function($) {

	$(document).ready(function() {
	
	function proccess_task_status(task_id, status) {
   	if (status == '2') {
   		var status_text = 'Active';
   		$('a[name="status_active' + task_id + '"]').hide();
   		$('a[name="status_inactive' + task_id + '"]').show();
   		$("#task_row" + task_id).removeClass("task_row_inactive");
   	} else {
   		var status_text = 'Inactive';
   		$('a[name="status_inactive' + task_id + '"]').hide();
   		$('a[name="status_active' + task_id + '"]').show();
   		$("#task_row" + task_id).addClass("task_row_inactive");
   	}
   		
   	$("#task_status" + task_id).html(status_text);
  	}

  	function set_task_status(task_id, status) {
  		$.ajax({
   		url: 			"<?php echo @DOC_ROOT;?>
/task_manager_ajax.php",
   		type: 			"post",
   		data: 			({
   				action: "change_status",
   				value:  status,
   				id:		  task_id
   		}),
   		cache: 		false,
   		success: function(response) {
   			if (response == '1')
   				proccess_task_status(task_id, status);
   		}
   	});
  	}
   
   $('a[id="status_inactive"]').click(function () {
   	var item_id = $(this).parent().attr('id');
   	set_task_status(item_id, 1);
  	});
  	
  	$('a[id="status_active"]').click(function () {
   	var item_id = $(this).parent().attr('id');
   	set_task_status(item_id, 2);
  	});
   	
   });
});
</script>

<?php }?>

<div class="warning_notice block" id="notice_not_active" style="display: block;">A cron job must be set in order to start Task Manager. Cpanel example: ***** php -q /home/USERNAME/public_html/cron/task_manager.php</div><div class="block"><br /><div style="font-size: 13px; font-weight: bold;" align="center">Multiple Action Tasks</div><br /><table class="list" style="margin-bottom: 30px;" width="100%"><thead><tr><th class="listHeader" width="25%">Task Name</th><th class="listHeader" width="25%">Description</th><th class="listHeader" width="12%">Last Run</th><th class="listHeader" width="14%">Reload Frequency</th><th class="listHeader" width="162" align="center">Progress</th><th class="listHeader">Status</th><th class="listHeader">Settings</th></tr></thead><tbody><?php if ($_smarty_tpl->tpl_vars['tasks']->value){?><?php  $_smarty_tpl->tpl_vars['task'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['task']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tasks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['task']->key => $_smarty_tpl->tpl_vars['task']->value){
$_smarty_tpl->tpl_vars['task']->_loop = true;
?><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['task']->value['STATUS']!='2'){?>task_row_inactive<?php }?>" id="task_row<?php echo $_smarty_tpl->tpl_vars['task']->value['ID'];?>
" <?php if ($_smarty_tpl->tpl_vars['task']->value['STATUS']!=2){?>title="Inactive Task"<?php }?>><td title="Items Done: <?php echo $_smarty_tpl->tpl_vars['task']->value['DONE_NUM'];?>
, Items Total: <?php echo $_smarty_tpl->tpl_vars['task']->value['TOTAL_NUM'];?>
"><b><?php echo $_smarty_tpl->tpl_vars['task']->value['NAME'];?>
</b></td><td><?php echo $_smarty_tpl->tpl_vars['task']->value['DESCRIPTION'];?>
</td><td><?php if ($_smarty_tpl->tpl_vars['task']->value['LAST_RUN']){?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['task']->value['LAST_RUN'],$_smarty_tpl->tpl_vars['date_format']->value);?>
<?php }else{ ?>N/A<?php }?></td><td style="text-align:center;"><?php echo $_smarty_tpl->tpl_vars['reload_freq']->value[$_smarty_tpl->tpl_vars['task']->value['LOAD_FREQ']];?>
</td><td width="162" align="center"><span class="progressBar" id="task_pb<?php echo $_smarty_tpl->tpl_vars['task']->value['ID'];?>
"><?php echo $_smarty_tpl->tpl_vars['task']->value['DONE_PERCENTS'];?>
%</span></td><td align="center" id="<?php echo $_smarty_tpl->tpl_vars['task']->value['ID'];?>
"><span id="task_status<?php echo $_smarty_tpl->tpl_vars['task']->value['ID'];?>
"><?php echo $_smarty_tpl->tpl_vars['task_status']->value[$_smarty_tpl->tpl_vars['task']->value['STATUS']];?>
</span><a href="" name="status_inactive<?php echo $_smarty_tpl->tpl_vars['task']->value['ID'];?>
" id="status_inactive" onclick="return false;" style="float:right; border: none; <?php if ($_smarty_tpl->tpl_vars['task']->value['STATUS']=='1'){?>display: none;<?php }?>"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_red.png" border="0"/></a><a href="" name="status_active<?php echo $_smarty_tpl->tpl_vars['task']->value['ID'];?>
" id="status_active" onclick="return false;" style="float:right; border: none; <?php if ($_smarty_tpl->tpl_vars['task']->value['STATUS']=='2'){?>display: none;<?php }?>"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_green.png" border="0"/></a></td><td align="center"><a style="border: none;" href="task_edit.php?a=E:<?php echo $_smarty_tpl->tpl_vars['task']->value['ID'];?>
" title="Edit Settings"><img border="0" src="<?php echo @TEMPLATE_ROOT;?>
/images/edit_link_type.png" title="Edit Settings" /></a></td></tr><?php }
if (!$_smarty_tpl->tpl_vars['task']->_loop) {
?><tr><td colspan="6" class="norec">No records found.</td></tr><?php } ?><?php }else{ ?><tr class="odd"><td colspan="7" align="center">No tasks installed yet</td></tr><?php }?></tbody></table><div align="right"><table><tr><td style="width: 16px; height: 16px; border: 1px solid black; background-color: #bcb5b5; opacity: 0.5;">&nbsp;</td><td> - Inactive Task</td></tr></table></div><br /><div style="font-size: 13px; font-weight: bold;" align="center">Single Action Tasks</div><br /><table class="list" style="margin-bottom: 30px;" width="100%"><thead><tr><th class="listHeader" width="25%">Action Name</th><th class="listHeader" width="25%">Description</th><th class="listHeader" width="12%">Last Run</th><th class="listHeader" width="14%">Reload Frequency</th><th class="listHeader" width="162" align="center">Status</th><th class="listHeader">Settings</th></tr></thead><tbody><?php if ($_smarty_tpl->tpl_vars['tasks']->value){?><?php  $_smarty_tpl->tpl_vars['action'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['action']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['action']->key => $_smarty_tpl->tpl_vars['action']->value){
$_smarty_tpl->tpl_vars['action']->_loop = true;
?><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['action']->value['STATUS']!='2'){?>task_row_inactive<?php }?>" id="task_row<?php echo $_smarty_tpl->tpl_vars['action']->value['ID'];?>
" <?php if ($_smarty_tpl->tpl_vars['action']->value['STATUS']!=2){?>title="Inactive Task"<?php }?>><td title="<?php echo $_smarty_tpl->tpl_vars['action']->value['NAME'];?>
"><b><?php echo $_smarty_tpl->tpl_vars['action']->value['NAME'];?>
</b></td><td><?php echo $_smarty_tpl->tpl_vars['action']->value['DESCRIPTION'];?>
</td><td><?php if ($_smarty_tpl->tpl_vars['action']->value['LAST_RUN']){?> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['action']->value['LAST_RUN'],$_smarty_tpl->tpl_vars['date_format']->value);?>
<?php }else{ ?>N/A<?php }?></td><td style="text-align:center;"><?php echo $_smarty_tpl->tpl_vars['reload_freq']->value[$_smarty_tpl->tpl_vars['action']->value['LOAD_FREQ']];?>
</td><td align="center" id="<?php echo $_smarty_tpl->tpl_vars['action']->value['ID'];?>
"><span id="task_status<?php echo $_smarty_tpl->tpl_vars['action']->value['ID'];?>
"><?php echo $_smarty_tpl->tpl_vars['task_status']->value[$_smarty_tpl->tpl_vars['action']->value['STATUS']];?>
</span><a href="" name="status_inactive<?php echo $_smarty_tpl->tpl_vars['action']->value['ID'];?>
" id="status_inactive" onclick="return false;" style="float:right; border: none; <?php if ($_smarty_tpl->tpl_vars['action']->value['STATUS']=='1'){?>display: none;<?php }?>"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_red.png" border="0"/></a><a href="" name="status_active<?php echo $_smarty_tpl->tpl_vars['action']->value['ID'];?>
" id="status_active" onclick="return false;" style="float:right; border: none; <?php if ($_smarty_tpl->tpl_vars['action']->value['STATUS']=='2'){?>display: none;<?php }?>"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/tag_green.png" border="0"/></a></td><td align="center"><a style="border: none;" href="task_edit.php?a=E:<?php echo $_smarty_tpl->tpl_vars['action']->value['ID'];?>
" title="Edit Settings"><img border="0" src="<?php echo @TEMPLATE_ROOT;?>
/images/edit_link_type.png" title="Edit Settings" /></a></td></tr><?php }
if (!$_smarty_tpl->tpl_vars['action']->_loop) {
?><tr><td colspan="6" class="norec">No records found.</td></tr><?php } ?><?php }else{ ?><tr class="odd"><td colspan="7" align="center">No actions installed yet</td></tr><?php }?></tbody></table><div align="right"><table><tr><td style="width: 16px; height: 16px; border: 1px solid black; background-color: #bcb5b5; opacity: 0.5;">&nbsp;</td><td> - Inactive Task</td></tr></table></div></div>
<?php }} ?>