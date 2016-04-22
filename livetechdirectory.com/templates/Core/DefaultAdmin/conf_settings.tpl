{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}

{strip}
{php}
   $this->assign('opt_bool', array(1 => $this->translate('Yes'), 0 => $this->translate('No')));
{/php}
{php}
   $this->assign('opt_bool1', array(2 => $this->translate('Do The Math Mod'), 1 => $this->translate('Yes'), 0 => $this->translate('No')));
{/php}
{if $show_cache_msg eq 1}
<div class="error block">{l}It is strongly advised that you rebuild the category cache now. Please click here{/l}: <a href="{$smarty.const.DOC_ROOT}/dir_categs.php?action=rebuild_cache" title="{l}Rebuild Category Cache{/l}">{l}Rebuild Category Cache{/l}</a></div>
{/if}
{if $posted}
   <div class="success block">
      {l}Settings updated.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="{if isset($conf_group)}{$smarty.const.DOC_ROOT}/conf_settings.php?c={$conf_group}{/if}" id="submit_form">
   <table class="formPage">
      <thead>
         <tr>
            <th colspan="2">{if !empty($conf_categs.$conf_group)}{$conf_categs.$conf_group|escape|trim}{else}{l}Configuration options{/l}{/if}</th>
         </tr>
      </thead>

      <tbody>
      {assign var="categ" value="0"}
      {foreach from=$conf item=row}
         {if $categ ne $row.CONFIG_GROUP}
            {assign var="categ" value=$row.CONFIG_GROUP}
         {/if}

         <tr class="{cycle values='odd,even'}">
            <td  class="label{if $row.REQUIRED eq 1} required{/if}"><label for="{$row.ID}">{$row.NAME|escape}:</label></td>
            <td  class="smallDesc">
                
               {if $row.TYPE eq 'STR'}
                  <input type="text" id="{$row.ID}" name="{$row.ID}" value="{$row.VALUE|escape|trim}" class="text" />
                 
	{elseif $row.TYPE eq 'CONTENT' || $row.TYPE eq 'TERMS' || $row.TYPE eq 'DISABLE_REASON'}
        
	{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/rte.tpl" NAME=$row.ID VALUE=$row.VALUE}
  
  {elseif $row.TYPE eq 'GOOGLE_ANALYTICS'}   
<textarea rows="5" cols="20"id="{$row.ID}" name="{$row.ID}" class="text" />{$row.VALUE}</textarea>
                 {elseif $row.TYPE eq 'PAS'}
                  <input type="password" id="{$row.ID}" name="{$row.ID}" value="{$row.VALUE|escape|trim}" class="text" />
               {elseif $row.TYPE eq 'URL'}
                  <input type="text" id="{$row.ID}" name="{$row.ID}" value="{$row.VALUE|escape|trim}" class="text" />
               {elseif $row.TYPE eq 'INT'}
                  <input type="text" id="{$row.ID}" name="{$row.ID}" value="{$row.VALUE|escape|trim}" class="text" />
               {elseif $row.TYPE eq 'NUM'}
                  <input type="text" id="{$row.ID}" name="{$row.ID}" value="{$row.VALUE|escape|trim}" class="text" />
               {elseif $row.TYPE eq 'LOG'}
                  {html_options options=$opt_bool selected=$row.VALUE name=$row.ID id=$row.ID}
               {elseif $row.TYPE eq 'LKP'}
                  {html_options options=$row.OPTIONS selected=$row.VALUE name=$row.ID id=$row.ID}
		  	{elseif $row.TYPE eq 'LOG1'}
                 {html_options options=$opt_bool1 selected=$row.VALUE name=$row.ID id=$row.ID}
               {/if}

               {if isset($row.DESCRIPTION) and !empty($row.DESCRIPTION)}
                  <div class="description">
                     {if is_array($row.DESCRIPTION)}
                        <ul>
                        {foreach from=$row.DESCRIPTION item=DescItem}
                           <li>{$DescItem}</li>
                        {/foreach}
                        </ul>
                     {else}
                        {$row.DESCRIPTION}
                     {/if}
                  </div>
               {/if}

               {if isset($row.NOTICE) and !empty($row.NOTICE)}
                  <div class="notice">
                     {if is_array($row.NOTICE)}
                        <ul>
                        {foreach from=$row.NOTICE item=NoticeItem}
                           <li>{$NoticeItem}</li>
                        {/foreach}
                        </ul>
                     {else}
                        {$row.NOTICE}
                     {/if}
                  </div>
               {/if}

               {if isset($row.WARNING) and !empty($row.WARNING)}
                  <div class="warning">
                     {if is_array($row.WARNING)}
                        <ul>
                        {foreach from=$row.WARNING item=WarningItem}
                           <li>{$WarningItem}</li>
                        {/foreach}
                        </ul>
                     {else}
                        {$row.WARNING}
                     {/if}
                  </div>
               {/if}
            </td>
         </tr>
      {/foreach}
      </tbody>
      <tfoot>
         <tr>
            <td><input type="reset" id="reset-conf-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
            <td><input type="submit" id="send-conf-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save settings{/l}" class="button" /></td>
         </tr>
      </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   </form>
</div>
{/strip}