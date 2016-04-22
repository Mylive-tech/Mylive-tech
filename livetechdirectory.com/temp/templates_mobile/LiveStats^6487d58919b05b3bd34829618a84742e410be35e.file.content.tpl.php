<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:18:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/application/widgets/LiveStats/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:624686829535ab4fc66d647-42518916%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6487d58919b05b3bd34829618a84742e410be35e' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/application/widgets/LiveStats/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '624686829535ab4fc66d647-42518916',
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
  'unifunc' => 'content_535ab4fc68de83_54057077',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab4fc68de83_54057077')) {function content_535ab4fc68de83_54057077($_smarty_tpl) {?>
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