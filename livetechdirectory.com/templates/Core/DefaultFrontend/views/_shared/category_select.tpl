{* Build the category selection *}
{literal}
    <script src="{/literal}{$smarty.const.SITE_URL}{literal}templates/Core/DefaultFrontend/style/cat_menu_tree/jquery-ui.custom.js" type="text/javascript"></script>
    <script src="{/literal}{$smarty.const.SITE_URL}{literal}templates/Core/DefaultFrontend/style/cat_menu_tree/jquery.cookie.js" type="text/javascript"></script>

    <link href="{/literal}{$smarty.const.SITE_URL}{literal}templates/Core/DefaultFrontend/style/cat_menu_tree/ui.dynatree.css" rel="stylesheet" type="text/css" id="skinSheet">
    <script src="{/literal}{$smarty.const.SITE_URL}{literal}templates/Core/DefaultFrontend/style/cat_menu_tree/jquery.dynatree.js" type="text/javascript"></script>

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

{/literal}
{strip}
{if $smarty.const.CAT_SELECTION_METHOD eq 1}
	{if !empty ($CategoryTitle)}{$CategoryTitle}{else}{l}Please select a category!{/l}{/if}
	<input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID" value="{$CATEGORY_ID}" />
{else}
    {*html_options options=$categs selected=$CATEGORY_ID name="CATEGORY_ID"*}
    <div class="phpld-fbox-select" style="position: relative">  
        <input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID1" value="{$CATEGORY_ID}" />
  <a tabindex="0" href="#CATEGORIES" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="hierarchy" style="font-size: 20px; height: 22px; margin-bottom: 5px; width: 70%; margin-top:1px;" >
        	<span class="ui-icon ui-icon-triangle-1-s"></span>
        	{l}Select Category{/l}
    	</a>
    <span id="menuLog" style="height:18px; float:left; width:76.5%; margin-top:0px; margin-right: 37px; border:1px solid #cccccc; border-radius:4px; padding:10px; background-color:#ffffff;"><span id="echoActive"></span></span>

        <div id="tree"></div>
    </div>
{/if}
{/strip}