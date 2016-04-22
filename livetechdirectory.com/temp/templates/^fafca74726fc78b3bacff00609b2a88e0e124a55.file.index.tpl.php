<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 04:11:45
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/author/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:147019664956553501dbb277-96120582%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fafca74726fc78b3bacff00609b2a88e0e124a55' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/author/index.tpl',
      1 => 1392433794,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '147019664956553501dbb277-96120582',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'NAME' => 0,
    'INFO' => 0,
    'WEBSITE' => 0,
    'WEBSITE_NAME' => 0,
    'RELATED' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56553501ebc6a1_43440226',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56553501ebc6a1_43440226')) {function content_56553501ebc6a1_43440226($_smarty_tpl) {?><h1><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
</h1>
<div class="authorPage">
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">Name:</div>
            <div class="phpld-label float-left">
                <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['NAME']->value, ENT_QUOTES, 'UTF-8', true));?>

            </div>
   </div>
   <?php if ($_smarty_tpl->tpl_vars['INFO']->value){?>
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">Info:</div>
            <div class="phpld-label float-left">
                <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['INFO']->value, ENT_QUOTES, 'UTF-8', true));?>

            </div>
   </div>
   <?php }?>
   
   <?php if ($_smarty_tpl->tpl_vars['WEBSITE']->value){?>
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">Website:</div>
            <div class="phpld-label float-left">
                <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['WEBSITE']->value, ENT_QUOTES, 'UTF-8', true));?>

            </div>
   </div>
   <?php }?>
   
   <?php if ($_smarty_tpl->tpl_vars['WEBSITE_NAME']->value){?>
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">Website Name:</div>
            <div class="phpld-label float-left">
                <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['WEBSITE_NAME']->value, ENT_QUOTES, 'UTF-8', true));?>

            </div>
   </div>
   <?php }?>
   <?php if ($_smarty_tpl->tpl_vars['RELATED']->value){?>
   <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">Members Listings:</div>
            <div class="phpld-label float-left">
                <?php echo $_smarty_tpl->tpl_vars['RELATED']->value;?>

            </div>
   </div>
   <?php }?>
   
</div>

<?php }} ?>