<!-- Build link action submit buttons -->
{strip}
<table class="list" id="multiple_controls" id="multiple_controls">
   <thead>
      <tr>
         <th class="listHeader" colspan="2">{l}Manage multiple selections{/l}</th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <td>
            <fieldset class="link_action">
               <legend>{l}Select{/l}</legend>
               <input type="button" name="check_all" id="check_all" value="{l}Check All{/l}" class="button" />
               <input type="button" name="uncheck_all" id="uncheck_all" value="{l}Uncheck All{/l}" class="button" />
            </fieldset>
			{if $rights.editPage eq 1}
            <fieldset class="link_action">
               <legend>{l}Change Status{/l}</legend>
               <input type="button" name="active" id="activeButton" value="{l}Active{/l}" title="{l}Set selected pages as active{/l}" class="button" />
               <input type="button" name="inactive" id="inactiveButton" value="{l}Inactive{/l}" title="{l}Set selected pages as inactive{/l}" class="button" />
            </fieldset>
			{/if}
			{if $rights.delPage eq 1}
            <fieldset class="link_action">
               <legend>{l}Action{/l}</legend>
               <input type="button" name="remove" id="removeButton" value="{l}Remove{/l}" title="{l}Remove selected pages!{/l} {l}Note: pages can not be restored after removal!{/l}" class="button" />
            </fieldset>
			{/if}
         </td>
      </tr>
      <tr>
         <td>
            <div id="multiselect_action_msg">
               {l}Select pages and choose an action to perform.{/l}
               <br />
               <span id="multiselect_action_count">&nbsp;</span> {l}item(s) selected{/l}.
            </div>
         </td>
      </tr>
   </tbody>
</table>
{/strip}