<?php /* Smarty version Smarty-3.1.12, created on 2014-05-02 10:22:53
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/details/business.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1073974747536371fd448078-70115910%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2aa682698602ed9eb2bd2baa6aaf772d8a3ad4e1' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/details/business.tpl',
      1 => 1386991058,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1073974747536371fd448078-70115910',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
    'WEBPAGE_URL' => 0,
    'ADDRESS' => 0,
    'LISTING_CATEGORIES_DETAILS' => 0,
    'LISTING_IMAGE' => 0,
    'LISTING_SUBMIT_ITEMS' => 0,
    'add_links' => 0,
    'add_link' => 0,
    'GOOGLE_MAP' => 0,
    'LISTING_CONTACT_LISTING' => 0,
    'LISTING_TELL_FRIEND' => 0,
    'LISTING_RATING' => 0,
    'LISTING_COMMENTS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_536371fd4afd26_34762734',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_536371fd4afd26_34762734')) {function content_536371fd4afd26_34762734($_smarty_tpl) {?><div class="listing-details listing-details-business">
    <h1><?php echo $_smarty_tpl->tpl_vars['LINK']->value['TITLE'];?>
</h1>
    <?php echo $_smarty_tpl->tpl_vars['WEBPAGE_URL']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['ADDRESS']->value;?>

    <div class="link-category">
		Category: <?php echo $_smarty_tpl->tpl_vars['LISTING_CATEGORIES_DETAILS']->value;?>

	</div>
    <div class="description-detail">
        <?php echo $_smarty_tpl->tpl_vars['LISTING_IMAGE']->value;?>

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
    <?php echo $_smarty_tpl->tpl_vars['GOOGLE_MAP']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['LISTING_CONTACT_LISTING']->value;?>
	
    <?php echo $_smarty_tpl->tpl_vars['LISTING_TELL_FRIEND']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['LISTING_RATING']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['LISTING_COMMENTS']->value;?>

</div><?php }} ?>