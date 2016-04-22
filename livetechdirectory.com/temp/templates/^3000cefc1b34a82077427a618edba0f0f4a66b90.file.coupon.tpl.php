<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:45:47
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/list/coupon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11247250175654943b278ac5-19842562%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3000cefc1b34a82077427a618edba0f0f4a66b90' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/list/coupon.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11247250175654943b278ac5-19842562',
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
  'unifunc' => 'content_5654943b290465_23702355',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654943b290465_23702355')) {function content_5654943b290465_23702355($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.date_format.php';
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