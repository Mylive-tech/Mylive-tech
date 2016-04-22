<!-- Error and confirmation messages -->
{include file="messages.tpl"}

{strip}
{if (isset($error) and $error gt 0) or !empty($sql_error)}
   <div class="error block">
      <h2>{l}Error{/l}</h2>
      <p>{l}An error occured while saving.{/l}</p>
      {if !empty($errorMsg)}
         <p>{$errorMsg|escape}</p>
      {/if}
      {if !empty($sql_error)}
         <p>{l}The database server returned the following message:{/l}</p>
         <p>{$sql_error|escape}</p>
      {/if}
   </div>
{/if}

{if not $id}
   {if $posted}
      <div class="success block">
         {l}Category saved.{/l}
      </div>
   {/if}

<div class="block">
   <form method="post" action="">
   <table class="formPage">
	<thead>
		<tr>
			<th colspan="2">
				{l}Close category to activities{/l}
			</th>
		</tr>
	</thead>
   
   <tbody>
    	<tr>
    		<td class="label">
    			<label for="CLOSED_TO_LINKS">{l}Closed to Link Submission{/l}:</label>
    		</td>
    		<td class="smallDesc">
    			<input type="checkbox" id="CLOSED_TO_LINKS" name="CLOSED_TO_LINKS" value="1" {if $CLOSED_TO_LINKS == 1}checked="checked"{/if} />
 				<p class="msg notice info">{l}Check this to prevent link submission to this category from the site's front end. The Admin will still be able to submit links.{/l}</p>
    		</td>
		</tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-categ-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-categ-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save category{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   <input type="hidden" name="formSubmitted" value="1" />
</form>
</div>
{else}
<div class="block">
   <form method="post" action="" name="delete">
   <input type="hidden" name="action" class="hidden" value="D:{$id}" />
   <table class="formPage">
      <tbody>
         <tr>
            <td colspan="2">{l}The category contains {/l}{$count_categs}{l} subcategorie(s) {/l}{$count_links}{l} link(s) and {/l}{$count_articles}{l} article(s){/l}.<br />{l}Cannot proceed with delete until further action is taken{/l}:</td>
         </tr>
         <tr>
            <td class="label"><label for="do-categ-select">{l}Move all to{/l}</label> <input type="radio" id="do-categ-select" name="do" value="move"{if $do eq "move"} checked="checked"{/if} /></td>
            <td class="smallDesc">
               <!-- Load category selection -->
               {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/admin_category_select.tpl"}

               {if $error}
                  <span class="errForm">{l}Please select another category.{/l}</span>
               {/if}
            </td>
         </tr>
         <tr>
            <td class="label" colspan="2"><label for="do-delete-all">{l}Delete all{/l}</label> <input type="radio" id="do-delete-all" name="do" value="delete"{if $do eq "delete"} checked="1"{/if} /></td>
         </tr>
      </tbody>
      <tfoot>
         <tr>
            <td>
               <input type="submit" name="delete" value="{l}Delete{/l}" onclick="return categ_rm_confirm('{escapejs}{l}Are you sure you want to remove this category?{/l}\n{l}Note: categories can not be restored after removal!{/l}{/escapejs}');" title="{l}Remove Category{/l}" class="action delete button" />
            </td>
            <td>
               <input type="submit" name="cancel" value="{l}Cancel{/l}" title="{l}Cancel category removal{/l}" class="button" />
            </td>
         </tr>
      </tfoot>
   </table>
   </form>
</div>
{/if}
{/strip}