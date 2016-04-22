{if isset ($useRTE) and $useRTE eq 'TINYMCE'}
{literal}<div id="editorcontainer">
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
                {/literal}
                {foreach from=$inline_widgets item=ad_unit name=inline_widget}
                	mlb.add('{$ad_unit.NAME}', '{$ad_unit.ID}');
                {/foreach}
                {literal}
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
		elements: "{/literal}{$NAME}{literal}",
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
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,outdent,indent,|,undo,redo,|,link,unlink,image,|,insertdate,inserttime,preview,|,forecolor,backcolor|,ltr,rtl,|,sub,sup",
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

		// Example content CSS (should be your site CSS)
		content_css : "content.css",
		setup : function(ed) {
		     // Add a custom button
			ed.addButton('nextpage', {
			
		        title : 'Next Page',
		        image : '/images/nextpage.gif',
		        onclick : function() {
			    var bElm = 'P,DIV,ADDRESS,PRE,FORM,TABLE,OL,UL,CAPTION,BLOCKQUOTE,CENTER,DL, DIR,FIELDSET,NOSCRIPT,NOFRAMES,MENU,ISINDEX,SAMP';
			    
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
			     ed.focus();
			     //ed.selection.setContent('<br />{--nextpage--}<br />');
			}
		});
		}
	});
	


</script>
{/literal}

<div class="wp_themeSkin" >
    <ul>
	    <li style="float:left;padding:0px;vertical-align:middle;padding-top:10px;"> Insert Media</li>
	    <li style="float:left;padding:0px;">
		<a title="Image Manager" class="mceButton mceButtonEnabled mce_image thickbox" href="{$smarty.const.DOC_ROOT}/media_manager.php?type=image&TB_iframe=1&width=600&height=500" onclick="showThickbox(this);return false;" ><span class="mceIcon mce_image"></span></a>
	    </li>
    </ul>
</div>
<br><br>
<textarea id="{$NAME}" name="{$NAME}"  rows="15" cols="80" style="width: 100%" class="mceContentBody">{$VALUE}
</textarea>
<script type="text/javascript">edCanvas = document.getElementById('{$NAME}');edInsertContent = null;</script>
{elseif $useRTE eq 'CKE'}
         <textarea  id="{$NAME}" name="{$NAME}" rows="20" cols="80" style="width: 100%">{$VALUE}
			</textarea>
        {literal}
         	<script type="text/javascript">
					CKEDITOR.replace( '{/literal}{$NAME}{literal}' );
				</script>
         {/literal}
      {elseif $useRTE eq 'YRTE'}
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
        					height: '300px',
        					width: '600px',
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
<textarea name="{$NAME}" id="{$NAME}" rows="6" cols="50" class="text" >{$VALUE}</textarea>{/if}
</div>