{* Pagination *}
{if !empty($MainPaging) and $MainPaging.page_total > 1}
<div class="navig">
    <div class="mainPaging">
    {* Display Paging Links *}
        <div class="pagingLinks">
            {paginate_prev id="MainPaging"} {paginate_middle id="MainPaging" format="page" prefix="" suffix="" link_prefix=" " link_suffix=" " current_page_prefix="[" current_page_suffix="]"} {paginate_next id="MainPaging"}
        </div>
    </div>

    {l}Total records:{/l} {$MainPaging.total}
</div>
{/if}
