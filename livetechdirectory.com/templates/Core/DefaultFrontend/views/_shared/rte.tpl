{if isset ($useRTE) and $useRTE eq 'TINYMCE'}
  {if empty($allready_mce)}
    <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/tiny_mce/tiny_mce.js"></script>
   {assign var="allready_mce" value="1" scope=global}
  {/if}

    {literal}
        <script type="text/javascript">
                tinyMCE.init({
                        // General options
                        mode : "exact",
                        elements: "{/literal}{$NAME}{literal}",
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
    {/literal}
    <textarea id="{$NAME}" name="{$NAME}"  rows="15" cols="80" style="width: 100%">{$VALUE}</textarea>
{elseif $useRTE eq 'CKE'}
    <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/ckeditor/ckeditor.js"></script>

    <textarea  id="{$NAME}" name="{$NAME}" rows="20" cols="80" style="width: 100%">{$VALUE}</textarea>
    {literal}
        <script type="text/javascript">
					CKEDITOR.replace( '{/literal}{$NAME}{literal}' );
        </script>
    {/literal}
{elseif $useRTE eq 'YRTE'}
    <!-- Skin CSS file -->
    <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}/javascripts/yui/css/skin.css">
    <!-- Utility Dependencies -->
    <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/yui/yahoo-dom-event.js"></script>
    <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/yui/element-min.js"></script>
    <!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
    <script src="{$smarty.const.DOC_ROOT}/javascripts/yui/container_core-min.js"></script>
    <script src="{$smarty.const.DOC_ROOT}/javascripts/yui/menu-min.js"></script>
    <script src="{$smarty.const.DOC_ROOT}/javascripts/yui/button-min.js"></script>
    <!-- Source file for Rich Text Editor-->
    <script src="{$smarty.const.DOC_ROOT}/javascripts/yui/editor-min.js"></script>

    <div class="yui-skin-sam">
        <textarea  id="{$NAME}" name="{$NAME}" rows="20" cols="80" style="width: 100%">{$VALUE}
        </textarea>
    </div>
    {literal}
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
                            var myEditor = new YAHOO.widget.SimpleEditor('{/literal}{$NAME}{literal}', myConfig);
                            myEditor._defaultToolbar.buttonType = 'advanced';
                            myEditor.render();

                            })();
        </script>
    {/literal}
{else}
    <div class="phpld-fbox-text">
        <textarea name="{$NAME}" id="{$NAME}" rows="6" cols="50" class="text" >{$VALUE}</textarea>
    </div>
{/if}