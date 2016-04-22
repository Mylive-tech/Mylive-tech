<?php /* Smarty version Smarty-3.1.12, created on 2014-05-11 17:09:38
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/address.tpl" */ ?>
<?php /*%%SmartyHeaderCode:105610595536faed2571557-10057750%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9ef9bc399a9dfa317379683aa8d03dc8d7637725' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/address.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '105610595536faed2571557-10057750',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_536faed25a56c7_85645563',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_536faed25a56c7_85645563')) {function content_536faed25a56c7_85645563($_smarty_tpl) {?><address>
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