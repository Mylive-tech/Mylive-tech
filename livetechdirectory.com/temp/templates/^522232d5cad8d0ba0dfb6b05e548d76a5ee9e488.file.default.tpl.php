<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/DevXBlue/layouts/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14962766756548c5e5dd648-47330429%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '522232d5cad8d0ba0dfb6b05e548d76a5ee9e488' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/DevXBlue/layouts/default.tpl',
      1 => 1400061722,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14962766756548c5e5dd648-47330429',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'PAGE_TITLE' => 0,
    'color' => 0,
    'MetaKeywords' => 0,
    'MetaDescription' => 0,
    'MetaAuthor' => 0,
    'MetaCopyright' => 0,
    'MetaRobots' => 0,
    'search' => 0,
    'category' => 0,
    'list' => 0,
    'links' => 0,
    'in_page_title' => 0,
    'p' => 0,
    'phpldThemeStyles' => 0,
    'phpldJavascripts' => 0,
    'phpldStylesheets' => 0,
    'USER_PANEL' => 0,
    'HEADER_LOGO' => 0,
    'HEADER_SEARCH_FORM' => 0,
    'MAIN_MENU' => 0,
    'BREADCRUMBS' => 0,
    'FLASH_MESSENGER' => 0,
    'layout_type' => 0,
    'widget_list' => 0,
    'content' => 0,
    'LINK_CLICK_TRACKER_CODE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e689f87_30224477',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e689f87_30224477')) {function content_56548c5e689f87_30224477($_smarty_tpl) {?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        
        <title><?php echo $_smarty_tpl->tpl_vars['PAGE_TITLE']->value;?>
</title>
        
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo @CHARSET;?>
" />
        <script type="text/javascript">
            var DOC_ROOT = '<?php echo @FRONT_DOC_ROOT;?>
';
        </script>
        
        
	<link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/<?php echo @TEMPLATE;?>
/style/column.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/<?php echo @TEMPLATE;?>
/style/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/<?php echo @TEMPLATE;?>
/style/main_min.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/<?php echo @TEMPLATE;?>
/style/colors/<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
-theme.css" />
         <!--[if IE 7]>
   	<link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/<?php echo @TEMPLATE;?>
/style/ie7.css" />
	  <![endif]-->

        
        <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/Core/DefaultFrontend/style/select2.css"  />
        <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/Core/DefaultFrontend/style/fg.menu.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/Core/DefaultFrontend/style/theme/jquery-ui-1.8.23.custom.css"  />


        
        <?php if (@ENABLE_META_TAGS){?>
            <?php if (!empty($_smarty_tpl->tpl_vars['MetaKeywords']->value)){?>
                <meta name="keywords" content="<?php echo trim(htmlspecialchars(preg_replace('!\s+!u', ' ',$_smarty_tpl->tpl_vars['MetaKeywords']->value), ENT_QUOTES, 'UTF-8', true));?>
" />
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['MetaDescription']->value)){?>
                <meta name="description" content="<?php echo trim(htmlspecialchars(preg_replace('!\s+!u', ' ',$_smarty_tpl->tpl_vars['MetaDescription']->value), ENT_QUOTES, 'UTF-8', true));?>
" />
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['MetaAuthor']->value)){?>
                <meta name="author" content="<?php echo $_smarty_tpl->tpl_vars['MetaAuthor']->value;?>
" />
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['MetaCopyright']->value)){?>
                <meta name="copyright" content="<?php echo trim(htmlspecialchars(preg_replace('!\s+!u', ' ',$_smarty_tpl->tpl_vars['MetaCopyright']->value), ENT_QUOTES, 'UTF-8', true));?>
" />
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['MetaRobots']->value)){?>
                <meta name="robots" content="<?php echo trim(htmlspecialchars(preg_replace('!\s+!u', ' ',$_smarty_tpl->tpl_vars['MetaRobots']->value), ENT_QUOTES, 'UTF-8', true));?>
" />
            <?php }?>
        <?php }?>

        
        <meta name="generator" content="PHP Link Directory <?php echo @CURRENT_VERSION;?>
