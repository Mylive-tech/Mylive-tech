<?php /* Smarty version Smarty-3.1.12, created on 2014-11-11 22:04:29
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/list/coupon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:471754602546287ed8a5468-36035313%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ba69cae0f2b21bcadc841f5dacf4e7841c227962' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/list/coupon.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '471754602546287ed8a5468-36035313',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTING_RATING' => 0,
    'LISTING_URL_TITLE' => 0,
    'LINK' => 0,
    'LISTING_CATEGORIES' => 0,
    'ANNOUNCE' => 0,
    'READ_MORE_LINK' => 0,
    'ratings_on' => 0,
    'RATING_STARS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_546287ed90e177_68107059',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_546287ed90e177_68107059')) {function content_546287ed90e177_68107059($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.date_format.php';
?><div class="listing-list-item listing-list linearize-level-1">
    <?php echo $_smarty_tpl->tpl_vars['LISTING_RATING']->value;?>

    <h4><?php echo $_smarty_tpl->tpl_vars['LISTING_URL_TITLE']->value;?>
</h4>
    Code: <a href="javascript:void(null);"  rel-data="<?php echo $_smarty_tpl->tpl_vars['LINK']->value['COUPON_CODE'];?>
" class="clipboard_enabled coupon_code"><span><?php echo $_smarty_tpl->tpl_vars['LINK']->value['COUPON_CODE'];?>
</span></a>
    <div class="phpld-box list-headline">
        <span class="date"> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['LINK']->value['DATE_ADDED']);?>
 | </span>
        <div class="listing-categories"><?php echo $_smarty_tpl->tpl_vars['LISTING_CATEGORIES']->value;?>
</div>
    </div>
    <div class="phpld-clearfix"></div>
    <div class="description listing-field">
    <?php echo $_smarty_tpl->tpl_vars['ANNOUNCE']->value;?>
... <?php echo $_smarty_tpl->tpl_vars['READ_MORE_LINK']->value;?>

    </div>
    <div class="phpld-clearfix"></div>
<?php if ($_smarty_tpl->tpl_vars['ratings_on']->value==1&&@LINK_RATING_DISPLAY=='image'){?>
    <div class="link_rating convo">
        <?php echo $_smarty_tpl->tpl_vars['RATING_STARS']->value;?>

    </div>
<?php }?>
    <div class="phpld-clearfix"></div>
</div>
<?php }} ?>