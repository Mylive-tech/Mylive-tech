{strip}
    {if $totalCount == 0}

        {* NO RESULTS FOUND - PRINT ERROR MESSAGE *}

        <div>
            <p>{l}Sorry, no records found that match your keyword(s){/l}{if $search}: "{$search|escape|wordwrap:200:"\n":true}"{/if}</p>
            <p>{l}<strong>Suggestions</strong>{/l}:</p>
            <p>
            <ul>
                <li>{l}Make sure all words are spelled correctly{/l}.</li>
                <li>{l}Try different keywords{/l}.</li>
                <li>{l}Try more general keywords{/l}.</li>
            </ul>
            </p>
        </div>
    {else}

        {* RESULTS FOUND - PRINT THEM ALL *}
        {if !empty($searchquery)}<p>Search results for: <strong>{$searchquery|escape}</strong></p>{/if}


        {* Show link search results *}
        {if !empty($links)}
            <h3>{l}Search Results{/l}</h3>
            <div class="listing-style-list">
                {foreach from=$links item=LINK}
                    {$LINK->listing()}
                {/foreach}
            </div>
        {/if}
    {/if}

{/strip}