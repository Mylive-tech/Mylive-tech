<div class="categoryLocations">
    <ul>
        {foreach from=$locations item="location"}
            <li><a href="{$location->getUrl()}">{$location.CITY}, {$location.STATE}</a></li>
        {/foreach}
    </ul>
</div>