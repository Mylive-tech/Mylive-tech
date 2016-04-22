{if $show_title eq 1}
    {$TITLE}
{/if}
<div class="spotlight">

    <img class="thumb" src="http://immediatenet.com/t/s?Size=1024x768&URL={$spotlight.URL|escape|trim}"><br />
    <a href="{$spotlight->getUrl()}" title="{l}Read more about{/l}: {$spotlight.TITLE|escape|trim}">{$spotlight.TITLE|escape|truncate:30:"...":true}</a>
</div>