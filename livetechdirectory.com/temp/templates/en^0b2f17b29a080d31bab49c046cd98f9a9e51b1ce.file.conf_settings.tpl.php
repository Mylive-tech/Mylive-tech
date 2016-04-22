<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:48
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_settings.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1469774985535a93d039e964-70942268%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0b2f17b29a080d31bab49c046cd98f9a9e51b1ce' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_settings.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1469774985535a93d039e964-70942268',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validators' => 0,
    'show_cache_msg' => 0,
    'posted' => 0,
    'conf_group' => 0,
    'conf_categs' => 0,
    'conf' => 0,
    'categ' => 0,
    'row' => 0,
    'opt_bool' => 0,
    'opt_bool1' => 0,
    'DescItem' => 0,
    'NoticeItem' => 0,
    'WarningItem' => 0,
    'submit_session' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a93d0603913_13981309',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93d0603913_13981309')) {function content_535a93d0603913_13981309($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
if (!is_callable('smarty_function_html_options')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_options.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/validation.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('form_id'=>"submit_form",'validators'=>$_smarty_tpl->tpl_vars['validators']->value), 0);?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo smarty_php_tag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
$this->assign('opt_bool', array(1 => $this->translate('Yes'), 0 => $this->translate('No')));<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_php_tag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo smarty_php_tag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
$this->assign('opt_bool1', array(2 => $this->translate('Do The Math Mod'), 1 => $this->translate('Yes'), 0 => $this->translate('No')));<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_php_tag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php if ($_smarty_tpl->tpl_vars['show_cache_msg']->value==1){?><div class="error block">It is strongly advised that you rebuild the category cache now. Please click here: <a href="<?php echo @DOC_ROOT;?>
/dir_categs.php?action=rebuild_cache" title="Rebuild Category Cache">Rebuild Category Cache</a></div><?php }?><?php if ($_smarty_tpl->tpl_vars['posted']->value){?><div class="success block">Settings updated.</div><?php }?><div class="block"><form method="post" action="<?php if (isset($_smarty_tpl->tpl_vars['conf_group']->value)){?><?php echo @DOC_ROOT;?>
/conf_settings.php?c=<?php echo $_smarty_tpl->tpl_vars['conf_group']->value;?>
<?php }?>" id="submit_form"><table class="formPage"><thead><tr><th colspan="2"><?php if (!empty($_smarty_tpl->tpl_vars['conf_categs']->value[$_smarty_tpl->tpl_vars['conf_group']->value])){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['conf_categs']->value[$_smarty_tpl->tpl_vars['conf_group']->value], ENT_QUOTES, 'UTF-8', true));?>
<?php }else{ ?>Configuration options<?php }?></th></tr></thead><tbody><?php if (isset($_smarty_tpl->tpl_vars["categ"])) {$_smarty_tpl->tpl_vars["categ"] = clone $_smarty_tpl->tpl_vars["categ"];
$_smarty_tpl->tpl_vars["categ"]->value = "0"; $_smarty_tpl->tpl_vars["categ"]->nocache = null; $_smarty_tpl->tpl_vars["categ"]->scope = 0;
} else $_smarty_tpl->tpl_vars["categ"] = new Smarty_variable("0", null, 0);?><?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['conf']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['categ']->value!=$_smarty_tpl->tpl_vars['row']->value['CONFIG_GROUP']){?><?php if (isset($_smarty_tpl->tpl_vars["categ"])) {$_smarty_tpl->tpl_vars["categ"] = clone $_smarty_tpl->tpl_vars["categ"];
$_smarty_tpl->tpl_vars["categ"]->value = $_smarty_tpl->tpl_vars['row']->value['CONFIG_GROUP']; $_smarty_tpl->tpl_vars["categ"]->nocache = null; $_smarty_tpl->tpl_vars["categ"]->scope = 0;
} else $_smarty_tpl->tpl_vars["categ"] = new Smarty_variable($_smarty_tpl->tpl_vars['row']->value['CONFIG_GROUP'], null, 0);?><?php }?><tr class="<?php echo smarty_function_cycle(array('values'=>'odd,even'),$_smarty_tpl);?>
"><td  class="label<?php if ($_smarty_tpl->tpl_vars['row']->value['REQUIRED']==1){?> required<?php }?>"><label for="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['NAME'], ENT_QUOTES, 'UTF-8', true);?>
:</label></td><td  class="smallDesc"><?php if ($_smarty_tpl->tpl_vars['row']->value['TYPE']=='STR'){?><input type="text" id="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['VALUE'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='CONTENT'||$_smarty_tpl->tpl_vars['row']->value['TYPE']=='TERMS'||$_smarty_tpl->tpl_vars['row']->value['TYPE']=='DISABLE_REASON'){?><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/rte.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('NAME'=>$_smarty_tpl->tpl_vars['row']->value['ID'],'VALUE'=>$_smarty_tpl->tpl_vars['row']->value['VALUE']), 0);?>
<?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='GOOGLE_ANALYTICS'){?><textarea rows="5" cols="20"id="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" class="text" /><?php echo $_smarty_tpl->tpl_vars['row']->value['VALUE'];?>
</textarea><?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='PAS'){?><input type="password" id="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['VALUE'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='URL'){?><input type="text" id="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['VALUE'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='INT'){?><input type="text" id="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['VALUE'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='NUM'){?><input type="text" id="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" name="<?php echo $_smarty_tpl->tpl_vars['row']->value['ID'];?>
" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['VALUE'], ENT_QUOTES, 'UTF-8', true));?>
" class="text" /><?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='LOG'){?><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['opt_bool']->value,'selected'=>$_smarty_tpl->tpl_vars['row']->value['VALUE'],'name'=>$_smarty_tpl->tpl_vars['row']->value['ID'],'id'=>$_smarty_tpl->tpl_vars['row']->value['ID']),$_smarty_tpl);?>
<?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='LKP'){?><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['row']->value['OPTIONS'],'selected'=>$_smarty_tpl->tpl_vars['row']->value['VALUE'],'name'=>$_smarty_tpl->tpl_vars['row']->value['ID'],'id'=>$_smarty_tpl->tpl_vars['row']->value['ID']),$_smarty_tpl);?>
<?php }elseif($_smarty_tpl->tpl_vars['row']->value['TYPE']=='LOG1'){?><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['opt_bool1']->value,'selected'=>$_smarty_tpl->tpl_vars['row']->value['VALUE'],'name'=>$_smarty_tpl->tpl_vars['row']->value['ID'],'id'=>$_smarty_tpl->tpl_vars['row']->value['ID']),$_smarty_tpl);?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'])&&!empty($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'])){?><div class="description"><?php if (is_array($_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'])){?><ul><?php  $_smarty_tpl->tpl_vars['DescItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['DescItem']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value['DESCRIPTION']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['DescItem']->key => $_smarty_tpl->tpl_vars['DescItem']->value){
$_smarty_tpl->tpl_vars['DescItem']->_loop = true;
?><li><?php echo $_smarty_tpl->tpl_vars['DescItem']->value;?>
</li><?php } ?></ul><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['row']->value['DESCRIPTION'];?>
<?php }?></div><?php }?><?php if (isset($_smarty_tpl->tpl_vars['row']->value['NOTICE'])&&!empty($_smarty_tpl->tpl_vars['row']->value['NOTICE'])){?><div class="notice"><?php if (is_array($_smarty_tpl->tpl_vars['row']->value['NOTICE'])){?><ul><?php  $_smarty_tpl->tpl_vars['NoticeItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['NoticeItem']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value['NOTICE']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['NoticeItem']->key => $_smarty_tpl->tpl_vars['NoticeItem']->value){
$_smarty_tpl->tpl_vars['NoticeItem']->_loop = true;
?><li><?php echo $_smarty_tpl->tpl_vars['NoticeItem']->value;?>
</li><?php } ?></ul><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['row']->value['NOTICE'];?>
<?php }?></div><?php }?><?php if (isset($_smarty_tpl->tpl_vars['row']->value['WARNING'])&&!empty($_smarty_tpl->tpl_vars['row']->value['WARNING'])){?><div class="warning"><?php if (is_array($_smarty_tpl->tpl_vars['row']->value['WARNING'])){?><ul><?php  $_smarty_tpl->tpl_vars['WarningItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['WarningItem']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value['WARNING']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['WarningItem']->key => $_smarty_tpl->tpl_vars['WarningItem']->value){
$_smarty_tpl->tpl_vars['WarningItem']->_loop = true;
?><li><?php echo $_smarty_tpl->tpl_vars['WarningItem']->value;?>
</li><?php } ?></ul><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['row']->value['WARNING'];?>
<?php }?></div><?php }?></td></tr><?php } ?></tbody><tfoot><tr><td><input type="reset" id="reset-conf-submit" name="reset" value="Reset" alt="Reset form" title="Reset form" class="button" /></td><td><input type="submit" id="send-conf-submit" name="save" value="Save" alt="Save form" title="Save settings" class="button" /></td></tr></tfoot></table><input type="hidden" name="formSubmitted" value="1" /><input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" /></form></div><?php }} ?>