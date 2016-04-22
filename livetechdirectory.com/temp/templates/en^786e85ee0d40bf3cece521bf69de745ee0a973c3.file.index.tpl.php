<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:01:15
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:802426085535a94db37cce6-84692048%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '786e85ee0d40bf3cece521bf69de745ee0a973c3' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/index.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '802426085535a94db37cce6-84692048',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'update_available' => 0,
    'version' => 0,
    'security_warnings' => 0,
    'warning' => 0,
    'directory_warnings' => 0,
    'search' => 0,
    'stats' => 0,
    'news' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a94db4e3a93_39699009',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a94db4e3a93_39699009')) {function content_535a94db4e3a93_39699009($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.date_format.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<!-- Version Update Info --><div class="block <?php if ($_smarty_tpl->tpl_vars['update_available']->value!=1){?>success<?php }else{ ?>warning<?php }?>"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['version']->value, ENT_QUOTES, 'UTF-8', true));?>
</div><!-- /Version Update Info --><?php if (is_array($_smarty_tpl->tpl_vars['security_warnings']->value)&&!empty($_smarty_tpl->tpl_vars['security_warnings']->value)){?><!-- Security Warnings --><?php  $_smarty_tpl->tpl_vars['warning'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['warning']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['security_warnings']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['warning']->key => $_smarty_tpl->tpl_vars['warning']->value){
$_smarty_tpl->tpl_vars['warning']->_loop = true;
?><div class="error block"><?php echo trim($_smarty_tpl->tpl_vars['warning']->value);?>
</div><?php } ?><!-- /Security Warnings --><?php }?><?php if (is_array($_smarty_tpl->tpl_vars['directory_warnings']->value)&&!empty($_smarty_tpl->tpl_vars['directory_warnings']->value)){?><!-- Directory Warnings --><?php  $_smarty_tpl->tpl_vars['warning'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['warning']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['directory_warnings']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['warning']->key => $_smarty_tpl->tpl_vars['warning']->value){
$_smarty_tpl->tpl_vars['warning']->_loop = true;
?><div class="error block"><?php echo trim($_smarty_tpl->tpl_vars['warning']->value);?>
</div><?php } ?><!-- /Directory Warnings --><?php }?><div class="block"><!-- Statistics --><table class="half_list" cellspacing="0"><thead><tr><th class="listHeader">Directory Searches</th></tr></thead><tbody><tr><td style="padding: 16px 0 16px 0 !important;"><form action="<?php echo @DOC_ROOT;?>
/dir_categs.php" method="get"><table style="border: none;" border="0"><tr><td style="border: none; padding:0px !important;" class="label" width="55%">Categories:<br/><span class="smallDesc">(type ID or keywords)</span></td><td style="border: none; padding:0px !important;" width="35%"><input type="text" id="searchinput" name="search" maxlength="255" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" class="text searchinput" title="Add your search keywords" style="width: 80%;" /></td><td style="border: none; padding:0px !important;" width="10%"><input type="submit" value="Search" title="Click to start search" class="button" /></td></tr></table></form></td></tr><tr><td style="padding: 16px 0 16px 0 !important;"><form action="<?php echo @DOC_ROOT;?>
/dir_links.php" method="get"><table style="border: none;" border="0"><tr><td style="border: none; padding:0px !important;" class="label" width="55%">Links:<br/><span class="smallDesc">(type ID or keywords)</span></td><td style="border: none; padding:0px !important;" width="35%"><input type="text" id="searchinput" name="search" maxlength="255" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" class="text searchinput" title="Add your search keywords" style="width: 80%;" /></td><td style="border: none; padding:0px !important;" width="10%"><input type="submit" value="Search" title="Click to start search" class="button" /></td></tr></table></form></td></tr><tr><td style="padding: 16px 0 16px 0 !important;"><form action="<?php echo @DOC_ROOT;?>
/dir_link_comments.php" method="get"><table style="border: none;" border="0"><tr><td style="border: none; padding:0px !important;" class="label" width="55%">Link Comments:<br/><span class="smallDesc">(type ID or keywords)</span></td><td style="border: none; padding:0px !important;" width="35%"><input type="text" id="searchinput" name="search" maxlength="255" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" class="text searchinput" title="Add your search keywords" style="width: 80%;" /></td><td style="border: none; padding:0px !important;" width="10%"><input type="submit" value="Search" title="Click to start search" class="button" /></td></tr></table></form></td></tr><tr><td style="padding: 16px 0 16px 0 !important;"><form action="<?php echo @DOC_ROOT;?>
/conf_users.php" method="get"><table style="border: none;" border="0"><tr><td style="border: none; padding:0px !important;" class="label" width="55%">Users:<br/><span class="smallDesc">(type ID or keywords)</span></td><td style="border: none; padding:0px !important;" width="35%"><input type="text" id="searchinput" name="search" maxlength="255" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" class="text searchinput" title="Add your search keywords" style="width: 80%;" /></td><td style="border: none; padding:0px !important;" width="10%"><input type="submit" value="Search" title="Click to start search" class="button" /></td></tr></table></form></td></tr></tbody></table><table class="half_list" style="float: right;"><thead><tr><th class="listHeader" style="width: 40%;" style="text-align: left;">Statistic</th><th class="listHeader" style="text-align: left;">Value</th></tr></thead><tbody><tr class="odd"><td class="label">Active Links</td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['stats']->value[0];?>
</td></tr><tr class="even"><td class="label"><a href="<?php echo @DOC_ROOT;?>
/dir_links.php?status=1&r=1">Pending Links</a></td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['stats']->value[1];?>
</td></tr><tr class="odd"><td class="label"><a href="<?php echo @DOC_ROOT;?>
/dir_links.php?status=0&r=1">Inactive Links</a></td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['stats']->value[2];?>
</td></tr><tr class="even"><td class="label">Active Link Comments</td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['stats']->value[3];?>
</td></tr><tr class="odd"><td class="label"><a href="<?php echo @DOC_ROOT;?>
/dir_approve_link_comments.php">Pending Link Comments</a></td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['stats']->value[4];?>
</td></tr><tr class="odd"><td class="label"><a href="<?php echo @DOC_ROOT;?>
/dir_categs.php">Categories</a></td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['stats']->value[10];?>
</td></tr></tbody></table><!-- /Statistics --></div><div style="clear: both;"></div><?php if (isset($_smarty_tpl->tpl_vars['news']->value)&&is_array($_smarty_tpl->tpl_vars['news']->value)&&!empty($_smarty_tpl->tpl_vars['news']->value)){?><!-- News --><div class="block"><?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['news']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?><div class="news"><h2 class="title"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['title'], ENT_QUOTES, 'UTF-8', true));?>
</h2><span class="date"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['item']->value['date'],"%b %e %Y");?>
</span><p class="body"><?php echo trim($_smarty_tpl->tpl_vars['item']->value['body']);?>
</p></div><?php } ?></div><!-- /News --><?php }?><?php }} ?>