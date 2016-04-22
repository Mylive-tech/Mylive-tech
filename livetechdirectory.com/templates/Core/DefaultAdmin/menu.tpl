{if isset($m.menu) and !empty($m.menu) and is_array($m.menu)}
   <ul class="code1 closed" style="display: none" id="{$m.label}">
   {foreach from=$m.menu item=l key=k}
      {if isset($l.menu) and !empty($l.menu) and is_array($l.menu)}
         <li class="menu-item has-submenu" title="{$l.label}">{$l.label}</li>
         {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/menu.tpl" m=$l}
      {elseif isset($l) and !empty($l) and is_array($l)}
         {if $l.disabled}
            <!--<li class=" menu-item disabled" title="{l}Menu item disabled{/l}">{$l.label}</li>-->
         {else}
            <li class="menu-item"><a href="{$l.url}{if strpos($l.url, '?') !== false}&amp;r=1{else}?r=1{/if}" title="{$l.label}">{$l.label}</a></li>
         {/if}
      {else}
         <li><a href="{$mk}_{$k}.php?r=1" title="{$l}" class="menu-item">{$l}</a></li>
      {/if}
   {/foreach}
   </ul>
{/if}