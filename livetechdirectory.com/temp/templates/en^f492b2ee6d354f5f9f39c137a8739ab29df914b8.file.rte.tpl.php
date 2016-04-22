<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:48
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/rte.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1631210511535a93d068d9e6-09811852%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f492b2ee6d354f5f9f39c137a8739ab29df914b8' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/rte.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1631210511535a93d068d9e6-09811852',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'useRTE' => 0,
    'inline_widgets' => 0,
    'ad_unit' => 0,
    'NAME' => 0,
    'VALUE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a93d07411a5_00188923',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93d07411a5_00188923')) {function content_535a93d07411a5_00188923($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['useRTE']->value)&&$_smarty_tpl->tpl_vars['useRTE']->value=='TINYMCE'){?>
<div id="editorcontainer">
<script type="text/javascript">
// inline_widget Units List plugin
tinymce.create('tinymce.plugins.ExamplePlugin', {
    createControl: function(n, cm) {
        switch (n) {
            case 'mylistbox':
                var mlb = cm.createListBox('mylistbox', {
                     title : 'Inline Widget',
                     image : '/images/adsense_icon.gif',
                     onselect : function(v) {
                         //tinyMCE.activeEditor.windowManager.alert('Value selected:' + v);
                         tinyMCE.activeEditor.focus();
                			tinyMCE.activeEditor.selection.setContent('<br />{inline_widget-' + v + '}<br />');
                     }
                });

                // Add some values to the list box
                
                <?php  $_smarty_tpl->tpl_vars['ad_unit'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ad_unit']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['inline_widgets']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ad_unit']->key => $_smarty_tpl->tpl_vars['ad_unit']->value){
$_smarty_tpl->tpl_vars['ad_unit']->_loop = true;
?>
                	mlb.add('<?php echo $_smarty_tpl->tpl_vars['ad_unit']->value['NAME'];?>
', '<?php echo $_smarty_tpl->tpl_vars['ad_unit']->value['ID'];?>
');
                <?php } ?>
                
                //mlb.add('Some item 1', 'val1');

                // Return the new listbox instance
                return mlb;

            case 'mysplitbutton':
                var c = cm.createSplitButton('mysplitbutton', {
                    title : 'My split button',

                    onclick : function() {
                        //tinyMCE.activeEditor.windowManager.alert('Button was clicked.');
                    }
                });

                c.onRenderMenu.add(function(c, m) {
                    m.add({title : 'Some title', 'class' : 'mceMenuItemTitle'}).setDisabled(1);

                    m.add({title : 'Some item 1', onclick : function() {
                        tinyMCE.activeEditor.windowManager.alert('Some  item 1 was clicked.');
                    }});

                    m.add({title : 'Some item 2', onclick : function() {
                        tinyMCE.activeEditor.windowManager.alert('Some  item 2 was clicked.');
                    }});
                });

                // Return the new splitbutton instance
                return c;
        }

        return null;
    }
});




// Register plugin with a short name
tinymce.PluginManager.add('example', tinymce.plugins.ExamplePlugin);
	tinyMCE.init({
		// General options
		mode : "exact",
		elements: "<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
",
		cleanup : false,
		theme : "advanced",
		skin : "wp_theme",
		plugins : "pdw,-example,safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,print,contextmenu,directionality,fullscreen,noneditable,nonbreaking,xhtmlxtras,template",
		convert_urls : false,
		// Theme options
		theme_advanced_buttons1 : "mylistbox,fontselect,fontsizeselect,bold,italic,underline,strikethrough,bullist,numlist,justifyleft,justifycenter,justifyright,nextpage",
		//theme_advanced_buttons2 : false,
		theme_advanced_buttons3 : false,
		theme_advanced_buttons4 : false,
		theme_advanced_buttons2 : "code,cut,copy,paste,pastetext,pasteword,outdent,indent,|,undo,redo,|,link,unlink,image,|,insertdate,inserttime,preview,|,forecolor,backcolor|,ltr,rtl,|,sub,sup",
		//theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,charmap,emotions,iespell,media,advhr,|,print,|,fullscreen",
		//theme_advanced_buttons2 : "",
		//theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		forced_root_block : false,
		force_br_newlines : true,
		force_p_newlines : false,

		pdw_toggle_on : 0,
		pdw_toggle_toolbars : "2",
        plugins : "paste",
        paste_text_sticky: false,
        paste_text_sticky_default: false,
	paste_auto_cleanup_on_paste: false,
		// Example content CSS (should be your site CSS)
		content_css : "content.css",
		setup : function(ed) {

		     // Add a custom button
			ed.addButton('nextpage', {

		        title : 'Next Page',
		        image : '/images/nextpage.gif',
		        onclick : function() {
			    var bElm = 'P,DIV,ADDRESS,PRE,FORM,TABLE,OL,UL,CAPTION,BLOCKQUOTE,CENTER,DL, DIR,FIELDSET,NOSCRIPT,NOFRAMES,MENU,ISINDEX,SAMP';
			     ed.focus();
			    n = ed.selection.getNode();

			    n = ed.dom.getParent(n, bElm, 'BODY') || n;

			    if(ed.dom.isBlock(n)){

				r = ed.dom.create('<p>', {'id' : 'pagebreaker'});
				r.innerHTML='{nextpage}';
				p = ed.dom.getParent(r, bElm, 'BODY');
				n.parentNode.insertBefore(r, n.previousSibling);

			    }
			    else
			    {
				ed.selection.setContent('<p>{nextpage}</p>');
			    }
			    // Add you own code to execute something on click

			     //ed.selection.setContent('<br />{--nextpage--}<br />');
			}
		});
		}
	});



</script>


<div class="wp_themeSkin" >
    <ul>
	    <li style="float:left;padding:0px;vertical-align:middle;padding-top:10px;"> Insert Media</li>
	    <li style="float:left;padding:0px;">
		<a title="Image Manager" class="mceButton mceButtonEnabled mce_image thickbox" href="<?php echo @DOC_ROOT;?>
/media_manager.php?type=image&TB_iframe=1&width=600&height=500" onclick="showThickbox(this);return false;" ><span class="mceIcon mce_image"></span></a>
	    </li>
    </ul>
</div>
<br><br>
<textarea id="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
"  rows="15" cols="80" style="width: 100%" class="mceContentBody"><?php echo $_smarty_tpl->tpl_vars['VALUE']->value;?>

</textarea>
<script type="text/javascript">edCanvas = document.getElementById('<?php echo $_smarty_tpl->tpl_vars['NAME']->value;?>
');edInsertContent = null;</script>
</div>

<?php }elseif($_smarty_tpl->tpl_vars['useRTE']->value=='CKE'){?>
    <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/../javascripts/ckeditor/ckeditor.js"></script>

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
/../javascripts/yui/css/skin.css">
    <!-- Utility Dependencies -->
    <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/../javascripts/yui/yahoo-dom-event.js"></script>
    <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/../javascripts/yui/element-min.js"></script>
    <!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
    <script src="<?php echo @DOC_ROOT;?>
/../javascripts/yui/container_core-min.js"></script>
    <script src="<?php echo @DOC_ROOT;?>
/../javascripts/yui/menu-min.js"></script>
    <script src="<?php echo @DOC_ROOT;?>
/../javascripts/yui/button-min.js"></script>
    <!-- Source file for Rich Text Editor-->
    <script src="<?php echo @DOC_ROOT;?>
/../javascripts/yui/editor-min.js"></script>

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