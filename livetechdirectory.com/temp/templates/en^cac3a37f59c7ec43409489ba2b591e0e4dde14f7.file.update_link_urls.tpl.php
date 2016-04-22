<?php /* Smarty version Smarty-3.1.12, created on 2014-10-10 17:41:16
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/update_link_urls.tpl" */ ?>
<?php /*%%SmartyHeaderCode:56938460154381a3c4fe718-56309671%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cac3a37f59c7ec43409489ba2b591e0e4dde14f7' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/update_link_urls.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '56938460154381a3c4fe718-56309671',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'rebuild' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_54381a3c54d4d9_76311107',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54381a3c54d4d9_76311107')) {function content_54381a3c54d4d9_76311107($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['rebuild']->value!='rebuild_urls'){?>
<h1>Update Links Urls</h1><p>Press "Rebuild Now" in order to update all links urls</p><form>   
<input type="hidden" name="rebuild" value="1">   
<input type="submit" value="Rebuild Now"></form>
<?php }?><?php }} ?>