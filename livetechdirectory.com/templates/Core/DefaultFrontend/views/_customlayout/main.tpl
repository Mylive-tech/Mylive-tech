<div class="phpld-col3"  style="margin-left:{$sidebar1.width}{$sidebar1.type};
                                margin-right:{$sidebar2.width}{$sidebar2.type};">

    <div class="phpld-cbox">
        {foreach from=$widget_zones.CENTRAL item=zone}
            {foreach from=$widgets[$zone.NAME] item=widget}
                {if $widget['NAME'] eq 'MainContent'}
                    {include file="views/_shared/widget.tpl" ID=$widget.ID  TITLE=$widget.SETTINGS.TITLE SHOW_TITLE=$widget.SETTINGS.SHOW_TITLE   CONTENT=$content WIDGET_HEADING=$widgetheading}
                {else}
                    {include file="views/_shared/widget.tpl" ID=$widget.ID  TITLE=$widget.SETTINGS.TITLE SHOW_TITLE=$widget.SETTINGS.SHOW_TITLE   CONTENT=$widget.CONTENT WIDGET_HEADING=$widgetheading}
                {/if}
            {/foreach}
        {/foreach}
    </div>
</div>