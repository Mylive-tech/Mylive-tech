<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:00:06
         compiled from "/home/mylive5/public_html/livetechdirectory.com/application/widgets/AlexaGraphDetailPage/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1809791840535ab0b6323106-52215432%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '52029b1f27a062127c7e92379806771262ef613e' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/application/widgets/AlexaGraphDetailPage/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1809791840535ab0b6323106-52215432',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SET_TITLE' => 0,
    'url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535ab0b63e2b67_77168030',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab0b63e2b67_77168030')) {function content_535ab0b63e2b67_77168030($_smarty_tpl) {?> <h3><?php echo $_smarty_tpl->tpl_vars['SET_TITLE']->value;?>
</h3>
 <div><img border="0" src="http://traffic.alexa.com/graph?w=379&h=216&r=6m&z=&y=r&u=<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['url']->value, ENT_QUOTES, 'UTF-8', true));?>
&u="></div>
				<br /><?php }} ?>