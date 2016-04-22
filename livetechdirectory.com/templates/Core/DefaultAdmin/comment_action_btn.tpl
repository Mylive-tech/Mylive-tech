{* Build category action submit buttons *}
{strip}
<table class="block list">
   <thead>
      <tr>
         <th class="listHeader" colspan="2">{l}Manage multiple selections{/l}</th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <td>
            <fieldset class="categ_action">
               <legend>{l}Select{/l}</legend>
               {formtool_checkall name="multiselect_checkbox[]" checkall_text="{l}Check All{/l}" uncheckall_text="{l}Uncheck All{/l}" class="button" id="checkallButton"}
            </fieldset>

            <fieldset class="categ_action">
               <legend>{l}Change Status{/l}</legend>
               <input type="button" name="active" id="activeButton" value="Active" title="{l}Set selected comments as active{/l}" class="button" onclick="selected_categ_active_confirm('{escapejs}{l}Are you sure you want to change status of selected comments to active?{/l}{/escapejs}');" />

               <input type="button" name="pending" id="pendingButton" value="Pending" title="{l}Set selected comments as pending{/l}" class="button" onclick="selected_categ_pending_confirm('{escapejs}{l}Are you sure you want to change status of selected comments to pending?{/l}{/escapejs}');" />

               <input type="button" name="inactive" id="inactiveButton" value="Inactive" title="{l}Set selected comments as inactive{/l}" class="button" onclick="selected_categ_inactive_confirm('{escapejs}{l}Are you sure you want to change status of selected comments to inactive?{/l}{/escapejs}');" />
            </fieldset>

            <fieldset class="link_action">
               <legend>{l}Action{/l}</legend>
               <input type="button" name="remove" id="removeButton" value="{l}Remove{/l}" title="{l}Remove selected comments!{/l} {l}Note: comments can not be restored after removal!{/l}" class="button" onclick="selected_categ_remove_confirm('{escapejs}{l}Are you sure you want to remove selected comments?{/l}\n{l}Note: Comments can not be restored after removal!{/l}{/escapejs}');" />
            </fieldset>

         </td>
      </tr>
      <tr>
         <td>
            <div id="multiselect_action_msg">
               {l}Select comments and choose an action to perform.{/l}
               <br />
               <span id="multiselect_action_count">&nbsp;</span> {l}item(s) selected{/l}.
            </div>
         </td>
      </tr>
   </tbody>
</table>
{/strip}