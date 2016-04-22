    <!-- Build link action submit buttons -->
{strip}
<table class="list">
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
               {formtool_checkall name="multiselect_checkbox[]" checkall_text="{l}Check All{/l}" uncheckall_text="{l}Uncheck All{/l}" class="btn" id="checkallButton"}
            </fieldset>
			{if $rights.editLink eq 1}
            <fieldset class="link_action">
               <legend>{l}Change Status{/l}</legend>
               <input type="button" name="active" id="activeButton" value="{l}Active{/l}" title="{l}Set selected links as active{/l}" class="button" onclick="selected_links_active_confirm('{escapejs}{l}Are you sure you want to change status of selected links to active?{/l}{/escapejs}');" />

               <input type="button" name="pending" id="pendingButton" value="{l}Pending{/l}" title="{l}Set selected links as pending{/l}" class="button" onclick="selected_links_pending_confirm('{escapejs}{l}Are you sure you want to change status of selected links to pending?{/l}{/escapejs}');" />

               <input type="button" name="inactive" id="inactiveButton" value="{l}Inactive{/l}" title="{l}Set selected links as inactive{/l}" class="button" onclick="selected_links_inactive_confirm('{escapejs}{l}Are you sure you want to change status of selected links to inactive?{/l}{/escapejs}');" />
            </fieldset>
			{/if}
			{if $rights.delLink eq 1}
            <fieldset class="link_action">
               <legend>{l}Action{/l}</legend>
               <input type="button" name="remove" id="removeButton" value="{l}Remove{/l}" title="{l}Remove selected links!{/l} {l}Note: links can not be restored after removal!{/l}" class="button" onclick="selected_links_remove_confirm('{escapejs}{l}Are you sure you want to remove selected links?{/l}\n{l}Note: links can not be restored after removal!{/l}{/escapejs}');" />
            </fieldset>
			{/if}
            {if $linkNotifButtons}
            <fieldset class="link_action">
               <legend>{l}Notifications{/l}</legend>
               <input type="button" name="expired" id="expiredButton" value="{l}Notify Expired{/l}" title="{l}Notify owner(s) of expired reciprocal link page.{/l}" class="button" onclick="selected_links_expired_confirm('{escapejs}{l}Are you sure you want to notify owner(s) of expired reciprocal link link pages?{/l}{/escapejs}');" />
            </fieldset>
            {/if}

			<!-- 
            <fieldset class="link_action">
               <legend>{l}Banning{/l}</legend>
               <input type="button" name="banip" id="banIpButton" value="{l}Ban IPs{/l}" title="{l}Ban IPs{/l}" class="button" onclick="selected_banip_confirm('{l}Are you sure you want to ban selected IPs?{/l}');" />
               <input type="button" name="bandomain" id="banDomainButton" value="{l}Ban Domains{/l}" title="{l}Ban domains{/l}" class="button" onclick="selected_bandomain_confirm('{l}Are you sure you want to ban selected domains?{/l}');" />
            </fieldset>
             -->
            {if $rights.delLink eq 1} 
            <fieldset class="link_action">
               <legend>{l}Spam{/l}</legend>
               <input type="button" name="spamlink" autosubmit-disabled="1" id="spamLinkButton" value="{l}Remove as Spam{/l}" title="{l}Remove as Spam{/l}" class="button" onclick="if(confirm('{escapejs}{l}Are you sure you want to remove selected links? Associated IPs and domains will be banned.{/l}{/escapejs}'))multiple_action('spamlink')" />
            </fieldset>
			{/if}
			{if $rights.editLink eq 1}
            <fieldset class="link_action">
               <legend>{l}Category{/l}</legend>
               <!-- Load category selection -->
               {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl"}
               <input type="button" name="changecategory" id="changeLinkCategoryButton" value="{l}Change category{/l}" title="{l}Change category{/l}" class="button" onclick="selected_change_categ_confirm('{escapejs}{l}Are you sure you want to change category?{/l}{/escapejs}');" />
            </fieldset>
            {/if}
         </td>
      </tr>
      <tr>
         <td>
            <div id="multiselect_action_msg">
               {l}Select links and choose an action to perform.{/l}
               <br />
               <span id="multiselect_action_count">&nbsp;</span> {l}item(s) selected{/l}.
            </div>
         </td>
      </tr>
   </tbody>
</table>
{/strip}