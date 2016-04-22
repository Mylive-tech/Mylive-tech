{* Error and confirmation messages *}
{include file="messages.tpl"}


{literal}<script type="text/javascript" src="../javascripts/editinplace/EditInPlace.js"></script>
<script type="text/javascript">EditInPlace.defaults['type'] = 'textarea';
EditInPlace.defaults['save_url'] = DOC_ROOT + '/ajax_lang_edit.php';
EditInPlace.defaults['escape_function'] = encodeURIComponent;
EditInPlace.defaults['click'] = 'click';
/*EditInPlace.defaults['on_blur'] = 'cancel';*/

EditInPlace.defaults['savebutton_text'] = '{/literal}{escapejs}{l}Save{/l}{/escapejs}{literal}';
EditInPlace.defaults['cancelbutton_text'] = '{/literal}{escapejs}{l}Cancel{/l}{/escapejs}{literal}';
EditInPlace.defaults['saving_text']     = '{/literal}{escapejs}{l}Saving{/l}...{/escapejs}{literal}';
EditInPlace.defaults['empty_text']      = '{/literal}.:: {escapejs}{l}Click to edit{/l}{/escapejs} ::.{literal}';
EditInPlace.defaults['edit_title']      = '{/literal}{escapejs}{l}Click to edit{/l}{/escapejs}{literal}';
EditInPlace.defaults['savefailed_text'] = '{/literal}{escapejs}{l}Failed to save changes{/l}{/escapejs}!{literal}';</script>{/literal}

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

{if $action eq 'edit'}
   {if is_array($lang1) and is_array($lang2)}
      <div class="block">
      <form method="post" name="lang">
         <table class="list">
         <thead>
            <tr>
               <th>{l}Reference language{/l}</th>
               <th>{l}Editable language{/l}</th>
               <th class="hidden">{l}Unique hash{/l}</th>
            </tr>
         </thead>

         <tbody>
            <tr><td colspan="3" class="info notice">{l}Click or double click on editable language phrases to edit them{/l}.</td></tr>
         {foreach from=$lang1 key=hash item=l2 name=language}
            <tr class="{cycle values="odd,even"}">
               <td class="label">{$l2|escape}</td>
               <td><span id="l2-{$hash}" title="{l}Click to edit{/l}"{if !isset($lang2.$hash) or empty($lang2.$hash)} class="eip_empty"{/if} onclick="{literal}EditInPlace.makeEditable({ id: '{/literal}l2-{$hash}{literal}', ajax_data: { hash: '{/literal}{$hash}{literal}', lang: '{/literal}{$getLang2}{literal}' } });{/literal}">{if !isset($lang2.$hash) or empty($lang2.$hash)}.:: {l}Click to edit{/l} ::.{else}{$lang2.$hash|escape}{/if}</span></td>
               <td class="hidden">{$hash}</td>
            </tr>
         {/foreach}
         </tbody>
      </table>
      </form>
      </div>
   {/if}
{elseif $action eq 'simpleedit'}
   {if is_array($lang)}
      <div class="block">
      <form method="post" name="lang">
         <table class="list">
         <thead>
            <tr>
               <th>{l}Edit language{/l}: {if isset($langInfo.LANGUAGE) and !empty($langInfo.LANGUAGE)}{$langInfo.LANGUAGE|escape}{else}{$getLang}{/if}</th>
            </tr>
         </thead>

         <tbody>
         {foreach from=$lang key=hash item=row name=language}
            <tr class="{cycle values="odd,even"}">
               <td><span id="l-{$hash}" title="{l}Click to edit{/l}"{if !isset($lang.$hash) or empty($lang.$hash)} class="eip_empty"{/if} onclick="{literal}EditInPlace.makeEditable({ id: '{/literal}l-{$hash}{literal}', ajax_data: { hash: '{/literal}{$hash}{literal}', lang: '{/literal}{$getLang}{literal}', forcenewhash: '0' } });{/literal}">{if !isset($lang.$hash) or empty($lang.$hash)}.:: {l}Click to edit{/l} ::.{else}{$lang.$hash|escape}{/if}</span></td>
            </tr>
         {/foreach}
         </tbody>
      </table>
      </form>
      </div>
   {/if}
{elseif $action eq 'addphrase'}
   {if is_array($lang)}
      <div class="block">
         <!-- Action Links -->
         <ul class="page-action-list">
            <li><a href="{$smarty.const.DOC_ROOT}/conf_language.php?action=addphrase&amp;lang={$getLang}" title="{l}Add a new phrase to same language file{/l}" class="button"><span class="new-phrase">{l}New phrase{/l}</span></a></li>
         </ul>
         <!-- /Action Links -->
      </div>

      <div class="block">
      <form method="post" name="lang">
         <table class="formPage">
         <thead>
            <tr>
               <th>{l}Edit language{/l}</th>
               <th>{l}Phrase to add{/l}</th>
            </tr>
         </thead>

         <tbody>
            <tr class="{cycle values="odd,even"}">
               <td class="label">{if isset($langInfo.LANGUAGE) and !empty($langInfo.LANGUAGE)}{$langInfo.LANGUAGE|escape}{else}{$getLang}{/if}</td>
               <td class="smallDesc"><span id="l-{$hash}" title="{l}Click to edit{/l}"{if !isset($lang.$hash) or empty($lang.$hash)} class="eip_empty"{/if} onclick="{literal}EditInPlace.makeEditable({ id: '{/literal}l-{$hash}{literal}', ajax_data: { hash: '{/literal}{$hash}{literal}', lang: '{/literal}{$getLang}{literal}',  } });{/literal}">{if !isset($lang.$hash) or empty($lang.$hash)}.:: {l}Click to edit{/l} ::.{else}{$lang.$hash|escape}{/if}</span></td>
            </tr>
         </tbody>
      </table>
      </form>
      </div>
   {/if}

