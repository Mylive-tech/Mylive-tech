{* Error and confirmation messages *}
{include file="messages.tpl"}

<div class="block">
    <form method="post" action="" enctype="multipart/form-data" id="submit_form">
        <input type="hidden" name="act" value="import">
        <table class="formPage">
            <thead>
                <tr>
                    <th colspan="3">
                        {l}Import Links{/l}
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr class="odd">
                    <td class="label required">
                        <label for="TITLE">{l}Category{/l}:</label>
                    </td>
                    <td>
                        {* Load category selection *}
                        {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" selected=$data.CATEGORY_ID  selected_parent=$data.PARENT_ID}
                        {if $link_type_details.MULTIPLE_CATEGORIES != ''}
                            {foreach from=$add_categs item=categ name=additional}
                                {if $smarty.foreach.additional.index < $link_type_details.MULTIPLE_CATEGORIES}
                                    <div>
                                    {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" categ_id=$categ.CATEGORY_ID additional_categs=true}&nbsp;
                                        <a href="#" onclick="removeCateg(this); return false;">{l}remove{/l}</a>
                                        <div style="float: none; clear: both; height: 8px;"></div>
                                    </div>
                                {/if}
                            {/foreach}
                        {/if}
                    </td>
                    <td></td>
                </tr>
                <tr class="even">
                    <td class="label required">
                        <label for="TITLE">{l}Type{/l}:</label>
                    </td>
                    <td>
                        {html_options options=$link_types_sel selected=$type_id name="type"}
                    </td>
                    <td></td>
                </tr>
                <tr class="odd">
                    <td class="label required">
                        <label for="TITLE">{l}Delimiter{/l}:</label>
                    </td>
                    <td>
                        {html_options options=$delimiters name="delimiter"}
                    </td>
                    <td></td>
                </tr>
                <tr class="even">
                    <td class="label required">
                        <label for="TITLE">{l}File{/l}:</label>
                    </td>
                    <td>
                        <input type="file" name="IMPORT_FILE" />
                    </td>
                    <td>
                        <h3>Download Sample Files</h3>
                        <ul>
                            {foreach from=$link_types item="type"}
                            <li><a href="dir_link_import.php?act=download_sample&type={$type.ID}">{$type.NAME}</a></li>
                            {/foreach}
                        </ul>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Import" class="button" />
                    </td>
                    <td>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

{literal}
<script type="text/javascript">
</script>
{/literal}