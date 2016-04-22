<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title><![CDATA[{$smarty.const.DIRECTORY_TITLE} {$title}]]></title>
        <link>{$url}</link>
        <description>{$smarty.const.SITE_DESC} {$description}</description>
	{foreach from=$links item=link}
        <item>
            <title><![CDATA[{$link.TITLE|trim}]]></title>
                <link><![CDATA[{$link->getUrl()}]]></link>
			{if $link.DESCRIPTION}
                <description><![CDATA[{$link.DESCRIPTION|trim}]]></description>
			{/if}
            <pubDate>{$link.DATE_ADDED|date_format:"%a, %d %b %Y %H:%M:%S GMT"}</pubDate>
        </item>
	{/foreach}

    </channel>
</rss>