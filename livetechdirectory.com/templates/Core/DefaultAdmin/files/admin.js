/**
# ################################################################################
# Project:   PHP Link Directory: Version 3.1
#
# **********************************************************************
# Copyright (C) 2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
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
#      on all pages of your directory in you purchased the $25 version
#      of the software.
#
# License holders are entitled to upgrades to the 3.4 branch of the software
# as they are made available at ttp://www.phplinkdirectory.com/
#
# In some cases, license holders may be required to agree to changes
# in the software license before receiving updates to the software.
# **********************************************************************
#
# For questions, help, comments, discussion, etc., please join the
# PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
#
# @link           http://www.phplinkdirectory.com/
# @copyright      2004-2006 NetCreated, Inc. (http://www.netcreated.com/)
# @projectManager David DuVal <david@david-duval.com>
# @package        PHPLinkDirectory
# @version        3.1 (=3.0.7)
# ################################################################################
*/


/* Function to load on event page start */
Event.observe(window, 'load', initadmin, false);

var link_action_default_msg = '';

/* Loaded on page load */
function initadmin()
{
   //Hide all new status lists
   allStatusListNodes = document.getElementsByClassName('list-status');
   for (i = 0; i < allStatusListNodes.length; i++)
   {
      allStatusListNodes[i].className = 'hidden';
   }

   //Listen to all "change status" clicks
   allPopStatusNodes = document.getElementsByClassName('pop-status');
   for (i = 0; i < allPopStatusNodes.length; i++)
   {
      ObservePopStatus(allPopStatusNodes[i].id);
   }

   //Observe if "Check All" button is clicked
   if ($('checkallButton'))
      Event.observe('checkallButton', 'click', toggleButtons, false);

   //Handle and observe checkboxes
   toggleButtons();
   observeCheckboxes();

   //Handle and observe sitemap compression
   if ($('googleSitemapFile') && $('googleCompressFile'))
   {
      Event.observe('googleCompressFile', 'click', checkGoogleSitemapCompress, false);
   }
   if ($('yahooSitemapFile') && $('yahooCompressFile'))
   {
      Event.observe('yahooCompressFile', 'click', checkYahooSitemapCompress, false);
   }

   //Look for link info requests
   var linkInfoItems = document.getElementsByClassName('more-info');
   for (var i = linkInfoItems.length - 1; i >= 0; i--)
   {
      if (linkInfoItems[i].id != "")
      {
         observeLinkDetails(linkInfoItems[i].id);
      }
   }

   //Look for category info requests
   var categInfoItems = document.getElementsByClassName('more-info-categ');
   for (var i = categInfoItems.length - 1; i >= 0; i--)
   {
      if (categInfoItems[i].id != "")
      {
         observeCategDetails(categInfoItems[i].id);
      }
   }

   //Look for category requests
   var categoryLinkItems = document.getElementsByClassName('category-link');
   for (var i = categoryLinkItems.length - 1; i >= 0; i--)
   {
      if (categoryLinkItems[i].id != "")
      {
         observeCategoryLink(categoryLinkItems[i].id);
      }
   }
}

function observeLinkDetails(linkID)
{
   //Get only numeric ID
   var RegExp = /\d+/;
   var matchID = RegExp.exec(linkID);
   var numericID = matchID[0];

   //Ovserve click event and show tooltip
   Event.observe(linkID, 'click', function() { ajaxLinkDetailsRequest(numericID) }, false);
}

function observeCategDetails(catID)
{
   //Get only numeric ID
   var RegExp = /\d+/;
   var matchID = RegExp.exec(catID);
   var numericID = matchID[0];

   //Ovserve click event and show tooltip
   Event.observe(catID, 'click', function() { ajaxCategDetailsRequest(numericID) }, false);
}

function observeCategoryLink(catID)
{
   //Get only numeric ID
   var RegExp = /\d+/;
   var matchID = RegExp.exec(catID);
   var numericID = matchID[0];

   //Ovserve click event and show tooltip
   Event.observe(catID, 'click', function() { ajaxCategLinkRequest(numericID) }, false);
}

