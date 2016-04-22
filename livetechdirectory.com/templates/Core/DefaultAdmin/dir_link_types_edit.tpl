{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}

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

{if $posted}
   <div class="success block">
      {l}Link Type saved.{/l}
   </div>
{/if}

<div class="block">
   <form method="post" action="" enctype="multipart/form-data" id="submit_form">
   <table class="formPage">

   {if isset($action) and ($action eq 'N' or $action eq 'E')}
   <thead>
      <tr>
         <th colspan="2">
            {if $action eq 'N'}
               {l}Create new Link Type{/l}
            {elseif $action eq 'E'}
               {l}Edit Link Type{/l}
            {/if}
         </th>
      </tr>
  </thead>
  {/if}

   <tbody>
       {if $smarty.const.PAYPAL_ACCOUNT eq ''}
       <tr>
           <td colspan="2"><div class="error block">{l}Your PAYPAL ACCOUNT has not been filled in. If any of your link types have their price set up, front end submission will not work at all. You may set your PAYPAL ACCOUNT {/l}<a href="{$smarty.const.DOC_ROOT}/conf_settings.php?c=9&r=1">{l}here{/l}</a>.</div></td>
       </tr>
       {/if}
      <tr>
         <td class="label required"><label for="NAME">{l}Name{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="NAME" name="NAME" value="{$NAME|escape|trim}" class="text" />
         </td>
      </tr>
      
      
      <tr>
         <td class="label required"><label for="DEEP_LINKS">{l}Deep Links{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="DEEP_LINKS" name="DEEP_LINKS" value="{if $DEEP_LINKS > 0}{$DEEP_LINKS|escape|trim}{/if}" class="text" />
            <p>{l}Number of additional URLs. Leave empty for no deep links.{/l}</p>
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="MULTIPLE_CATEGORIES">{l}Multiple Categories{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="MULTIPLE_CATEGORIES" name="MULTIPLE_CATEGORIES" value="{if $MULTIPLE_CATEGORIES > 0}{$MULTIPLE_CATEGORIES|escape|trim}{/if}" class="text" />
            <p>{l}Number of max allowed categories. Leave empty for 1 default category.{/l}</p>
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="COUNT_IMAGES">{l}Count Of Images{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="COUNT_IMAGES" name="COUNT_IMAGES" value="{if $COUNT_IMAGES > 0}{$COUNT_IMAGES|escape|trim}{/if}" class="text" />
            <p>{l}Number of IMAGES. Leave empty for no Additional images.{/l}</p>
         </td>
      </tr>
      <tr>
         <td class="label"><label for="DESCRIPTION">{l}Description{/l}:</label></td>
         <td class="smallDesc">
               {include file=$smarty.const.ADMIN_TEMPLATE|cat:"/rte.tpl" NAME="DESCRIPTION" VALUE=$DESCRIPTION}
             
         </td>
      </tr>

{if $IMGTN}
		<tr>
         <td class="label required"><label for="IMGTN">{l}Current Image{/l}:</label></td>
         <td class="smallDesc">
         	<a href="{$IMG}" class="thickbox"><img src="{$IMGTN}" border="0" alt="Current Link Image" /></a>
         </td>
      </tr>      
   {/if}
   
{* temporarly removed
      <tr>
     <td class="label"><label for="IMG">{l}Upload Image{/l}:</label></td>
     <td class="smallDesc">
            <input type="file" name="IMG" id="IMG" value="{$IMG}" class="text"/><br />
     </td>
  </tr>
*}
      
		<tr>
         <td class="label required"><label for="FEATURED">{l}Featured{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$no_yes selected=$FEATURED name="FEATURED" id="FEATURED"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="NOFOLLOW">{l}No Follow{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$no_yes selected=$NOFOLLOW name="NOFOLLOW" id="NOFOLLOW"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="NOFOLLOW">{l}Show Meta Fields In Front{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$no_yes selected=$SHOW_META name="SHOW_META" id="SHOW_META"}
         </td>
      </tr>      
      <tr>
         <td class="label required"><label for="PRICE">{l}Price{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="PRICE" name="PRICE" value="{$PRICE|escape|trim}" class="text" />
            <p>{l}Leave empty for free links.{/l}</p>
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="PAY_UM">{l}Time Unit{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$payment_um selected=$PAY_UM name="PAY_UM" id="PAY_UM"}
            <p class="small">Time unit used for paid links validity period<br /><b>(note: this option is only needed paid links)</b></p>
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="STATUS">{l}Status{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$stats selected=$STATUS name="STATUS" id="STATUS"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="REQUIRE_APPROVAL">{l}Require Admin Approval{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$no_yes selected=$REQUIRE_APPROVAL name="REQUIRE_APPROVAL" id="REQUIRE_APPROVAL"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="LIST_TEMPLATE">{l}List Template{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$lists selected=$LIST_TEMPLATE name="LIST_TEMPLATE" id="LIST_TEMPLATE"}
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="DETAILS_TEMPLATE">{l}Details Template{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$details selected=$DETAILS_TEMPLATE name="DETAILS_TEMPLATE" id="DETAILS_TEMPLATE"}
         </td>
      </tr>
      
      <tr>
         <td class="label required"><label for="THUMBNAIL_GRID">{l}Default Grid Image width{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="THUMBNAIL_GRID" name="THUMBNAIL_GRID" value="{$DEFAULT_THUMBNAIL_GRID|escape|trim}" class="text" />
         </td>
      </tr>
      <tr>
         <td class="label required"><label for="THUMBNAIL_LIST">{l}Default List Image width{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="THUMBNAIL_LIST" name="THUMBNAIL_LIST" value="{$DEFAULT_THUMBNAIL_LIST|escape|trim}" class="text" />
         </td>
      </tr>     
      <tr>
         <td class="label required"><label for="PAGERANK_MIN">{l}Minimum PageRank{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="PAGERANK_MIN" name="PAGERANK_MIN" value="{$PAGERANK_MIN|escape|trim}" class="text" />
            <p>{l}Minimum pagerank for link submission. Leave blank for no restriction.{/l}</p>
            <div class="error block">{l}This feature is considered Beta and may not work as expected. Enable at your own risk.{/l}</div>
         </td>
      </tr>
   </tbody>

   <tfoot>
      <tr>
         <td><input type="reset" id="reset-link-submit" name="reset" value="{l}Reset{/l}" alt="{l}Reset form{/l}" title="{l}Reset form{/l}" class="button" /></td>
         <td><input type="submit" id="send-link-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save link{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
   
   <input type="hidden" name="id" value="{$id}" />
   <input type="hidden" name="formSubmitted" value="1" />
   <input type="hidden" name="submit_session" value="{$submit_session}" />
   
   </form>
</div>
{/strip}