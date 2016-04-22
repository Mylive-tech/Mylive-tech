{if $show_title eq 1}
    {$TITLE}
{/if}
<ul class="boxPopCats">
    {foreach from=$pages item=page name=pages}
        <li>
            <a href="{$page->getUrl()}">{$page.NAME}</a>
        </li>
    {/foreach}
</ul>