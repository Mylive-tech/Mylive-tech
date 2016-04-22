<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:45:47
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/couponRating.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12831034195654943b257b98-02270192%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be3fc249a61987cdebc88c89492029f1e152b0be' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/couponRating.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12831034195654943b257b98-02270192',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
    'regular_user_details' => 0,
    'ratingError' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654943b275cb3_21792257',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654943b275cb3_21792257')) {function content_5654943b275cb3_21792257($_smarty_tpl) {?><div class="thumb-rating">
    <div class="title">
        Success
    </div>

    <div class="rating">
        <?php echo ceil((($_smarty_tpl->tpl_vars['LINK']->value['RATING']/$_smarty_tpl->tpl_vars['LINK']->value['VOTES'])*100));?>
%
    </div>
    <div class="thumbs">
        <?php if (@REQUIRE_REGISTERED_USER_LINK_RATING==1&&!empty($_smarty_tpl->tpl_vars['regular_user_details']->value)){?>
            <?php if ($_smarty_tpl->tpl_vars['ratingError']->value){?>
                <div class="box error">Please choose your rating, and after this click Rate.</div>
            <?php }?>


            <form action="<?php echo @DOC_ROOT;?>
/listing/rate/<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" method="post">
                <input type="hidden" name="RATING" value="1">
                <input type="submit" value="" class="thumb-up-icon">
            </form>
            <form action="<?php echo @DOC_ROOT;?>
/listing/rate/<?php echo $_smarty_tpl->tpl_vars['LINK']->value['ID'];?>
" method="post">
                <input type="hidden" name="RATING" value="0">
                <input type="submit" value="" class="thumb-down-icon">
            </form>
            <?php }else{ ?>
            

        <?php }?>
    </div>
</div><?php }} ?>