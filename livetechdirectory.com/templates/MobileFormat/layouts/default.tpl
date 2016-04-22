<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   {* Document/Browser title *}
 <title>{$PAGE_TITLE}</title>
 {* Document character set *}
   <meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.CHARSET}" />
   <script type="text/javascript">
            var DOC_ROOT = '{$smarty.const.FRONT_DOC_ROOT}';
   </script>

{*************************}
{literal}
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
<link rel="apple-touch-icon" href="{/literal}{$smarty.const.DOC_ROOT}{literal}/templates/MobileFormat/iui/iui-logo-touch-icon.png" />
<meta name="apple-touch-fullscreen" content="YES" />
<link rel="stylesheet" href="{/literal}{$smarty.const.DOC_ROOT}{literal}/templates/MobileFormat/iui/iui.css" type="text/css" />
<link rel="stylesheet" href="{/literal}{$smarty.const.DOC_ROOT}{literal}/templates/MobileFormat/iui/t/{/literal}{$color}{literal}/{/literal}{$color}{literal}-theme.css" type="text/css"/>
<script type="application/x-javascript" src="{/literal}{$smarty.const.DOC_ROOT}{literal}/templates/MobileFormat/iui/iui.js"></script>

{/literal}
{*




   
*}
{*************************}
   
   {* CSS Style file *}
   <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/MobileFormat/style/main.css" />
 <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/select2.css"  />
        <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/templates/Core/DefaultFrontend/style/fg.menu.css" />
   {* Custom META tags *}
   {if $smarty.const.ENABLE_META_TAGS}
      {if !empty($MetaKeywords)}
         <meta name="keywords" content="{$MetaKeywords|strip|escape|trim}" />
      {/if}
      {if !empty($MetaDescription)}
         <meta name="description" content="{$MetaDescription|strip|escape|trim}" />
      {/if}
      {if !empty($MetaAuthor)}
         <meta name="author" content="{$MetaAuthor|strip|escape|trim}" />
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

   {* JavaScript libraries *}
   
    {if $smarty.const.USE_CDN}
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
        {else}
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery-ui.js"></script>
     {/if}
           <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.select2.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.fg.menu.js"></script>
     {literal}
            <script type="text/javascript">
             var $ = jQuery.noConflict();
	     
	     
	     $(function() {
	      $('.listingsList > li').unwrap();
	      $('.phpld-cbox > li').unwrap();
	      
	      $('.phpld-col3 > li').unwrap();
	      
	       
	    });
	  


            </script>
     {/literal}
	
	
	
</head>
<body>





<div class="toolbar">
    <h1 id="pageTitle"></h1>
    <a id="homeButton" class="button" href="{$smarty.const.DOC_ROOT}/" target="_self">Home</a>
    <a class="button" href="#searchForm">Search</a>
</div>


<ul id="{if !empty($smarty.request.controller)}{$smarty.request.controller}{else}home{/if}" title="{$PAGE_TITLE}" selected="true"> 
{* Error and confirmation messages *}
	 {include file="views/_shared/messages.tpl"}
	 {$FLASH_MESSENGER}
	
			
	
	{$content}
	<li><a href="{$smarty.const.DOC_ROOT}/submit" target="_self"> {l}Add A Listing{/l}</a></li>
	
	 {if ($smarty.const.REQUIRE_REGISTERED_USER == 1)}
    
         {if empty($regular_user_details)}
         <li><a href="{$smarty.const.DOC_ROOT}/user/register" title="{l}Register new user{/l}">{l}Register{/l}</a></li>
         <li><a href="{$smarty.const.DOC_ROOT}/user/login" title="{l}User Login{/l}">{l}User Login{/l}</a></li>
         {else}
            <li><a href="{$smarty.const.DOC_ROOT}/user" title="{l}My Account{/l}">{l}My Account{/l}</a></li>
	       <li><a href="{$smarty.const.DOC_ROOT}/user/logout" title="{l}My Account{/l}">{l}Logout{/l}</a></li>
         {/if}
      {/if}
	
	<li>
	<div class="footer">
		<span class="copyr"><a href="http://www.phplinkdirectory.com" title="PHP Link Directory" target="_parent">PHP Link Directory - Mobile Format</a></span>
	</div>
	</li>
</ul>
<form id="searchForm" class="dialog" action="{$smarty.const.DOC_ROOT}/search" method="get" target="_self">
    <fieldset style="height:50px;">
        <h1>Search</h1>
        <a class="button leftButton" type="cancel" href="#" target="_self">Cancel</a>
        <a class="button blueButton" type="submit" onclick="submit()">Search</a>
        <div style="text-align:center;padding-top:30px;" {*style="position:relative;left:-25%;top:30px;"*}>
        <label style="color:#fff;">Search:</label>
        <input type="text" name="search" maxlength="250" value="{if !empty($search)}{$search|escape}{/if}" style="width:100px" />
		</div>
    </fieldset>
</form>
{$smarty.const.GOOGLE_ANALYTICS}
</body>
</html>
{/strip}
