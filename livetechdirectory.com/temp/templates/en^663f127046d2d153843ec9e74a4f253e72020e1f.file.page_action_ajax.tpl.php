<?php /* Smarty version Smarty-3.1.12, created on 2014-04-28 07:38:41
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/page_action_ajax.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1946111001535e0581a93934-60399119%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '663f127046d2d153843ec9e74a4f253e72020e1f' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/page_action_ajax.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1946111001535e0581a93934-60399119',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'rights' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535e0581ab0ef8_29797350',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535e0581ab0ef8_29797350')) {function content_535e0581ab0ef8_29797350($_smarty_tpl) {?><!-- Build link action submit buttons -->
<table class="list" id="multiple_controls" id="multiple_controls"><thead><tr><th class="listHeader" colspan="2">Manage multiple selections</th></tr></thead><tbody><tr><td><fieldset class="link_action"><legend>Select</legend><input type="button" name="check_all" id="check_all" value="Check All" class="button" /><input type="button" name="uncheck_all" id="uncheck_all" value="Uncheck All" class="button" /></fieldset><?php if ($_smarty_tpl->tpl_vars['rights']->value['editPage']==1){?><fieldset class="link_action"><legend>Change Status</legend><input type="button" name="active" id="activeButton" value="Active" title="Set selected pages as active" class="button" /><input type="button" name="inactive" id="inactiveButton" value="Inactive" title="Set selected pages as inactive" class="button" /></fieldset><?php }?><?php if ($_smarty_tpl->tpl_vars['rights']->value['delPage']==1){?><fieldset class="link_action"><legend>Action</legend><input type="button" name="remove" id="removeButton" value="Remove" title="Remove selected pages! Note: pages can not be restored after removal!" class="button" /></fieldset><?php }?></td></tr><tr><td><div id="multiselect_action_msg">Select pages and choose an action to perform.<br /><span id="multiselect_action_count">&nbsp;</span> item(s) selected.</div></td></tr></tbody></table><?php }} ?>