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
			{if $rights.editCat eq 1}
            <fieldset class="categ_action">
               <legend>{l}Change Status{/l}</legend>
               <input type="button" name="active" id="activeButton" value="Active" title="{l}Set selected categories as active{/l}" class="button" onclick="selected_categ_active_confirm('{escapejs}{l}Are you sure you want to change status of selected categories to active?{/l}{/escapejs}');" />

               <input type="button" name="pending" id="pendingButton" value="Pending" title="{l}Set selected categories as pending{/l}" class="button" onclick="selected_categ_pending_confirm('{escapejs}{l}Are you sure you want to change status of selected categories to pending?{/l}{/escapejs}');" />

               <input type="button" name="inactive" id="inactiveButton" value="Inactive" title="{l}Set selected categories as inactive{/l}" class="button" onclick="selected_categ_inactive_confirm('{escapejs}{l}Are you sure you want to change status of selected categories to inactive?{/l}{/escapejs}');" />
            </fieldset>
			{/if}
			{if $rights.delCat eq 1}
            <fieldset class="link_action">
               <legend>{l}Action{/l}</legend>
               <input type="button" name="remove" id="removeButton" value="{l}Remove{/l}" title="{l}Remove selected categories!{/l} {l}Note: categories can not be restored after removal!{/l}" class="button" onclick="selected_categ_remove_confirm('{escapejs}{l}Are you sure you want to remove selected categories?{/l}\n{l}Note: Categories can not be restored after removal!{/l}{/escapejs}');" />

               <input type="button" name="removecomplete" id="removeCompleteButton" value="{l}Remove with content{/l}" title="{l}Remove selected categories, their subcategories, links and articles!{/l} {l}Note: Selected categories, subcategories, their links and articles can not be restored after removal!{/l}" class="button" onclick="selected_categ_complete_remove_confirm('{escapejs}{l}Are you sure you want to remove selected categories, their subcategories, links and articles?{/l}\n{l}Note: Selected categories, subcategories, their links and articles can not be restored after removal!{/l}{/escapejs}');" />
            </fieldset>
			{/if}
			{if $rights.editCat eq 1}
            <fieldset class="categ_action">
               <legend>{l}Parent category{/l}</legend>
               {* Load category selection *}
               {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" parent="1"}
               <input type="button" name="changeparent" id="changeParentCategoryButton" value="{l}Change parent category{/l}" title="{l}Change parent category{/l}" class="button" onclick="selected_change_parentcateg_confirm('{escapejs}{l}Are you sure you want to change parent category?{/l}{/escapejs}');" />
            </fieldset>
            {/if}
         </td>
      </tr>
      <tr>
         <td>
            <div id="multiselect_action_msg">
               {l}Select categories and choose an action to perform.{/l}
               <br />
               <span id="multiselect_action_count">&nbsp;</span> {l}item(s) selected{/l}.
            </div>
         </td>
      </tr>
   </tbody>
</table>
{/strip}