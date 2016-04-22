<ul>
    {foreach from=$LISTING_CATEGORIES_LIST item="category"}
        <li><a href="{$category->getUrl()}">{$category.TITLE}</a></li>
    {/foreach}
</ul>
