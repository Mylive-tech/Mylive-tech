<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:13:35
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widgets_per_zone.tpl" */ ?>
<?php /*%%SmartyHeaderCode:342844822535a97bfac3245-91352573%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c2da9fe09e4d24f9832cda3714c900372b43baac' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/dir_widgets_per_zone.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '342844822535a97bfac3245-91352573',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'zone' => 0,
    'wid_message' => 0,
    'wid_error' => 0,
    'op_status' => 0,
    'columns' => 0,
    'col' => 0,
    'name' => 0,
    'list' => 0,
    'row' => 0,
    'col_count' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a97c00d4b82_47590885',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a97c00d4b82_47590885')) {function content_535a97c00d4b82_47590885($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
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
    jQuery(document).ready(function(){
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                jQuery(this).width(jQuery(this).width());
            });
            return ui;
        };
        jQuery('table.list tbody').sortable({
            cursor: 'move',
            axis:   'y',
            opacity: 0.6,
            stop: function(e, ui) {
                helper: fixHelper,
                jQuery(this).sortable("refresh");
                sorted = jQuery(this).sortable('toArray');
                var order = new Array;
                for(var i = 0; i < sorted.length; i++ ){
                    var ids = sorted[i].split('_');
                    if (typeof ids[1] != undefined) {
                        order.push(ids[1]);
                    }
                    
                }    
                /*jQuery().each(sorted, function(k,v){
                    var ids = v.attr('id').split('_');
                    if (typeof ids[1] != undefined) {
                        order.push(ids[1]);
                    }
                });*/

                jQuery.get('<?php echo @DOC_ROOT;?>
/dir_widgets_per_zone_edit.php?action=O', {'ids' : order});
            }
            
            
            
            /*helper: fixHelper,
            axis: "y",
            opacity: 0.6,
            stop: function(event, ui) {
                order = new Array();
                jQuery(event.target).each(function(){
                    var ids = jQuery(this).attr('id').split('_');
                    if (typeof ids[1] != undefined) {
                        order.push(ids[1]);
                    }
                });
                jQuery.get('<?php echo @DOC_ROOT;?>
/dir_widgets_per_zone_edit.php?action=O', {'ids' : order});
            }*/
        });
    });

    </script>


