<?php /* Smarty version Smarty-3.1.12, created on 2014-05-06 13:51:43
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/list/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9409220085368e8efdd1813-42420325%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3924f46db5d57db3f272a6d373a8a2075e8874b0' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/list/default.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9409220085368e8efdd1813-42420325',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LISTING_URL_TITLE' => 0,
    'LINK' => 0,
    'LISTING_CATEGORIES' => 0,
    'PAGERANK' => 0,
    'ANNOUNCE' => 0,
    'READ_MORE_LINK' => 0,
    'ratings_on' => 0,
    'RATING_STARS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5368e8efe0e740_73260646',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5368e8efe0e740_73260646')) {function content_5368e8efe0e740_73260646($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.date_format.php';
?><div class="listing-list-item listing-list linearize-level-1">
        <h4><?php echo $_smarty_tpl->tpl_vars['LISTING_URL_TITLE']->value;?>
</h4>
        <div class="phpld-box list-headline"><span class="date"> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['LINK']->value['DATE_ADDED']);?>
 | </span><div class="listing-categories"><?php echo $_smarty_tpl->tpl_vars['LISTING_CATEGORIES']->value;?>
</div></div>
        <?php if (@SHOW_PAGERANK){?>
            <div class="page-rank"><?php echo $_smarty_tpl->tpl_vars['PAGERANK']->value;?>
</div>
        <?php }?>
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