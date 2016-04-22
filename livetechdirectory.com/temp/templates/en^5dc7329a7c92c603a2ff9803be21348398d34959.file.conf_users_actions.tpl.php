<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:45:22
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_users_actions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1231177203535abb522066c4-21146002%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5dc7329a7c92c603a2ff9803be21348398d34959' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_users_actions.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1231177203535abb522066c4-21146002',
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
    'default' => 0,
    'k' => 0,
    'user_types' => 0,
    'def' => 0,
    'id' => 0,
    'actual' => 0,
    'level' => 0,
    'submit_session' => 0,
    'WARN' => 0,
    'u' => 0,
    'CATEGORY_ID' => 0,
    'permsTitleMsg' => 0,
    'CATEGORY' => 0,
    'CHILD_CATEGORIES' => 0,
    'columns' => 0,
    'ENABLE_REWRITE' => 0,
    'col' => 0,
    'columnURLs' => 0,
    'name' => 0,
    'SORT_FIELD' => 0,
    'requestOrder' => 0,
    'list' => 0,
    'row' => 0,
    'category' => 0,
    'idperm' => 0,
    'col_count' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535abb525140a0_05917793',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abb525140a0_05917793')) {function content_535abb525140a0_05917793($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
if (!is_callable('smarty_block_escapejs')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/block.escapejs.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"edit_article_form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>


<?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while saving.</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if ($_smarty_tpl->tpl_vars['posted']->value){?><div class="success block">User saved.</div><?php }?><div class="block"><form method="post" action=""><!--<table class="formPage"><tbody><?php  $_smarty_tpl->tpl_vars['def'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['def']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['default']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['def']->key => $_smarty_tpl->tpl_vars['def']->value){
$_smarty_tpl->tpl_vars['def']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['def']->key;
?><tr><td class="types" colspan="2"><?php echo $_smarty_tpl->tpl_vars['user_types']->value[$_smarty_tpl->tpl_vars['k']->value];?>
</td></tr><?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['def']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
?><tr><td class="label"><label for="ACTION"><?php echo $_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['NAME'];?>
:</label></td><td class="smallDesc"><?php if (isset($_smarty_tpl->tpl_vars['id'])) {$_smarty_tpl->tpl_vars['id'] = clone $_smarty_tpl->tpl_vars['id'];
$_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID']; $_smarty_tpl->tpl_vars['id']->nocache = null; $_smarty_tpl->tpl_vars['id']->scope = 0;
} else $_smarty_tpl->tpl_vars['id'] = new Smarty_variable($_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'], null, 0);?><input type="checkbox" id="<?php echo $_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'];?>
" value="1"<?php if ($_smarty_tpl->tpl_vars['actual']->value[$_smarty_tpl->tpl_vars['id']->value]!=''){?><?php if ($_smarty_tpl->tpl_vars['actual']->value[$_smarty_tpl->tpl_vars['id']->value]==1){?>checked="checked"<?php }?><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['VALUE']==1){?>checked="checked"<?php }?><?php }?>/></td></tr><?php endfor; endif; ?><?php } ?></tbody><tfoot><tr><td><input type="reset" id="reset-user-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-user-submit" name="save" value="Save" alt="Save form" title="Save user" class="button" /></td></tr></tfoot></table>--><table class="formPage"><thead><tr><td class="listHeader">User type</td><?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['default']->value[0]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
?><td class="listHeader"><?php echo $_smarty_tpl->tpl_vars['default']->value[0][$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['NAME'];?>
</td><?php endfor; endif; ?></tr></thead><?php  $_smarty_tpl->tpl_vars['def'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['def']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['default']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['def']->key => $_smarty_tpl->tpl_vars['def']->value){
$_smarty_tpl->tpl_vars['def']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['def']->key;
?><?php if (($_smarty_tpl->tpl_vars['k']->value==0)||($_smarty_tpl->tpl_vars['k']->value==$_smarty_tpl->tpl_vars['level']->value)){?><tr><td class="listHeader" style="color: #000;"><?php echo $_smarty_tpl->tpl_vars['user_types']->value[$_smarty_tpl->tpl_vars['k']->value];?>
</td><?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['def']->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
?><td class="smallDesc"><?php if (isset($_smarty_tpl->tpl_vars['id'])) {$_smarty_tpl->tpl_vars['id'] = clone $_smarty_tpl->tpl_vars['id'];
$_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID']; $_smarty_tpl->tpl_vars['id']->nocache = null; $_smarty_tpl->tpl_vars['id']->scope = 0;
} else $_smarty_tpl->tpl_vars['id'] = new Smarty_variable($_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'], null, 0);?><input type="checkbox" id="<?php echo $_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['ID'];?>
" value="1"<?php if ($_smarty_tpl->tpl_vars['actual']->value[$_smarty_tpl->tpl_vars['id']->value]!=''){?><?php if ($_smarty_tpl->tpl_vars['actual']->value[$_smarty_tpl->tpl_vars['id']->value]==1){?>checked="checked"<?php }?><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['def']->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['VALUE']==1){?>checked="checked"<?php }?><?php }?>/></td><?php endfor; endif; ?></tr><?php }?><?php } ?></table><table><tr><td><input type="reset" id="reset-user-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-user-submit" name="save" value="Save" alt="Save form" title="Save user" class="button" /></td></tr></table><input type="hidden" name="formSubmitted" value="1" /><input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" /></form></div><?php if ((isset($_smarty_tpl->tpl_vars['level']->value)&&$_smarty_tpl->tpl_vars['level']->value==2)){?><?php if ((isset($_smarty_tpl->tpl_vars['error']->value)&&$_smarty_tpl->tpl_vars['error']->value>0)||!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><div class="error block"><h2>Error</h2><p>An error occured while saving.</p><?php if (!empty($_smarty_tpl->tpl_vars['errorMsg']->value)){?><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['errorMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sql_error']->value, ENT_QUOTES, 'UTF-8', true);?>
</p><?php }?></div><?php }?><?php if ($_smarty_tpl->tpl_vars['posted']->value){?><div class="success block"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['posted']->value, ENT_QUOTES, 'UTF-8', true);?>
</div><?php }?><?php if ($_smarty_tpl->tpl_vars['WARN']->value){?><div class="block"><form method="post" action="<?php echo @DOC_ROOT;?>
/conf_user_permissions.php<?php if (!empty($_smarty_tpl->tpl_vars['u']->value)){?>?u=<?php echo $_smarty_tpl->tpl_vars['u']->value;?>
<?php }?>" name="delete"><input type="hidden" id="warn" name="warn" value="1" class="hidden" /><input type="hidden" name="CATEGORY_ID" value="<?php echo $_smarty_tpl->tpl_vars['CATEGORY_ID']->value;?>
" class="hidden" /><table class="formPage"><thead><tr><th colspan="2"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['permsTitleMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</th></tr></thead><tbody><tr><td class="notice" colspan="2"><p>Category <?php echo $_smarty_tpl->tpl_vars['CATEGORY']->value;?>
 is parent to <?php echo $_smarty_tpl->tpl_vars['CHILD_CATEGORIES']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['CHILD_CATEGORIES']->value==0){?>category<?php }else{ ?>categories<?php }?> that this user has permission to.</p><p>Proceed to grant permission to category <?php echo $_smarty_tpl->tpl_vars['CATEGORY']->value;?>
 and delete the existing permission to the <?php echo $_smarty_tpl->tpl_vars['CHILD_CATEGORIES']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['CHILD_CATEGORIES']->value==0){?>category<?php }else{ ?>categories<?php }?>?</p></td></tr></tbody><tfoot><tr><td><input type="submit" name="cancel" value="Cancel" title="Cancel" class="button" /></td><td><input type="submit" name="proceed" value="Proceed" title="Grant permission to parent category including child categories" class="button" /></td></tr></tfoot></table><input type="hidden" name="formSubmitted" value="1" /><input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" /></form></div><?php }else{ ?><div class="block"><form method="post" action="<?php echo @DOC_ROOT;?>
/conf_user_permissions.php?action=N<?php if (!empty($_smarty_tpl->tpl_vars['u']->value)){?>&amp;u=<?php echo $_smarty_tpl->tpl_vars['u']->value;?>
<?php }?>" id="edit_user_actions_form"><table class="formPage"><thead><tr><th colspan="2"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['permsTitleMsg']->value, ENT_QUOTES, 'UTF-8', true);?>
</th></tr></thead><tbody><tr><td class="label"><label for="CATEGORY_ID">Category:</label></td><td class="smallDesc"><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/admin_category_select.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</td></tr></tbody><tfoot><tr><td><input type="reset" id="reset-perms-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-perms-submit" name="add" value="Add permission" alt="Add permission" title="Add permission to selected category" class="button" /></td></tr></tfoot></table><input type="hidden" name="formSubmitted" value="1" /><input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" /></form></div><?php }?><div class="block"><table class="list"><thead><tr><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><?php if ($_smarty_tpl->tpl_vars['ENABLE_REWRITE']->value||$_smarty_tpl->tpl_vars['col']->value!='TITLE_URL'){?><th class="listHeader" id="<?php echo $_smarty_tpl->tpl_vars['col']->value;?>
"><a href="<?php echo @DOC_ROOT;?>
/dir_links.php<?php if (isset($_smarty_tpl->tpl_vars['columnURLs']->value)&&is_array($_smarty_tpl->tpl_vars['columnURLs']->value)){?>?<?php echo $_smarty_tpl->tpl_vars['columnURLs']->value[$_smarty_tpl->tpl_vars['col']->value];?>
<?php }?>" title="Sort by: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
" class="sort<?php if ($_smarty_tpl->tpl_vars['SORT_FIELD']->value==$_smarty_tpl->tpl_vars['col']->value&&$_smarty_tpl->tpl_vars['requestOrder']->value==1){?> <?php echo @SORT_ORDER;?>
<?php }?>"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</a></th><?php }?><?php } ?><th class="last-child">Action</th></tr></thead><tbody><?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['idperm'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['idperm']->value = $_smarty_tpl->tpl_vars['row']->key;
?><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><?php if ($_smarty_tpl->tpl_vars['ENABLE_REWRITE']->value||$_smarty_tpl->tpl_vars['col']->value!='TITLE_URL'){?><td><?php if ($_smarty_tpl->tpl_vars['col']->value=='CATEGORY_PATH'){?><?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['category']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['path']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value){
$_smarty_tpl->tpl_vars['category']->_loop = true;
 $_smarty_tpl->tpl_vars['category']->index++;
 $_smarty_tpl->tpl_vars['category']->first = $_smarty_tpl->tpl_vars['category']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['path']['first'] = $_smarty_tpl->tpl_vars['category']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['path']['iteration']++;
?><?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['path']['iteration']>2){?> &gt; <?php }?><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['path']['first']){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['category']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?><?php } ?><?php }else{ ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], ENT_QUOTES, 'UTF-8', true);?>
<?php }?></td><?php }?><?php } ?><td class="last-child"><a id="remove-userperms-<?php echo $_smarty_tpl->tpl_vars['idperm']->value;?>
" href="<?php echo @DOC_ROOT;?>
/conf_user_permissions.php?action=D:<?php echo $_smarty_tpl->tpl_vars['idperm']->value;?>
&u=<?php echo $_smarty_tpl->tpl_vars['u']->value;?>
" onclick="return link_rm_confirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('escapejs', array()); $_block_repeat=true; echo smarty_block_escapejs(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Are you sure you want to remove this permission?<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_escapejs(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');" title="Remove Permission" class="action delete"><span>Delete</span></a></td></tr><?php }
if (!$_smarty_tpl->tpl_vars['row']->_loop) {
?><tr><td colspan="<?php echo $_smarty_tpl->tpl_vars['col_count']->value;?>
" class="norec">No records found.</td></tr><?php } ?></tbody></table><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/list_pager.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div><?php }?>
<?php }} ?>