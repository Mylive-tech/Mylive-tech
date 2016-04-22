<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:19
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/category/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:72690805456548c638ad051-49180793%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '62e5ea62bb2724a092f36da462f754fbb3912210' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/category/index.tpl',
      1 => 1390868836,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '72690805456548c638ad051-49180793',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c638b2248_62514088',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c638b2248_62514088')) {function content_56548c638b2248_62514088($_smarty_tpl) {?><h3><?php echo $_smarty_tpl->tpl_vars['category']->value['TITLE'];?>
</h3>
<div class="category-description"><?php echo $_smarty_tpl->tpl_vars['category']->value['CATCONTENT'];?>
</div>
<?php }} ?>