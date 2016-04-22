<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:20:43
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/address.tpl" */ ?>
<?php /*%%SmartyHeaderCode:137473062556548e5b25fd61-84558507%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '22c59260a4a934b7ac6a0dd8c66d15261ae9afaf' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/address.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '137473062556548e5b25fd61-84558507',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548e5b2750c0_94350338',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548e5b2750c0_94350338')) {function content_56548e5b2750c0_94350338($_smarty_tpl) {?><address>
    <div class="address"><?php echo $_smarty_tpl->tpl_vars['LINK']->value['ADDRESS'];?>
</div>
    <div class="addressCityStateZip"><?php echo $_smarty_tpl->tpl_vars['LINK']->value['CITY'];?>
 <?php echo $_smarty_tpl->tpl_vars['LINK']->value['STATE'];?>
 <?php echo $_smarty_tpl->tpl_vars['LINK']->value['ZIP'];?>
 <?php echo $_smarty_tpl->tpl_vars['LINK']->value['COUNTRY'];?>
</div>
    <div class="contacts">
    <?php if ($_smarty_tpl->tpl_vars['LINK']->value['PHONE_NUMBER']){?>
        <div class="phone"><label>Phone: </label><?php echo $_smarty_tpl->tpl_vars['LINK']->value['PHONE_NUMBER'];?>
</div>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['LINK']->value['FAX_NUMBER']){?>
        <div class="fax"><label>Fax: </label><?php echo $_smarty_tpl->tpl_vars['LINK']->value['FAX_NUMBER'];?>
</div>
    <?php }?>
    </div>
</address><?php }} ?>