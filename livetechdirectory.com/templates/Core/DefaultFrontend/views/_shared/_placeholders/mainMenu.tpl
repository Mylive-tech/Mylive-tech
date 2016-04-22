{strip}
{* Define the function *}
{function name=render_menu level=0}
  <ul>
  {foreach $items as $item}
    <li><a href="{if "/http:/"|@preg_match:$item.URL}{else}{$smarty.const.SITE_URL}{/if}{eval var=$item.URL}">{l}{$item.LABEL}{/l}</a>
      {if count($item.pages) > 0}
	{call name=render_menu items=$item.pages level=$level+1}
      {/if}
    </li>
  {/foreach}
  </ul>
  {/function}
  {* Call the function with the root nodes collection *}
  {call render_menu items=$menuList}		   			     
{/strip}