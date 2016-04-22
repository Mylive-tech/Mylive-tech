<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {* Document/Browser title *}
        <title>{$PAGE_TITLE}</title>
        {* Document character set *}
        <meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.CHARSET}" />
        <script type="text/javascript">
            var DOC_ROOT = '{$smarty.const.FRONT_DOC_ROOT}';
        </script>
        {* CSS Style file *}
        
	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/column.css" />
	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/form.css" />
	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/main_min.css" />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/colors/{$color}-theme.css" />
         <!--[if IE 7]>
   	<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.TEMPLATE}/style/ie7.css" />
	  <![endif]-->

        {*<link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/{$smarty.const.USE_TEMPLATE}/chosen/chosen.css" media="screen"  />*}
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/select2.css"  />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/fg.menu.css" />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/theme/jquery-ui-1.8.23.custom.css"  />


        {* Custom META tags *}
        {if $smarty.const.ENABLE_META_TAGS}
            {if !empty($MetaKeywords)}
                <meta name="keywords" content="{$MetaKeywords|strip|escape|trim}" />
            {/if}
            {if !empty($MetaDescription)}
                <meta name="description" content="{$MetaDescription|strip|escape|trim}" />
            {/if}
            {if !empty($MetaAuthor)}
                <meta name="author" content="{$MetaAuthor}" />
            {/if}
            {if !empty($MetaCopyright)}
                <meta name="copyright" content="{$MetaCopyright|strip|escape|trim}" />
            {/if}
            {if !empty($MetaRobots)}
                <meta name="robots" content="{$MetaRobots|strip|escape|trim}" />
            {/if}
        {/if}

        {* Please keep this line for better version tracking *}
        <meta name="generator" content="PHP Link Directory {$smarty.const.CURRENT_VERSION}" />

        {* Live Bookmarks *}
        {if $smarty.const.ENABLE_RSS and (!empty($search) or $category.ID gt 0 or $list) and count($links) gt 0}
            <link rel="alternate" type="application/rss+xml" title="{$in_page_title|escape|trim}" href="{$smarty.const.SITE_URL}/rss.php?{if !empty($search)}search={$search|@urlencode}{elseif $p > 1}p={$p}{elseif $list}list={$list}{else}c={$category.ID}{/if}" />
        {/if}

        {if $smarty.const.USE_CDN}
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
        {else}
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery-ui.js"></script>
	    {/if}

        {literal}
            <script type="text/javascript">
             var $ = jQuery.noConflict();
            </script>
        {/literal}

        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.select2.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.fg.menu.js"></script>

        {$phpldThemeStyles}
        {$phpldJavascripts}
        {$phpldStylesheets}
    
	</head>
    <body>
    <div class="wrap">
        <div class="masthead">
            <div>
                <div class="phpld-wbox">
                    {$USER_PANEL}
                    <div class="phpld-clearfix"></div>

                    <div class="header">
                        <div class="headerLogo">
                            {$HEADER_LOGO}
                        </div>
                        {$HEADER_SEARCH_FORM}
                    </div>
                </div>
            </div>
            {include file="views/_shared/messages.tpl"}
            <div class="phpld-clearfix"></div>

            <div id="nav">
                <div class="phpld-wbox">
                    <div class="phpld-hlist">
                        {$MAIN_MENU}
                   </div>
                </div>
            </div>
        </div>
        <div class="phpld-wbox">
        <div class="phpld-clearfix"></div>
        {strip}

        <div class="content-wrapper">
            <div class="path">
                {$BREADCRUMBS}
            </div>
            {$FLASH_MESSENGER}
	    <div class="phpld-column linearize-level-1">
		{if $layout_type eq "custom"}
		{include file="views/_customlayout/left_sidebar.tpl" widgets=$widget_list.LEFT_COLUMN}
		{include file="views/_customlayout/right_sidebar.tpl" widgets=$widget_list.RIGHT_COLUMN}
		{include file="views/_customlayout/main.tpl" widgets=$widget_list}
		
		{else}
		    {$content}
		{/if}
	    </div>
        </div>
        <div class="footer">
        <ul class="ldcolumn">
        <li class="one3">
        <p> <div><a href="http://www.alexa.com/siteinfo/http://livetechdirectory.com"><SCRIPT type='text/javascript' language='JavaScript' src='http://xslt.alexa.com/site_stats/js/s/a?url=http://livetechdirectory.com'></SCRIPT></a></div> </p>
            <p>Powered By: Live-Tech</p>
        </li>
        
        <li class="one3">
<div id="mc_embed_signup">
<form action="http://mylive-tech.us4.list-manage.com/subscribe/post?u=e177b3f8e4ae6344dd3e3e738&amp;id=2f4e5bcd1f" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
	<label for="mce-EMAIL">Subscribe to our mailing list</label>
	<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
    <div style="position: absolute; left: -5000px;"><input type="text" name="b_e177b3f8e4ae6344dd3e3e738_2f4e5bcd1f" tabindex="-1" value=""></div>
    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
</form>
</div>

        </li>
         <li class="one3 lastcol">      
       <p> 
       <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.livetechdirectory.com%2F&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=true&amp;share=true&amp;height=21&amp;appId=715319921835642" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>
       </p> 
<p>
<!-- Place this tag where you want the share button to render. -->
<div class="g-plus" data-action="share" data-height="24" data-href="http://www.livetechdirectory.com/"></div>
<!-- Place this tag after the last share tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
</p>
       
        </li>
        </ul>
        
    </div>
    
    {$smarty.const.GOOGLE_ANALYTICS}
    {$LINK_CLICK_TRACKER_CODE}

<!-- Begin SupportSuite Javascript Code --> 
<script type="text/javascript" src="http://support.mylive-tech.com/visitor/index.php?_m=livesupport&_a=htmlcode&nolink=1"></script> 
<!-- End SupportSuite Javascript Code -->

</body>
</html>
{/strip}
