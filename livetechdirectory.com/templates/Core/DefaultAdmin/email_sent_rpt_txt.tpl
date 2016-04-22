{strip}

{foreach from=$columns key=col item=name name=cols}
   {if not $smarty.foreach.cols.last}{/if}{$name}
{/foreach}

{foreach from=$list item=row key=id}
   {foreach from=$columns key=col item=name}
      {$row.$col}
   {/foreach}
{foreachelse}
   {l}No records found.{/l}
{/foreach}
{/strip}