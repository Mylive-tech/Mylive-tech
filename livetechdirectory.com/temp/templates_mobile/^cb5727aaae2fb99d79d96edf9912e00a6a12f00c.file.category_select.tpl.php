<?php /* Smarty version Smarty-3.1.12, created on 2014-07-15 11:19:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/_shared/category_select.tpl" */ ?>
<?php /*%%SmartyHeaderCode:67345860653c50e38183292-30736675%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cb5727aaae2fb99d79d96edf9912e00a6a12f00c' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/_shared/category_select.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '67345860653c50e38183292-30736675',
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
  'unifunc' => 'content_53c50e381cc2c6_49473694',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53c50e381cc2c6_49473694')) {function content_53c50e381cc2c6_49473694($_smarty_tpl) {?>

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
" /><div id="tree"></div><br/></div><?php }?><?php }} ?>