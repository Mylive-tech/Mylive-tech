<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:14:07
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_templates_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:299551983535a97dff33f53-77629143%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e32538507a98e56191300f1a5136b044444cf4d' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_templates_edit.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '299551983535a97dff33f53-77629143',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'edit_screen' => 0,
    'file_saved' => 0,
    'current_template' => 0,
    'file_name' => 0,
    'file_content' => 0,
    'showPreview' => 0,
    'thumbType' => 0,
    'layout' => 0,
    'zone' => 0,
    'option' => 0,
    'sidebar_types' => 0,
    'yes_no' => 0,
    'template_files' => 0,
    'file' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a97e02c1b70_08623721',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a97e02c1b70_08623721')) {function content_535a97e02c1b70_08623721($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
if (!is_callable('smarty_function_thumb')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.thumb.php';
if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
if (!is_callable('smarty_mb_wordwrap')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/shared.mb_wordwrap.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="block"><!-- Action Links --><ul class="page-action-list"><?php if ($_smarty_tpl->tpl_vars['edit_screen']->value){?><li><a href="<?php echo @DOC_ROOT;?>
/conf_templates_edit.php?r=1" title="Back to files" class="button"><span class="page-tpl">Back to files</span></a></li><?php }?><li><a href="<?php echo @DOC_ROOT;?>
/conf_templates.php?r=1" title="Manage templates" class="button"><span class="manage-tpl">Manage Templates</span></a></li></ul><!-- /Action Links --></div><?php if (isset($_smarty_tpl->tpl_vars['file_saved']->value)){?><?php if ($_smarty_tpl->tpl_vars['file_saved']->value==0){?><div class="error block">An error occured while saving.!</div><?php }elseif($_smarty_tpl->tpl_vars['file_saved']->value==1){?><div class="success block">File saved!</div><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['edit_screen']->value){?><div class="block"><table class="list active-template"><thead><tr><th colspan="2">Current template</th></tr></thead><tbody><tr><td class="label">Title:</td><td class="smallDesc title"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['current_template']->value['theme_uri'];?>
" title="Browse template homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr><td class="label">Version:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_version'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr><td class="label">Author:</td><td class="smallDesc"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_author_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['current_template']->value['theme_author_uri'];?>
" title="Browse template author homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr><td class="label">Description:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_description'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr></tbody></table></div><div class="block"><form method="post" action=""><table class="formPage"><thead><tr><th colspan="2">Edit file</th></tr></thead><tbody><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['file_name']->value, ENT_QUOTES, 'UTF-8', true));?>
</td><td><textarea id="file_content" name="file_content" rows="30" cols="100" class="text code"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['file_content']->value, ENT_QUOTES, 'UTF-8', true));?>
</textarea></td></tr></tbody><tfoot><tr><td><input type="reset" id="reset-tpl-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-link-submit" name="submit" value="Save" alt="Save form" title="Save template" class="button" /></td></tr></tfoot></table></form></div><?php }else{ ?><div class="block"><table class="list active-template"><thead><tr><th colspan="2">Current template</th></tr></thead><tbody><tr><td class="label">Title:</td><td class="smallDesc title"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['current_template']->value['theme_uri'];?>
" title="Browse template homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr><td class="label">Version:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_version'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr><td class="label">Author:</td><td class="smallDesc"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_author_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['current_template']->value['theme_author_uri'];?>
" title="Browse template author homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr><td class="label">Description:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_description'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr><td class="label">Preview:</td><td class="smallDesc preview"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_screenshot_file'])&&$_smarty_tpl->tpl_vars['showPreview']->value=='1'){?><?php echo smarty_function_thumb(array('file'=>$_smarty_tpl->tpl_vars['current_template']->value['theme_screenshot_file'],'width'=>"250",'link'=>"true",'type'=>$_smarty_tpl->tpl_vars['thumbType']->value,'cache'=>"../temp/cache/"),$_smarty_tpl);?>
<?php }else{ ?>No preview available<?php }?></td></tr></tbody></table></div><div class="block"><form name="layoutForm" id="layoutForm" action="" method="post"><table class="list active-template"><thead><tr><th colspan="2">Template Settings</th></tr></thead><tbody><tr><td colspan="2" class="label"><div><?php echo $_smarty_tpl->tpl_vars['layout']->value['label'];?>
</div><hr /></td></tr><?php  $_smarty_tpl->tpl_vars['zone'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['zone']->_loop = false;
 $_smarty_tpl->tpl_vars['key_zone'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['layout']->value['zones']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['zone']->key => $_smarty_tpl->tpl_vars['zone']->value){
$_smarty_tpl->tpl_vars['zone']->_loop = true;
 $_smarty_tpl->tpl_vars['key_zone']->value = $_smarty_tpl->tpl_vars['zone']->key;
?><tr><td class="label"><?php echo $_smarty_tpl->tpl_vars['zone']->value['label'];?>
:</td><td class="smallDesc title"><?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['layout']->value['options']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['option']->key;
?><div style="display:inline-block"><table><tr><td text-align="left"><div style=""><img src="<?php echo $_smarty_tpl->tpl_vars['option']->value['image'];?>
" width="90px" height="90px" align="left" style="margin:0px" /></div></td></tr><tr><td text-align="center"><div><?php if ($_smarty_tpl->tpl_vars['zone']->value['value']==$_smarty_tpl->tpl_vars['option']->value['key']){?><input name="zone[<?php echo $_smarty_tpl->tpl_vars['zone']->value['name'];?>
]" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['option']->value['key'];?>
" checked /><?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
<?php }else{ ?><input name="zone[<?php echo $_smarty_tpl->tpl_vars['zone']->value['name'];?>
]" type="radio" value="<?php echo $_smarty_tpl->tpl_vars['option']->value['key'];?>
" /><?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
<?php }?></div></td></tr></table></div><?php } ?><div style="clear:both" /></td></tr><?php } ?><tr><td class="label"><?php echo $_smarty_tpl->tpl_vars['layout']->value['color']['label'];?>
:</td><td class="smallDesc title"><select name="color" id="color"><?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['layout']->value['coloroptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['option']->key;
?><?php if ($_smarty_tpl->tpl_vars['layout']->value['color']['value']==$_smarty_tpl->tpl_vars['option']->value['key']){?><option value="<?php echo $_smarty_tpl->tpl_vars['option']->value['key'];?>
" selected><?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
</option><?php }else{ ?><option value="<?php echo $_smarty_tpl->tpl_vars['option']->value['key'];?>
"><?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
</option><?php }?><?php } ?></select><div style="clear:both" /></td></tr><tr><td class="label"><?php echo $_smarty_tpl->tpl_vars['layout']->value['titleheading']['label'];?>
:</td><td class="smallDesc title"><select name="titleheading" id="font"><?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['layout']->value['heading']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['option']->key;
?><?php if ($_smarty_tpl->tpl_vars['layout']->value['titleheading']['value']==$_smarty_tpl->tpl_vars['option']->value['value']){?><option value="<?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['option']->value['label'];?>
</option><?php }else{ ?><option value="<?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
"><?php echo $_smarty_tpl->tpl_vars['option']->value['label'];?>
</option><?php }?><?php } ?></select><div style="clear:both" /></td></tr><tr><td class="label"><?php echo $_smarty_tpl->tpl_vars['layout']->value['widgetheading']['label'];?>
:</td><td class="smallDesc title"><select name="widgetheading" id="font"><?php  $_smarty_tpl->tpl_vars['option'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['option']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['layout']->value['heading']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['option']->key => $_smarty_tpl->tpl_vars['option']->value){
$_smarty_tpl->tpl_vars['option']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['option']->key;
?><?php if ($_smarty_tpl->tpl_vars['layout']->value['widgetheading']['value']==$_smarty_tpl->tpl_vars['option']->value['value']){?><option value="<?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['option']->value['label'];?>
</option><?php }else{ ?><option value="<?php echo $_smarty_tpl->tpl_vars['option']->value['value'];?>
"><?php echo $_smarty_tpl->tpl_vars['option']->value['label'];?>
</option><?php }?><?php } ?></select><div style="clear:both" /></td></tr><tr><td class="label">Sidebar Left:</td><td class="smallDesc"><input type="text" name="sidebar1" size="5" value="<?php echo $_smarty_tpl->tpl_vars['layout']->value['sidebar1'];?>
"  class="text" style="width:auto;"/><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['sidebar_types']->value,'selected'=>$_smarty_tpl->tpl_vars['layout']->value['sidebar1_type'],'name'=>"sidebar1_type",'id'=>"sidebar1_type"),$_smarty_tpl);?>
</td></tr><tr><td class="label">Sidebar Right:</td><td class="smallDesc"><input type="text" name="sidebar2" size="5" value="<?php echo $_smarty_tpl->tpl_vars['layout']->value['sidebar2'];?>
"  class="text" style="width:auto;"/><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['sidebar_types']->value,'selected'=>$_smarty_tpl->tpl_vars['layout']->value['sidebar2_type'],'name'=>"sidebar2_type",'id'=>"sidebar2_type"),$_smarty_tpl);?>
</td></tr><tr><td class="label"><?php echo $_smarty_tpl->tpl_vars['layout']->value['submit']['label'];?>
:</td><td class="smallDesc"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['yes_no']->value,'selected'=>$_smarty_tpl->tpl_vars['layout']->value['submit']['value'],'name'=>"submitwidgets",'id'=>"submitwidgets"),$_smarty_tpl);?>
</td></tr><tr><td class="label">Description:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_description'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr></tbody><tfoot><tr><td><input type="hidden" id="settings-link" name="settings" value="save" alt="Save form" title="Save template settings" class="button" /></td><td><input type="submit" id="send-link-submit" name="submit" value="Save" alt="Save form" title="Save template settings" class="button" /></td></tr></tfoot></table></form></div><div class="block"><table class="list"><thead><tr><th class="listHeader first-child">Filename</th><th class="listHeader">Full path</th><th class="listHeader last-child">Status</th></tr></thead><tbody><?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['file']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['template_files']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
$_smarty_tpl->tpl_vars['file']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['file']->key;
?><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label"><?php echo $_smarty_tpl->tpl_vars['file']->value['name'];?>
</td><td><?php echo nl2br(smarty_mb_wordwrap(htmlspecialchars($_smarty_tpl->tpl_vars['file']->value['path'], ENT_QUOTES, 'UTF-8', true),80,"\n",1));?>
</td><?php if (is_string($_smarty_tpl->tpl_vars['file']->value['permission'])){?><td class="error"><?php echo $_smarty_tpl->tpl_vars['file']->value['permission'];?>
</td><?php }else{ ?><td><a href="<?php echo @DOC_ROOT;?>
/conf_templates_edit.php?action=edit&amp;filename=<?php echo $_smarty_tpl->tpl_vars['file']->value['name'];?>
" title="Edit: <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['file']->value['name'], ENT_QUOTES, 'UTF-8', true));?>
" class="button"><span class="edit-tpl">Edit</span></a></td><?php }?></tr><?php } ?></tbody></table></div><?php }?><?php }} ?>