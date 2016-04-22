{if $LINK.PAGERANK ge 0}
    PR: {$LINK.PAGERANK}
    {else}
    N/A
{/if}
<div class="prg">
    <div class="prb" style="width: {if $LINK.PAGERANK gt -1}{math equation="x*4" x=$LINK.PAGERANK}{else}0{/if}px"></div>
</div>
