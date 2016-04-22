{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="edit_link_form" validators=$validators}

{* Error and confirmation messages *}
{include file="messages.tpl"}

<div class="block">
    <form method="post" action="" id="edit_link_form" enctype="multipart/form-data">
        <input type="hidden" value="{$data.ID}" ID="ID" name="ID"/>
        <table class="formPage">
        {if isset($action) and ($action eq 'N' or $action eq 'E')}
            <thead>
            <tr>
                <th colspan="2">
                    {if $action eq 'N'}
                        {l}Create new tag{/l}
                        {elseif $action eq 'E'}
                        {l}Edit tag{/l}
                    {/if}
                </th>
            </tr>
            </thead>
        {/if}

            <tbody>
                <tr>
                    <td class="label"><label for="TITLE">{l}Title{/l}:</label></td>
                    <td class="smallDesc">
                        <input type="text" name="TITLE" id="TITLE" class="text" value="{$data.TITLE}" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><label for="STATUS">{l}Status{/l}:</label></td>
                    <td class="smallDesc">
                        <select type="text" name="STATUS" id="STATUS" class="text">
                            <option value="2" {if $data.STATUS == 2}selected="selected"{/if}>Active</option>
                            <option value="1" {if $data.STATUS == 1}selected="selected"{/if}>Pending</option>
                            <option value="0" {if $data.STATUS == 0 && !is_null($data.STATUS)}selected="selected"{/if}>Banned</option>
                        </select>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><input type="submit" id="send-link-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save link{/l}" class="button" /></td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>