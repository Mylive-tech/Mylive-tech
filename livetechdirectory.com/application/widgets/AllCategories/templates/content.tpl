{* Categories *}
{if !empty($categs)}
    {if !empty($category.ID) or (empty($category.ID) and empty($set.TITLE))}
    <h3>{l}Categories{/l}</h3>
    {/if}


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
    <div class="phpld-grid AllCategories">
        {foreach from=$categs item=cat name=categs}
            <div class="{$catgridClass} ">
                <div class="phpld-gbox">
                    <h4><a href="{$cat->getUrl()}" {if $cat.NEW_WINDOW eq 1}target="blank"{/if}  title="{$cat.TITLE|escape}">{$cat.TITLE|escape}</a>{if $smarty.const.CATS_COUNT}<span>({$cat.COUNT})</span>{/if}</h4>
                {* Display subcategories *}
                    {assign var="subcategory" value=$cat.SUBCATS}
                    {assign var=sub_count value=$cat.SUBCATS|@count}
                    {if !empty($subcategory)}
                        <ul>
                            {foreach from=$subcategory item=scat key=k name=scategs}
                                {if $smarty.foreach.scategs.iteration <= $smarty.const.CATS_PREVIEW }
                                    <li>
                                        <a href="{$scat->getUrl()}" {if $scat.NEW_WINDOW eq 1}target="blank"{/if}  title="{$scat.TITLE|escape}" class="phpld-gray" >{$scat.TITLE|escape}</a>
                                    </li>
                                {/if}

                            {/foreach}
                        </ul>
                    {/if}
                </div>
            </div>
        {/foreach}
    </div>
{/if}