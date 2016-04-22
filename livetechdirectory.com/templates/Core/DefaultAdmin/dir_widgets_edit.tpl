{* Error and confirmation messages *}
{include file="messages.tpl"}
{$widgetJsScripts}
{strip}
{if (isset($error) and $error gt 0) or !empty($sql_error)}
   <div class="error block">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while saving.{/l}</p>
      {if !empty($errorMsg)}
         <p>{$errorMsg|escape}</p>
      {/if}
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p>{$sql_error|escape}</p>
      {/if}
   </div>
{/if}

{if $posted}
   <div class="success block">
      {l}Widget settings saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="">
   <table class="formPage">

   <thead>
      <tr>
         <th colspan="2">
               {l}Edit widget settings{/l}
         </th>
      </tr>
  </thead>

   <tbody>
      {if $config_before}
        <tr>
	        <td colspan="2">
            {include file=$config_before}
            </td>
        </tr>
      {/if}
      {section name=k loop=$list}
      <tr>
         <td class="label required"><label for="{$list[k].NAME}">{l}{$list[k].NAME}{/l}:</label></td>
         <td class="smallDesc">
            {if $list[k].ALLOWED[0] neq ''}
            <select id="{$list[k].IDENTIFIER}" name="{$list[k].IDENTIFIER}">
            {section name=i loop=$list[k].ALLOWED}
             {if strpos($list[k].ALLOWED[i], ':') !== false}
                {assign var="value" value=':'|explode:$list[k].ALLOWED[i]   }
                 <option value="{$value.0}" {if $list[k].VALUE eq $value.0}selected="selected"{/if}>
                    {$value.1}
                 </option>
             {else}
                 <option value="{$list[k].ALLOWED[i]}" {if $list[k].VALUE eq $list[k].ALLOWED[i]}selected="selected"{/if}>
                     {$list[k].ALLOWED[i]}
                 </option>
             {/if}
            {/section}
            </select>
           {elseif $list[k].IDENTIFIER eq 'TEXTBOX'}
               {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/rte.tpl" NAME=$list[k].IDENTIFIER VALUE=$list[k].VALUE|escape|trim}
               {elseif $list[k].IDENTIFIER eq 'EMBED'}
	    <TEXTAREA id="{$list[k].IDENTIFIER}" name="{$list[k].IDENTIFIER}" rows="10" cols="50">{$list[k].VALUE|escape}</TEXTAREA>   
	    <br />
	    Preview: {$list[k].VALUE}
           {else}    
            <input type="text" id="{$list[k].IDENTIFIER}" name="{$list[k].IDENTIFIER}" value="{$list[k].VALUE}" class="text" />
           {/if}
            <p class="limitDesc"><br/>{$list[k].INFO}</p>
         </td>
      </tr>
      {/section}
	   {if $config_after}
       <tr>
           <td colspan="2">
	        {include file=$config_after}
	       </td>
       </tr>
	   {/if}
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-widget-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-widget-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save Widget Settings{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
   </form>
</div>
{/strip}