{else}
   <div class="block">
   <form method="post" action="{$smarty.const.DOC_ROOT}/conf_language.php?action=edit">
      <table class="formPage">
      <thead>
         <tr>
            <th colspan="2">{l}Edit language files{/l}</th>
         </tr>
      </thead>

      <tbody>
      <tr>
         <td class="label required"><label for="lang1">{l}Choose your reference language{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$languages selected=$language name="lang1" id="lang1"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="lang2">{l}Choose language to edit{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$languages selected=$language name="lang2" id="lang2"}
            <p class="info">{l}Second language must be different{/l}</p>
         </td>
      </tr>
      </tbody>

      <tfoot>
         <tr>
            <td><input type="reset" id="reset-langedit-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
            <td><input type="submit" id="send-langedit-submit" name="continue" value="{l}Continue{/l}" alt="{l}Continue{/l}" title="{l}Continue with language edit{/l}" class="button" /></td>
         </tr>
      </tfoot>
      </table>
      <input type="hidden" name="formSubmitted" value="1" />
   </form>
   </div>

   <div class="block">
   <form method="post" action="{$smarty.const.DOC_ROOT}/conf_language.php?action=simpleedit">
      <table class="formPage">
      <thead>
         <tr>
            <th colspan="2">{l}Edit language file{/l}</th>
         </tr>
      </thead>

      <tbody>
      <tr>
         <td class="label required"><label for="lang">{l}Choose language to edit{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$languages selected=$language name="lang" id="lang"}
         </td>
      </tr>
      </tbody>

      <tfoot>
         <tr>
            <td><input type="reset" id="reset-simpleedit-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
            <td><input type="submit" id="send-simpleedit-submit" name="continue" value="{l}Continue{/l}" alt="{l}Continue{/l}" title="{l}Continue with language edit{/l}" class="button" /></td>
         </tr>
      </tfoot>
      </table>
      <input type="hidden" name="formSubmitted" value="1" />
   </form>
   </div>

   <div class="block">
   <form method="post" action="{$smarty.const.DOC_ROOT}/conf_language.php?action=addphrase">
      <table class="formPage">
      <thead>
         <tr>
            <th colspan="2">{l}Add phrase{/l}</th>
         </tr>
      </thead>

      <tbody>
      <tr>
         <td class="label required"><label for="lang">{l}Choose language to add phrase to{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$languages selected=$language name="lang" id="lang"}
         </td>
      </tr>
      </tbody>

      <tfoot>
         <tr>
            <td><input type="reset" id="reset-addphrase-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
            <td><input type="submit" id="send-addphrase-submit" name="continue" value="{l}Continue{/l}" alt="{l}Continue{/l}" title="{l}Continue with language edit{/l}" class="button" /></td>
         </tr>
      </tfoot>
      </table>
      <input type="hidden" name="formSubmitted" value="1" />
   </form>
   </div>
{/if}