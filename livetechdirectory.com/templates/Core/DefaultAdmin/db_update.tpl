{* Error and confirmation messages *}
{include file="messages.tpl"}
{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="spider_form" validators=$validators}
{strip}

{if (isset($error) and $error gt 0) or !empty($sql_error)}
<div class="error block">
	<h2>{l}Error{/l}</h2>
	<p>{l}An error occured while saving.{/l}</p>
	{if !empty($errorMsg)}
		<p>{$errorMsg|escape}</p>
	{/if}
	{if !empty($sql_error)}
		<p>{l}The database server returned the following message:{/l}</p>
		<p>{$sql_error|escape}</p>
	{/if}
</div>
{/if}

<style type="text/css">
		{literal}
			.infoDiv {margin: 10px 0; background: #FFD;}
			.infoDiv.success {background: #DFD;}
			.infoDiv.failure {background: #FDD;}
		{/literal}
</style>
<div class="block">
	<form method="post" action="" name="spider-google" id="spider_form">
		<table class="formPage">
			<thead>
				<tr>
					<th colspan="2">{l}Database Update{/l}</th>
				</tr>
			</thead>
{if $action eq "default"}
			<tbody>
				<tr>
					<td colspan="2">{l}If you have just uploaded all the changed files for your phpLD installation, you can click the button below and the phpLD will attempt to update the database. Please be aware this is an advanced feature, and it is highly recommended that you backup your database before taking action{/l}.</td>
				</tr>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="2"><input type="submit" name="submit" value="{l}Update Database Now{/l}" alt="{l}Update Database Now{/l}" title="{l}Update Database Now{/l}" class="button" /></td>
				</tr>
			</tfoot>
{else}
			<tbody>
				<tr>
					<td colspan="2">{l}Updating tables{/l}...</td>
				</tr>
				<tr>
					<td colspan="2" id="result_content">
						<script type="text/javascript">
							jQuery(document).ready(function($) {ldelim}
								var tables={$tables};
								{literal}

								var container = $("#result_content");
								var info = {};
								var currentIndex=-1;
								function updateTable(tableIndex)
								{
									if(currentIndex<tableIndex)
									{
										currentIndex=tableIndex;
										if(tableIndex<tables.length)
										{
											InformInitiateRequest(tables[tableIndex]);
											$.ajax({
														url: window.location.url,
														data: {
															table: tables[tableIndex]
														},
														dataType: "json",
														type: "POST"
													})
													.done(function(data) {
														UpdateResult(tables[tableIndex], data.message, data.status);
														updateTable(tableIndex+1);
													})
													.fail(function(jqXHR, textStatus) {
														UpdateResult(tables[tableIndex], textStatus, -1);
														updateTable(tableIndex+1);
													});
										}
										else
										{
											alert("Database upgrade completed.\nReview page content for upgrade log.");
										}
									}
								}
								updateTable(0);

								function InformInitiateRequest(table)
								{
									var theDiv = $(document.createElement("div"))
											.addClass("infoDiv")
											.appendTo(container);
									info[table]=theDiv;
									$(document.createElement("div"))
											.appendTo(theDiv)
											.html("Initiating update of table <strong>'"+table+"'</strong>");
								}

								function UpdateResult(table, data, status)
								{
									var theDiv = info[table];
									$(document.createElement("div"))
											.appendTo(theDiv)
											.html(data);
									if(status==0)
									{
										$(document.createElement("div"))
												.appendTo(theDiv)
												.html("Table updated successfully");
										theDiv.addClass("success");
									}
									else
									{
										$(document.createElement("div"))
												.appendTo(theDiv)
												.html("Operation failed");
										theDiv.addClass("failure");
									}
								}
								{/literal}
							{rdelim});
						</script>
					</td>
				</tr>
			</tbody>
{/if}
		</table>
	</form>
</div>


{/strip}