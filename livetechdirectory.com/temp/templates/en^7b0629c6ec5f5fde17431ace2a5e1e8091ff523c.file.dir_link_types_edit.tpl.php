<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 18:47:18
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_link_types_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:947665595535aadb6348076-77353417%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b0629c6ec5f5fde17431ace2a5e1e8091ff523c' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_link_types_edit.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '947665595535aadb6348076-77353417',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validators' => 0,
    'error' => 0,
    'sql_error' => 0,
    'errorMsg' => 0,
    'posted' => 0,
    'action' => 0,
    'NAME' => 0,
    'DEEP_LINKS' => 0,
    'MULTIPLE_CATEGORIES' => 0,
    'COUNT_IMAGES' => 0,
    'DESCRIPTION' => 0,
    'IMGTN' => 0,
    'IMG' => 0,
    'no_yes' => 0,
    'FEATURED' => 0,
    'NOFOLLOW' => 0,
    'SHOW_META' => 0,
    'PRICE' => 0,
    'payment_um' => 0,
    'PAY_UM' => 0,
    'stats' => 0,
    'STATUS' => 0,
    'REQUIRE_APPROVAL' => 0,
    'lists' => 0,
    'LIST_TEMPLATE' => 0,
    'details' => 0,
    'DETAILS_TEMPLATE' => 0,
    'DEFAULT_THUMBNAIL_GRID' => 0,
    'DEFAULT_THUMBNAIL_LIST' => 0,
    'PAGERANK_MIN' => 0,
    'id' => 0,
    'submit_session' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535aadb64f9c48_41836377',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535aadb64f9c48_41836377')) {function content_535aadb64f9c48_41836377($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"submit_form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>


<?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while saving.</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if ($_smarty_tpl->tpl_vars['posted']->value){?><div class="success block">Link Type saved.</div><?php }?><div class="block"><form method="post" action="" enctype="multipart/form-data" id="submit_form"><table class="formPage"><?php if (isset($_smarty_tpl->tpl_vars['action']->value)&&($_smarty_tpl->tpl_vars['action']->value=='N'||$_smarty_tpl->tpl_vars['action']->value=='E')){?><thead><tr><th colspan="2"><?php if ($_smarty_tpl->tpl_vars['action']->value=='N'){?>Create new Link Type<?php }elseif($_smarty_tpl->tpl_vars['action']->value=='E'){?>Edit Link Type<?php }?></th></tr></thead><?php }?><tbody><?php if (@PAYPAL_ACCOUNT==''){?><tr><td colspan="2"><div class="error block">Your PAYPAL ACCOUNT has not been filled in. If any of your link types have their price set up, front end submission will not work at all. You may set your PAYPAL ACCOUNT <a href="<?php echo @DOC_ROOT;?>
/conf_settings.php?c=9&r=1">here</a>.</div></td></tr><?php }?><tr><td class="label required"><label for="NAME">Name:</label></td><td class="smallDesc"><input type="text" id="NAME" name="NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></td></tr><tr><td class="label required"><label for="DEEP_LINKS">Deep Links:</label></td><td class="smallDesc"><input type="text" id="DEEP_LINKS" name="DEEP_LINKS" value="<?php if ($_smarty_tpl->tpl_vars['DEEP_LINKS']->value>0){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['DEEP_LINKS']->value, ENT_QUOTES, 'UTF-8', true));?>
<?php }?>" class="text" /><p>Number of additional URLs. Leave empty for no deep links.</p></td></tr><tr><td class="label required"><label for="MULTIPLE_CATEGORIES">Multiple Categories:</label></td><td class="smallDesc"><input type="text" id="MULTIPLE_CATEGORIES" name="MULTIPLE_CATEGORIES" value="<?php if ($_smarty_tpl->tpl_vars['MULTIPLE_CATEGORIES']->value>0){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['MULTIPLE_CATEGORIES']->value, ENT_QUOTES, 'UTF-8', true));?>
<?php }?>" class="text" /><p>Number of max allowed categories. Leave empty for 1 default category.</p></td></tr><tr><td class="label required"><label for="COUNT_IMAGES">Count Of Images:</label></td><td class="smallDesc"><input type="text" id="COUNT_IMAGES" name="COUNT_IMAGES" value="<?php if ($_smarty_tpl->tpl_vars['COUNT_IMAGES']->value>0){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['COUNT_IMAGES']->value, ENT_QUOTES, 'UTF-8', true));?>
<?php }?>" class="text" /><p>Number of IMAGES. Leave empty for no Additional images.</p></td></tr><tr><td class="label"><label for="DESCRIPTION">Description:</label></td><td class="smallDesc"><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/rte.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('NAME'=>"DESCRIPTION",'VALUE'=>$_smarty_tpl->tpl_vars['DESCRIPTION']->value), 0);?>
</td></tr><?php if ($_smarty_tpl->tpl_vars['IMGTN']->value){?><tr><td class="label required"><label for="IMGTN">Current Image:</label></td><td class="smallDesc"><a href="<?php echo $_smarty_tpl->tpl_vars['IMG']->value;?>
" class="thickbox"><img src="<?php echo $_smarty_tpl->tpl_vars['IMGTN']->value;?>
" border="0" alt="Current Link Image" /></a></td></tr><?php }?><tr><td class="label required"><label for="FEATURED">Featured:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['no_yes']->value,'selected'=>$_smarty_tpl->tpl_vars['FEATURED']->value,'name'=>"FEATURED",'id'=>"FEATURED"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="NOFOLLOW">No Follow:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['no_yes']->value,'selected'=>$_smarty_tpl->tpl_vars['NOFOLLOW']->value,'name'=>"NOFOLLOW",'id'=>"NOFOLLOW"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="NOFOLLOW">Show Meta Fields In Front:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['no_yes']->value,'selected'=>$_smarty_tpl->tpl_vars['SHOW_META']->value,'name'=>"SHOW_META",'id'=>"SHOW_META"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="PRICE">Price:</label></td><td class="smallDesc"><input type="text" id="PRICE" name="PRICE" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['PRICE']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><p>Leave empty for free links.</p></td></tr><tr><td class="label required"><label for="PAY_UM">Time Unit:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['payment_um']->value,'selected'=>$_smarty_tpl->tpl_vars['PAY_UM']->value,'name'=>"PAY_UM",'id'=>"PAY_UM"),$_smarty_tpl);?>
<p class="small">Time unit used for paid links validity period<br /><b>(note: this option is only needed paid links)</b></p></td></tr><tr><td class="label required"><label for="STATUS">Status:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['stats']->value,'selected'=>$_smarty_tpl->tpl_vars['STATUS']->value,'name'=>"STATUS",'id'=>"STATUS"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="REQUIRE_APPROVAL">Require Admin Approval:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['no_yes']->value,'selected'=>$_smarty_tpl->tpl_vars['REQUIRE_APPROVAL']->value,'name'=>"REQUIRE_APPROVAL",'id'=>"REQUIRE_APPROVAL"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="LIST_TEMPLATE">List Template:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['lists']->value,'selected'=>$_smarty_tpl->tpl_vars['LIST_TEMPLATE']->value,'name'=>"LIST_TEMPLATE",'id'=>"LIST_TEMPLATE"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="DETAILS_TEMPLATE">Details Template:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['details']->value,'selected'=>$_smarty_tpl->tpl_vars['DETAILS_TEMPLATE']->value,'name'=>"DETAILS_TEMPLATE",'id'=>"DETAILS_TEMPLATE"),$_smarty_tpl);?>
</td></tr><tr><td class="label required"><label for="THUMBNAIL_GRID">Default Grid Image width:</label></td><td class="smallDesc"><input type="text" id="THUMBNAIL_GRID" name="THUMBNAIL_GRID" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['DEFAULT_THUMBNAIL_GRID']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></td></tr><tr><td class="label required"><label for="THUMBNAIL_LIST">Default List Image width:</label></td><td class="smallDesc"><input type="text" id="THUMBNAIL_LIST" name="THUMBNAIL_LIST" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['DEFAULT_THUMBNAIL_LIST']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></td></tr><tr><td class="label required"><label for="PAGERANK_MIN">Minimum PageRank:</label></td><td class="smallDesc"><input type="text" id="PAGERANK_MIN" name="PAGERANK_MIN" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['PAGERANK_MIN']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><p>Minimum pagerank for link submission. Leave blank for no restriction.</p><div class="error block">This feature is considered Beta and may not work as expected. Enable at your own risk.</div></td></tr></tbody><tfoot><tr><td><input type="reset" id="reset-link-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-link-submit" name="save" value="Save" alt="Save form" title="Save link" class="button" /></td></tr></tfoot></table><input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" /><input type="hidden" name="formSubmitted" value="1" /><input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" /></form></div><?php }} ?>