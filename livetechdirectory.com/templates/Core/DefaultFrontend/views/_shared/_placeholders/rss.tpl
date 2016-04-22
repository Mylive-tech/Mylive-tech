 {if $smarty.const.ENABLE_RSS and ($category.ID gt 0 or $list)}
 <link rel="alternate" type="application/rss+xml" title="{$in_page_title|escape|trim}" href="{$smarty.const.SITE_URL}rss.php?{if !empty($search)}search={$search|@urlencode}{elseif $p > 1}p={$p}{elseif $list}list={$list}{else}c={$category.ID}{/if}" />
 {/if}