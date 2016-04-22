<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:20:43
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingStats.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2734097456548e5b23a379-34548830%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '30042c3a08efc731e8dff2bb025523479904aad3' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/listingStats.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2734097456548e5b23a379-34548830',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'comments_on' => 0,
    'ratings_on' => 0,
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548e5b25d662_06891061',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548e5b25d662_06891061')) {function content_56548e5b25d662_06891061($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.truncate.php';
?><?php if ($_smarty_tpl->tpl_vars['comments_on']->value==1||$_smarty_tpl->tpl_vars['ratings_on']->value==1){?>
    <div class="stats convo">
        <span class="review"><?php if ($_smarty_tpl->tpl_vars['comments_on']->value==1){?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['COMMENT_COUNT']!=''){?><?php echo $_smarty_tpl->tpl_vars['LINK']->value['COMMENT_COUNT'];?>
<?php }else{ ?>0<?php }?> Reviews.<?php }?><?php if ($_smarty_tpl->tpl_vars['ratings_on']->value==1){?> Rating: <?php echo $_smarty_tpl->tpl_vars['LINK']->value['RATING']/smarty_modifier_truncate($_smarty_tpl->tpl_vars['LINK']->value['VOTES'],4,'');?>
 Total Votes: <?php if (!$_smarty_tpl->tpl_vars['LINK']->value['VOTES']){?>0<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['LINK']->value['VOTES'];?>
<?php }?><?php }?></span>
    </div>
<?php }?><?php }} ?>