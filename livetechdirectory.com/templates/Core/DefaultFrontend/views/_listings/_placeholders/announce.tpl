{if empty($LINK.ANNOUNCE)}
    {$LINK.DESCRIPTION|strip_tags|truncate:200:"":false}
{else}
    {$LINK.ANNOUNCE}
{/if}