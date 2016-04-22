<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 23:08:02
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11241453795654edd2c3da70-64853738%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fa0d54738628b5bbc419de4ed7c5b5dd51063bd5' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/index.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11241453795654edd2c3da70-64853738',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'titleheading' => 0,
    'profileUpdate' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654edd2c4e467_14060411',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654edd2c4e467_14060411')) {function content_5654edd2c4e467_14060411($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"User Area",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>


<?php if ($_smarty_tpl->tpl_vars['profileUpdate']->value){?>
<p>Profile updated</p>

<?php }?>


<p>   <a href="<?php echo @DOC_ROOT;?>
/user/submissions" title="Browse personal links">My Submissions</a></p>

<p>  <a href="<?php echo @DOC_ROOT;?>
/user/profile" title="Edit Profile">Edit Profile</a>
</p><?php }} ?>