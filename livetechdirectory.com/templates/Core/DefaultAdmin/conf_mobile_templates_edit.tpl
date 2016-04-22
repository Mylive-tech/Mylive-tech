{* Error and confirmation messages *}
{include file="messages.tpl"}

{strip}
    <div class="block">
        <!-- Action Links -->
        <ul class="page-action-list">
            {if $edit_screen}
                <li><a href="{$smarty.const.DOC_ROOT}/conf_mobile_templates_edit.php?r=1" title="{l}Back to files{/l}" class="button"><span class="page-tpl">{l}Back to files{/l}</span></a></li>
            {/if}
            <li><a href="{$smarty.const.DOC_ROOT}/conf_mobile_templates.php?r=1" title="{l}Manage templates{/l}" class="button"><span class="manage-tpl">{l}Manage Mobile Templates{/l}</span></a></li>
        </ul>
        <!-- /Action Links -->
    </div>

    {if isset($file_saved)}
        {if $file_saved eq 0}
            <div class="error block">
                {l}An error occured while saving.{/l}!
            </div>
        {elseif $file_saved eq 1}
            <div class="success block">
                {l}File saved{/l}!
            </div>
        {/if}
    {/if}

    {if $edit_screen}

        <div class="block">
            <table class="list active-template">
                <thead>
                    <tr>
                        <th colspan="2">{l}Current template{/l}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="label">{l}Title{/l}:</td>
                        <td class="smallDesc title">{if !empty ($current_template.theme_uri)}<a href="{$current_template.theme_uri}" title="{l}Browse template homepage{/l}" target="_blank">{$current_template.theme_name|escape|trim}</a>{else}{$current_template.theme_name|escape|trim}{/if}</td>
                    </tr>
                    <tr>
                        <td class="label">{l}Version{/l}:</td>
                        <td class="smallDesc">{$current_template.theme_version|escape|trim}</td>
                    </tr>
                    <tr>
                        <td class="label">{l}Author{/l}:</td>
                        <td class="smallDesc">{if !empty ($current_template.theme_author_uri)}<a href="{$current_template.theme_author_uri}" title="{l}Browse template author homepage{/l}" target="_blank">{$current_template.theme_author|escape|trim}</a>{else}{$current_template.theme_author|escape|trim}{/if}</td>
                    </tr>
                    <tr>
                        <td class="label">{l}Description{/l}:</td>
                        <td class="smallDesc">{$current_template.theme_description|escape|trim}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="block">
            <form method="post" action="">
                <table class="formPage">
                    <thead>
                        <tr>
                            <th colspan="2">{l}Edit file{/l}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="{cycle values="odd,even"}">
                            <td class="label">{$file_name|escape|trim}</td>
                            <td>
                                <textarea id="file_content" name="file_content" rows="30" cols="100" class="text code">{$file_content|escape|trim}</textarea>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td><input type="reset" id="reset-tpl-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
                            <td><input type="submit" id="send-link-submit" name="submit" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save template{/l}" class="button" /></td>
                        </tr>
                    </tfoot>
                </table>

            </form>
        </div>

    {else}

        <div class="block">
            <table class="list active-template">
                <thead>
                    <tr>
                        <th colspan="2">{l}Current template{/l}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="label">{l}Title{/l}:</td>
                        <td class="smallDesc title">{if !empty ($current_template.theme_uri)}<a href="{$current_template.theme_uri}" title="{l}Browse template homepage{/l}" target="_blank">{$current_template.theme_name|escape|trim}</a>{else}{$current_template.theme_name|escape|trim}{/if}</td>
                    </tr>
                    <tr>
                        <td class="label">{l}Version{/l}:</td>
                        <td class="smallDesc">{$current_template.theme_version|escape|trim}</td>
                    </tr>
                    <tr>
                        <td class="label">{l}Author{/l}:</td>
                        <td class="smallDesc">{if !empty ($current_template.theme_author_uri)}<a href="{$current_template.theme_author_uri}" title="{l}Browse template author homepage{/l}" target="_blank">{$current_template.theme_author|escape|trim}</a>{else}{$current_template.theme_author|escape|trim}{/if}</td>
                    </tr>
                    <tr>
                        <td class="label">{l}Description{/l}:</td>
                        <td class="smallDesc">{$current_template.theme_description|escape|trim}</td>
                    </tr>
                    <tr>
                        <td class="label">{l}Preview{/l}:</td>
                        <td class="smallDesc preview">{if !empty($current_template.theme_screenshot_file) and $showPreview eq '1'}{thumb file=$current_template.theme_screenshot_file width="250" link="true" type=$thumbType cache="../temp/cache/"}{else}{l}No preview available{/l}{/if}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    <div class="block">
        <form name="layoutForm" id="layoutForm" action="" method="post">    
            <table class="list active-template">
                <thead>
                    <tr>
                        <th colspan="2">{l}Template Settings{/l}</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="2" class="label"><div>{l}{$layout.label}{/l}</div><hr />
                        <input type="hidden" name="titleheading" value="{$layout.titleheading.value}" />
                        <input type="hidden" name="widgetheading" value="{$layout.widgetheading.value}" />
                        </td>
                    </tr>
                    {foreach key=key_zone item=zone from=$layout.zones}
                    <tr>
                        <td class="label">{l}{$zone.label}{/l}:</td>
                        <td class="smallDesc title">
                            {*<select name="layoutType" id="layoutType">*}
                                {foreach key=key item=option from=$layout.options}
                                    <div style="display:inline-block">
                                        <table><tr><td text-align="left">
                                        <div style="">
                                    <img src="{$option.image}" width="90px" height="90px" align="left" style="margin:0px" />
                                    </div>
                                    </td></tr><tr><td text-align="center"><div>
                                            
                                    {if $zone.value eq $option.key }
                                        <input name="zone[{$zone.name}]" type="radio" value="{$option.key}" checked />{$option.value}
                                        {*<option value="{$option.key}" selected>{$option.value}</option>*}
                                    {else}
                                        <input name="zone[{$zone.name}]" type="radio" value="{$option.key}" />{$option.value}
                                        {*<option value="{$option.key}">{$option.value}</option>*}
                                    {/if}
                                    </div></td></tr></table>
                                    </div>
                                {/foreach}    
                            {*</select> *}
                            <div style="clear:both" />
                        </td>
                    </tr>    
                    {/foreach} 
                    <tr>
                        <td class="label">{l}{$layout.color.label}{/l}:</td>
                        <td class="smallDesc title">
                            <select name="color" id="color">
                                {foreach key=key item=option from=$layout.coloroptions}
                                    {if $layout.color.value eq $option.key }

                                        <option value="{$option.key}" selected>{$option.value}</option>
                                    {else}
                                        
                                        <option value="{$option.key}">{$option.value}</option>
                                    {/if}
                                {/foreach}    
                            </select> 
                            <div style="clear:both" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="label">{l}{$layout.mainmenu.label}{/l}:</td>
                        <td class="smallDesc">
                            {html_options options=$yes_no selected=$layout.mainmenu.value name="mainmenu" id="mainmenu"}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">{l}{$layout.categories.label}{/l}:</td>
                        <td class="smallDesc">
                            {html_options options=$yes_no selected=$layout.categories.value name="categories" id="categories"}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">{l}{$layout.latestlinks.label}{/l}:</td>
                        <td class="smallDesc">
                            {html_options options=$yes_no selected=$layout.latestlinks.value name="latestlinks" id="latestlinks"}
                        </td>
                    </tr>    
                    <tr>
                        <td class="label">{l}Description{/l}:</td>
                        <td class="smallDesc">{$current_template.theme_description|escape|trim}</td>
                    </tr>
                </tbody>
                    <tfoot>
                        <tr>
                            <td><input type="hidden" id="settings-link" name="settings" value="{l}save{/l}" alt="{l}Save form{/l}" title="{l}Save template settings{/l}" class="button" /></td>
                            <td><input type="submit" id="send-link-submit" name="submit" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save template settings{/l}" class="button" /></td>
                        </tr>
                    </tfoot>
                
            </table>    
        </form>
    </div>   
        <div class="block">
            <table class="list">
                <thead>
                    <tr>
                        <th class="listHeader first-child">{l}Filename{/l}</th>
                        <th class="listHeader">{l}Full path{/l}</th>
                        <th class="listHeader last-child">{l}Status{/l}</th>
                    </tr>
                </thead>

                <tbody>
                    {foreach key=key item=file from=$template_files}
                        <tr class="{cycle values="odd,even"}">
                            <td class="label">{$file.name}</td>
                            <td>
                                {$file.path|escape|wordwrap:80:"\n":1|nl2br}
                            </td>
                            {if is_string($file.permission)}
                                <td class="error">{$file.permission}</td>
                            {else}
                                <td><a href="{$smarty.const.DOC_ROOT}/conf_mobile_templates_edit.php?action=edit&amp;filename={$file.name}" title="{l}Edit{/l}: {$file.name|escape|trim}" class="button"><span class="edit-tpl">{l}Edit{/l}</span></a></td>
                            {/if}
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

    {/if}
{/strip}