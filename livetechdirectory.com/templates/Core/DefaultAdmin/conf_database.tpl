{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="addfields" validators=$validators_addfields}
{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="conf_backup" validators=$validators_conf_backup}
{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="query_db" validators=$validators_query_db}

{strip}

<p style="color: red;">
You may receive a timeout or a blank page in some environments. If this happens, you will need to create your backup through your hosting control panel rather than through phpLD 
</p>

<div class="block">
   <!-- Database Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/conf_database.php#dbbackup" title="{l}Jump to database backup{/l}" class="button"><span class="jump">{l}Jump to database backup{/l}</span></a></li>
      <li><a href="{$smarty.const.DOC_ROOT}/conf_database.php#sqlrun" title="{l}Jump to SQL execution{/l}" class="button"><span class="jump">{l}Jump to SQL execution{/l}</span></a></li>
      <li><a href="{$smarty.const.DOC_ROOT}/conf_database.php#dbfields" title="{l}Jump to add database fields{/l}" class="button"><span class="jump">{l}Jump to add database fields{/l}</span></a></li>
      <li><a href="{$smarty.const.DOC_ROOT}/conf_database.php#sysinfo" title="{l}Jump to database system info{/l}" class="button"><span class="jump">{l}Jump to database system info{/l}</span></a></li>
   </ul>
   <!-- /Database Action Links -->
</div>