function ajaxLinkDetailsRequest(id)
{
   //Build URL of link details script
   var url = DOC_ROOT + '/link_details.php';

   //Build request options
   var opt = {
      //Use Get
      method: 'get',
      //Add parameters
      parameters: 'id=' + id + '&type=ajax' + '&onlycontent=1',
      //Handle successful response
      onSuccess: showLinkDetails,
      //Handle 404
      on404: AjaxError404,
      //Handle other errors
      onFailure: AjaxError
   }

   //Make request
   new Ajax.Request(url, opt);

   return false;
}

function ajaxCategDetailsRequest(id)
{
   //Build URL of link details script
   var url = DOC_ROOT + '/categ_details.php';

   //Build request options
   var opt = {
      //Use Get
      method: 'get',
      //Add parameters
      parameters: 'id=' + id + '&type=ajax' + '&onlycontent=1',
      //Handle successful response
      onSuccess: showCategDetails,
      //Handle 404
      on404: AjaxError404,
      //Handle other errors
      onFailure: AjaxError
   }

   //Make request
   new Ajax.Request(url, opt);

   return false;
}

function ajaxArticleDetailsRequest(id)
{
   //Build URL of article details script
   var url = DOC_ROOT + '/article_details.php';

   //Build request options
   var opt = {
      //Use Get
      method: 'get',
      //Add parameters
      parameters: 'id=' + id + '&type=ajax' + '&onlycontent=1',
      //Handle successful response
      onSuccess: showLinkDetails,
      //Handle 404
      on404: AjaxError404,
      //Handle other errors
      onFailure: AjaxError
    }

   //Make request
   new Ajax.Request(url, opt);

   return false;
}

function ajaxCategLinkRequest(id)
{
   //Build URL of link details script
   var url = DOC_ROOT + '/categ_link_options.php';

   //Build request options
   var opt = {
      //Use Get
      method: 'get',
      //Add parameters
      parameters: 'id=' + id + '&type=ajax' + '&onlycontent=1',
      //Handle successful response
      onSuccess: categoryLinkItems,
      //Handle 404
      on404: AjaxError404,
      //Handle other errors
      onFailure: AjaxError
   }



   //Make request
   if (id != 0) {
	   new Ajax.Request(url, opt);
   }

   return false;
}

//Handle successful response
//SHOW TOOLTIP TEXT =>> "response.responseText"
var showLinkDetails = function(response)
{
   new LITBox(response.responseText, {type: 'alert', overlay:false, height:500, width:500, draggable:true, resizable:true, opacity:0.9});
   return false;
}
var showCategDetails = function(response)
{
   new LITBox(response.responseText, {type: 'alert', overlay:false, height:500, width:600, draggable:true, resizable:true, opacity:0.9});
   return false;
}
var categoryLinkItems = function(response)
{
   new LITBox(response.responseText, {type: 'alert', overlay:false, height:300, width:250, draggable:true, resizable:true, opacity:0.9});
   return false;
}

//Handle 404 errors
var AjaxError404 = function(response)
{
   alert('Error 404: location "' + response.statusText + '" was not found.');
   return false;
}

//Handle other Ajax errors
var AjaxError = function(response)
{
   alert('Error ' + response.status + ' -- ' + response.statusText);
   return false;
}

function ObservePopStatus(id)
{
   //Get only numeric ID
   var RegExp = /\d+/;
   var matchID = RegExp.exec(id);
   var numericID = matchID[0];

   //Ovserve click event and show new status list
   Event.observe(id, 'click', function() { toggleStatusList(numericID); }, false);
}

function toggleStatusList(id)
{
   var fullID = 'list-status-' + id;

   if ($(fullID).className == 'hidden')
   {
      $(fullID).className = 'list-status';
   }
   else
   {
      $(fullID).className = 'hidden';
   }
}

