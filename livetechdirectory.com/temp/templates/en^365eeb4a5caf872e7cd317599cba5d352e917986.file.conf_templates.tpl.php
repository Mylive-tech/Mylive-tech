<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:34
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_templates.tpl" */ ?>
<?php /*%%SmartyHeaderCode:441653538535a93c29be844-96564475%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '365eeb4a5caf872e7cd317599cba5d352e917986' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_templates.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '441653538535a93c29be844-96564475',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'posted' => 0,
    'current_template' => 0,
    'showPreview' => 0,
    'thumbType' => 0,
    'available_templates' => 0,
    'zebra' => 0,
    'template' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a93c2b4b6a2_39292964',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93c2b4b6a2_39292964')) {function content_535a93c2b4b6a2_39292964($_smarty_tpl) {?><?php if (!is_callable('smarty_function_thumb')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.thumb.php';
if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="block"><!-- Action Links --><ul class="page-action-list"><li><a href="<?php echo @DOC_ROOT;?>
/conf_templates_edit.php?r=1" title="Edit current template files" class="button"><span class="edit-tpl">Edit current template files</span></a></li></ul><!-- /Action Links --></div><?php if ($_smarty_tpl->tpl_vars['posted']->value){?><div class="success block">Template updated.</div><?php }?><div class="block"><table class="list active-template"><thead><tr><th colspan="2">Current template</th></tr></thead><tbody><tr><td class="label">Title:</td><td class="smallDesc title"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['current_template']->value['theme_uri'];?>
" title="Browse template homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr><td class="label">Version:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_version'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr><td class="label">Author:</td><td class="smallDesc"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_author_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['current_template']->value['theme_author_uri'];?>
" title="Browse template author homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr><td class="label">Description:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['current_template']->value['theme_description'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr><td class="label">Preview:</td><td class="smallDesc preview"><?php if (!empty($_smarty_tpl->tpl_vars['current_template']->value['theme_screenshot_file'])&&$_smarty_tpl->tpl_vars['showPreview']->value=='1'){?><?php echo smarty_function_thumb(array('file'=>$_smarty_tpl->tpl_vars['current_template']->value['theme_screenshot_file'],'width'=>"250",'link'=>"true",'type'=>$_smarty_tpl->tpl_vars['thumbType']->value,'cache'=>"../temp/cache/"),$_smarty_tpl);?>
<?php }else{ ?>No preview available<?php }?></td></tr></tbody></table></div><?php if (is_array($_smarty_tpl->tpl_vars['available_templates']->value)&&!empty($_smarty_tpl->tpl_vars['available_templates']->value)){?><div class="block"><table class="list"><thead><tr><th colspan="2">Available templates</th></tr></thead><tbody><?php  $_smarty_tpl->tpl_vars['template'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['template']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['available_templates']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['template']->key => $_smarty_tpl->tpl_vars['template']->value){
$_smarty_tpl->tpl_vars['template']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['template']->key;
?><?php echo smarty_function_cycle(array('assign'=>'zebra','values'=>"odd,even"),$_smarty_tpl);?>
<tr class="<?php echo $_smarty_tpl->tpl_vars['zebra']->value;?>
"><td class="label">Title:</td><td class="smallDesc title"><?php if (!empty($_smarty_tpl->tpl_vars['template']->value['theme_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['template']->value['theme_uri'];?>
" title="Browse template homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['template']->value['theme_name'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr class="<?php echo $_smarty_tpl->tpl_vars['zebra']->value;?>
"><td class="label">Action:</td><td class="smallDesc title"><a href="<?php echo @DOC_ROOT;?>
/conf_templates.php?r=1&amp;action=activate&amp;template=<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['template']->value['theme_path'], ENT_QUOTES, 'UTF-8', true));?>
" class="button activate"><span>Activate</span></a></td></tr><tr class="<?php echo $_smarty_tpl->tpl_vars['zebra']->value;?>
"><td class="label">Version:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['template']->value['theme_version'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo $_smarty_tpl->tpl_vars['zebra']->value;?>
"><td class="label">Author:</td><td class="smallDesc"><?php if (!empty($_smarty_tpl->tpl_vars['template']->value['theme_author_uri'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['template']->value['theme_author_uri'];?>
" title="Browse template author homepage" target="_blank"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
</a><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['template']->value['theme_author'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr class="<?php echo $_smarty_tpl->tpl_vars['zebra']->value;?>
"><td class="label">Description:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['template']->value['theme_description'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo $_smarty_tpl->tpl_vars['zebra']->value;?>
"><td class="label">Preview:</td><td class="smallDesc preview"><?php if (!empty($_smarty_tpl->tpl_vars['template']->value['theme_screenshot_file'])&&$_smarty_tpl->tpl_vars['showPreview']->value=='1'){?><?php echo smarty_function_thumb(array('file'=>$_smarty_tpl->tpl_vars['template']->value['theme_screenshot_file'],'width'=>"250",'link'=>"true",'type'=>"3",'cache'=>"../temp/cache/"),$_smarty_tpl);?>
<?php }else{ ?>No preview available<?php }?></td></tr><?php } ?></tbody></table></div><?php }?><?php }} ?>