<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:35
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/main.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1453394444535a93c36cb040-89373030%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '223fdf1f72be30cfadda0255ddb7d12fdb0fc1f4' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/main.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1453394444535a93c36cb040-89373030',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'show_me' => 0,
    'adminJs' => 0,
    'adminJsCode' => 0,
    'adminStyles' => 0,
    'requestUri' => 0,
    'rights' => 0,
    'user_details' => 0,
    'menu' => 0,
    'mm' => 0,
    'mk' => 0,
    'content' => 0,
    'redirect' => 0,
    'nextUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a93c3830223_00079756',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93c3830223_00079756')) {function content_535a93c3830223_00079756($_smarty_tpl) {?><?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo smarty_php_tag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

   $isExplorer = false;
   $isMozilla = false;
   $agent = strtolower($HTTP_USER_AGENT);
   if ((strpos ($agent, "ie")  !== false)) { $isExplorer = true; } else { $isMozilla = true; }
   if ((strpos ($agent, "jig") !== false)) { $isExplorer = false; $isMozilla = false; }

   $this->assign('isExplorer', $isExplorer);
   $this->assign('isMozilla', $isMozilla);
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_php_tag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>PHP Link Directory Phoenix v<?php echo @CURRENT_VERSION;?>
 Admin<?php if (!empty($_smarty_tpl->tpl_vars['title']->value)){?> - <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8', true));?>
<?php }?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo @TEMPLATE_ROOT;?>
/files/style.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo @TEMPLATE_ROOT;?>
/files/litbox.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo @TEMPLATE_ROOT;?>
/files/fileuploader.css" />

        <!-- Define document root for Javascript -->
        <script type="text/javascript">
            /* <![CDATA[ */
            var DOC_ROOT = '<?php echo @DOC_ROOT;?>
';
            /* ]]> */
        </script>


      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

	<script type="text/javascript" src="../javascripts/thickbox/thickbox.js"></script>
   <link rel="stylesheet" href="../javascripts/thickbox/ThickBox.css" type="text/css" media="screen" />

   <script src="../javascripts/jquery/jquery.jeditable.mini.js"></script>
   <script src="../javascripts/jquery/jquery.dataTables.js"></script>
   <script src="../javascripts/jquery.validate.js"></script>
   <script src="../javascripts/jquery/jquery.field.min.js"></script>

        <script type="text/javascript">
        jQuery.noConflict();
    </script>



    
    
    <script type="text/javascript" src="../javascripts/formtool/formtool.js"></script>

    
    <script type="text/javascript" src="../javascripts/prototype/prototype.js"></script>
    
    <script type="text/javascript" src="../javascripts/scriptaculous/scriptaculous.js"></script>

    
    <script type="text/javascript" src="../javascripts/litbox/litbox.js"></script>
    <script type="text/javascript" src="../javascripts/litbox/dragdrop.js"></script>
    
    <script type="text/javascript" src="../javascripts/phpld_global.js"></script>
    
    <script type="text/javascript" src="../javascripts/categ_selection/categ_selection.js"></script>
    
    <script type="text/javascript" src="../javascripts/tiny_mce/tiny_mce.js"></script>

        
        <script type="text/javascript" src="<?php echo @TEMPLATE_ROOT;?>
/files/admin.js"></script>
        <script type="text/javascript" src="<?php echo @TEMPLATE_ROOT;?>
/files/style.js"></script>

           
            <script src="../javascripts/google_jsapi.js" type="text/javascript"></script> 
        
        <?php if ($_smarty_tpl->tpl_vars['show_me']->value==1){?>
            <script type="text/javascript" src="../javascripts/google_import.js"></script>
        <?php }?>

        <noscript>

            <meta http-equiv="refresh" content="2">

        </noscript>

        
            <script type="text/javascript">
                var valid_obj = new Object();
            </script>
        
    <?php echo $_smarty_tpl->tpl_vars['adminJs']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['adminJsCode']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['adminStyles']->value;?>

</head>
<body onload="showWhich('<?php echo $_smarty_tpl->tpl_vars['requestUri']->value;?>
')">

    <!-- Main --><div id="wrap"><!-- Header --><div id="header"><div id="header-title"><a href="http://www.phplinkdirectory.com/" title="Visit PHP Link Directory homepage" class="phpldBackLink" target="_blank" style="float: left;"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/phpldlogo.png" alt="PHP Link Directory" id="logo" /></a><div id="admin-top-links"><?php if ($_smarty_tpl->tpl_vars['rights']->value['addLink']==1){?><a href="<?php echo @DOC_ROOT;?>
