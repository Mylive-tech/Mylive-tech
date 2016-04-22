{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}

<div class="block">
{if $mod == 'main'}
<table border="0" class="formPage">
	<thead>
		<tr>
			<th>{l}Permissions management{/l}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
			Here you can change main options for permission system. Create, modify and delete actions and assign them to objects.
			</td>
		</tr>
		<tr>
			<td>
			Actions lists | New object
			</td>
		</tr>
	</tbody>
</table>
{/if}
   <form method="post" action="">
   <table border="0" class="formPage">

   <thead>
      <tr>
         <th colspan="2">{l}Manage permissions system{/l}</th>
      </tr>
  </thead>

   <tbody>
		<tr>
			<td class="label">a</td>
			<td class="smallDesc">b</td>
		</tr>
   </tbody>

   <tfoot>
      <tr>
         <td><!-- /// --></td>
         <td><!-- /// --></td>
      </tr>
   </tfoot>
   </table>
   </form>
</div>
{/strip}