<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:19:03
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/layouts/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1951880751535a9907ca0ef7-30001461%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b80322684b259db30511a03b6481c73b395aff2' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/layouts/default.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1951880751535a9907ca0ef7-30001461',
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
    'FLASH_MESSENGER' => 0,
    'content' => 0,
    'regular_user_details' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a9907e3ae92_98686175',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a9907e3ae92_98686175')) {function content_535a9907e3ae92_98686175($_smarty_tpl) {?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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



<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<link rel="apple-touch-icon" href="<?php echo @DOC_ROOT;?>
/templates/MobileFormat/iui/iui-logo-touch-icon.png" />
<meta name="apple-touch-fullscreen" content="YES" />
<link rel="stylesheet" href="<?php echo @DOC_ROOT;?>
/templates/MobileFormat/iui/iui.css" type="text/css" />
<link rel="stylesheet" href="<?php echo @DOC_ROOT;?>
/templates/MobileFormat/iui/t/<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['color']->value;?>
-theme.css" type="text/css"/>
<script type="application/x-javascript" src="<?php echo @DOC_ROOT;?>
/templates/MobileFormat/iui/iui.js"></script>




   
   
   <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/MobileFormat/style/main.css" />
 <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/Core/DefaultFrontend/style/select2.css"  />
        <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/Core/DefaultFrontend/style/fg.menu.css" />
   
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
         <meta name="author" content="<?php echo trim(htmlspecialchars(preg_replace('!\s+!u', ' ',$_smarty_tpl->tpl_vars['MetaAuthor']->value), ENT_QUOTES, 'UTF-8', true));?>
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
           <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/jquery/jquery.select2.js"></script>
        <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/jquery/jquery.fg.menu.js"></script>
     
            <script type="text/javascript">
             var $ = jQuery.noConflict();
	     
	     
	     $(function() {
	      $('.listingsList > li').unwrap();
	      $('.phpld-cbox > li').unwrap();
	      
	      $('.phpld-col3 > li').unwrap();
	      
	       
	    });
	  


            </script>
     
	
	
	
</head>
<body>





<div class="toolbar">
    <h1 id="pageTitle"></h1>
    <a id="homeButton" class="button" href="<?php echo @DOC_ROOT;?>
/" target="_self">Home</a>
    <a class="button" href="#searchForm">Search</a>
</div>


<ul id="<?php if (!empty($_REQUEST['controller'])){?><?php echo $_REQUEST['controller'];?>
<?php }else{ ?>home<?php }?>" title="<?php echo $_smarty_tpl->tpl_vars['PAGE_TITLE']->value;?>
" selected="true"> 

	 <?php echo $_smarty_tpl->getSubTemplate ("views/_shared/messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	 <?php echo $_smarty_tpl->tpl_vars['FLASH_MESSENGER']->value;?>

	
			
	
	<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

	<li><a href="<?php echo @DOC_ROOT;?>
/submit" target="_self"> Add A Listing</a></li>
	
	 <?php if ((@REQUIRE_REGISTERED_USER==1)){?>
    
         <?php if (empty($_smarty_tpl->tpl_vars['regular_user_details']->value)){?>
         <li><a href="<?php echo @DOC_ROOT;?>
/user/register" title="Register new user">Register</a></li>
         <li><a href="<?php echo @DOC_ROOT;?>
/user/login" title="User Login">User Login</a></li>
         <?php }else{ ?>
            <li><a href="<?php echo @DOC_ROOT;?>
/user" title="My Account">My Account</a></li>
	       <li><a href="<?php echo @DOC_ROOT;?>
/user/logout" title="My Account">Logout</a></li>
         <?php }?>
      <?php }?>
	
	<li>
	<div class="footer">
		<span class="copyr"><a href="http://www.phplinkdirectory.com" title="PHP Link Directory" target="_parent">PHP Link Directory - Mobile Format</a></span>
	</div>
	</li>
</ul>
<form id="searchForm" class="dialog" action="<?php echo @DOC_ROOT;?>
/search" method="get" target="_self">
    <fieldset style="height:50px;">
        <h1>Search</h1>
        <a class="button leftButton" type="cancel" href="#" target="_self">Cancel</a>
        <a class="button blueButton" type="submit" onclick="submit()">Search</a>
        <div style="text-align:center;padding-top:30px;" >
        <label style="color:#fff;">Search:</label>
        <input type="text" name="search" maxlength="250" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" style="width:100px" />
		</div>
    </fieldset>
</form>
<?php echo @GOOGLE_ANALYTICS;?>

</body>
</html>

<?php }} ?>