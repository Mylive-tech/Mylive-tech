
    {foreach from=$LISTING_CATEGORIES_LIST item="category"}
      <a class="link-category-url" href="{$category->getUrl()}">{$category.TITLE}</a> &nbsp;
    {/foreach}