/dir_links_edit.php?action=N" class="new_link">New link</a><?php }?><?php if ($_smarty_tpl->tpl_vars['rights']->value['addCat']==1){?><a href="<?php echo @DOC_ROOT;?>
/dir_categs_edit.php?action=N" class="new_cat">New category</a><?php }?><?php if ($_smarty_tpl->tpl_vars['rights']->value['addPage']==1){?><a href="<?php echo @DOC_ROOT;?>
/dir_pages_edit.php?action=N" class="new_page">New page</a><?php }?><div style="clear: left ;"></div></div></div><div class="session">Welcome <?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['user_details']->value['NAME'], ENT_QUOTES, 'UTF-8', true));?>
 [<a href="<?php echo trim(htmlspecialchars(@SITE_URL, ENT_QUOTES, 'UTF-8', true));?>
" title="View Directory" target="_blank">View Directory</a>, <a href="<?php echo @DOC_ROOT;?>
/logout.php" title="Logout of admin control panel">Logout</a>]</div></div><!-- /Header --><?php if (!empty($_smarty_tpl->tpl_vars['title']->value)){?><h1><span><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8', true));?>
</span></h1><?php }?><div class="clearfix"><!-- Secondary Content --><div id="content-secondary"><div class="secondary-column-box"><?php if (isset($_smarty_tpl->tpl_vars['menu']->value)&&!empty($_smarty_tpl->tpl_vars['menu']->value)&&is_array($_smarty_tpl->tpl_vars['menu']->value)){?><!-- Main menu --><div id="navigation"><script type="text/javascript" src="<?php echo @TEMPLATE_ROOT;?>
/files/menu_admin.js"></script><h2 class="navigation-title"><span>Navigation</span></h2><ul><?php  $_smarty_tpl->tpl_vars['mm'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mm']->_loop = false;
 $_smarty_tpl->tpl_vars['mk'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['menu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mm']->key => $_smarty_tpl->tpl_vars['mm']->value){
$_smarty_tpl->tpl_vars['mm']->_loop = true;
 $_smarty_tpl->tpl_vars['mk']->value = $_smarty_tpl->tpl_vars['mm']->key;
?><?php if (is_array($_smarty_tpl->tpl_vars['mm']->value['menu'])&&!empty($_smarty_tpl->tpl_vars['mm']->value['menu'])){?><li title="<?php echo $_smarty_tpl->tpl_vars['mm']->value['label'];?>
" class="code1 closed" style="padding-top: 3px;"><span class="menu-button <?php echo $_smarty_tpl->tpl_vars['mm']->value['class'];?>
"><?php echo $_smarty_tpl->tpl_vars['mm']->value['label'];?>
</span><?php }else{ ?><li  style="padding-top: 3px;"><a href="<?php echo $_smarty_tpl->tpl_vars['mk']->value;?>
.php" title="<?php echo $_smarty_tpl->tpl_vars['mm']->value;?>
" class="topmenu"><span class="menu-button  <?php echo $_smarty_tpl->tpl_vars['mm']->value['class'];?>
"><?php echo $_smarty_tpl->tpl_vars['mm']->value['label'];?>
</span></a><?php }?><?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/menu.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('m'=>$_smarty_tpl->tpl_vars['mm']->value), 0);?>
</li><?php } ?></ul></div><!-- /Main menu --><?php }?></div></div><!-- /Secondary Content --><!-- Main Content --><div id="content-main"><div class="main-column-box"><!-- Content --><div id="content"><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
</div><!-- /Content --></div></div><!-- /Main Content --><div class="clearfix"></div></div><!-- Footer --><div id="footer">PHP Link Directory Phoenix v<?php echo @CURRENT_VERSION;?>
, Copyright &copy; 2004-<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo smarty_php_tag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
echo date('Y');<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_php_tag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 NetCreated. More Information: <a href="http://www.phplinkdirectory.com/forum/" title="Browse PHP Link Directory Forums" target="_blank">Community Support</a>.</div><!-- /Footer --></div><!-- /Main --><?php if (!empty($_smarty_tpl->tpl_vars['redirect']->value)){?><?php echo $_smarty_tpl->tpl_vars['redirect']->value;?>
<?php }?><?php echo $_smarty_tpl->tpl_vars['nextUrl']->value;?>

</body>
</html>
<?php }} ?>