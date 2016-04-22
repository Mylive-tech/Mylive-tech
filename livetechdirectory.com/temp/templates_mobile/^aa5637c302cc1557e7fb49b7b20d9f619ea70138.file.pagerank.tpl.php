<?php /* Smarty version Smarty-3.1.12, created on 2015-11-26 17:56:31
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/pagerank.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1529539114565747cfd26459-94333874%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aa5637c302cc1557e7fb49b7b20d9f619ea70138' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/pagerank.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1529539114565747cfd26459-94333874',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_565747cfd37456_70177264',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_565747cfd37456_70177264')) {function content_565747cfd37456_70177264($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.math.php';
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