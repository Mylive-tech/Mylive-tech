<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/application/widgets/LiveStats/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12490998656548c5e52dda0-62845228%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12cdaaf17d39a54591e705723899eaa4a0f566d3' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/application/widgets/LiveStats/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12490998656548c5e52dda0-62845228',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lsBG' => 0,
    'lsTC' => 0,
    'lsBC' => 0,
    'lsLC' => 0,
    'lsHC' => 0,
    'lsW' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e53b4d7_61644991',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e53b4d7_61644991')) {function content_56548c5e53b4d7_61644991($_smarty_tpl) {?>
<center>	 
	<script type="text/javascript" src="http://feedjit.com/serve/?bc=<?php echo $_smarty_tpl->tpl_vars['lsBG']->value;?>
&amp;tc=<?php echo $_smarty_tpl->tpl_vars['lsTC']->value;?>
&amp;brd1=<?php echo $_smarty_tpl->tpl_vars['lsBC']->value;?>
&amp;lnk=<?php echo $_smarty_tpl->tpl_vars['lsLC']->value;?>
&amp;hc=<?php echo $_smarty_tpl->tpl_vars['lsHC']->value;?>
&amp;ww=<?php echo $_smarty_tpl->tpl_vars['lsW']->value;?>
"></script><noscript><a href="http://feedjit.com/">Live Stats</a></noscript>
	</center>


<?php }} ?>