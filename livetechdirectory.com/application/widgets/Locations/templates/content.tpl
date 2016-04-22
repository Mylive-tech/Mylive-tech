{if $smarty.const.CATS_PER_ROW == 2}
    {assign var="catgridClass" value='phpld-g50 phpld-gl'}
    {elseif  $smarty.const.CATS_PER_ROW == 3}
    {assign var="catgridClass" value='phpld-g33 phpld-gl'}
    {elseif  $smarty.const.CATS_PER_ROW == 4}
    {assign var="catgridClass" value='phpld-g25 phpld-gl'}
    {elseif  $smarty.const.CATS_PER_ROW == 5}
    {assign var="catgridClass" value='phpld-g20 phpld-gl'}
    {else}
    {assign var="catgridClass" value='phpld-gbox '}
{/if}

<div class="phpld-grid perStateLocations">
        {foreach from=$locations key="state" item="cities"}
        <div class="{$catgridClass} ">
            <div class="phpld-gbox">
                <h4>{$state}</h4>
                <ul>
                    {foreach from=$cities item="city"}
                        <li><a href="{$city->getUrl()}">{$city.CITY}</a></li>
                    {/foreach}
                </ul>
            </div>
        </div>
        {/foreach}
</div>