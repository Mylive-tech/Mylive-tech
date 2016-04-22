<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:13:30
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widgets.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1234556265535a97ba116235-75005975%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd9a37dd56e48f19976ed6bb5a695dad3a4b50e7f' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widgets.tpl',
      1 => 1387505366,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1234556265535a97ba116235-75005975',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'wid_message' => 0,
    'wid_error' => 0,
    'op_status' => 0,
    'list' => 0,
    'col_count' => 0,
    'key' => 0,
    'available' => 0,
    'columns' => 0,
    'col' => 0,
    'name' => 0,
    'type' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a97ba33b644_30196688',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a97ba33b644_30196688')) {function content_535a97ba33b644_30196688($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
if (!is_callable('smarty_modifier_truncate')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.truncate.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>



<script type="text/javascript">
function status_hide(id) {
	var display = document.getElementById('list-status-'+id).style.display;
	if (display == 'none') {
		document.getElementById('list-status-'+id).style.display = 'block';
	} else {
		document.getElementById('list-status-'+id).style.display = 'none';
	}
	return false;
}
function getPosition(e, id) {
    e = e || window.event;
    var cursor = {x:0, y:0};
    if (e.pageX || e.pageY) {
        cursor.x = e.pageX;
        cursor.y = e.pageY;
    } 
    else {
        var de = document.documentElement;
        var b = document.body;
        cursor.x = e.clientX + 
            (de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
        cursor.y = e.clientY + 
            (de.scrollTop || b.scrollTop) - (de.clientTop || 0);
    }

    document.getElementById(id).style.top = eval(cursor.y+1)+'px';
	document.getElementById(id).style.left = eval(cursor.x)+'px';
}

function select_all(key,all) {

	if (document.getElementsByClassName == undefined) {
	document.getElementsByClassName = function(className)
	{
		var hasClassName = new RegExp("(?:^|\\s)" + className + "(?:$|\\s)");
		var allElements = document.getElementsByTagName("*");
		var results = [];

		var element;
		for (var i = 0; (element = allElements[i]) != null; i++) {
			var elementClass = element.className;
			if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass))
				results.push(element);
		}

		return results;
	}
	}	

	var elem = document.getElementsByClassName(key);
	if (all == 1) {
		for (var i in elem) {
			elem[i].checked=true;
		}
	} else {
		for (var i in elem) {
			elem[i].checked=false;
		}
	}
}
</script>

<?php if ($_smarty_tpl->tpl_vars['wid_message']->value!=''){?><div class="success block"><?php echo $_smarty_tpl->tpl_vars['wid_message']->value;?>
</div><?php }?><?php if ($_smarty_tpl->tpl_vars['wid_error']->value!=''){?><div class="block"><div class="error"><?php echo $_smarty_tpl->tpl_vars['wid_error']->value;?>
</div></div><?php }?><?php if ($_smarty_tpl->tpl_vars['op_status']->value==1){?><div class="success block">Operation successful.</div><?php }elseif($_smarty_tpl->tpl_vars['op_status']->value==-1){?><div class="block"><div class="error"><h2>Error</h2><p>Some errors occured during the operation.</p></div></div><?php }?><div class="block"><form action="<?php echo @DOC_ROOT;?>
/dir_widgets_edit.php" method="post"><?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value){
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['type']->key;
?><table class="list availWidgets" style="margin-bottom: 30px;"><thead><tr><th class="listHeader" colspan="<?php echo $_smarty_tpl->tpl_vars['col_count']->value;?>
"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['key']->value, ENT_QUOTES, 'UTF-8', true));?>
 WIDGETS : Can be placed on the following zones:&nbsp;<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['i'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['available']->value[$_smarty_tpl->tpl_vars['key']->value]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
?><a style="display: inline;" href="<?php echo @DOC_ROOT;?>
/dir_widgets_per_zone.php?Z=<?php echo $_smarty_tpl->tpl_vars['available']->value[$_smarty_tpl->tpl_vars['key']->value][$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['NAME'];?>
&T=<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['available']->value[$_smarty_tpl->tpl_vars['key']->value][$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['NAME'];?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['section']['i']['last']){?>,&nbsp;<?php }?><?php endfor; endif; ?></th></tr><tr><th class="listHeader">&nbsp;</th><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><?php if ($_smarty_tpl->tpl_vars['col']->value!='TITLE_URL'){?><th class="listHeader"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</th><?php }?><?php } ?></tr></thead><tbody><?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['type']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['NAME']->value = $_smarty_tpl->tpl_vars['row']->key;
?><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="first-child" style="width: 25px;"><label><input type="checkbox" name="multi-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" id="multi-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" title="Check box to select this item." /></label></td><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><?php if (isset($_smarty_tpl->tpl_vars["val"])) {$_smarty_tpl->tpl_vars["val"] = clone $_smarty_tpl->tpl_vars["val"];
$_smarty_tpl->tpl_vars["val"]->value = $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value]; $_smarty_tpl->tpl_vars["val"]->nocache = null; $_smarty_tpl->tpl_vars["val"]->scope = 0;
} else $_smarty_tpl->tpl_vars["val"] = new Smarty_variable($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], null, 0);?><?php if ($_smarty_tpl->tpl_vars['col']->value=='INSTALLED'){?><td <?php if ($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value]=='1'){?>class="status-2"<?php }else{ ?>class="status-0"<?php }?>><?php if ($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value]=='1'){?><span class="link-status pop">Installed</span><h3 id="chgStat-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" class="chgStatTitle" onclick="return status_hide('<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
');">Change status</h3><ul id="list-status-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" style="display: none;"><li><a class="new-status-0" href="<?php echo @DOC_ROOT;?>
/dir_widgets_edit.php?action=U:<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" title="Uninstall widget: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
">Uninstall</a></li></ul><?php }else{ ?><span class="link-status pop">Not Installed</span><h3 id="chgStat-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" class="chgStatTitle" onclick="return status_hide('<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
');">Change status</h3><ul id="list-status-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" style="display: none;"><li><a class="new-status-2" href="<?php echo @DOC_ROOT;?>
/dir_widgets_edit.php?action=I:<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" title="Install widget: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
">Install</a></li></ul><?php }?></td><?php }elseif($_smarty_tpl->tpl_vars['col']->value=='ACTION'){?><td style="width: 40px;"><a href="<?php echo @DOC_ROOT;?>
/dir_widgets_pick_zones.php?id=<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" title="New widget for: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
" class="action newWidget"><span>NEW WIDGET</span></a></td><?php }elseif($_smarty_tpl->tpl_vars['col']->value=='NAME'){?><td><a href="" style="border: none; text-decoration: underline;" onmouseover="document.getElementById('wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
').style.display='block'; getPosition(event, 'wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
');" onmouseout="document.getElementById('wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
').style.display='none';" onclick="return false;"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], ENT_QUOTES, 'UTF-8', true));?>
</a><span id="wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
" class="wid_details" style="display: none;"><?php if (trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'], ENT_QUOTES, 'UTF-8', true))!=''){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'], ENT_QUOTES, 'UTF-8', true));?>
<?php }else{ ?>No description.<?php }?></span></td><?php }elseif($_smarty_tpl->tpl_vars['col']->value=='DESCRIPTION'){?><td style="padding: 0px 5px;"><?php if (trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'], ENT_QUOTES, 'UTF-8', true))!=''){?><?php echo smarty_modifier_truncate(trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'], ENT_QUOTES, 'UTF-8', true)),250);?>
<?php }else{ ?>No description.<?php }?></td><?php }else{ ?><td><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], ENT_QUOTES, 'UTF-8', true));?>
</td><?php }?><?php } ?></tr><?php }
if (!$_smarty_tpl->tpl_vars['row']->_loop) {
?><tr><td colspan="<?php echo $_smarty_tpl->tpl_vars['col_count']->value;?>
" class="norec">No records found.</td></tr><?php } ?></tbody></table><table class="list"><tr><td colspan="2" style="font-weight: bold; text-align: center;">Manage multiple selections</td></tr><tr><td><fieldset class="link_action"><legend>Select</legend><input type="button" name="all" id="allButton" value="All" title="Select All" class="button" onclick="select_all('<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
', 1);" /><input type="button" name="none" id="noneButton" value="None" title="Select None" class="button" onclick="select_all('<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
', 0);" /></fieldset><fieldset class="link_action"><legend>Change Status</legend><input type="submit" name="install" id="installButton" value="Install" title="Install selected widgets" class="button"/><input type="submit" name="uninstall" id="uninstallButton" value="Uninstall" title="Uninstall selected widgets" class="button"/><input type="hidden" name="action" value="multi"/><input type="hidden" name="type" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"/></fieldset></td></tr></table><?php } ?></form></div>
<?php }} ?>