" />

        
        <?php if (@ENABLE_RSS&&(!empty($_smarty_tpl->tpl_vars['search']->value)||$_smarty_tpl->tpl_vars['category']->value['ID']>0||$_smarty_tpl->tpl_vars['list']->value)&&count($_smarty_tpl->tpl_vars['links']->value)>0){?>
            <link rel="alternate" type="application/rss+xml" title="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['in_page_title']->value, ENT_QUOTES, 'UTF-8', true));?>
" href="<?php echo @SITE_URL;?>
/rss.php?<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?>search=<?php echo urlencode($_smarty_tpl->tpl_vars['search']->value);?>
<?php }elseif($_smarty_tpl->tpl_vars['p']->value>1){?>p=<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
<?php }elseif($_smarty_tpl->tpl_vars['list']->value){?>list=<?php echo $_smarty_tpl->tpl_vars['list']->value;?>
<?php }else{ ?>c=<?php echo $_smarty_tpl->tpl_vars['category']->value['ID'];?>
<?php }?>" />
        <?php }?>

        <?php if (@USE_CDN){?>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
        <?php }else{ ?>
        <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/jquery/jquery.js"></script>
        <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/jquery/jquery-ui.js"></script>
	    <?php }?>

        
            <script type="text/javascript">
             var $ = jQuery.noConflict();
            </script>
        

        <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/jquery/jquery.select2.js"></script>
        <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/jquery/jquery.fg.menu.js"></script>

        <?php echo $_smarty_tpl->tpl_vars['phpldThemeStyles']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['phpldJavascripts']->value;?>

        <?php echo $_smarty_tpl->tpl_vars['phpldStylesheets']->value;?>

    
	</head>
    <body>
    <div class="wrap">
        <div class="masthead">
            <div>
                <div class="phpld-wbox">
                    <?php echo $_smarty_tpl->tpl_vars['USER_PANEL']->value;?>

                    <div class="phpld-clearfix"></div>

                    <div class="header">
                        <div class="headerLogo">
                            <?php echo $_smarty_tpl->tpl_vars['HEADER_LOGO']->value;?>

                        </div>
                        <?php echo $_smarty_tpl->tpl_vars['HEADER_SEARCH_FORM']->value;?>

                    </div>
                </div>
            </div>
            <?php echo $_smarty_tpl->getSubTemplate ("views/_shared/messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            <div class="phpld-clearfix"></div>

            <div id="nav">
                <div class="phpld-wbox">
                    <div class="phpld-hlist">
                        <?php echo $_smarty_tpl->tpl_vars['MAIN_MENU']->value;?>

                   </div>
                </div>
            </div>
        </div>
        <div class="phpld-wbox">
        <div class="phpld-clearfix"></div>
        <div class="content-wrapper"><div class="path"><?php echo $_smarty_tpl->tpl_vars['BREADCRUMBS']->value;?>
</div><?php echo $_smarty_tpl->tpl_vars['FLASH_MESSENGER']->value;?>
<div class="phpld-column linearize-level-1"><?php if ($_smarty_tpl->tpl_vars['layout_type']->value=="custom"){?><?php echo $_smarty_tpl->getSubTemplate ("views/_customlayout/left_sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('widgets'=>$_smarty_tpl->tpl_vars['widget_list']->value['LEFT_COLUMN']), 0);?>
<?php echo $_smarty_tpl->getSubTemplate ("views/_customlayout/right_sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('widgets'=>$_smarty_tpl->tpl_vars['widget_list']->value['RIGHT_COLUMN']), 0);?>
<?php echo $_smarty_tpl->getSubTemplate ("views/_customlayout/main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('widgets'=>$_smarty_tpl->tpl_vars['widget_list']->value), 0);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
<?php }?></div></div><div class="footer"><ul class="ldcolumn"><li class="one3"><p> <div><a href="http://www.alexa.com/siteinfo/http://livetechdirectory.com"><SCRIPT type='text/javascript' language='JavaScript' src='http://xslt.alexa.com/site_stats/js/s/a?url=http://livetechdirectory.com'></SCRIPT></a></div> </p><p>Powered By: Live-Tech</p></li><li class="one3"><div id="mc_embed_signup"><form action="http://mylive-tech.us4.list-manage.com/subscribe/post?u=e177b3f8e4ae6344dd3e3e738&amp;id=2f4e5bcd1f" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate><label for="mce-EMAIL">Subscribe to our mailing list</label><input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required><div style="position: absolute; left: -5000px;"><input type="text" name="b_e177b3f8e4ae6344dd3e3e738_2f4e5bcd1f" tabindex="-1" value=""></div><div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div></form></div></li><li class="one3 lastcol"><p><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.livetechdirectory.com%2F&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=true&amp;share=true&amp;height=21&amp;appId=715319921835642" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe></p><p><!-- Place this tag where you want the share button to render. --><div class="g-plus" data-action="share" data-height="24" data-href="http://www.livetechdirectory.com/"></div><!-- Place this tag after the last share tag. --><script type="text/javascript">(function() {var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;po.src = 'https://apis.google.com/js/platform.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);})();</script></p></li></ul></div><?php echo @GOOGLE_ANALYTICS;?>
<?php echo $_smarty_tpl->tpl_vars['LINK_CLICK_TRACKER_CODE']->value;?>
<!-- Begin SupportSuite Javascript Code --><script type="text/javascript" src="http://support.mylive-tech.com/visitor/index.php?_m=livesupport&_a=htmlcode&nolink=1"></script><!-- End SupportSuite Javascript Code --></body></html>
<?php }} ?>