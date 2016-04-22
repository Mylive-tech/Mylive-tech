<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/application/widgets/AdsenseCentral/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9361794885654ac8c890073-27326694%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6a0ce9a969b0ce4fc2bcac7c38b169c98ecf5829' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/application/widgets/AdsenseCentral/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9361794885654ac8c890073-27326694',
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
  'unifunc' => 'content_5654ac8c8b40e5_71773725',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac8c8b40e5_71773725')) {function content_5654ac8c8b40e5_71773725($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['set']->value['EMBED']!=''){?>
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