<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:45:06
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/rss.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1685797850535a9f225cb0c4-97199453%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3a6f321adb9aed8c84712d993f869a8fab2f960e' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/index/rss.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1685797850535a9f225cb0c4-97199453',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'url' => 0,
    'description' => 0,
    'links' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a9f226567b3_87368804',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a9f226567b3_87368804')) {function content_535a9f226567b3_87368804($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.date_format.php';
?><?php echo '<?xml';?> version="1.0" encoding="UTF-8"<?php echo '?>';?>

<rss version="2.0">
    <channel>
        <title><![CDATA[<?php echo @DIRECTORY_TITLE;?>
 <?php echo $_smarty_tpl->tpl_vars['title']->value;?>
]]></title>
        <link><?php echo $_smarty_tpl->tpl_vars['url']->value;?>
</link>
        <description><?php echo @SITE_DESC;?>
 <?php echo $_smarty_tpl->tpl_vars['description']->value;?>
</description>
	<?php  $_smarty_tpl->tpl_vars['link'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link']->key => $_smarty_tpl->tpl_vars['link']->value){
$_smarty_tpl->tpl_vars['link']->_loop = true;
?>
        <item>
            <title><![CDATA[<?php echo trim($_smarty_tpl->tpl_vars['link']->value['TITLE']);?>
]]></title>
                <link><![CDATA[<?php echo $_smarty_tpl->tpl_vars['link']->value->getUrl();?>
]]></link>
			<?php if ($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION']){?>
                <description><![CDATA[<?php echo trim($_smarty_tpl->tpl_vars['link']->value['DESCRIPTION']);?>
]]></description>
			<?php }?>
            <pubDate><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['link']->value['DATE_ADDED'],"%a, %d %b %Y %H:%M:%S GMT");?>
</pubDate>
        </item>
	<?php } ?>

    </channel>
</rss><?php }} ?>