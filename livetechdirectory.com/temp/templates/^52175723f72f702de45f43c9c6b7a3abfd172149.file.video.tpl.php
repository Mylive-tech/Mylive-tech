<?php /* Smarty version Smarty-3.1.12, created on 2015-10-29 00:13:11
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/details/video.tpl" */ ?>
<?php /*%%SmartyHeaderCode:58306772056316497b059e6-33213781%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '52175723f72f702de45f43c9c6b7a3abfd172149' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/details/video.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '58306772056316497b059e6-33213781',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
    'LISTING_VIDEO_EMBED' => 0,
    'LISTING_SUBMIT_ITEMS' => 0,
    'add_links' => 0,
    'add_link' => 0,
    'LISTING_TELL_FRIEND' => 0,
    'LISTING_RATING' => 0,
    'LISTING_COMMENTS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56316497b94b98_87560439',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56316497b94b98_87560439')) {function content_56316497b94b98_87560439($_smarty_tpl) {?><div class="listing-details listing-details-picture">
    <h1><?php echo $_smarty_tpl->tpl_vars['LINK']->value['TITLE'];?>
</h1>
    <?php echo $_smarty_tpl->tpl_vars['LISTING_VIDEO_EMBED']->value;?>

    <div class="description-detail">
        <?php echo $_smarty_tpl->tpl_vars['LINK']->value['DESCRIPTION'];?>

    </div>
    <div class="phpld-clearfix"></div>
    <?php echo $_smarty_tpl->tpl_vars['LISTING_SUBMIT_ITEMS']->value;?>

    <?php if ($_smarty_tpl->tpl_vars['add_links']->value){?>
        <div>
            <div class="phpld-label float-left">Deep Links:</div>
            <div class="smallDesc float-left">
                <?php  $_smarty_tpl->tpl_vars['add_link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['add_link']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['add_links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['add_link']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['add_link']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['add_link']->key => $_smarty_tpl->tpl_vars['add_link']->value){
$_smarty_tpl->tpl_vars['add_link']->_loop = true;
 $_smarty_tpl->tpl_vars['add_link']->iteration++;
 $_smarty_tpl->tpl_vars['add_link']->last = $_smarty_tpl->tpl_vars['add_link']->iteration === $_smarty_tpl->tpl_vars['add_link']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['add_links']['last'] = $_smarty_tpl->tpl_vars['add_link']->last;
?>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['add_link']->value['URL'];?>
" target="_blank" title="<?php echo $_smarty_tpl->tpl_vars['add_link']->value['TITLE'];?>
"><?php echo $_smarty_tpl->tpl_vars['add_link']->value['TITLE'];?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['add_links']['last']){?>, <?php }?>
                <?php } ?>
            </div>
        </div>
    <?php }?>

    <?php echo $_smarty_tpl->tpl_vars['LISTING_TELL_FRIEND']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['LISTING_RATING']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['LISTING_COMMENTS']->value;?>


</div><?php }} ?>