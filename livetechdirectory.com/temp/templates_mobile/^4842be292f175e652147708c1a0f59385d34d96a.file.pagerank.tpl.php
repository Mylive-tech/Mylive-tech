<?php /* Smarty version Smarty-3.1.12, created on 2014-05-11 17:09:38
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/pagerank.tpl" */ ?>
<?php /*%%SmartyHeaderCode:423928363536faed2664454-16949934%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4842be292f175e652147708c1a0f59385d34d96a' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/pagerank.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '423928363536faed2664454-16949934',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_536faed26924e3_05198246',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_536faed26924e3_05198246')) {function content_536faed26924e3_05198246($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.math.php';
?><?php if ($_smarty_tpl->tpl_vars['LINK']->value['PAGERANK']>=0){?>
    PR: <?php echo $_smarty_tpl->tpl_vars['LINK']->value['PAGERANK'];?>

    <?php }else{ ?>
    N/A
<?php }?>
<div class="prg">
    <div class="prb" style="width: <?php if ($_smarty_tpl->tpl_vars['LINK']->value['PAGERANK']>-1){?><?php echo smarty_function_math(array('equation'=>"x*4",'x'=>$_smarty_tpl->tpl_vars['LINK']->value['PAGERANK']),$_smarty_tpl);?>
<?php }else{ ?>0<?php }?>px"></div>
</div>
<?php }} ?>