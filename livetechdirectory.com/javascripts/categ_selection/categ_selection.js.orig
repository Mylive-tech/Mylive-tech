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
<<<<<<< HEAD

Event.observe(window, 'load', init_ajax_categs, false);
Event.observe(window, 'load', init_ajax_categs1, false);
=======
/* Anton */

jQuery(document).ready(function(){
    init_ajax_categs();
    init_ajax_categs1();
	
});
/*Event.observe(window, 'load', init_ajax_categs, false);
Event.observe(window, 'load', init_ajax_categs1, false);*/
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab

/* Loaded on page load */
function init_ajax_categs()
{
<<<<<<< HEAD
   if ($('toggleCategTree'))
   {
      Event.observe('toggleCategTree', 'click', toggleTree, false);
=======
   if (jQuery('#toggleCategTree').length)
   {
		jQuery('#toggleCategTree').bind('click', function(){
			toggleTree();
		});
      //Event.observe('toggleCategTree', 'click', toggleTree, false);
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   reload_categ_title();

   return false;
}

function init_ajax_categs1()
{
<<<<<<< HEAD
   if ($('toggleCategTree1'))
   {
      Event.observe('toggleCategTree1', 'click', toggleTree1, false);
=======
   if (jQuery('#toggleCategTree1').length)
   {
		jQuery('#toggleCategTree1').bind('click', function(){
			toggleTree1();
		});   
      //Event.observe('toggleCategTree1', 'click', toggleTree1, false);
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   reload_categ_title1();

   return false;
}

/* Effect applied to category title */
function catTitleEffect()
{
<<<<<<< HEAD
   if ($('catTitle'))
   {
      new Effect.Highlight('catTitle',{duration:1.5});
=======
   if (jQuery('#catTitle').length)
   {
	jQuery('#catTitle').effect("highlight", {}, 1500);
    //  new Effect.Highlight('catTitle',{duration:1.5});
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   return false;
}

function catTitleEffect1()
{
<<<<<<< HEAD
   if ($('catTitle1'))
   {
      new Effect.Highlight('catTitle1',{duration:1.5});
=======
   if (jQuery('#catTitle1').length)
   {
		jQuery('#catTitle1').effect("highlight", {}, 1500);
      //new Effect.Highlight('catTitle1',{duration:1.5});
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   return false;
}

/* Close category tree */
function catTreeClose()
{
<<<<<<< HEAD
   if ($('categtree'))
   {
      new Effect.BlindUp('categtree',{duration:0.9, delay: 1});
=======
   if (jQuery('#categtree').length>0)
   {
		jQuery('#categtree').hide("blind", { direction: "vertical" }, 1000);
		//new Effect.BlindUp('categtree',{duration:0.9, delay: 1});
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   /* Remove category tree element. Suggestion: leave this code intact */
   setTimeout('destroyCatTree();', 2100);

   return false;
}

function catTreeClose1()
{
<<<<<<< HEAD
   if ($('categtree1'))
   {
      new Effect.BlindUp('categtree1',{duration:0.9, delay: 1});
=======
   if (jQuery('#categtree1').length>0)
   {
		jQuery('#categtree1').hide("blind", { direction: "vertical" }, 1000);
      //new Effect.BlindUp('categtree1',{duration:0.9, delay: 1});
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   /* Remove category tree element. Suggestion: leave this code intact */
   setTimeout('destroyCatTree1();', 2100);

   return false;
}

/* Destroy category tree element. Useful to clean generated code and prevent overwriting of elements. */
function destroyCatTree()
{
<<<<<<< HEAD
   if ($('categtree'))
      Element.remove('categtree');
=======
   if (jQuery('#categtree').length)
		jQuery('#categtree').remove();
      //Element.remove('categtree');
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
}

function destroyCatTree1()
{
<<<<<<< HEAD
   if ($('categtree1'))
      Element.remove('categtree1');
=======
   if (jQuery('#categtree1').length)
		jQuery('#categtree1').remove();
      //Element.remove('categtree1');
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
}

/* Apply different functions to category tree */
function toggleTree()
{
<<<<<<< HEAD
   if (!$('categtree'))
   {
      if ($('CATEGORY_ID'))
      {
         reload_categ_tree($F('CATEGORY_ID'));
      }
      else if ($('PARENT_ID'))
      {
         reload_categ_tree($F('PARENT_ID'));
=======

   if (jQuery('#categtree').length==0)
   {
      if (jQuery('#CATEGORY_ID'))
      {
         reload_categ_tree(jQuery('#CATEGORY_ID').val());
      }
      else if (jQuery('#PARENT_ID'))
      {
         reload_categ_tree(jQuery('#PARENT_ID').val());
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
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
<<<<<<< HEAD
   if (!$('categtree1'))
   {
      if ($('CATEGORY_SYMBOLIC_ID'))
      {
         reload_categ_tree1($F('CATEGORY_SYMBOLIC_ID'));
      }
      else if ($('SYMBOLIC_ID'))
      {
         reload_categ_tree1($F('SYMBOLIC_ID'));
=======
   if (jQuery('#categtree1').length ==0)
   {
      if (jQuery('#CATEGORY_SYMBOLIC_ID'))
      {
         reload_categ_tree1(jQuery('#CATEGORY_SYMBOLIC_ID').val());
      }
      else if (jQuery('#SYMBOLIC_ID').length)
      {
         reload_categ_tree1(jQuery('#SYMBOLIC_ID').val());
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
      }
   }
   else
   {
      catTreeClose1();
   }

   return false;
}

/* Update the category tree depending on provided information */
<<<<<<< HEAD
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
=======

>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab

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

<<<<<<< HEAD
   if ($('CATEGORY_ID') || $('PARENT_ID'))
=======
   if (jQuery('#CATEGORY_ID').length || jQuery('#PARENT_ID').length)
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
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
<<<<<<< HEAD
      var myAjax = new Ajax.Updater(target, url, { method: 'get', parameters: params});
=======
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
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   return false;
}

function reload_categ_tree1(categID)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

<<<<<<< HEAD
   if ($('CATEGORY_SYMBOLIC_ID') || $('SYMBOLIC_ID'))
=======
   if (jQuery('#CATEGORY_SYMBOLIC_ID').length || jQuery('#SYMBOLIC_ID').length)
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
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
<<<<<<< HEAD
      var myAjax = new Ajax.Updater(target, url, { method: 'get', parameters: params});
=======
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
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }
	   

   return false;
}

/* Reload category title by available category ID (found automatically) */
function reload_categ_title()
{
<<<<<<< HEAD
   if ($('catTitle') && ($('CATEGORY_ID') || $('PARENT_ID')))
   {
      var url = 'categ-tree.php';

      if ($('CATEGORY_ID'))
         var params = 'action=titleupdate&categID=' + escape ($F('CATEGORY_ID'));
      else if ($('PARENT_ID'))
         var params = 'action=titleupdate&categID=' + escape ($F('PARENT_ID'));
=======
   if (jQuery('#catTitle').length && (jQuery('#CATEGORY_ID').length || jQuery('#PARENT_ID').length))
   {
      var url = 'categ-tree.php';

      if (jQuery('#CATEGORY_ID').length)
         var params = 'action=titleupdate&categID=' + escape (jQuery('#CATEGORY_ID').val());
      else if (jQuery('#PARENT_ID'))
         var params = 'action=titleupdate&categID=' + escape (jQuery('#PARENT_ID').val());
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab

      var cur_url = document.location.href;
      if ( cur_url.indexOf("submit.php") != -1) {
    	  params = params + "&submit=link";
      }
      if ( cur_url.indexOf("submit_article.php") != -1) {
    	  params = params + "&submit=article";
      }
      
      var target = 'catTitle';
<<<<<<< HEAD
      var myAjax = new Ajax.Updater(target, url, { method: 'get', parameters: params});
=======
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
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   return false;
}

function reload_categ_title1()
{
<<<<<<< HEAD
   if ($('catTitle1') && ($('CATEGORY_SYMBOLIC_ID') || $('SYMBOLIC_ID')))
=======
   if (jQuery('#catTitle1').length && (jQuery('#CATEGORY_SYMBOLIC_ID').length || jQuery('#SYMBOLIC_ID').length))
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   {
      var url = 'categ-tree.php';

      
      
<<<<<<< HEAD
      if ($('CATEGORY_SYMBOLIC_ID'))
         var params = 'action=titleupdate&categID=' + escape ($F('CATEGORY_SYMBOLIC_ID'));
      else if ($('SYMBOLIC_ID'))
         var params = 'action=titleupdate&categID=' + escape ($F('SYMBOLIC_ID'));
=======
      if (jQuery('#CATEGORY_SYMBOLIC_ID').length)
         var params = 'action=titleupdate&categID=' + escape (jQuery('#CATEGORY_SYMBOLIC_ID').val());
      else if (jQuery('#SYMBOLIC_ID'))
         var params = 'action=titleupdate&categID=' + escape (jQuery('#SYMBOLIC_ID').val());
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab

      params = params + "&symbolic=1";
      
      var cur_url = document.location.href;
      if ( cur_url.indexOf("submit.php") != -1) {
    	  params = params + "&submit=link";
      }
      if ( cur_url.indexOf("submit_article.php") != -1) {
    	  params = params + "&submit=article";
      }
      
      var target = 'catTitle1';
<<<<<<< HEAD
      var myAjax = new Ajax.Updater(target, url, { method: 'get', parameters: params});
=======
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
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
   }

   return false;
}

<<<<<<< HEAD
=======

>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
/* Make category selection */
function select_cat(categID)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

<<<<<<< HEAD
   if ($('CATEGORY_ID'))
   {
      document.getElementById('CATEGORY_ID').value = categID;
      reload_categ_title();
      catTitleEffect();
   }
   else if ($('PARENT_ID'))
   {
      document.getElementById('PARENT_ID').value = categID;
=======
   if (jQuery('#CATEGORY_ID').length)
   {
      jQuery('#CATEGORY_ID').attr('value', categID);
      reload_categ_title();
      catTitleEffect();
   }
   else if (jQuery('#PARENT_ID').length)
   {
      jQuery('#PARENT_ID').attr('value', categID);
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
      reload_categ_title();
      catTitleEffect();
   }

   return false;
}

<<<<<<< HEAD
=======
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

>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
function select_cat1(categID)
{
   /* Validate category ID */
   categID = (categID ? categID : 0);
   categID = (categID < 0 ? 0 : categID);

<<<<<<< HEAD
   if ($('CATEGORY_SYMBOLIC_ID'))
   {
      document.getElementById('CATEGORY_SYMBOLIC_ID').value = categID;
      reload_categ_title1();
      catTitleEffect1();
   }
   else if ($('SYMBOLIC_ID'))
   {
      document.getElementById('SYMBOLIC_ID').value = categID;
=======
   if (jQuery('#CATEGORY_SYMBOLIC_ID').length)
   {
      jQuery('#CATEGORY_SYMBOLIC_ID').attr('value', categID);
      reload_categ_title1();
      catTitleEffect1();
   }
   else if (jQuery('#SYMBOLIC_ID').length)
   {
      jQuery('#SYMBOLIC_ID').attr('value', categID);
>>>>>>> 29ab54b49e3fe62469838f96988edb89e86620ab
      reload_categ_title1();
      catTitleEffect1();
   }

   return false;
}