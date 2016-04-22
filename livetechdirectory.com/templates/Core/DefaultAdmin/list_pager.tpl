{strip}
{* Pagination *}
{if !empty($paginate) and $paginate.page_total > 1}
   <div class="navig block">
      <div class="main-paging">

         {* Display Paging Header *}
         <div class="paging-info">{l}Items {/l}{$paginate.first}-{$paginate.last}{l} out of {/l}{$paginate.total}{l} displayed.{/l}</div>
         

         {* Display Paging Links *}
         <div class="paging-links">
            {paginate_first} {paginate_prev} {paginate_middle format="page" prefix="" suffix="" link_prefix=" " link_suffix=" " current_page_prefix="[" current_page_suffix="]"} {paginate_next} {paginate_last}
         </div>

      </div>

      <div class="paging-total">{l}Total records:{/l} <span class="paging-total-count">{$paginate.total}</span></div>
   </div>
{/if}
{/strip}