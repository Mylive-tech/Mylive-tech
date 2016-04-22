<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 18:53:16
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/link_action_ajax.tpl" */ ?>
<?php /*%%SmartyHeaderCode:957971088535aaf1ccbe811-79625259%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32991a7ab9de27c926661747edafec49d30b0a09' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/link_action_ajax.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '957971088535aaf1ccbe811-79625259',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'rights' => 0,
    'linkNotifButtons' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535aaf1cd14379_95523886',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535aaf1cd14379_95523886')) {function content_535aaf1cd14379_95523886($_smarty_tpl) {?><?php if (!is_callable('smarty_block_escapejs')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/block.escapejs.php';
?><!-- Build link action submit buttons -->
<table class="list" id="multiple_controls" id="multiple_controls"><thead><tr><th class="listHeader" colspan="2">Manage multiple selections</th></tr></thead><tbody><tr><td><fieldset class="link_action"><legend>Select</legend><input type="button" name="check_all" id="check_all" value="Check All" class="button" /><input type="button" name="uncheck_all" id="uncheck_all" value="Uncheck All" class="button" /></fieldset><?php if ($_smarty_tpl->tpl_vars['rights']->value['editLink']==1){?><fieldset class="link_action"><legend>Change Status</legend><input type="button" name="active" id="activeButton" value="Active" title="Set selected links as active" class="button" /><input type="button" name="pending" id="pendingButton" value="Pending" title="Set selected links as pending" class="button" /><input type="button" name="inactive" id="inactiveButton" value="Inactive" title="Set selected links as inactive" class="button" /></fieldset><?php }?><?php if ($_smarty_tpl->tpl_vars['rights']->value['delLink']==1){?><fieldset class="link_action"><legend>Action</legend><input type="button" name="remove" id="removeButton" value="Remove" title="Remove selected links! Note: links can not be restored after removal!" class="button" /></fieldset><?php }?><?php if ($_smarty_tpl->tpl_vars['linkNotifButtons']->value){?><fieldset class="link_action"><legend>Notifications</legend><input type="button" name="expired" id="expiredButton" value="Notify Expired" title="Notify owner(s) of expired reciprocal link page." class="button" /></fieldset><?php }?><!--<fieldset class="link_action"><legend>Banning</legend><input type="button" name="banip" id="banIpButton" value="Ban IPs" title="Ban IPs" class="button" onclick="selected_banip_confirm('Are you sure you want to ban selected IPs?');" /><input type="button" name="bandomain" id="banDomainButton" value="Ban Domains" title="Ban domains" class="button" onclick="selected_bandomain_confirm('Are you sure you want to ban selected domains?');" /></fieldset>--><?php if ($_smarty_tpl->tpl_vars['rights']->value['delLink']==1){?><fieldset class="link_action"><legend>Spam</legend><input type="button" autosubmit-disabled="1" id="spamLinkButton" value="Remove as Spam" title="Remove as Spam" class="button" onclick="if(confirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to remove selected links? Associated IPs and domains will be banned.<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
')){multiple_action('spamlink')}" /></fieldset><?php }?><?php if ($_smarty_tpl->tpl_vars['rights']->value['editLink']==1){?><fieldset class="link_action"><legend>Category</legend><!-- Load category selection --><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/admin_category_select.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<input type="button" name="changecategory" id="changeLinkCategoryButton" value="Change category" title="Change category" class="button"  /></fieldset><?php }?></td></tr><tr><td><div id="multiselect_action_msg">Select links and choose an action to perform.<br /><span id="multiselect_action_count">&nbsp;</span> item(s) selected.</div></td></tr></tbody></table><?php }} ?>