<ul class="tags tags-latest">
{foreach from=$TAGS item="tag"}
    <li><a href="{$tag->getUrl()}">{$tag.TITLE} <span class="count">({$tag.COUNT})</span></a></li>
{/foreach}
</ul>