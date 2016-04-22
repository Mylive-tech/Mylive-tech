<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:08:56
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/db_update.tpl" */ ?>
<?php /*%%SmartyHeaderCode:292413519535a96a8966b02-35787555%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1e713d982fa0a0e7f474dd54d2da9ba8826ecdfd' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/db_update.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '292413519535a96a8966b02-35787555',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validators' => 0,
    'error' => 0,
    'sql_error' => 0,
    'errorMsg' => 0,
    'action' => 0,
    'tables' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a96a8a18fc3_14210652',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a96a8a18fc3_14210652')) {function content_535a96a8a18fc3_14210652($_smarty_tpl) {?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"spider_form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>

<?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while saving.</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><style type="text/css">
			.infoDiv {margin: 10px 0; background: #FFD;}
			.infoDiv.success {background: #DFD;}
			.infoDiv.failure {background: #FDD;}
		</style><div class="block"><form method="post" action="" name="spider-google" id="spider_form"><table class="formPage"><thead><tr><th colspan="2">Database Update</th></tr></thead><?php if ($_smarty_tpl->tpl_vars['action']->value=="default"){?><tbody><tr><td colspan="2">If you have just uploaded all the changed files for your phpLD installation, you can click the button below and the phpLD will attempt to update the database. Please be aware this is an advanced feature, and it is highly recommended that you backup your database before taking action.</td></tr></tbody><tfoot><tr><td colspan="2"><input type="submit" name="submit" value="Update Database Now" alt="Update Database Now" title="Update Database Now" class="button" /></td></tr></tfoot><?php }else{ ?><tbody><tr><td colspan="2">Updating tables...</td></tr><tr><td colspan="2" id="result_content"><script type="text/javascript">jQuery(document).ready(function($) {var tables=<?php echo $_smarty_tpl->tpl_vars['tables']->value;?>
;

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
								});</script></td></tr></tbody><?php }?></table></form></div><?php }} ?>