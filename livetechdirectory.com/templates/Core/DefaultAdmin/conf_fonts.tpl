{* Error and confirmation messages *}
{include file="messages.tpl"}

<div class="block">

        <table class="formPage">
            <thead>
            <tr>
                <th colspan="2">{if !empty($conf_categs.$conf_group)}{$conf_categs.$conf_group|escape|trim}{else}{l}Font options{/l}{/if}</th>
            </tr>
            </thead>
        </table>
        <form method="post" id="submit_form" enctype="multipart/form-data">
            <input type="hidden" name="act" value="upload_font" />
            <table class="formPage">
                <tbody>

                    <tr class="{cycle values='odd,even'}">
                        <td  class="label{if $row.REQUIRED eq 1} required{/if}">
                            <label for="header_font">Font Name</label>
                        </td>
                        <td  class="smallDesc">
                            <input type="name" id="font_name" name="font_name" />
                        </td>
                    </tr>
                    <tr class="{cycle values='odd,even'}">
                        <td  class="label{if $row.REQUIRED eq 1} required{/if}">
                            <label for="header_font">Upload new Font</label>
                        </td>
                        <td  class="smallDesc">
                            <input type="file" id="font" name="font" />
                            <div class="description">
                                TTF, OTF and WOFF font formats allowed only
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2"><input type="submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save settings{/l}" class="button" /></td>
                </tr>
                </tfoot>
            </table>
        </form>
        <form method="post">
            <table class="formPage">
                <tbody>
                    <tr class="{cycle values='odd,even'}">
                        <td  class="label{if $row.REQUIRED eq 1} required{/if}">
                            <label for="header_font">Headers Font</label>
                        </td>
                        <td  class="smallDesc">
                            <select name="HEADER_FONT" id="header_font">
                                <option value="">-- Use Defined in Template --</option>
                                {foreach from=$fonts item="font" key="key"}
                                    <option value="{$key}" {if $currentHeaderFont == $key}selected="selected"{/if}>{$font}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr class="{cycle values='odd,even'}">
                        <td  class="label{if $row.REQUIRED eq 1} required{/if}">
                            <label for="content_font">Content Font</label>
                        </td>
                        <td  class="smallDesc">
                            <select name="CONTENT_FONT" id="content_font">
                                <option value="">-- Use Defined in Template --</option>
                                {foreach from=$fonts item="font" key="key"}
                                    <option value="{$key}" {if $currentContentFont == $key}selected="selected"{/if}>{$font}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>
                    <tr class="{cycle values='odd,even'}">
                        <td  class="label{if $row.REQUIRED eq 1} required{/if}">
                            <label for="header_font">Site Name Font</label>
                        </td>
                        <td  class="smallDesc">
                            <select name="SITENAME_FONT" id="sitename_font">
                                <option value="">-- Use Defined in Template --</option>
                                {foreach from=$fonts item="font" key="key"}
                                    <option value="{$key}" {if $currentSitenameFont == $key}selected="selected"{/if}>{$font}</option>
                                {/foreach}
                            </select>
                        </td>
                    </tr>

                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2"><input type="submit" id="send-conf-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save settings{/l}" class="button" /></td>
                </tr>
                </tfoot>
            </table>
        </form>
</div>