{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
{php}
   $p = array();
   for($i = 1; $i <= $this->get_template_vars('difference'); $i++)
   {
      $p[] = ($this->get_template_vars('percent_last') + $i) * 5 - 256;
   }
   $this->assign('p', $p);
{/php}

<tr class="{cycle values="odd,even"}">
   <td>
      {foreach from=$p item=i}
      <div class="prog-single" style="margin-left:{$i}px;">
         <img width="5" height="15" src="{$smarty.const.TEMPLATE_ROOT}/images/progbar-single.gif" alt="progress" />
      </div>
      {/foreach}
   </td>
</tr>

<tr class="{cycle values="odd,even"}">
   <td>{$url|escape|trim}{if $VALIDATE_RECPR}<br />{$recpr_url|escape|trim}{/if}</td>
   {if $VALIDATE_LINKS}
      <td class="status-{$link_valid}">{$valid.$link_valid}</td>
   {/if}
   {if $VALIDATE_RECPR}
      <td class="valid-URL-{if $recpr_valid >0}2{else}0{/if}">{if $recpr_valid gt 0}{l}Found{/l}{else}{l}Not found{/l}{/if}</td>
   {/if}
   {if $VALIDATE_LINKS}
      <td>{$errstr|escape}</td>
   {/if}
   {if $VALIDATE_RECPR}
      <td>{if $recpr_valid eq -2}{l}No recpr. link{/l}{elseif $recpr_valid eq -1}{l}Page not found{/l}{elseif $recpr_valid eq 0}{l}Link not found{/l}{else}{l}Ok.{/l}{/if}</td>
   {/if}
</tr>
{/strip}