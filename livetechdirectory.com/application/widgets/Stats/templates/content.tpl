{if $show_title eq 1}
{$TITLE}
<br/>
{/if}
<ul class="boxStats">
	{if $active_links eq 1}
	<li><strong>{l}Active Listings{/l}:</strong> {$statActiveLinks}</li>
	{/if}
	{if $pending_links eq 1}
	<li><strong>{l}Pending Listings{/l}:</strong> {$statPendingLinks}</li>
	{/if}
	{if $today_links eq 1}
	<li><strong>{l}Todays Listings{/l}:</strong> {$statTodaysLinks}</li>
	{/if}
    {if $total_categories eq 1}
	<li><strong>{l}Total Categories{/l}:</strong> {$statCategories}</li>
	{/if}
	{if $subcategories eq 1}
	<li><strong>{l}Sub Categories{/l}:</strong> {$statSubCategories}</li>
	{/if}
</ul>