/* Disable all action buttons */
function disableActionButtons()
{
   if ($('activeButton'))
      $('activeButton').disabled = 'disabled';

   if ($('inactiveButton'))
      $('inactiveButton').disabled = 'disabled';

   if ($('pendingButton'))
      $('pendingButton').disabled = 'disabled';

   if ($('featuredButton'))
      $('featuredButton').disabled = 'disabled';

   if ($('regularButton'))
      $('regularButton').disabled = 'disabled';

   if ($('removeButton'))
      $('removeButton').disabled = 'disabled';

  if ($('removeCompleteButton'))
      $('removeCompleteButton').disabled = 'disabled';

   if ($('expiredButton'))
      $('expiredButton').disabled = 'disabled';

   if ($('banIpButton'))
      $('banIpButton').disabled = 'disabled';

   if ($('banDomainButton'))
      $('banDomainButton').disabled = 'disabled';

   if ($('spamLinkButton'))
      $('spamLinkButton').disabled = 'disabled';

   if ($('changeLinkCategoryButton'))
      $('changeLinkCategoryButton').disabled = 'disabled';

   if ($('changeParentCategoryButton'))
      $('changeParentCategoryButton').disabled = 'disabled';
}

/* Enable all action buttons */
function enableActionButtons()
{
   if ($('activeButton'))
      $('activeButton').disabled = false;

   if ($('inactiveButton'))
      $('inactiveButton').disabled = false;

   if ($('pendingButton'))
      $('pendingButton').disabled = false;

   if ($('featuredButton'))
      $('featuredButton').disabled = false;

   if ($('regularButton'))
      $('regularButton').disabled = false;

   if ($('removeButton'))
      $('removeButton').disabled = false;

   if ($('removeCompleteButton'))
      $('removeCompleteButton').disabled = false;

   if ($('expiredButton'))
      $('expiredButton').disabled = false;

   if ($('banIpButton'))
      $('banIpButton').disabled = false;

   if ($('banDomainButton'))
      $('banDomainButton').disabled = false;

   if ($('spamLinkButton'))
      $('spamLinkButton').disabled = false;

   if ($('changeLinkCategoryButton'))
      $('changeLinkCategoryButton').disabled = false;

   if ($('changeParentCategoryButton'))
      $('changeParentCategoryButton').disabled = false;
}

/* Add an event observer for checkboxes */
function observeCheckboxes()
{
   var fName = 'multiselect_list';
   if (document.getElementById('multiselect_list'))
   {
      /* Did you know that counting backwards in a loop you gain extra speed?? */
      for (var i = document.forms[fName].elements.length - 1; i >= 0; i--)
      {
         if ('checkbox' == document.forms[fName].elements[i].type)
         {
            Event.observe(document.forms[fName].elements[i], 'click', toggleButtons, true);
         }
      }
   }
}

/* Disable/Enable action buttons depending on ticked checkboxes */
function toggleButtons()
{
   var fName = 'multiselect_list';

   if (document.getElementById('multiselect_list'))
   {
      var countChecked = 0;

      for (var i = document.forms[fName].elements.length - 1; i >= 0; i--)
      {
         if ('checkbox' == document.forms[fName].elements[i].type)
         {
            if (document.forms[fName].elements[i].checked)
            {
               countChecked++;
            }
         }
      }

      if ($('multiselect_action_count'))
         $('multiselect_action_count').innerHTML = countChecked;

      /* Toggle action buttons if checked or not */
      if (countChecked < 1)
      	//disableActionButtons();
      	enableActionButtons
      else
         enableActionButtons();
   }
}