<div class="block"><h2 style="font-size: 12px;">Now viewing: <?php echo $_smarty_tpl->tpl_vars['zone']->value;?>
. View all zones <a href="<?php echo @DOC_ROOT;?>
/dir_widget_zones.php">here</a>.</h2></div><?php if ($_smarty_tpl->tpl_vars['wid_message']->value!=''){?><div class="success block"><?php echo $_smarty_tpl->tpl_vars['wid_message']->value;?>
</div><?php }?><?php if ($_smarty_tpl->tpl_vars['wid_error']->value!=''){?><div class="block"><div class="error"><?php echo $_smarty_tpl->tpl_vars['wid_error']->value;?>
</div></div><?php }?><?php if ($_smarty_tpl->tpl_vars['op_status']->value==1){?><div class="success block">Operation successful.</div><?php }elseif($_smarty_tpl->tpl_vars['op_status']->value==-1){?><div class="block"><div class="error"><h2>Error</h2><p>Some errors occured during the operation.</p></div></div><?php }?><div class="block"><form action="<?php echo @DOC_ROOT;?>
/dir_widgets_edit.php" method="post"><input type="hidden" name="submitAction" id="submitAction" value="" class="hidden" /><table class="list"><thead><tr><th class="listHeader">&nbsp;</th><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><?php if ($_smarty_tpl->tpl_vars['col']->value!='TITLE_URL'){?><th class="listHeader"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true));?>
</th><?php }?><?php } ?></tr></thead><tbody><?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['NAME']->value = $_smarty_tpl->tpl_vars['row']->key;
?><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
" id="row_<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
"><td class="first-child" style="width: 25px;"><label><?php if ($_smarty_tpl->tpl_vars['row']->value['NAME']!='MainContent'){?><input type="checkbox" name="multi-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['zone']->value;?>
" id="multi-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" title="Check box to select this item." /><?php }?></label></td><?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['columns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['col']->value = $_smarty_tpl->tpl_vars['name']->key;
?><?php if (isset($_smarty_tpl->tpl_vars["val"])) {$_smarty_tpl->tpl_vars["val"] = clone $_smarty_tpl->tpl_vars["val"];
$_smarty_tpl->tpl_vars["val"]->value = $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value]; $_smarty_tpl->tpl_vars["val"]->nocache = null; $_smarty_tpl->tpl_vars["val"]->scope = 0;
} else $_smarty_tpl->tpl_vars["val"] = new Smarty_variable($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], null, 0);?><?php if ($_smarty_tpl->tpl_vars['col']->value=='ACTIVE'){?><td style="width: 140px;" <?php if ($_smarty_tpl->tpl_vars['row']->value['ZONE']!=''&&$_smarty_tpl->tpl_vars['row']->value['ZONE']==$_smarty_tpl->tpl_vars['zone']->value){?> class="status-2"<?php }else{ ?>class="status-0"<?php }?>><?php if ($_smarty_tpl->tpl_vars['row']->value['ACTIVE']==1){?><span class="link-status pop">On</span><h3 id="chgStat-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" class="chgStatTitle" onclick="return status_hide('<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
');">Change status</h3><ul id="list-status-<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" style="display: none;"><li><a class="new-status-0" href="<?php echo @DOC_ROOT;?>
/dir_widgets_per_zone_edit.php?action=D:<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" title="Turn Off Widget: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
">Off</a></li></ul><?php }else{ ?><span class="link-status pop">Off</span><h3 id="chgStat-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" class="chgStatTitle" onclick="return status_hide('<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
');">Change status</h3><ul id="list-status-<?php echo $_smarty_tpl->tpl_vars['row']->value['NAME'];?>
" style="display: none;"><li><a class="new-status-2" href="<?php echo @DOC_ROOT;?>
/dir_widgets_per_zone_edit.php?action=A:<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" title="Turn On Widget: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
">On</a></li></ul><?php }?></td><?php }elseif($_smarty_tpl->tpl_vars['col']->value=='ACTION'){?><?php if ($_smarty_tpl->tpl_vars['row']->value['ZONE']!=''&&$_smarty_tpl->tpl_vars['row']->value['ZONE']==$_smarty_tpl->tpl_vars['zone']->value){?><?php if ($_smarty_tpl->tpl_vars['row']->value['NAME']!='MainContent'){?><td class="noborder" style="width: 50px; white-space: nowrap"><a style="float:left; margin-right:8px; margin-left:8px" href="<?php echo @DOC_ROOT;?>
/dir_widgets_edit.php?action=E:<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" title="Edit settings for: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
" class="action edit"><span>EDIT SETTINGS</span></a><a style="float:left; margin-right:8px; margin-left:8px" href="<?php echo @DOC_ROOT;?>
/dir_widgets_per_zone_edit.php?action=R:<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" title="Remore widget from current zone: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
" class="action delete" onclick="return confirm('Are you sure?');"><span>REMOVE WIDGET</span></a></td><?php }else{ ?><td class="noborder"></td><?php }?><?php }else{ ?><td class="noborder" colspan="3" style="width: 60px;"></td><?php }?><?php }elseif($_smarty_tpl->tpl_vars['col']->value=='ORDER_ID'&&$_smarty_tpl->tpl_vars['row']->value['ZONE']==''&&$_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value]==1000){?><td>None yet</td><?php }elseif($_smarty_tpl->tpl_vars['col']->value=='NAME'){?><td><a href="" style="border: none; text-decoration: underline;" onmouseover="document.getElementById('wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
').style.display='block'; getPosition(event, 'wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
');" onmouseout="document.getElementById('wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
').style.display='none';" onclick="return false;"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], ENT_QUOTES, 'UTF-8', true));?>
</a><span id="wid_details_<?php echo $_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value];?>
" class="wid_details" style="display: none;"><?php if (trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'], ENT_QUOTES, 'UTF-8', true))!=''){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'], ENT_QUOTES, 'UTF-8', true));?>
<?php }else{ ?>No description.<?php }?></span></td><?php }elseif($_smarty_tpl->tpl_vars['col']->value=='TITLE'){?><td><a href="" style="border: none; text-decoration: underline;" onclick="return false;"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], ENT_QUOTES, 'UTF-8', true));?>
</a></td><?php }else{ ?><td><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value[$_smarty_tpl->tpl_vars['col']->value], ENT_QUOTES, 'UTF-8', true));?>
</td><?php }?><?php } ?></tr><?php }
if (!$_smarty_tpl->tpl_vars['row']->_loop) {
?><tr><td colspan="<?php echo $_smarty_tpl->tpl_vars['col_count']->value;?>
" class="norec">No records found.</td></tr><?php } ?></tbody></table><table class="list"><tr><td colspan="2" style="font-weight: bold; text-align: center;">Manage multiple selections</td></tr><tr><td><fieldset class="link_action"><legend>Select</legend><input type="button" name="all" id="allButton" value="All" title="Select All" class="button" onclick="select_all('<?php echo $_smarty_tpl->tpl_vars['zone']->value;?>
', 1);" /><input type="button" name="none" id="noneButton" value="None" title="Select None" class="button" onclick="select_all('<?php echo $_smarty_tpl->tpl_vars['zone']->value;?>
', 0);" /></fieldset><fieldset class="link_action"><legend>Change Status</legend><input type="submit" name="hide" id="hideButton" value="Hide" title="Hide selected widgets" class="button"/><input type="submit" name="show" id="hideButton" value="Show" title="Show selected widgets" class="button"/><input type="hidden" name="action" value="multi"/><input type="hidden" name="type" value="<?php echo $_smarty_tpl->tpl_vars['zone']->value;?>
"/></fieldset></td></tr></table></form></div>
<?php }} ?>