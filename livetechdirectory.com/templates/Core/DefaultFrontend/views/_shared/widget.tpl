<div class="phpld-grid {if $DISPLAY_IN_BOX}phpld-full  phpld-widget{/if}" id="widget_{$ID}">
     {if $DISPLAY_IN_BOX}<div class="boxTop"></div>{/if}

    {if $SHOW_TITLE && !empty($TITLE)}
        <h{$WIDGET_HEADING}>{$TITLE}</h{$WIDGET_HEADING}>
    {/if}
    {$CONTENT}
</div>

