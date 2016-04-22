<?php /* Smarty version Smarty-3.1.12, created on 2014-05-06 13:48:57
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/link_details.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14137281495368e84943c894-15009635%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7aac79bcd007ae49f02936da6fbf5259d1e7adb5' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/link_details.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14137281495368e84943c894-15009635',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'noLinkID' => 0,
    'error' => 0,
    'sql_error' => 0,
    'id' => 0,
    'linkInfo' => 0,
    'row' => 0,
    'stats' => 0,
    'link_type_str' => 0,
    'date_format' => 0,
    'valid' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5368e849704763_54346083',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5368e849704763_54346083')) {function content_5368e849704763_54346083($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.date_format.php';
?><?php if ($_smarty_tpl->tpl_vars['noLinkID']->value){?><div class="block"><!-- Error --><div class="error"><h2>Error</h2><p>No, or invalid link ID!</p></div><!-- /Error --></div><?php }elseif($_smarty_tpl->tpl_vars['error']->value>'1'){?><div class="block"><!-- Error --><div class="error"><h2>Error</h2><p>An occured while processing.</p><?php if (!empty($_smarty_tpl->tpl_vars['sql_error']->value)){?><p>The database server returned the following message:</p><p class="sql_error"><?php echo $_smarty_tpl->tpl_vars['sql_error']->value;?>
</p><?php }?></div><!-- /Error --></div><?php }else{ ?><div class="tooltip block" id="tooltip-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><table class="tooltip-table list"><tbody><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Link ID:</td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['linkInfo']->value['ID'];?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Title:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['TITLE'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">URL:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
 <?php ob_start();?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1){?><a href="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['URL'], ENT_QUOTES, 'UTF-8', true));?>
" target='_blank'>Visit</a><?php }?></td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Description:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['DESCRIPTION'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Category:</td><td class="smallDesc"><?php if ($_smarty_tpl->tpl_vars['row']->value['CATEGORY_ID']=='-1'){?><span class="orphan">Orphan</span><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['CATEGORY'], ENT_QUOTES, 'UTF-8', true));?>
 (<?php echo $_smarty_tpl->tpl_vars['linkInfo']->value['CATEGORY_ID'];?>
)<?php }?></td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Status:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['stats']->value[$_smarty_tpl->tpl_vars['linkInfo']->value['STATUS']], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Type:</td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['link_type_str']->value[$_smarty_tpl->tpl_vars['linkInfo']->value['LINK_TYPE']];?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">PageRank:</td><td class="smallDesc"><?php if ($_smarty_tpl->tpl_vars['linkInfo']->value['PAGERANK']==-1){?><em>N/A</em><?php }else{ ?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['PAGERANK'], ENT_QUOTES, 'UTF-8', true));?>
<?php }?></td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Hits:</td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['linkInfo']->value['HITS'];?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Date Added:</td><td class="smallDesc"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['linkInfo']->value['DATE_ADDED'],$_smarty_tpl->tpl_vars['date_format']->value);?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Valid:</td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['valid']->value[$_smarty_tpl->tpl_vars['linkInfo']->value['VALID']];?>
 (<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['linkInfo']->value['LAST_CHECKED'],$_smarty_tpl->tpl_vars['date_format']->value);?>
)</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Recpr. Link URL:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['RECPR_URL'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Recpr. PageRank:</td><td class="smallDesc"><?php if ($_smarty_tpl->tpl_vars['linkInfo']->value['RECPR_PAGERANK']==-1){?>N/A<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['linkInfo']->value['RECPR_PAGERANK'];?>
<?php }?></td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Recpr. Valid:</td><td class="smallDesc"><?php echo $_smarty_tpl->tpl_vars['valid']->value[$_smarty_tpl->tpl_vars['linkInfo']->value['RECPR_VALID']];?>
<?php if (!empty($_smarty_tpl->tpl_vars['linkInfo']->value['RECPR_LAST_CHECKED'])){?> (<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['linkInfo']->value['RECPR_LAST_CHECKED'],$_smarty_tpl->tpl_vars['date_format']->value);?>
)<?php }?></td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Owner ID:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['OWNER_ID'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Owner Name:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['OWNER_NAME'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Owner Email:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['OWNER_EMAIL'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Owner IP:</td><td class="smallDesc"><?php echo trim($_smarty_tpl->tpl_vars['linkInfo']->value['IPADDRESS']);?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Meta Keywords:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['META_KEYWORDS'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr><tr class="<?php echo smarty_function_cycle(array('values'=>"odd,even"),$_smarty_tpl);?>
"><td class="label">Meta Description:</td><td class="smallDesc"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['linkInfo']->value['META_DESCRIPTION'], ENT_QUOTES, 'UTF-8', true));?>
</td></tr></tbody></table></div><?php }?><?php }} ?>