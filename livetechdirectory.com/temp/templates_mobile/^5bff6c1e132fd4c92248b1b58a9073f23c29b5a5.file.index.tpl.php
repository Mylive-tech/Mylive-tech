<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 13:46:46
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:406365808535d0a463369a9-94277394%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5bff6c1e132fd4c92248b1b58a9073f23c29b5a5' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/index.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '406365808535d0a463369a9-94277394',
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
  'unifunc' => 'content_535d0a46572d91_51914258',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d0a46572d91_51914258')) {function content_535d0a46572d91_51914258($_smarty_tpl) {?><li class="group"><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"User Area",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>
</li>

<?php if ($_smarty_tpl->tpl_vars['profileUpdate']->value){?>
<li>Profile updated</li>

<?php }?>


<li>  <a href="<?php echo @DOC_ROOT;?>
/user/submissions" title="Browse personal links">My Submissions</a></li>

<li>  <a href="<?php echo @DOC_ROOT;?>
/user/profile" title="Edit Profile">Edit Profile</a>
</li><?php }} ?>