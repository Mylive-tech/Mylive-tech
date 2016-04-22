<script src="{$smarty.const.DOC_ROOT}/javascripts/jvectormap/maps/{$widget->getMapSettings('file')}"></script>
<link rel="stylesheet" media="all" href="{$smarty.const.DOC_ROOT}/javascripts/jvectormap/jquery-jvectormap.css"/>

<script>
    {literal}
        $(function(){
            var urlMap = {/literal}{$widget->getUrlMapJson()}{literal};
            $('#map_{/literal}{$widget->getId()}{literal}').vectorMap(
                {
                    map: '{/literal}{$widget->getMapSettings('id')}{literal}',
                    {/literal}{if !empty($widgetSettings.REGION_COLOR)} color : '{$widgetSettings.REGION_COLOR}',{/if}{literal}
                    {/literal}{if !empty($widgetSettings.MOUSEOVER_COLOR)} hoverColor : '{$widgetSettings.MOUSEOVER_COLOR}',{/if}{literal}
                    {/literal}{if !empty($widgetSettings.BACKGROUND_COLOR)} backgroundColor : '{$widgetSettings.BACKGROUND_COLOR}',{/if}{literal}
                    onRegionClick: function(event, code){
                        if (urlMap != null && urlMap[code] != undefined) {
                            document.location = urlMap[code];
                        }
                    }
                }
            );
        });
    {/literal}
</script>
<div id="map_{$widget->getId()}" class="jmap"></div>