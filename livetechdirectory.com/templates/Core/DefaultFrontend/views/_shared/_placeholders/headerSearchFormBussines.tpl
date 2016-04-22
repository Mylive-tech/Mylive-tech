{literal}
<script type="text/javascript">

var servername = "{/literal}{$smarty.const.DOC_ROOT}{literal}"+"/Search/ajaxSearchLinks/";
var servernameLocation = "{/literal}{$smarty.const.DOC_ROOT}{literal}"+"/Search/ajaxSearchAddress/";
function split( val ) {
    return val.split( /,\s*/ );
}
function extractLast( term ) {
    return split( term ).pop();
}

function linkFormatResult(link) {
    var markup = "<hr /><div class='listing-list-item ajax-search-result'>";
    markup += "<div class='link-info'><div class='listing-title'><b><a href='"+link.url+"'>" + link.title + "</b></a></div>";
    if (link.description !== undefined) {
        markup += "<div class='description'><a href='"+link.url+"'>" + link.description + "</a></div>";
    }
    markup += "</div></div>";
    return markup;
}

function linkFormatSelection(link) {
    return link.title;
}

var currEnteredData = null;

jQuery(document).ready(function($){
	$("#autoquery").select2({
		placeholder:"Search for a link",
		minimumInputLength:1,
		ajax:{
			url:servername,
			dataType:'json',
			quietMillis:100,
			allowClear:true,
			data:function (term, page) { // page is the one-based page number tracked by Select2
				return {
					linkQuery: {q: term}, //search term
					page_limit:100 // page size
				};
			},
			results:function (data, page) {
				// notice we return the value of more so Select2 knows if more results can be loaded
                data.result.unshift({'id':currEnteredData, 'title':currEnteredData, 'url':'{/literal}{$smarty.const.DOC_ROOT}{literal}/search?search='+currEnteredData})
				return {results:data.result};
			}
		},
		formatResult:linkFormatResult, // omitted for brevity, see the source of this page
		formatSelection:linkFormatSelection // omitted for brevity, see the source of this page
	});

    jQuery(".select2-search input[type='text']").keyup(function(event){
            currEnteredData = jQuery(this).val();
    });

	$("#autoquery-location").select2({
		placeholder:"Near...",
		minimumInputLength:1,
		ajax:{
			url:servernameLocation,
			dataType:'json',
			quietMillis:100,
			allowClear:true,
			data:function (term, page) { // page is the one-based page number tracked by Select2
				return {
					locQuery: {loc: term}, //search term
					page_limit:100 // page size
				};
			},
			results:function (data, page) {
				// notice we return the value of more so Select2 knows if more results can be loaded
				return {results:data.result};
			}
		},
		formatResult:linkFormatResult, // omitted for brevity, see the source of this page
		formatSelection:linkFormatSelection // omitted for brevity, see the source of this page
	});
});

</script>
{/literal}
<form class="phpld-form headerSearch" action="{$smarty.const.DOC_ROOT}/search" method="get" id="search_form" class="phpld-form">
    <div class="phpld-columnar phpld-equalize" style="overflow: visible">
        <div class="phpld-fbox-text float-left" style="margin-top:5px;">
            <input name="search" maxlength="150" id="autoquery" style="width: 400px;" value="{if !empty($search)}{$search|escape}{/if}" />
        </div>
		<div class="phpld-fbox-text float-left" style="margin-top:5px;">
			<input name="location" maxlength="150" id="autoquery-location" style="width: 400px;" value="{if !empty($search)}{$search|escape}{/if}" />
		</div>
        <div class="phpld-fbox-button float-left ">
            <input class="button phpld-searchbutton" type="submit" value="{l}GO{/l}" />
        </div>
    </div>
</form>