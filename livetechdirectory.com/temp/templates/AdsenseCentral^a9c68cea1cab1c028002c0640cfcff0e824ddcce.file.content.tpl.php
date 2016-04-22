<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:01:47
         compiled from "/home/mylive5/public_html/livetechdirectory.com/application/widgets/AdsenseCentral/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1285738327535ab11b71fac1-12154648%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a9c68cea1cab1c028002c0640cfcff0e824ddcce' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/application/widgets/AdsenseCentral/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1285738327535ab11b71fac1-12154648',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'set' => 0,
    'TYPE' => 0,
    'WIDTH' => 0,
    'HEIGHT' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535ab11b7749e9_61545461',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab11b7749e9_61545461')) {function content_535ab11b7749e9_61545461($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['set']->value['EMBED']!=''){?>
<?php echo $_smarty_tpl->tpl_vars['set']->value['EMBED'];?>

<?php }else{ ?>
<?php if ($_smarty_tpl->tpl_vars['set']->value['PUBLISHER_ID']==''){?>
<p style="color: green">The <?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
 widget needs the publisher id set up in order to work.</p>
<?php }else{ ?>
<div style="width: <?php echo $_smarty_tpl->tpl_vars['WIDTH']->value;?>
px; height: <?php echo $_smarty_tpl->tpl_vars['HEIGHT']->value;?>
px; margin: 0px auto;">

<script type="text/javascript">
<!--
google_ad_client = "<?php echo $_smarty_tpl->tpl_vars['set']->value['PUBLISHER_ID'];?>
";
google_ad_width = <?php echo $_smarty_tpl->tpl_vars['WIDTH']->value;?>
;
google_ad_height = <?php echo $_smarty_tpl->tpl_vars['HEIGHT']->value;?>
;
google_ad_type = "text";

google_color_border = "<?php echo $_smarty_tpl->tpl_vars['set']->value['BORDER_COLOR'];?>
";
google_color_bg = "<?php echo $_smarty_tpl->tpl_vars['set']->value['BACKGROUND_COLOR'];?>
";
google_color_link = "<?php echo $_smarty_tpl->tpl_vars['set']->value['TITLE_COLOR'];?>
";
google_color_text = "<?php echo $_smarty_tpl->tpl_vars['set']->value['TEXT_COLOR'];?>
";
google_color_url = "<?php echo $_smarty_tpl->tpl_vars['set']->value['URL_COLOR'];?>
";
//-->

</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

</div>
<?php }?>
<?php }?><?php }} ?>