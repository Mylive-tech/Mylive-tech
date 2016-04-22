/*
#########################################################################################################
# Project:     PHP Link Directory - Link exchange directory @ http://www.phplinkdirectory.com/
# Module:      [AJAX Category Selection] @ http://www.frozenminds.com/phpld-ajax-categories.html
# Author:      Constantin Bejenaru aKa Boby @ http://www.frozenminds.com/
# Language:    AJAX (Based on Prototype and Scriptaculous libraries)
# License:     MIT (Copyright (c) 2006 Constantin Bejenaru - http://www.frozenminds.com)
# Version:     1.1
# Notice:      Please maintain this section
#########################################################################################################
*/

/* Function to load on event page start */


jQuery(document).ready(function(){
    init_ajax_categs();
    init_ajax_categs1();
	
});
/*Event.observe(window, 'load', init_ajax_categs, false);
Event.observe(window, 'load', init_ajax_categs1, false);*/

/* Loaded on page load */
function init_ajax_categs()
{
   if (jQuery('#toggleCategTree').length)
   {
		jQuery('#toggleCategTree').bind('click', function(){
			toggleTree();
		});
      //Event.observe('toggleCategTree', 'click', toggleTree, false);
   }

   reload_categ_title();

   return false;
}

function init_ajax_categs1()
{
   if (jQuery('#toggleCategTree1').length)
   {
		jQuery('#toggleCategTree1').bind('click', function(){
			toggleTree1();
		});   
      //Event.observe('toggleCategTree1', 'click', toggleTree1, false);
   }

   reload_categ_title1();

   return false;
}

/* Effect applied to category title */
function catTitleEffect()
{
   if (jQuery('#catTitle').length)
   {
	jQuery('#catTitle').effect("highlight", {}, 1500);
    //  new Effect.Highlight('catTitle',{duration:1.5});
   }

   return false;
}

function catTitleEffect1()
{
   if (jQuery('#catTitle1').length)
   {
		jQuery('#catTitle1').effect("highlight", {}, 1500);
      //new Effect.Highlight('catTitle1',{duration:1.5});
   }

   return false;
}

/* Close category tree */
function catTreeClose()
{
   if (jQuery('#categtree').length>0)
   {
		jQuery('#categtree').hide("blind", { direction: "vertical" }, 1000);
		//new Effect.BlindUp('categtree',{duration:0.9, delay: 1});
   }

   /* Remove category tree element. Suggestion: leave this code intact */
   setTimeout('destroyCatTree();', 2100);

   return false;
}

function catTreeClose1()
{
   if (jQuery('#categtree1').length>0)
   {
		jQuery('#categtree1').hide("blind", { direction: "vertical" }, 1000);
      //new Effect.BlindUp('categtree1',{duration:0.9, delay: 1});
   }

   /* Remove category tree element. Suggestion: leave this code intact */
   setTimeout('destroyCatTree1();', 2100);

   return false;
}

/* Destroy category tree element. Useful to clean generated code and prevent overwriting of elements. */
function destroyCatTree()
{
   if (jQuery('#categtree').length)
		jQuery('#categtree').remove();
      //Element.remove('categtree');
}

function destroyCatTree1()
{
   if (jQuery('#categtree1').length)
		jQuery('#categtree1').remove();
      //Element.remove('categtree1');
}

/* Apply different functions to category tree */
function toggleTree()
{

   if (jQuery('#categtree').length==0)
   {
      if (jQuery('#CATEGORY_ID'))
      {
         reload_categ_tree(jQuery('#CATEGORY_ID').val());
      }
      else if (jQuery('#PARENT_ID'))
      {
         reload_categ_tree(jQuery('#PARENT_ID').val());
      }
   }
   else
   {
      catTreeClose();
   }

   return false;
}


function toggleTree1()
{
   if (jQuery('#categtree1').length ==0)
   {
      if (jQuery('#CATEGORY_SYMBOLIC_ID'))
      {
         reload_categ_tree1(jQuery('#CATEGORY_SYMBOLIC_ID').val());
      }
      else if (jQuery('#SYMBOLIC_ID').length)
      {
         reload_categ_tree1(jQuery('#SYMBOLIC_ID').val());
      }
   }
   else
   {
      catTreeClose1();
   }

   return false;
}

/* Update the category tree depending on provided information */


function update_categ_selection1(categID, parentID, subcategs)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

   select_cat1(categID);

   if (subcategs == 1)
      reload_categ_tree1(categID);

   return false;
}

