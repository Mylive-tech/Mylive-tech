{* Error and confirmation messages *}
{include file="messages.tpl"}

<script src="../javascripts/progressbar/jquery.progressbar.js"></script>

{if $tasks}
{literal}
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
   		url: 			"{/literal}{$smarty.const.DOC_ROOT}{literal}/task_manager_ajax.php",
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
{/literal}
{/if}

{strip}


<div class="warning_notice block" id="notice_not_active" style="display: block;">
	A cron job must be set in order to start Task Manager. Cpanel example: ***** php -q /home/USERNAME/public_html/cron/task_manager.php
</div>

<div class="block">


<br />
<div style="font-size: 13px; font-weight: bold;" align="center">Multiple Action Tasks</div>
<br />

<table class="list" style="margin-bottom: 30px;" width="100%">
	<thead>
 		<tr>
      	<th class="listHeader" width="25%">Task Name</th>
      	<th class="listHeader" width="25%">Description</th>
      	<th class="listHeader" width="12%">Last Run</th>
	<th class="listHeader" width="14%">Reload Frequency</th>
	<th class="listHeader" width="162" align="center">Progress</th>
      	<th class="listHeader">Status</th>
      	<th class="listHeader">Settings</th>
      </tr>
   </thead>

   <tbody>
   {if $tasks}
   	{foreach from=$tasks item=task}
      	<tr class="{cycle values="odd,even"} {if $task.STATUS neq '2'}task_row_inactive{/if}" id="task_row{$task.ID}" {if $task.STATUS neq 2}title="Inactive Task"{/if}>
				<td title="Items Done: {$task.DONE_NUM}, Items Total: {$task.TOTAL_NUM}"><b>{$task.NAME}</b></td>
				<td>{$task.DESCRIPTION}</td>
				<td>{if $task.LAST_RUN}{$task.LAST_RUN|date_format:$date_format}{else}N/A{/if}</td>
				<td style="text-align:center;">{$reload_freq[$task.LOAD_FREQ]}</td>
				<td width="162" align="center"><span class="progressBar" id="task_pb{$task.ID}">{$task.DONE_PERCENTS}%</span></td>
				
				<td align="center" id="{$task.ID}">
					<span id="task_status{$task.ID}">{$task_status[$task.STATUS]}</span><a href="" name="status_inactive{$task.ID}" id="status_inactive" onclick="return false;" style="float:right; border: none; {if $task.STATUS eq '1'}display: none;{/if}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a><a href="" name="status_active{$task.ID}" id="status_active" onclick="return false;" style="float:right; border: none; {if $task.STATUS eq '2'}display: none;{/if}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>
					{*{if $task.STATUS == 2}<a href="task_manager.php?a=P:{$task.ID}">Pause</a>{else}<a href="" onclick="return false;" id="{$task.ID}" name="start_task_btn">Start</a>{/if}&nbsp;<a href="task_manager.php?a=R:{$task.ID}">Restart</a>*}
				</td>
				<td align="center"><a style="border: none;" href="task_edit.php?a=E:{$task.ID}" title="Edit Settings"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/edit_link_type.png" title="Edit Settings" /></a></td>
			</tr>
   	{foreachelse}
    		<tr>
        		<td colspan="6" class="norec">{l}No records found.{/l}</td>
     		</tr>
   	{/foreach}
   {else}
   	<tr class="odd">
   		<td colspan="7" align="center">No tasks installed yet</td>
   	</tr>
   {/if}
   </tbody>
 </table>
 
 <div align="right"><table><tr><td style="width: 16px; height: 16px; border: 1px solid black; background-color: #bcb5b5; opacity: 0.5;">&nbsp;</td><td> - Inactive Task</td></tr></table></div>

 <br />
<div style="font-size: 13px; font-weight: bold;" align="center">Single Action Tasks</div>
<br />

 <table class="list" style="margin-bottom: 30px;" width="100%">
	<thead>
 		<tr>
      	<th class="listHeader" width="25%">Action Name</th>
      	<th class="listHeader" width="25%">Description</th>
      	<th class="listHeader" width="12%">Last Run</th>
	<th class="listHeader" width="14%">Reload Frequency</th>
      	<th class="listHeader" width="162" align="center">Status</th>
      	<th class="listHeader">Settings</th>
      </tr>
   </thead>

   <tbody>
   {if $tasks}
   	{foreach from=$actions item=action}
      	<tr class="{cycle values="odd,even"} {if $action.STATUS neq '2'}task_row_inactive{/if}" id="task_row{$action.ID}" {if $action.STATUS neq 2}title="Inactive Task"{/if}>
				<td title="{$action.NAME}"><b>{$action.NAME}</b></td>
				<td>{$action.DESCRIPTION}</td>
				<td>{if $action.LAST_RUN} {$action.LAST_RUN|date_format:$date_format}{else}N/A{/if}</td>
				<td style="text-align:center;">{$reload_freq[$action.LOAD_FREQ]}</td>
				<td align="center" id="{$action.ID}">
					<span id="task_status{$action.ID}">{$task_status[$action.STATUS]}</span><a href="" name="status_inactive{$action.ID}" id="status_inactive" onclick="return false;" style="float:right; border: none; {if $action.STATUS eq '1'}display: none;{/if}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_red.png" border="0"/></a><a href="" name="status_active{$action.ID}" id="status_active" onclick="return false;" style="float:right; border: none; {if $action.STATUS eq '2'}display: none;{/if}"><img src="{$smarty.const.TEMPLATE_ROOT}/images/tag_green.png" border="0"/></a>
					{*{if $task.STATUS == 2}<a href="task_manager.php?a=P:{$task.ID}">Pause</a>{else}<a href="" onclick="return false;" id="{$task.ID}" name="start_task_btn">Start</a>{/if}&nbsp;<a href="task_manager.php?a=R:{$task.ID}">Restart</a>*}
				</td>
				<td align="center"><a style="border: none;" href="task_edit.php?a=E:{$action.ID}" title="Edit Settings"><img border="0" src="{$smarty.const.TEMPLATE_ROOT}/images/edit_link_type.png" title="Edit Settings" /></a></td>
			</tr>
   	{foreachelse}
    		<tr>
        		<td colspan="6" class="norec">{l}No records found.{/l}</td>
     		</tr>
   	{/foreach}
   {else}
   	<tr class="odd">
   		<td colspan="7" align="center">No actions installed yet</td>
   	</tr>
   {/if}
   </tbody>

 </table>
 
<div align="right"><table><tr><td style="width: 16px; height: 16px; border: 1px solid black; background-color: #bcb5b5; opacity: 0.5;">&nbsp;</td><td> - Inactive Task</td></tr></table></div>


</div>
{/strip}
