    <div class="rssFeed">{if !empty($LISTINGS)}    {foreach from=$LISTINGS.articles item="item"}        <div class="feedItem">            <a href="{$item.link}" target="_blank">{$item.title}</a>        </div>    {/foreach}{else}    Feed URL is not set{/if}</div>