/* Reload the category tree by provided category ID */
function reload_categ_tree(categID)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

   if (jQuery('#CATEGORY_ID').length || jQuery('#PARENT_ID').length)
   {
      var url = 'categ-tree.php';
      var params = 'categID=' + categID;
      
      var cur_url = document.location.href;
      if ( cur_url.indexOf("submit.php") != -1) {
    	  params = params + "&submit=link";
      }
      if ( cur_url.indexOf("submit_article.php") != -1) {
    	  params = params + "&submit=article";
      }
      
      var target = 'categtreebox';
      //var myAjax = new Ajax.Updater(target, url, {method: 'get', parameters: params});
	  jQuery.ajax({
			method: 'get',
			url: url,
			data: params,
			dataType: 'html',
			success: function(data) {
				jQuery("#"+target).html(data);
			}
	  });
   }

   return false;
}

function reload_categ_tree1(categID)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

   if (jQuery('#CATEGORY_SYMBOLIC_ID').length || jQuery('#SYMBOLIC_ID').length)
   {
      var url = 'categ-tree.php';
      var params = 'categID=' + categID;
      
      params = params+"&symbolic=1";
      
      var cur_url = document.location.href;
      if ( cur_url.indexOf("submit.php") != -1) {
    	  params = params + "&submit=link";
      }
      if ( cur_url.indexOf("submit_article.php") != -1) {
    	  params = params + "&submit=article";
      }
      
      var target = 'categtreebox1';
     // var myAjax = new Ajax.Updater(target, url, {method: 'get', parameters: params});
	  jQuery.ajax({
			method: 'get',
			url: url,
			data: params,
			dataType: 'html',
			success: function(data) {
				jQuery("#"+target).html(data);
			}
	  });	  
   }
	   

   return false;
}

/* Reload category title by available category ID (found automatically) */
function reload_categ_title()
{
   if (jQuery('#catTitle').length && (jQuery('#CATEGORY_ID').length || jQuery('#PARENT_ID').length))
   {
      var url = 'categ-tree.php';

      if (jQuery('#CATEGORY_ID').length)
         var params = 'action=titleupdate&categID=' + escape (jQuery('#CATEGORY_ID').val());
      else if (jQuery('#PARENT_ID'))
         var params = 'action=titleupdate&categID=' + escape (jQuery('#PARENT_ID').val());

      var cur_url = document.location.href;
      if ( cur_url.indexOf("submit.php") != -1) {
    	  params = params + "&submit=link";
      }
      if ( cur_url.indexOf("submit_article.php") != -1) {
    	  params = params + "&submit=article";
      }
      
      var target = 'catTitle';
      //var myAjax = new Ajax.Updater(target, url, {method: 'get', parameters: params});
	  jQuery.ajax({
			method: 'get',
			url: url,
			data: params,
			dataType: 'html',
			success: function(data) {
				jQuery("#"+target).html(data);
			}
	  });	  
   }

   return false;
}

function reload_categ_title1()
{
   if (jQuery('#catTitle1').length && (jQuery('#CATEGORY_SYMBOLIC_ID').length || jQuery('#SYMBOLIC_ID').length))
   {
      var url = 'categ-tree.php';

      
      
      if (jQuery('#CATEGORY_SYMBOLIC_ID').length)
         var params = 'action=titleupdate&categID=' + escape (jQuery('#CATEGORY_SYMBOLIC_ID').val());
      else if (jQuery('#SYMBOLIC_ID'))
         var params = 'action=titleupdate&categID=' + escape (jQuery('#SYMBOLIC_ID').val());

      params = params + "&symbolic=1";
      
      var cur_url = document.location.href;
      if ( cur_url.indexOf("submit.php") != -1) {
    	  params = params + "&submit=link";
      }
      if ( cur_url.indexOf("submit_article.php") != -1) {
    	  params = params + "&submit=article";
      }
      
      var target = 'catTitle1';
      //var myAjax = new Ajax.Updater(target, url, {method: 'get', parameters: params});
	  jQuery.ajax({
			method: 'get',
			url: url,
			data: params,
			dataType: 'html',
			success: function(data) {
				jQuery("#"+target).html(data);
			}
	  });	  
   }

   return false;
}

/* Make category selection */
/* Make category selection */
function select_cat(categID)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

   if (jQuery('#CATEGORY_ID').length)
   {
      jQuery('#CATEGORY_ID').attr('value', categID);
      reload_categ_title();
      catTitleEffect();
   }
   else if (jQuery('#PARENT_ID').length)
   {
      jQuery('#PARENT_ID').attr('value', categID);
      reload_categ_title();
      catTitleEffect();
   }

   return false;
}

function update_categ_selection(categID, parentID, subcategs)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

   select_cat(categID);

   if (subcategs == 1)
      reload_categ_tree(categID);

   return false;
}

function select_cat1(categID)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

   if (jQuery('#CATEGORY_SYMBOLIC_ID').length)
   {
      jQuery('#CATEGORY_SYMBOLIC_ID').attr('value', categID);
      reload_categ_title1();
      catTitleEffect1();
   }
   else if (jQuery('#SYMBOLIC_ID').length)
   {
      jQuery('#SYMBOLIC_ID').attr('value', categID);
      reload_categ_title1();
      catTitleEffect1();
   }

   return false;
}