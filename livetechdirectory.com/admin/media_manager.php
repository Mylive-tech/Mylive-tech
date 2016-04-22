<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 

	 

	
/**
 # ################################################################################
 # Project:   PHP Link Directory
 #
 # **********************************************************************
 # Copyright (C) 2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 #
 # This software is for use only to those who have purchased a license.
 # A license must be purchased for EACH installation of the software.
 #
 # By using the software you agree to the terms:
 #
 #    - You may not redistribute, sell or otherwise share this software
 #      in whole or in part without the consent of the the ownership
 #      of PHP Link Directory. Please contact david@david-duval.com
 #      if you need more information.
 #
 #    - You agree to retain a link back to http://www.phplinkdirectory.com/
 #      on all pages of your directory if you purchased any of our "link back" 
 #      versions of the software.
 #
 #
 # In some cases, license holders may be required to agree to changes
 # in the software license before receiving updates to the software.
 # **********************************************************************
 #
 # For questions, help, comments, discussion, etc., please join the
 # PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
 #
 # @link           http://www.phplinkdirectory.com/
 # @copyright      2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.1.0 Phoenix Release
 # ################################################################################
 */
 
    require_once 'init.php';
    
     //Pagination
     $PAGER_LPP = 10;
    // Determine current index
    $current_item = (!empty ($_REQUEST['p']) && preg_match ('`^[\d]+$`', $_REQUEST['p']) ? intval ($_REQUEST['p']) : 1);
    $current_item--;
    // Determine page
    $page         = ceil ($current_item / $PAGER_LPP);
    $page = ($page < 1 ? 1 : $page); // Check again for negative page

    // Remove rewrite page link
    //$_SERVER['REQUEST_URI'] = preg_replace("/page-(\d+)\.htm[l]?$/", "", $_SERVER['REQUEST_URI']);
    $pattern = array ("/page-(\d+)\.htm[l]?/", '`([?]|[&])(p=)+[\d]*`', '`([?]|[&])(cat_page=)+[\d]*`', '`([?]|[&])(article_page=)+[\d]*`');
    $_SERVER['REQUEST_URI'] = preg_replace ($pattern, '', request_uri());
   
    $limit = ' LIMIT '.($current_item < 1 ? '0' : $current_item).', '.$PAGER_LPP;
    
    
    //select type of Media 
    switch($_REQUEST['type'])
    {
        case 'image':
         $typeTitle = 'Image';
         $allowedExtensions = array('jpeg','jpg','gif','png');
            break;
        default:
        die();
    }

//Makes Media User Dir if it not exists
$user_id='0';
if(empty($_SESSION['phpld']['adminpanel']['id']))
    $user_id = $_SESSION['phpld']['adminpanel']['id'];
    
$media_path = substr(dirname( __file__ ), 0, strrpos(dirname( __file__ ), 'admin'));
$media_path .= "uploads/media/".$user_id;

if(!is_dir($media_path))
    mkdir($media_path,0777);

if(empty($_SESSION['phpld']['adminpanel']['is_admin']))
    $where_user = "AND USER_ID=".$db->qstr($user_id);
//Get Media List
$file_list = $db->GetAll("SELECT * FROM `{$tables['media_manager_items']['name']}` WHERE 1 ".$where_user." ORDER BY DATE_ADDED DESC ".$limit);
$count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['media_manager_items']['name']}` WHERE 1 ".$where_user);

foreach($file_list as $k=>$v)
{
    if(!empty($file_list[$k]['USER_ID'])&&!empty($file_list[$k]['FILE_NAME']))
    $file_list[$k]['FILE_PATH'] = "/".$file_list[$k]['USER_ID']."/".$file_list[$k]['FILE_NAME'];
    if($file_list[$k]['TYPE']=='image')
    {
	$image_size = getimagesize(INSTALL_PATH. "uploads/media".$file_list[$k]['FILE_PATH']);
	$file_list[$k]['IMAGE_WIDTH'] = $image_size[0] ;
	$file_list[$k]['IMAGE_HEIGHT'] = $image_size[1] ;
    }
  
}


$count          = (empty ($count) || $count < 0 ? 0 : intval ($count));
$PagerGroupings = $PAGER_LPP;
$LinksPerPage   = $PAGER_LPP;


//raluca	
if ((!empty($current_item) || $current_item == 0) && !empty($count) && ENABLE_REWRITE == 1)
{
	$lastPage = ceil ($current_item / $LinksPerPage) +1;
	$tpl->assign('lp', $lastPage);	
}
//raluca

if (!empty($current_item) && !empty($count) && ($current_item + 1 > $count))
{
   //Send HTTP header status "404/Not Found"
   httpstatus('404');

   //Build new URL (it will calculate the last valid page)
   $redirectURL  = trim ($_SERVER['REQUEST_URI']);

   //Regular redirect
   if (ENABLE_REWRITE == 1 && empty ($_REQUEST['search']))
   {
      //calculate last page possible
      $lastPage = ceil ($count / $LinksPerPage);

      if ($lastPage > 1)
      {
         //append "page-XYZ.html" except "page-1.html"
         $redirectURL .= (substr ($_SERVER['REQUEST_URI'], -1) == '/') ? '' : '/';
         $redirectURL .= ($lastPage > 1 ? 'page-' . $lastPage . '.html' : '');
      }

   }
   else
   {
      //calculate last page possible except for first page
      $lastPage = ($count % $LinksPerPage > 0) ? $count - ($count % $LinksPerPage) + 1 : $count - $LinksPerPage + 1;

      if ($lastPage > $LinksPerPage)
      {
         //append "&p=XYZ" or "?p=XYZ"
         $redirectURL .= (strpos ($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
         $redirectURL .= ($lastPage > $LinksPerPage ? 'p=' . intval ($lastPage) : '');
      }
   }
   //Redirect to last page
 http_custom_redirect($redirectURL,'',0,'',301);
}

if (empty ($_REQUEST['list'])) //Build paging options
{
   SmartyPaginate :: connect('MainPaging'); //Connect Paging
   // Build Paging
   if ($page < 2)
   {
      SmartyPaginate :: disconnect('MainPaging');
      SmartyPaginate :: reset     ('MainPaging');
   }
   SmartyPaginate :: setPrevText    ('Previous'              , 'MainPaging');
   SmartyPaginate :: setNextText    ('Next'                  , 'MainPaging');
   SmartyPaginate :: setFirstText   ('First'                 , 'MainPaging');
   SmartyPaginate :: setLastText    ('Last'                  , 'MainPaging');
   SmartyPaginate :: setTotal       ($count                  , 'MainPaging');
   SmartyPaginate :: setUrlVar      ('p'                     , 'MainPaging');
   SmartyPaginate :: setUrl         ($_SERVER['REQUEST_URI'] , 'MainPaging');
   SmartyPaginate :: setCurrentItem ($current_item + 1       , 'MainPaging');
   SmartyPaginate :: setLimit       ($LinksPerPage           , 'MainPaging');
   SmartyPaginate :: setPageLimit   ($PagerGroupings         , 'MainPaging');
   SmartyPaginate :: assign         ($tpl                    , 'MainPaging', 'MainPaging');
}

$tpl->assign('typeTitle', $typeTitle);
$tpl->assign('file_list', $file_list);
$tpl->assign('user_id', $user_id);
//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Compress output for faster loading
if (COMPRESS_OUTPUT == 1)
   $tpl->load_filter('output', 'CompressOutput');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/media_manager.tpl');
    
?>