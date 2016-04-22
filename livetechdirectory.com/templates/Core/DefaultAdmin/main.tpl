{php}
   $isExplorer = false;
   $isMozilla = false;
   $agent = strtolower($HTTP_USER_AGENT);
   if ((strpos ($agent, "ie")  !== false)) { $isExplorer = true; } else { $isMozilla = true; }
   if ((strpos ($agent, "jig") !== false)) { $isExplorer = false; $isMozilla = false; }

   $this->assign('isExplorer', $isExplorer);
   $this->assign('isMozilla', $isMozilla);
{/php}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>PHP Link Directory Phoenix v{$smarty.const.CURRENT_VERSION} Admin{if !empty($title)} - {$title|escape|trim}{/if}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ROOT}/files/style.css" />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ROOT}/files/litbox.css" />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ROOT}/files/fileuploader.css" />

        <!-- Define document root for Javascript -->
        <script type="text/javascript">
            /* <![CDATA[ */
            var DOC_ROOT = '{$smarty.const.DOC_ROOT}';
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
        {literal}jQuery.noConflict();{/literal}
    </script>



    {* JavaScript libraries *}
    {* SmartyFormtool for manipulating forms with JavaScript *}
    <script type="text/javascript" src="../javascripts/formtool/formtool.js"></script>

    {* Prototype library - all comming JavaScripts are based on this *}
    <script type="text/javascript" src="../javascripts/prototype/prototype.js"></script>
    {* Scriptaculous - Incredible effects and controls library built on Prototype *}
    <script type="text/javascript" src="../javascripts/scriptaculous/scriptaculous.js"></script>

    {* LITBox library for popus and alert messages *}
    <script type="text/javascript" src="../javascripts/litbox/litbox.js"></script>
    <script type="text/javascript" src="../javascripts/litbox/dragdrop.js"></script>
    {* PHP Link Directory - Global JavaScript library *}
    <script type="text/javascript" src="../javascripts/phpld_global.js"></script>
    {* AJAX Category Selection library *}
    <script type="text/javascript" src="../javascripts/categ_selection/categ_selection.js"></script>
    {* TinyMCE text editor *}
    <script type="text/javascript" src="../javascripts/tiny_mce/tiny_mce.js"></script>

        {* Powerful library for admin control panel *}
        <script type="text/javascript" src="{$smarty.const.TEMPLATE_ROOT}/files/admin.js"></script>
        <script type="text/javascript" src="{$smarty.const.TEMPLATE_ROOT}/files/style.js"></script>

        {literal}   
            <script src="../javascripts/google_jsapi.js" type="text/javascript"></script> 
        {/literal}
        {if $show_me eq 1}
            <script type="text/javascript" src="../javascripts/google_import.js"></script>
        {/if}

        <noscript>

            <meta http-equiv="refresh" content="2">

        </noscript>

        {literal}
            <script type="text/javascript">
                var valid_obj = new Object();
            </script>
        {/literal}
    {$adminJs}
    {$adminJsCode}
    {$adminStyles}
</head>
<body onload="showWhich('{$requestUri}')">

    {strip}
        <!-- Main -->
        <div id="wrap">
            <!-- Header -->
            <div id="header">
                <div id="header-title">
                    <a href="http://www.phplinkdirectory.com/" title="{l}Visit PHP Link Directory homepage{/l}" class="phpldBackLink" target="_blank" style="float: left;"><img src="{$smarty.const.TEMPLATE_ROOT}/images/phpldlogo.png" alt="PHP Link Directory" id="logo" /></a>
                    <div id="admin-top-links">
                        {if $rights.addLink eq 1}
                            <a href="{$smarty.const.DOC_ROOT}/dir_links_edit.php?action=N" class="new_link">{l}New link{/l}</a>
                        {/if}
                        {if $rights.addCat eq 1}
                            <a href="{$smarty.const.DOC_ROOT}/dir_categs_edit.php?action=N" class="new_cat">{l}New category{/l}</a>
                        {/if}
                        {if $rights.addPage eq 1}
                            <a href="{$smarty.const.DOC_ROOT}/dir_pages_edit.php?action=N" class="new_page">{l}New page{/l}</a>
                        {/if}
                        <div style="clear: left ;"></div>
                    </div> 
                </div>

                <div class="session">{l}Welcome{/l} {$user_details.NAME|escape|trim} [<a href="{$smarty.const.SITE_URL|escape|trim}" title="{l}View Directory{/l}" target="_blank">{l}View Directory{/l}</a>, <a href="{$smarty.const.DOC_ROOT}/logout.php" title="{l}Logout of admin control panel{/l}">{l}Logout{/l}</a>]</div>

            </div>
            <!-- /Header -->

            {* Show page title *}
            {if !empty($title)}
                <h1><span>{$title|escape|trim}</span></h1>
            {/if}

        <div class="clearfix">
            <!-- Secondary Content -->
            <div id="content-secondary">
                <div class="secondary-column-box">

                    {* Load main menu *}
                    {if isset($menu) and !empty($menu) and is_array($menu)}
                        <!-- Main menu -->
                        <div id="navigation">
                            <script type="text/javascript" src="{$smarty.const.TEMPLATE_ROOT}/files/menu_admin.js"></script>
                            <h2 class="navigation-title"><span>{l}Navigation{/l}</span></h2>
                            <ul>
                                {foreach from=$menu item=mm key=mk}
                                    {if is_array($mm.menu) and !empty($mm.menu)}
                                        <li title="{$mm.label}" class="code1 closed" style="padding-top: 3px;">
                                            <span class="menu-button {$mm.class}">{$mm.label}</span>
                                        {else}
                                            <li  style="padding-top: 3px;">
                                                <a href="{$mk}.php" title="{$mm}" class="topmenu"><span class="menu-button  {$mm.class}">{$mm.label}</span></a>
                                            {/if}
                                            {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/menu.tpl" m=$mm}

                                        </li>
                                    {/foreach}
                            </ul>
                        </div>
                        <!-- /Main menu -->
                    {/if}
                </div>
            </div>
            <!-- /Secondary Content -->

            <!-- Main Content -->
            <div id="content-main">
                <div class="main-column-box">
                    {* Show page content *}
                    <!-- Content -->
                    <div id="content">{$content}</div>
                    <!-- /Content -->
                </div>
            </div>

            <!-- /Main Content -->

            <div class="clearfix"></div>
        </div>
            <!-- Footer -->
            <div id="footer">PHP Link Directory Phoenix v{$smarty.const.CURRENT_VERSION}, Copyright &copy; 2004-{php}echo date('Y');{/php} NetCreated. More Information: <a href="http://www.phplinkdirectory.com/forum/" title="Browse PHP Link Directory Forums" target="_blank">Community Support</a>.</div>
            <!-- /Footer -->

        </div>
        <!-- /Main -->

        {* Proceed to page redirect if needed *}
        {if !empty ($redirect)}
            {$redirect}
        {/if}

        {$nextUrl}
    {/strip}
</body>
</html>
