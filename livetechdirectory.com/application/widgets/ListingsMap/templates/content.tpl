<div align="center" style="margin-right: 18px;">
    <div id="listing_map_{$widget->getId()}" style="width:100%; height:400px;"></div>
</div>

<script type="text/javascript">
    var valid_obj = new Object();

</script>
<script type="text/javascript">
{literal}
var map_links = Array;
var markers = {/literal}{$markers}{literal};
var default_lat = '{/literal}{$widgetSettings.LAT}{literal}';
var default_lon = '{/literal}{$widgetSettings.LON}{literal}';
var default_item = {/literal}{$default_item}{literal};
var default_zoom = {/literal}{$widgetSettings.ZOOM}{literal};

    $(document).ready(function() {

        initListingsMap({'map':'listing_map_{/literal}{$widget->getId()}{literal}', 'markers':markers, 'default_item':default_item});
});
{/literal}
</script>