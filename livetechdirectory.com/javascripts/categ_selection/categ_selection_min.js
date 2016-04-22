
Event.observe(window,'load',init_ajax_categs,false);Event.observe(window,'load',init_ajax_categs1,false);function init_ajax_categs()
{if($('toggleCategTree'))
{Event.observe('toggleCategTree','click',toggleTree,false);}
reload_categ_title();return false;}
function init_ajax_categs1()
{if($('toggleCategTree1'))
{Event.observe('toggleCategTree1','click',toggleTree1,false);}
reload_categ_title1();return false;}
function catTitleEffect()
{if($('catTitle'))
{new Effect.Highlight('catTitle',{duration:1.5});}
return false;}
function catTitleEffect1()
{if($('catTitle1'))
{new Effect.Highlight('catTitle1',{duration:1.5});}
return false;}
function catTreeClose()
{if($('categtree'))
{new Effect.BlindUp('categtree',{duration:0.9,delay:1});}
setTimeout('destroyCatTree();',2100);return false;}
function catTreeClose1()
{if($('categtree1'))
{new Effect.BlindUp('categtree1',{duration:0.9,delay:1});}
setTimeout('destroyCatTree1();',2100);return false;}
function destroyCatTree()
{if($('categtree'))
Element.remove('categtree');}
function destroyCatTree1()
{if($('categtree1'))
Element.remove('categtree1');}
function toggleTree()
{if(!$('categtree'))
{if($('CATEGORY_ID'))
{reload_categ_tree($F('CATEGORY_ID'));}
else if($('PARENT_ID'))
{reload_categ_tree($F('PARENT_ID'));}}
else
{catTreeClose();}
return false;}
function toggleTree1()
{if(!$('categtree1'))
{if($('CATEGORY_SYMBOLIC_ID'))
{reload_categ_tree1($F('CATEGORY_SYMBOLIC_ID'));}
else if($('SYMBOLIC_ID'))
{reload_categ_tree1($F('SYMBOLIC_ID'));}}
else
{catTreeClose1();}
return false;}
function update_categ_selection(categID,parentID,subcategs)
{categID=(categID?categID:0);categID=(categID<0?0:categID);select_cat(categID);if(subcategs==1)
reload_categ_tree(categID);return false;}
function update_categ_selection1(categID,parentID,subcategs)
{categID=(categID?categID:0);categID=(categID<0?0:categID);select_cat1(categID);if(subcategs==1)
reload_categ_tree1(categID);return false;}
function reload_categ_tree(categID)
{categID=(categID?categID:0);categID=(categID<0?0:categID);if($('CATEGORY_ID')||$('PARENT_ID'))
{var url='categ-tree.php';var params='categID='+categID;var cur_url=document.location.href;if(cur_url.indexOf("submit.php")!=-1){params=params+"&submit=link";}
if(cur_url.indexOf("submit_article.php")!=-1){params=params+"&submit=article";}
var target='categtreebox';var myAjax=new Ajax.Updater(target,url,{method:'get',parameters:params});}
return false;}
function reload_categ_tree1(categID)
{categID=(categID?categID:0);categID=(categID<0?0:categID);if($('CATEGORY_SYMBOLIC_ID')||$('SYMBOLIC_ID'))
{var url='categ-tree.php';var params='categID='+categID;params=params+"&symbolic=1";var cur_url=document.location.href;if(cur_url.indexOf("submit.php")!=-1){params=params+"&submit=link";}
if(cur_url.indexOf("submit_article.php")!=-1){params=params+"&submit=article";}
var target='categtreebox1';var myAjax=new Ajax.Updater(target,url,{method:'get',parameters:params});}
return false;}
function reload_categ_title()
{if($('catTitle')&&($('CATEGORY_ID')||$('PARENT_ID')))
{var url='categ-tree.php';if($('CATEGORY_ID'))
var params='action=titleupdate&categID='+escape($F('CATEGORY_ID'));else if($('PARENT_ID'))
var params='action=titleupdate&categID='+escape($F('PARENT_ID'));var cur_url=document.location.href;if(cur_url.indexOf("submit.php")!=-1){params=params+"&submit=link";}
if(cur_url.indexOf("submit_article.php")!=-1){params=params+"&submit=article";}
var target='catTitle';var myAjax=new Ajax.Updater(target,url,{method:'get',parameters:params});}
return false;}
function reload_categ_title1()
{if($('catTitle1')&&($('CATEGORY_SYMBOLIC_ID')||$('SYMBOLIC_ID')))
{var url='categ-tree.php';if($('CATEGORY_SYMBOLIC_ID'))
var params='action=titleupdate&categID='+escape($F('CATEGORY_SYMBOLIC_ID'));else if($('SYMBOLIC_ID'))
var params='action=titleupdate&categID='+escape($F('SYMBOLIC_ID'));params=params+"&symbolic=1";var cur_url=document.location.href;if(cur_url.indexOf("submit.php")!=-1){params=params+"&submit=link";}
if(cur_url.indexOf("submit_article.php")!=-1){params=params+"&submit=article";}
var target='catTitle1';var myAjax=new Ajax.Updater(target,url,{method:'get',parameters:params});}
return false;}
function select_cat(categID)
{categID=(categID?categID:0);categID=(categID<0?0:categID);if($('CATEGORY_ID'))
{document.getElementById('CATEGORY_ID').value=categID;reload_categ_title();catTitleEffect();}
else if($('PARENT_ID'))
{document.getElementById('PARENT_ID').value=categID;reload_categ_title();catTitleEffect();}
return false;}
function select_cat1(categID)
{categID=(categID?categID:0);categID=(categID<0?0:categID);if($('CATEGORY_SYMBOLIC_ID'))
{document.getElementById('CATEGORY_SYMBOLIC_ID').value=categID;reload_categ_title1();catTitleEffect1();}
else if($('SYMBOLIC_ID'))
{document.getElementById('SYMBOLIC_ID').value=categID;reload_categ_title1();catTitleEffect1();}
return false;}