<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 18:56:59
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widgets_pick_zones.tpl" */ ?>
<?php /*%%SmartyHeaderCode:784674698535aaffb8f44a2-66144387%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '973669092710a0be88a4f9c81b5a8c2070ee72b6' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widgets_pick_zones.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '784674698535aaffb8f44a2-66144387',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'op_status' => 0,
    'widname' => 0,
    'columns' => 0,
    'list' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535aaffb987776_86642179',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535aaffb987776_86642179')) {function content_535aaffb987776_86642179($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php if ($_smarty_tpl->tpl_vars['op_status']->value==1){?><div class="success block">Operation successful.</div><?php }elseif($_smarty_tpl->tpl_vars['op_status']->value==-1){?><div class="block"><div class="error"><h2>Error</h2><p>Some errors occured during the operation.</p></div></div><?php }?><div class="block"><h2 style="font-size: 12px;">You are installing the <?php echo $_smarty_tpl->tpl_vars['widname']->value;?>
 widget.</h2><form action="" method="post"><table class="list" style="width: 50%; margin-bottom: 20px;"><thead><tr><th class="listHeader" colspan="<?php echo count($_smarty_tpl->tpl_vars['columns']->value);?>
"  colspan="2">WIDGET ZONES</th></tr></thead><tbody><tr><td  colspan="2"><p style="padding: 10px;">Your widget is now successfully installed. Please select the zone(s) where you want it visible. These are the areas where it will actually show up on the site's front end.</p></td></tr><?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['NAME']->value = $_smarty_tpl->tpl_vars['row']->key;
?><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td  colspan="2"><input type="checkbox" id="<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" name="zones[]" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" />&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
</td></tr><?php }
if (!$_smarty_tpl->tpl_vars['row']->_loop) {
?><tr><td class="norec"  colspan="2">No records found.</td></tr><?php } ?></tbody><tfoot><tr><td><input type="submit" name="save" value="Save" alt="Save" title="Save" class="button" /></td><td><input type="submit" name="cancel" value="Cancel" alt="Cancel" title="Cancel" class="button" /></td></tr></tfoot></table><input type="hidden" name="formSubmitted" value="1" /></form></div>
<?php }} ?>