{if $feat_links->countWithoutLimit() > 0}
    {foreach from=$feat_links item=link}
        {$link->listing('list')}
    {/foreach}
{/if}
<hr/>
{if $links->countWithoutLimit() > 0}
    {foreach from=$links item=link}
        {$link->listing('list')}
    {/foreach}
{/if}

{if $feat_links->countWithoutLimit() == 0 AND $links->countWithoutLimit() == 0}
    There are currently no submissions listed for this user
{/if}