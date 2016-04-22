<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:52:50
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/spider.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2025523497535abd122564c7-54169077%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6cc59c396117e792811f344d63eda5381d86363b' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/spider.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2025523497535abd122564c7-54169077',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validators' => 0,
    'error' => 0,
    'sql_error' => 0,
    'errorMsg' => 0,
    'showImport' => 0,
    'action' => 0,
    'langChoice' => 0,
    'gdata' => 0,
    'googleResultsCount' => 0,
    'ddata' => 0,
    'importSuccess' => 0,
    'importSkipped' => 0,
    'importErrors' => 0,
    'importResults' => 0,
    'for_dmoz' => 0,
    'nextStart' => 0,
    'start' => 0,
    'googleAddress' => 0,
    'link_types' => 0,
    'k' => 0,
    'linktypeid' => 0,
    'v' => 0,
    'key' => 0,
    'result' => 0,
    'has_url' => 0,
    'has_description' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535abd12499c47_08458561',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abd12499c47_08458561')) {function content_535abd12499c47_08458561($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
if (!is_callable('smarty_function_formtool_checkall')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.formtool_checkall.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"spider_form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>

<?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while saving.</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if (!$_smarty_tpl->tpl_vars['showImport']->value||!$_smarty_tpl->tpl_vars['action']->value||empty($_smarty_tpl->tpl_vars['action']->value)){?><div class="block"><form method="post" action="" name="spider-google" id="spider_form"><table class="formPage"><thead><tr><th colspan="2">Google import</th></tr></thead><tbody><tr><td colspan="2">The spider only works for addiing Regular Links and may not work with other link types!</td></tr><tr><td class="label required"><label for="host">Search in:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['langChoice']->value,'selected'=>$_smarty_tpl->tpl_vars['gdata']->value['lr'],'name'=>"lr",'id'=>"lr"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="as_q">Keywords:</label></td><td class="smallDesc"><input type="text" id="as_q" name="as_q" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['gdata']->value['as_q'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></td></tr><tr><td class="label required"><label for="num">Number of links to request:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['googleResultsCount']->value,'selected'=>$_smarty_tpl->tpl_vars['gdata']->value['num'],'name'=>"num",'id'=>"num"),$_smarty_tpl);?>
</td></tr></tbody><tfoot><tr><td><input type="reset" id="reset-gspider-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-gspider-submit" name="submit" value="Continue" alt="Continue" title="Continue" class="button" /></td></tr></tfoot></table><input type="hidden" name="formSubmitted" value="1" /></form></div><div class="block"><form method="post" action=""><table class="formPage"><thead><tr><th colspan="2">Dmoz import</th></tr></thead><tbody><tr><td class="label required"><label for="dmozurl">Category URL from <a href="http://www.dmoz.org/" title="Browse Open Directory Project homepage" target="_blank">dmoz.org</a>:</label></td><td class="smallDesc"><input type="text" id="dmozurl" name="dmozurl" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['ddata']->value['dmozurl'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><p class="info">Example: <code>http://www.dmoz.org/category/subcategory/</code></p></td></tr><tr><td class="label required"><label for="dimpdomain">Import domain only:</label></td><td class="smallDesc"><input type="checkbox" name="impdomain" id="dimpdomain" value="<?php echo $_smarty_tpl->tpl_vars['ddata']->value['impdomain'];?>
"<?php if ($_smarty_tpl->tpl_vars['ddata']->value['impdomain']=='1'){?> checked="checked"<?php }?> /></td></tr></tbody><tfoot><tr><td><input type="reset" id="reset-dspider-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-dspider-submit" name="dsubmit" value="Continue" alt="Continue" title="Continue" class="button" /></td></tr></tfoot></table><input type="hidden" name="action" value="dmoz" /></form></div><?php }else{ ?><?php if (isset($_smarty_tpl->tpl_vars['importSuccess']->value)&&$_smarty_tpl->tpl_vars['importSuccess']->value>0){?><div class="success block"><p><?php echo $_smarty_tpl->tpl_vars['importSuccess']->value;?>
 links successfully imported!</p></div><?php }?><?php if (isset($_smarty_tpl->tpl_vars['importSkipped']->value)&&$_smarty_tpl->tpl_vars['importSkipped']->value>0){?><div class="notice block"><p><?php echo $_smarty_tpl->tpl_vars['importSkipped']->value;?>
 links skipped from import! Already available!</p></div><?php }?><?php if (isset($_smarty_tpl->tpl_vars['importErrors']->value)&&$_smarty_tpl->tpl_vars['importErrors']->value>0){?><div class="error block"><p>Importing errors: <?php echo $_smarty_tpl->tpl_vars['importErrors']->value;?>
</p></div><?php }?><?php if (!is_array($_smarty_tpl->tpl_vars['importResults']->value)||empty($_smarty_tpl->tpl_vars['importResults']->value)){?><div id="noreserror" <?php if ($_smarty_tpl->tpl_vars['for_dmoz']->value!=1){?>style="display: none;"<?php }?> class="error block"><p>No results found.</p></div><?php }else{ ?><div class="block"><form action="" method="post"><input type="hidden" id="start" name="start" value="<?php echo $_smarty_tpl->tpl_vars['nextStart']->value;?>
" class="hidden" /><input type="hidden" id="prevStart" name="prevStart" value="<?php echo $_smarty_tpl->tpl_vars['start']->value;?>
" class="hidden" /><?php if (!empty($_smarty_tpl->tpl_vars['googleAddress']->value)){?><div class="notice"><h2><label for="googleURL">Fetched Google URL</label></h2><p><input type="text" id="googleURL" name="googleURL" value="<?php echo trim($_smarty_tpl->tpl_vars['googleAddress']->value);?>
" class="text" /> <a href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['googleAddress']->value, ENT_QUOTES, 'UTF-8', true));?>
" title="Browse fetched Google URL" target="_blank">Open</a></p></div></div><?php }?><table class="formPage"><tbody><tr><td class="label required"><label for="CATEGORY_ID">Category:</label></td><td class="smallDesc"><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/admin_category_select.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</td></tr><tr><td class="label required"><label for="LINK_TYPE">Link Type:</label></td><td class="smallDesc"><select name="LINK_TYPE" id="LINK_TYPE"><?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['link_types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['linktypeid']->value==$_smarty_tpl->tpl_vars['k']->value){?> selected="selected" <?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value['NAME'];?>
</option><?php } ?></select></td></tr><tr><td><input type="reset" id="reset-import-submit-top" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /><?php echo smarty_function_formtool_checkall(array('name'=>"toimport[]",'checkall_text'=>"Check All",'uncheckall_text'=>"Uncheck All",'class'=>"btn",'id'=>"checkallButton"),$_smarty_tpl);?>
</td><td><input type="submit" id="import-top" name="import" value="Continue" alt="Continue" title="Continue" class="button" /></td></tr><?php  $_smarty_tpl->tpl_vars['result'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['result']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['importResults']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['result']->key => $_smarty_tpl->tpl_vars['result']->value){
$_smarty_tpl->tpl_vars['result']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['result']->key;
?><tr class="thead"><th><label><input type="checkbox" name="toimport[]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" title="Check box to select this item." checked="checked" /></label></th><th><?php echo trim($_smarty_tpl->tpl_vars['result']->value['TITLE']);?>
</th></tr><tr><td class="label required"><label for="TITLE-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
">Title:</label></td><td class="smallDesc"><input type="text" id="TITLE-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" name="TITLE[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" value="<?php echo trim($_smarty_tpl->tpl_vars['result']->value['TITLE']);?>
" class="text" /></td></tr><?php if ($_smarty_tpl->tpl_vars['has_url']->value==1){?><tr><td class="label required"><label for="URL-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
">URL:</label></td><td class="smallDesc"><input type="text" id="URL-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" name="URL[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /> <a href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['result']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
" target="_blank">Browse</a></td></tr><?php }?><?php if ($_smarty_tpl->tpl_vars['has_description']->value==1){?><tr><td class="label"><label for="DESCRIPTION-[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]">Description:</label></td><td class="smallDesc"><textarea id="DESCRIPTION-[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" name="DESCRIPTION[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" rows="6" cols="50" class="text"><?php echo trim($_smarty_tpl->tpl_vars['result']->value['DESCRIPTION']);?>
</textarea></td></tr><?php }?><tr><td class="label"><label for="OWNER_NAME-[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]">Owner Name:</label></td><td class="smallDesc"><input type="text" id="OWNER_NAME-[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" name="OWNER_NAME[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" value="<?php echo trim($_smarty_tpl->tpl_vars['result']->value['OWNER_NAME']);?>
" maxlength="<?php echo @USER_NAME_MAX_LENGTH;?>
" class="text" /></td></tr><tr><td class="label"><label for="OWNER_EMAIL-[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]">Owner Email:</label></td><td class="smallDesc"><input type="text" id="OWNER_EMAIL-[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" name="OWNER_EMAIL[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" value="<?php echo trim($_smarty_tpl->tpl_vars['result']->value['OWNER_EMAIL']);?>
" maxlength="255" class="text" /></td></tr><?php } ?></tbody><tfoot><tr><td><input type="reset" id="reset-import-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /><?php echo smarty_function_formtool_checkall(array('name'=>"toimport[]",'checkall_text'=>"Check All",'uncheckall_text'=>"Uncheck All",'class'=>"btn",'id'=>"checkallButton2"),$_smarty_tpl);?>
</td><td><input type="submit" id="import" name="import" value="Continue" alt="Continue" title="Continue" class="button" /></td></tr></tfoot></table></form></div><?php }?><?php }?><?php }} ?>