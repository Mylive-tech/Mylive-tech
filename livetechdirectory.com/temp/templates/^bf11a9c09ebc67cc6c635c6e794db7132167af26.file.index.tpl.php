<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:47:24
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:714639331535abbccb18ee4-70787443%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bf11a9c09ebc67cc6c635c6e794db7132167af26' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/index.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '714639331535abbccb18ee4-70787443',
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
  'unifunc' => 'content_535abbccc26249_10520301',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abbccc26249_10520301')) {function content_535abbccc26249_10520301($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("views/_shared/_placeholders/pagetitle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('PAGETITLE'=>"User Area",'titleheading'=>$_smarty_tpl->tpl_vars['titleheading']->value), 0);?>


<?php if ($_smarty_tpl->tpl_vars['profileUpdate']->value){?>
<p>Profile updated</p>

<?php }?>


<p>   <a href="<?php echo @DOC_ROOT;?>
/user/submissions" title="Browse personal links">My Submissions</a></p>

<p>  <a href="<?php echo @DOC_ROOT;?>
/user/profile" title="Edit Profile">Edit Profile</a>
</p><?php }} ?>