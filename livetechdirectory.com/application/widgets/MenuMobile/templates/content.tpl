{if $show_title eq 1}
    {$TITLE}
{/if}
{foreach $menuList as $item}
    <li><a href="{if "/http:/"|@preg_match:$item.URL}{else}{$smarty.const.SITE_URL}{/if}{eval var=$item.URL}">{l}{$item.LABEL}{/l}</a></li>
  {/foreach}