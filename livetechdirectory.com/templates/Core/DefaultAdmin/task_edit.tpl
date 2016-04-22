{* Error and confirmation messages *}
{include file="messages.tpl"}

{*include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators*}

{strip}

{if $account_info_empty eq 1}
<div class="error block">{l}It seems that you not enter your Google Account information. Please click here and check it out{/l}: <a href="{$smarty.const.DOC_ROOT}/conf_settings.php?c=22" title="{l}Edit Google Account{/l}">{l}Edit Google Account{/l}</a></div>
{/if}

{if isset($result)}
	{if $result > 0}
   	<div class="success block">
     	 {l}Settings saved.{/l}
   	</div>
   {else}
   	<div class="success block">
     	 {l}An error occured while saving Task Settings.{/l}
   	</div>
   {/if}
{/if}

<div class="block">
	<a style="text-decoration:none; font-weight: bold; " href="{$smarty.const.DOC_ROOT}/task_manager.php" title="Task Manager">Return To Task Manager</a>
</div>


<div class="block">
   <form method="post" action="" id="submit_form">
   <table class="formPage">
      <thead>
         <tr>
            <th colspan="2">Edit Task {$task_info.NAME}</th>
         </tr>
      </thead>

      <tbody>
      	<tr class="{cycle values='odd,even'}">
            <td  class="label"><label for="STATUS">Status:</label></td>
            <td  class="smallDesc">
            	{html_options options=$task_status selected=$task_info.STATUS name=STATUS id=STATUS}
            	<p class="msg notice info">Select Task Status</p>
            </td>
       	</tr>
	<tr class="{cycle values='odd,even'}">
            <td  class="label"><label for="LOAD_FREQ">Reload Frequency:</label></td>
            <td  class="smallDesc">
            	{html_options options=$load_freq selected=$task_info.LOAD_FREQ name=LOAD_FREQ id=LOAD_FREQ}
            	<p class="msg notice info">Task Reload Frequency after all items was procceed</p>
            </td>
       	</tr>
       	{if !empty($sets)}
       		{foreach from=$sets item=setting name=settings}
       			<tr class="{cycle values='odd,even'}">
            		<td  class="label"><label for="{$setting.ID}">{$setting.NAME}:</label></td>
            		<td  class="smallDesc">
            			{if $setting.OPTIONS}
            				{html_options output=$setting.OPTIONS values=$setting.OPTIONS selected=$setting.VALUE name=$setting.ID}
            			{elseif $setting.AVAILABLE eq 'CAT'}
            				{* Load category selection *}
								{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" selected=$setting.VALUE}
							{elseif $setting.AVAILABLE eq 'LINK_TYPE'}
								{html_options options=$link_types name=$setting.ID selected=$setting.VALUE}
            			{else}
            				<input type="text" id="{$setting.ID}" name="{$setting.ID}" value="{$setting.VALUE|escape|trim}" class="text" />
            			{/if}
            			{if $setting.DESCRIPTION}
            				<p class="msg notice info">{$setting.DESCRIPTION}</p>
            			{/if}
            		</td>
       			</tr>	
       		{/foreach}
       	{/if}

      </tbody>
      <tfoot>
         <tr>
         	<td><input type="button" name="back" value="{l}Back{/l}" alt="{l}Back{/l}" title="{l}Back{/l}" class="button" onclick="window.location.href='task_manager.php';" /></td>
            <td><input type="submit" name="submit" value="{l}Save Settings{/l}" alt="{l}Start Exporting{/l}" title="{l}Save Settings{/l}" class="button" /></td>
         </tr>
      </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   </form>
</div>
{/strip}