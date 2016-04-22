{strip}
        <h3>"{$letter}" - {l}Listings{/l}</h3>
        {if !empty($links)}
            
            <div class="listing-style-list">
                {foreach from=$links item=LINK}
                    {$LINK->listing()}
                {/foreach}
            </div>
            {$PAGINATOR}
        {/if}
{/strip}