/* Build confirmation box for selected links status: active */
function selected_change_categ_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change status of selected links to 'active'?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('changeLinkCategoryButton'))
      $('submitAction').value = $('changeLinkCategoryButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected links status: active */
function selected_links_active_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change status of selected links to 'active'?";
   answer = confirm (message);

   if (!answer)
      return false;
*/

   if ($('submitAction') && $('activeButton'))
      $('submitAction').value = $('activeButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected links status: pending */
function selected_links_pending_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change status of selected links to 'pending'?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('pendingButton'))
      $('submitAction').value = $('pendingButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected links status: inactive */
function selected_links_inactive_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change status of selected links to 'inactive'?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('inactiveButton'))
      $('submitAction').value = $('inactiveButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected links type: featured */
function selected_links_featured_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change type of selected regular links to featured?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('featuredButton'))
      $('submitAction').value = $('featuredButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected links type: regular */
function selected_links_regular_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change type of selected featured links to regular?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('regularButton'))
      $('submitAction').value = $('regularButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected links removal */
function selected_links_remove_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove selected links?\n Note: links can not be restored after removal!";
   answer = confirm (message);

   if (answer == 0)
      return false;

   if ($('submitAction') && $('removeButton'))
      $('submitAction').value = $('removeButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for link removal */
function link_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this link?\n Note: links can not be restored after removal!";
   var answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for selected categories to change parent category */
function selected_change_parentcateg_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change parent category?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('changeParentCategoryButton'))
      $('submitAction').value = $('changeParentCategoryButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected categories status: active */
function selected_categ_active_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change status of selected categories to 'active'?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('activeButton'))
      $('submitAction').value = $('activeButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected categories status: pending */
function selected_categ_pending_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change status of selected categories to 'pending'?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('pendingButton'))
      $('submitAction').value = $('pendingButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for selected categories status: inactive */
function selected_categ_inactive_confirm(msg)
{
/*
   var message = msg.length > 3 ? msg : "Are you sure you want to change status of selected categories to 'inactive'?";
   answer = confirm (message);

   if (!answer)
      return false;
*/
   if ($('submitAction') && $('inactiveButton'))
      $('submitAction').value = $('inactiveButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for category removal */
function selected_categ_remove_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove selected categories and all their subcategories?\n Note: categories can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   if ($('submitAction') && $('removeButton'))
      $('submitAction').value = $('removeButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for complete category removal */
function selected_categ_complete_remove_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove selected categories, all their subcategories and links?\n Note: categories, subcategories and links can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   if ($('submitAction') && $('removeCompleteButton'))
      $('submitAction').value = $('removeCompleteButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for link owner notification of expired links */
function selected_links_expired_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to notify owner(s) of expired reciprocal link link pages?";
   answer = confirm (message);

   if (!answer)
      return false;

   if ($('submitAction') && $('expiredButton'))
      $('submitAction').value = $('expiredButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for category removal */
function categ_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this category and all it's subcategories?\n Note: categories can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for user account removal */
function user_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this user account?\n Note: user accounts can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for payment listing removal */
function payment_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this payment?\n Note: payment listings can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for email template removal */
function email_tpl_rm_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to remove this email template?\n Note: email templates can not be restored after removal!";
   answer = confirm (message);

   if (!answer)
      return false;

   return true;
}

/* Build confirmation box for link submitter IP banning */
function selected_banip_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to ban selected IPs?";
   answer = confirm (message);

   if (!answer)
      return false;

   if ($('submitAction') && $('banIpButton'))
      $('submitAction').value = $('banIpButton').name;

   document.multiselect_list.submit();

   return true;
}

/* Build confirmation box for link domain banning */
function selected_bandomain_confirm(msg)
{
   var message = msg.length > 3 ? msg : "Are you sure you want to ban selected domains?";
   answer = confirm (message);

   if (!answer)
      return false;

   if ($('submitAction') && $('banDomainButton'))
      $('submitAction').value = $('banDomainButton').name;

   document.multiselect_list.submit();

   return true;
}

function checkGoogleSitemapCompress()
{
   var googleSitemapFile = $('googleSitemapFile').value;
   if($('googleCompressFile').checked)
   {
      if (googleSitemapFile.substring(googleSitemapFile.length - 3) != ".gz")
      {
         $('googleSitemapFile').value = googleSitemapFile + ".gz";
      }
   }
   else
   {
      if (googleSitemapFile.substring(googleSitemapFile.length - 3) == ".gz") {
         var end = googleSitemapFile.length - 3;
         $('googleSitemapFile').value = googleSitemapFile.substring(0, end);
      }
   }
}

function checkYahooSitemapCompress()
{
   var yahooSitemapFile = $('yahooSitemapFile').value;
   if($('yahooCompressFile').checked)
   {
      if (yahooSitemapFile.substring(yahooSitemapFile.length - 3) != ".gz")
      {
         $('yahooSitemapFile').value = yahooSitemapFile + ".gz";
      }
   }
   else
   {
      if (yahooSitemapFile.substring(yahooSitemapFile.length - 3) == ".gz") {
         var end = yahooSitemapFile.length - 3;
         $('yahooSitemapFile').value = yahooSitemapFile.substring(0, end);
      }
   }
}