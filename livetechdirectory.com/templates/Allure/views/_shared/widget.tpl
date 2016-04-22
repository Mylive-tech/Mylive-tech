<div class="phpld-grid phpld-full phpld-widget" id="widget_{$ID}">
    <div class="boxTop"></div>
    {if $SHOW_TITLE && !empty($TITLE)}
        <h{$WIDGET_HEADING}>{$TITLE}</h{$WIDGET_HEADING}>
    {/if}
    {$CONTENT}
</div>