<div id="dbbackup" class="block">
   {if $show_backup_results}
      {if $backup_created}
         <div class="block">
            <div class="success">{l}Backup file created successfully{/l}!</div>
            {if $request_email}
               {if !$backup_emailed}
                  <div class="error">{l}Could not email backup to your requested address{/l}!</div>
               {else}
                  <div class="success">{l}Successfully emailed backup to your requested address{/l}!</div>
               {/if}
            {/if}
         </div>
      {else}
         <div class="block">
            <div class="error">{l}Database backup could not be created{/l}!</div>
         </div>
      {/if}
   {/if}

   <form method="post" name="backup_form" id="conf_backup" action="{$smarty.const.DOC_ROOT}/conf_database.php#dbbackup">
   <input type="hidden" name="db-backup-submit" value="1" />
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}MySQL Database Backup{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="backup_folder">{l}Full path to backup folder{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="backup_folder" name="backup_folder" value="{$backup_folder}" class="text" />
            {if $folder_error}
               <p class="error">{$folder_error}</p>
            {/if}
            {if $backup_folder_validation !== TRUE}
               <p class="error">{$backup_folder_validation}</p>
            {/if}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="backup_file">{l}File name template{/l}:</label></td>
         <td>
            <input type="text" id="backup_file" name="backup_file" value="{$backup_file}" class="text" />
            {if $file_error}
               <p class="error">{$file_error}</p>
            {/if}
            {if is_string($backup_file_validation)}
               <p class="error">{$backup_file_validation}</p>
            {/if}
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}What to do with the backup file{/l}:</label></td>
         <td class="smallDesc">
            <p><label for="backup_to_download"><input type="radio" id="backup_to_download" name="backup_options" value="download"{if $backup_options == 'download' || !$backup_options} checked="checked"{/if} />{l}Download to your computer{/l}</label></p>
            <p><label for="backup_to_server"><input type="radio" id="backup_to_server" name="backup_options" value="server"{if $backup_options == 'server'} checked="checked"{/if} />{l}Save to server{/l}</label></p>
            <p><label for="backup_to_mail"><input type="radio" id="backup_to_mail" name="backup_options" value="email"{if $backup_options == 'email'} checked="checked"{/if} />{l}Email backup to{/l}</label></p>

            <input type="text" id="backup_to_email" name="backup_to_email" value="{$backup_to_email}" maxlength="255" class="text" />
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label>{l}Compression{/l}:</label></td>
         <td class="smallDesc">
            <p><label for="compression_none"><input type="radio" id="compression_none" name="compression" value="sql"{if $compression == 'sql' || !$compression} checked="checked"{/if} />{l}none{/l}</label></p>
            <p><label for="compression_zipped"><input type="radio" id="compression_zipped" name="compression" value="zip"{if $compression == 'zip'} checked="checked"{/if} />{l}zipped{/l}</label></p>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-dbbackup-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-dbbackup-submit" name="db-backup-submit" value="{l}Backup{/l}" alt="{l}Backup{/l}" title="{l}Backup database{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   </form>
</div>

<div id="sqlrun" class="block">
   {if $show_query_results}
      <div class="block">
         <div class="success">{l}SQL query executed{/l}</div>
         {if $affected_rows}
            <div class="notice">{l}Affected rows{/l}: {$affected_rows}</div>
         {/if}
         {if $rs2html}
            {$rs2html}
         {/if}
      </div>
   {/if}
   <form method="post" name="query_db" id="query_db" action="{$smarty.const.DOC_ROOT}/conf_database.php#sqlrun">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Run SQL query on database{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="notice">
         <td class="notice" colspan="2">
            <p>{l}Enter SQL queries, but be aware to use this option on your own risk. There is no detailed output, just an error message if your query fails or a confirmation message if your query was successful.{/l}</p>
            <p class="warning">{l}This option is just for experienced users with at least basic MySQL knowledge.{/l}</p>
         </td>
      </tr>


      <tr class="{cycle values="odd,even"}">
         <td class="smallDesc" colspan="2">
            <textarea id="sql_query" name="sql_query" rows="10" cols="80" class="text code">{$sql_query|escape|trim}</textarea>

            {if $sql_error_nr || $sql_error_msg}
               <p class="error">#{$sql_error_nr} - {$sql_error_msg}</p>
            {/if}
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-sqlquery-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-sqlquery-submit" name="sql-query-submit" value="{l}Run query{/l}" alt="{l}Run SQL query{/l}" title="{l}Run SQL query{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   </form>
</div>

<div id="dbfields" class="block">
   <form method="post" id="addfields" name="addfields" action="{$smarty.const.DOC_ROOT}/conf_database.php#dbfields">
   {if isset($addfieldstatus) or !empty($sql_error)}
      {if $addfieldstatus eq 1}
         <div class="success block">
            <p>{l}Successfully added a new field{/l}</p>
         </div>

         {if isset($tplExample) and !empty($tplExample)}
            <div class="information notice block">
               <p>{l}You may now add input fields to submit forms in your template files.{/l}</p>
               <p>{l}Just make sure the input field name is the same as the newly created database field name{/l}{if !empty($previewDbFieldName)}: <span class="important">{$previewDbFieldName|escape}</span>{/if}</p>

               <p><textarea id="preview-tplExample" name="preview-tplExample" rows="4" cols="80" class="text code">{$tplExample|escape|trim}</textarea></p>
            </div>
         {/if}
      {elseif $addfieldstatus eq 0 or !empty($sql_error)}
         <div class="error block">
            <h2>{l}Error{/l}</h2>
            <p>{l}An error occured while adding new field to database{/l}!</p>
            {if !empty($sql_error)}
               <p>{l}The database server returned the following message:{/l}</p>
               <p>{$sql_error|escape}</p>
            {/if}
         </div>
      {/if}
   {/if}

   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Add fields to database tables{/l}</th>
      </tr>
   </thead>

   <tbody>
      <tr class="notice">
         <td class="notice" colspan="2">
            <p class="warning">{l}This option is just for experienced users with at least basic MySQL knowledge.{/l}</p>
         </td>
      </tr>

      {if isset($addfieldSQL) and !empty($addfieldSQL)}
      <tr>
         <td colspan="2"><p>{l}SQL query{/l}:</p>
            <textarea id="preview-addfieldSQL" name="preview-addfieldSQL" rows="2" cols="80" class="text code">{$addfieldSQL|escape|trim}</textarea>
         </td>
      </tr>
      {/if}

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="dbtable">{l}Database table{/l}</label></td>
         <td class="smallDesc">
            {html_options options=$dbtableList selected=$dbtable name="dbtable" id="dbtable"}
         </td>
      </tr>
      <tr class="{cycle values="odd,even"}">
         <td class="label required"><label for="dbfieldname">{l}Field name{/l}</label></td>
         <td class="smallDesc">
            <input type="text" id="dbfieldname" name="dbfieldname" value="{$dbfieldname|escape|trim}" class="text" />
         </td>
      </tr>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="dbfieldtype">{l}Field type{/l}</label></td>
         <td class="smallDesc">
            {html_options options=$fieldTypeList selected=$dbfieldtype name="dbfieldtype" id="dbfieldtype"}
         </td>
      </tr>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="dbfieldlength">{l}Length/Values{/l}</label></td>
         <td class="smallDesc">
            <input type="text" id="dbfieldlength" name="dbfieldlength" value="{$dbfieldlength|escape|trim}" class="text" />
         </td>
      </tr>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="dbfieldnull">{l}NULL{/l}</label></td>
         <td class="smallDesc">
            {html_options options=$fieldNullList selected=$dbfieldnull name="dbfieldnull" id="dbfieldnull"}
         </td>
      </tr>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="dbfielddefault">{l}Default{/l}</label></td>
         <td class="smallDesc">
            <input type="text" id="dbfielddefault" name="dbfielddefault" value="{$dbfielddefault|escape|trim}" class="text" />
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-addfields-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-addfields-submit" name="submitAddfields" value="{l}Add fields{/l}" alt="{l}Add fields{/l}" title="{l}Add fields{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   </form>
</div>

<div id="sysinfo" class="block">
   <table class="list">
      <thead>
         <tr>
            <th>{l}Table name{/l}</th>
            <th>{l}Records{/l}</th>
            <th>{l}Data size{/l}</th>
            <th>{l}Overhead{/l}</th>
            <th>{l}Effective size{/l}</th>
            <th>{l}Index size{/l}</th>
            <th>{l}Total size{/l}</th>
            <th colspan="2">{l}Action{/l}</th>
        </tr>
      </thead>

      <tbody>
      {if isset($tableInfoList) and is_array($tableInfoList) and !empty($tableInfoList)}
         {foreach from=$tableInfoList item=tbl key=tblname}
         <tr class="{cycle values="odd,even"}">
            <td>{$tblname|escape}</td>
            <td>{$tbl.Rows|escape}</td>
            <td>{assign var="DataSize" value="`$tbl.Data_length+$tbl.Data_free`"}{$DataSize|nicesize}</td>
            <td>{if $tbl.Data_free gt 0}<a href="{$smarty.const.DOC_ROOT}/conf_database.php?action=optimize&amp;tbl={$tblname|escape:'url'}" title="{l}Optimize table{/l}: {$tblname|escape}">{$tbl.Data_free|nicesize}</a>{else}{$tbl.Data_free|nicesize}{/if}</td>
            <td>{assign var="EffectiveSize" value="`$tbl.Data_length-$tbl.Data_free`"}{$EffectiveSize|nicesize}</td>
            <td>{$tbl.Index_length|nicesize}</td>
            <td>{assign var="TotalSize" value="`$tbl.Index_length+$tbl.Data_length+$tbl.Data_free`"}{$TotalSize|nicesize}</td>
            <td><a href="{$smarty.const.DOC_ROOT}/conf_database.php?action=repair&amp;tbl={$tblname|escape:'url'}#sysinfo" title="{l}Repair table{/l}: {$tblname|escape}">{l}Repair{/l}</a></td>
            <td><a href="{$smarty.const.DOC_ROOT}/conf_database.php?action=flush&amp;tbl={$tblname|escape:'url'}#sysinfo" title="{l}Flush table{/l}: {$tblname|escape}">{l}Flush{/l}</a></td>
         </tr>

         {assign var="total" value="`$total+$tbl.Index_length+$tbl.Data_length`"}
         {assign var="totaloverhead" value="`$totaloverhead+$tbl.Data_free`"}
         {/foreach}

         <tr class="notice">
            <td>{l}Totals{/l}:</td>
            <td colspan="2"></td>
            <td>{$totaloverhead|nicesize}</td>
            <td colspan="2"></td>
            <td>{$total|nicesize}</td>
            <td colspan="2"></td>
         </tr>
         <tr class="information">
            <td colspan="9">{l}Overhead is unused space reserved by MySQL. To free up this space, click on the table's overhead figure.{/l}</td>
         </tr>
      {else}
         <tr>
            <td colspan="9" class="norec">{l}Could not determine database tables status information.{/l}</td>
         </tr>
      {/if}
      </tbody>
   </table>
</div>

{/strip}