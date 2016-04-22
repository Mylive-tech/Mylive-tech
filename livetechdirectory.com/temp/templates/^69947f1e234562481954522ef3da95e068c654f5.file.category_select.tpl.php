<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:57:46
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/category_select.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1276225913535a940ae3c1a4-27572424%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '69947f1e234562481954522ef3da95e068c654f5' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/category_select.tpl',
      1 => 1390973688,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1276225913535a940ae3c1a4-27572424',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'CategoryTitle' => 0,
    'CATEGORY_ID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a940ae81090_46605435',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a940ae81090_46605435')) {function content_535a940ae81090_46605435($_smarty_tpl) {?>

    <script src="<?php echo @SITE_URL;?>
templates/Core/DefaultFrontend/style/cat_menu_tree/jquery-ui.custom.js" type="text/javascript"></script>
    <script src="<?php echo @SITE_URL;?>
templates/Core/DefaultFrontend/style/cat_menu_tree/jquery.cookie.js" type="text/javascript"></script>

    <link href="<?php echo @SITE_URL;?>
templates/Core/DefaultFrontend/style/cat_menu_tree/ui.dynatree.css" rel="stylesheet" type="text/css" id="skinSheet">
    <script src="<?php echo @SITE_URL;?>
templates/Core/DefaultFrontend/style/cat_menu_tree/jquery.dynatree.js" type="text/javascript"></script>

    <!-- (Irrelevant source removed.) -->

    <script type="text/javascript">
    $(function(){
      $("#tree").dynatree({
          initAjax: {
              url: "./categ_tree_ajax.php",
              data: { action: "categtree" }
          },
          onActivate: function(node) {
            var href = node.data.key;
            $("#echoActive").text(node.data.title);
            $("#CATEGORY_ID").val(href);
          },
          onLazyRead: function(node){
              node.appendAjax({
                 url: "./categ_tree_ajax.php",
                data: {key: node.data.key,
                       action: "categtree"
                         }
              });
          },
          generateIds: true,
          autoFocus: false
      });
    });
    </script>


<?php if (@CAT_SELECTION_METHOD==1){?><?php if (!empty($_smarty_tpl->tpl_vars['CategoryTitle']->value)){?><?php echo $_smarty_tpl->tpl_vars['CategoryTitle']->value;?>
<?php }else{ ?>Please select a category!<?php }?><input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID" value="<?php echo $_smarty_tpl->tpl_vars['CATEGORY_ID']->value;?>
" /><?php }else{ ?><div class="phpld-fbox-select" style="position: relative"><input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID1" value="<?php echo $_smarty_tpl->tpl_vars['CATEGORY_ID']->value;?>
" /><a tabindex="0" href="#CATEGORIES" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="hierarchy" style="font-size: 20px; height: 22px; margin-bottom: 5px; width: 70%; margin-top:1px;" ><span class="ui-icon ui-icon-triangle-1-s"></span>Select Category</a><span id="menuLog" style="height:18px; float:left; width:76.5%; margin-top:0px; margin-right: 37px; border:1px solid #cccccc; border-radius:4px; padding:10px; background-color:#ffffff;"><span id="echoActive"></span></span><div id="tree"></div></div><?php }?><?php }} ?>