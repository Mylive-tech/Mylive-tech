{* Build category action submit buttons *}
{strip}
<table class="block list" id="multiple_controls" id="multiple_controls">
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
               <input type="button" name="check_all" id="check_all" value="{l}Check All{/l}" class="button" />
               <input type="button" name="uncheck_all" id="uncheck_all" value="{l}Uncheck All{/l}" class="button" />
            </fieldset>
			{if $rights.editCat eq 1}
            <fieldset class="categ_action">
               <legend>{l}Change Status{/l}</legend>
               <input type="button" name="active" id="status_active" value="Active" title="{l}Set selected categories as active{/l}" class="button" />

               <input type="button" name="inactive" id="status_inactive" value="Inactive" title="{l}Set selected categories as inactive{/l}" class="button" />
            </fieldset>
			{/if}
			{if $rights.delCat eq 1}
            <fieldset class="link_action">
               <legend>{l}Action{/l}</legend>
               <input type="button" name="remove" id="remove" value="{l}Remove{/l}" title="{l}Remove selected categories!{/l} {l}Note: categories can not be restored after removal!{/l}" class="button" />

               <input type="button" name="removecomplete" id="removecomplete" value="{l}Remove with content{/l}" title="{l}Remove selected categories, their subcategories, and links!{/l} {l}Note: Selected categories, subcategories, and their links can not be restored after removal!{/l}" class="button" />
            </fieldset>
			{/if}
			{if $rights.editCat eq 1}
            <fieldset class="categ_action">
               <legend>{l}Parent category{/l}</legend>
               {* Load category selection *}
               {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl" parent="1"}
               <input type="button" name="changeparent" id="changeparent" value="{l}Change parent category{/l}" title="{l}Change parent category{/l}" class="button" />
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