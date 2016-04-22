<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:48:08
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/rte.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1499522785535abbf8960fd0-43695960%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '820ccb5a3cc0fd3e0965f962094973bd6381be2a' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/rte.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1499522785535abbf8960fd0-43695960',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'useRTE' => 0,
    'allready_mce' => 0,
    'NAME' => 0,
    'VALUE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535abbf8a07be3_78663338',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abbf8a07be3_78663338')) {function content_535abbf8a07be3_78663338($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['useRTE']->value)&&$_smarty_tpl->tpl_vars['useRTE']->value=='TINYMCE'){?>
  <?php if (empty($_smarty_tpl->tpl_vars['allready_mce']->value)){?>
    <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/tiny_mce/tiny_mce.js"></script>
   <?php if (isset($_smarty_tpl->tpl_vars["allready_mce"])) {$_smarty_tpl->tpl_vars["allready_mce"] = clone $_smarty_tpl->tpl_vars["allready_mce"];
$_smarty_tpl->tpl_vars["allready_mce"]->value = "1"; $_smarty_tpl->tpl_vars["allready_mce"]->nocache = null; $_smarty_tpl->tpl_vars["allready_mce"]->scope = 3;
} else $_smarty_tpl->tpl_vars["allready_mce"] = new Smarty_variable("1", null, 3);
$_ptr = $_smarty_tpl->parent; while ($_ptr != null) {$_ptr->tpl_vars["allready_mce"] = clone $_smarty_tpl->tpl_vars["allready_mce"]; $_ptr = $_ptr->parent; }
Smarty::$global_tpl_vars["allready_mce"] = clone $_smarty_tpl->tpl_vars["allready_mce"];?>
  <?php }?>

    
        <script type="text/javascript">
                tinyMCE.init({
                        // General options
                        mode : "exact",
                        elements: "<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
",
                        theme : "advanced",
                        //plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
                        paste_text_sticky: true,
                        paste_text_sticky_default: true,
                        convert_urls : false,
                        // Theme options
                        theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,sub,sup",
                        theme_advanced_buttons2 : false, //"cut,copy,paste,pastetext,|,undo,redo",
                        theme_advanced_buttons3 : false, //"hr,removeformat,|,charmap,emotions,iespell,advhr,|,fullscreen",
                        theme_advanced_buttons4 : false, //"insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
                        theme_advanced_toolbar_location : "top",
                        theme_advanced_toolbar_align : "left",
                        theme_advanced_statusbar_location : "bottom",
                        theme_advanced_resizing : true,
	
                        // Example content CSS (should be your site CSS)
                        content_css : "css/content.css"
                });
        </script>
    
    <textarea id="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
"  rows="15" cols="80" style="width: 100%"><?php echo $_smarty_tpl->tpl_vars['VALUE']->value;?>
</textarea>
<?php }elseif($_smarty_tpl->tpl_vars['useRTE']->value=='CKE'){?>
    <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/ckeditor/ckeditor.js"></script>

    <textarea  id="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" rows="20" cols="80" style="width: 100%"><?php echo $_smarty_tpl->tpl_vars['VALUE']->value;?>
</textarea>
    
        <script type="text/javascript">
					CKEDITOR.replace( '<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
' );
        </script>
    
<?php }elseif($_smarty_tpl->tpl_vars['useRTE']->value=='YRTE'){?>
    <!-- Skin CSS file -->
    <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/javascripts/yui/css/skin.css">
    <!-- Utility Dependencies -->
    <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/yui/yahoo-dom-event.js"></script>
    <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/yui/element-min.js"></script>
    <!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
    <script src="<?php echo @DOC_ROOT;?>
/javascripts/yui/container_core-min.js"></script>
    <script src="<?php echo @DOC_ROOT;?>
/javascripts/yui/menu-min.js"></script>
    <script src="<?php echo @DOC_ROOT;?>
/javascripts/yui/button-min.js"></script>
    <!-- Source file for Rich Text Editor-->
    <script src="<?php echo @DOC_ROOT;?>
/javascripts/yui/editor-min.js"></script>

    <div class="yui-skin-sam">
        <textarea  id="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" rows="20" cols="80" style="width: 100%"><?php echo $_smarty_tpl->tpl_vars['VALUE']->value;?>

        </textarea>
    </div>
    
        <script>

                            (function() {
                            var Dom = YAHOO.util.Dom,
                            Event = YAHOO.util.Event;

                            var myConfig = {
                                            height: '100px',
                                            width: '320px',
                                            dompath: true,
                                            focusAtStart: true,
                 handleSubmit: true
                            };

                            YAHOO.log('Create the Editor..', 'info', 'example');
                            var myEditor = new YAHOO.widget.SimpleEditor('<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
', myConfig);
                            myEditor._defaultToolbar.buttonType = 'advanced';
                            myEditor.render();

                            })();
        </script>
    
<?php }else{ ?>
    <div class="phpld-fbox-text">
        <textarea name="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" rows="6" cols="50" class="text" ><?php echo $_smarty_tpl->tpl_vars['VALUE']->value;?>
</textarea>
    </div>
<?php }?><?php }} ?>