{assign var="count" value=$items|@count}
{assign var="i" value=1}
{if $count > 0}
    <div class="breadcrumbs">
    {foreach from=$items item="crumb"}
        <span>
            {if !empty($crumb.URL)}<a href="{$crumb.URL}">{/if}
                {$crumb.LABEL}
            {if !empty($crumb.URL)}</a>{/if}

            {if $i < $count}
                <span class="divider">/</span>
            {/if}
        </span>
        {assign var="i" value=$i+1}
    {/foreach}
    </div>
{/if}