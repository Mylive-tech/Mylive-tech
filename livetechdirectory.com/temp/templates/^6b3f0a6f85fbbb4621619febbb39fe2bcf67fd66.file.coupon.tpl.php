<?php /* Smarty version Smarty-3.1.12, created on 2014-10-18 13:58:03
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/details/coupon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1901057644544271eb2b8829-98206106%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b3f0a6f85fbbb4621619febbb39fe2bcf67fd66' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/details/coupon.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1901057644544271eb2b8829-98206106',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTING_RATING' => 0,
    'LINK' => 0,
    'LISTING_URL' => 0,
    'LISTING_SUBMIT_ITEMS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_544271eb2d1505_84748140',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_544271eb2d1505_84748140')) {function content_544271eb2d1505_84748140($_smarty_tpl) {?><div class="listing-details listing-details-default">
    <?php echo $_smarty_tpl->tpl_vars['LISTING_RATING']->value;?>

    <h1><?php echo $_smarty_tpl->tpl_vars['LINK']->value['TITLE'];?>
</h1>
    Code: <a href="javascript:void(null);"  rel-data="<?php echo $_smarty_tpl->tpl_vars['LINK']->value['COUPON_CODE'];?>
" class="clipboard_enabled coupon_code"><span><?php echo $_smarty_tpl->tpl_vars['LINK']->value['COUPON_CODE'];?>
</span></a>
    <?php echo $_smarty_tpl->tpl_vars['LISTING_URL']->value;?>

    <div class="description-detail">
        <?php echo $_smarty_tpl->tpl_vars['LINK']->value['DESCRIPTION'];?>

    </div>
    <?php echo $_smarty_tpl->tpl_vars['LISTING_SUBMIT_ITEMS']->value;?>

    <div class="phpld-clearfix"></div>
</div><?php }} ?>