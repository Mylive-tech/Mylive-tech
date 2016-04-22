<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:07:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_submit_items_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1107082257535ab268e5bc18-25099174%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '25c570d2bc7242ff8526df5fa149a2be912f314a' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_submit_items_edit.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1107082257535ab268e5bc18-25099174',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'form_validators' => 0,
    'error' => 0,
    'sql_error' => 0,
    'errorMsg' => 0,
    'posted' => 0,
    'tid' => 0,
    'action' => 0,
    'NAME' => 0,
    'special_field' => 0,
    'FIELD_NAME' => 0,
    'types' => 0,
    'TYPE' => 0,
    'DROPDOWN_VALUE' => 0,
    'MULTICHECKBOX_VALUE' => 0,
    'DESCRIPTION' => 0,
    'validators' => 0,
    'id' => 0,
    'submit_session' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535ab269155085_57570241',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab269155085_57570241')) {function content_535ab269155085_57570241($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"submit_form",'validators'=>$_smarty_tpl->tpl_vars['form_validators']->value), 0);?>



<script type="text/javascript">
	jQuery(function($) {
		$(document).ready(function() {
			$("#TYPE").change(function() {
                var type = $(this).val();
                $('.additionalOption').hide();
                $('div[data-field="'+type+'"]').show();
//				if ($(this).val() == 'DROPDOWN') {
//					$("#DROPDOWN_VALUE").show("fast");
//				} else {
//					$("#DROPDOWN_VALUE").hide("fast");
//				}
			});
		});
	});
</script>


<?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while saving.</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if ($_smarty_tpl->tpl_vars['posted']->value){?><div class="success block">Submit item saved.</div><?php }?><div class="block"><form method="post" action="" id="submit_form"><?php if ($_smarty_tpl->tpl_vars['tid']->value){?><input type="hidden" name="LINK_TYPE_ID" value="<?php echo $_smarty_tpl->tpl_vars['tid']->value;?>
" /><?php }?><table class="formPage"><?php if (isset($_smarty_tpl->tpl_vars['action']->value)&&($_smarty_tpl->tpl_vars['action']->value=='N'||$_smarty_tpl->tpl_vars['action']->value=='E')){?><thead><tr><th colspan="2"><?php if ($_smarty_tpl->tpl_vars['action']->value=='N'){?>Create new submit item<?php }elseif($_smarty_tpl->tpl_vars['action']->value=='E'){?>Edit Submit Item<?php }?></th></tr></thead><?php }?><tbody><tr><td class="label required"><label for="NAME">Name:</label></td><td class="smallDesc"><input type="text" id="NAME" name="NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></td></tr><?php if ($_smarty_tpl->tpl_vars['special_field']->value!=1){?><tr><td class="label required"><label for="FIELD_NAME">Field Name:</label></td><td class="smallDesc"><input type="text" id="FIELD_NAME" name="FIELD_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['FIELD_NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><span class="errForm" id="warning_field" name="warning_field">To add this field, you will be inserting a new field into the links table.</span></td></tr><tr><td class="label required"><label for="TYPE">Type:</label></td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['types']->value,'selected'=>$_smarty_tpl->tpl_vars['TYPE']->value,'name'=>"TYPE",'id'=>"TYPE"),$_smarty_tpl);?>
<br /><div <?php if ($_smarty_tpl->tpl_vars['TYPE']->value!='DROPDOWN'){?> style="display: none;" <?php }?> class="additionalOption" data-field="DROPDOWN"><input type="text" id="DROPDOWN_VALUE" name="DROPDOWN_VALUE" class="text" value="<?php echo $_smarty_tpl->tpl_vars['DROPDOWN_VALUE']->value;?>
" /><div class="description">Enter your choices here separated by comma</div></div><div <?php if ($_smarty_tpl->tpl_vars['TYPE']->value!='MULTICHECKBOX'){?> style="display: none;" <?php }?> class="additionalOption" data-field="MULTICHECKBOX"><input type="text" id="MULTICHECKBOX_VALUE" name="MULTICHECKBOX_VALUE" class="text" value="<?php echo $_smarty_tpl->tpl_vars['MULTICHECKBOX_VALUE']->value;?>
" /><div class="description">Enter your choices here separated by comma</div></div></td></tr><?php }else{ ?><input type="hidden" id="FIELD_NAME" name="FIELD_NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['FIELD_NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><input type="hidden" id="TYPE" name="TYPE" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['TYPE']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><?php }?><tr><td class="label"><label for="DESCRIPTION">Description:</label></td><td class="smallDesc"><input type="text" id="DESCRIPTION" name="DESCRIPTION" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['DESCRIPTION']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="text" /></td></tr><?php if ($_smarty_tpl->tpl_vars['special_field']->value!=1||$_smarty_tpl->tpl_vars['FIELD_NAME']->value=='RECPR_URL'){?><tr><td class="label required"><label>Basic Validators:</label></td><td class="smallDesc"><?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['validators']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?><?php if ($_smarty_tpl->tpl_vars['special_field']->value!=1||($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='RECPR_URL'&&$_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID']==1)){?> <?php if ($_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['IS_REMOTE']==0){?><input type="checkbox" name="VALIDATORS[]" value="<?php echo $_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'];?>
" <?php if ($_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['SELECTED']=='1'){?>checked="1"<?php }?>>&nbsp;<?php echo $_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['TITLE'];?>
&nbsp;<?php }?><?php }?><?php endfor; endif; ?></td></tr><?php }?><?php if ($_smarty_tpl->tpl_vars['special_field']->value!=1){?><tr><td class="label required"><label>Advanced Validator:</label></td><td class="smallDesc"><select name="ADV_VALIDATOR"><option value="" selected="selected">None</option><?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['validators']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?><?php if ($_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['IS_REMOTE']==1){?><option value="<?php echo $_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'];?>
" <?php if ($_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['SELECTED']=='1'){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['validators']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['TITLE'];?>
</option><?php }?><?php endfor; endif; ?></select></td></tr><?php }?></tbody><tfoot><tr><!-- <input type="hidden" name="action" value="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
"/>--><input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"/><td><input type="reset" id="reset-link-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-link-submit" name="save" value="Save" alt="Save form" title="Save link" class="button" /></td></tr></tfoot></table><input type="hidden" name="formSubmitted" value="1" /><input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" /></form></div>
<?php }} ?>