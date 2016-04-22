{strip}
    {if $totalCount == 0}

        {* NO RESULTS FOUND - PRINT ERROR MESSAGE *}

        <li>
            <p>{l}Sorry, no records found that match your keyword(s){/l}{if $search}: "{$search|escape|wordwrap:200:"\n":true}"{/if}</p>
            <p>{l}<strong>Suggestions</strong>{/l}:</p>
            <p>
            <ul>
                <li>{l}Make sure all words are spelled correctly{/l}.</li>
                <li>{l}Try different keywords{/l}.</li>
                <li>{l}Try more general keywords{/l}.</li>
            </ul>
            </p>
        </li>
    {else}

       


        {* Show link search results *}
        {if !empty($links)}
            <li class="group">{l}Search Results{/l} - "{$smarty.request.search}"</li>
                {foreach from=$links item=link}
                    <li>      

                    <a class="link" id="id_{$link.ID}" href="{$link.URL|escape|trim}" title="{$link.TITLE|escape|trim}"
                        {if $link.NOFOLLOW} rel="nofollow"{/if} target="_blank">{$link.TITLE|escape|trim|truncate:45:"...":true}</a>   


                    <div style="width:95%">	   
                        {if $link.DESCRIPTION}
                        <p id="description{$link.ID}" style="font-size:15px;font-weight:normal;">
                            <span id="editdescrip_{$link.ID}">{if !empty($link.DESCRIPTION)}{$link.DESCRIPTION|trim|truncate:230:"...":true}{else}[{l}No Description{/l}]{/if}</span>
                        </p>	
                        {/if}
                        <a class="readMore" href="{$link->getUrl()}" title="{l}Read more about{/l}: {$link.TITLE|escape|trim}" target="_self">{l}Read&nbsp;more{/l}</a>
                        {if ($smarty.const.REQUIRE_REGISTERED_USER == 1 || $smarty.const.REQUIRE_REGISTERED_USER_ARTICLE == 1) and !empty ($regular_user_details) and ($regular_user_details.ID == $link.OWNER_ID)}
                            ,&nbsp;<a class="readMore" href="{$smarty.const.DOC_ROOT}/submit?linkid={$link.ID}" title="{l}Edit or Remove your link{/l}">{l}Review{/l}</a>
                            {/if}
                    </div>
                    </li>
                {/foreach}
        {/if}
    {/if}

{/strip}