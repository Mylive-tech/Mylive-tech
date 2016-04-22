<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/headerSearchForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:69423444656548c5e488cf9-99859804%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0e5eafe024c7558338f7fc88cd9c4c1d1e79917b' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/headerSearchForm.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '69423444656548c5e488cf9-99859804',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'search' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e4c2334_66690941',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e4c2334_66690941')) {function content_56548c5e4c2334_66690941($_smarty_tpl) {?>

<script type="text/javascript">

var servername = "<?php echo @DOC_ROOT;?>
"+"/Search/ajaxSearchLinks/";

function split( val ) {
    return val.split( /,\s*/ );
}

function extractLast( term ) {
    return split( term ).pop();
}

function linkFormatResult(link) {
    //<![CDATA[
    var markup = "<hr \/><div class='listing-list-item ajax-search-result'>";
    markup += "<div class='link-info'><div class='listing-title'><a href='"+link.url+"'><b>" + link.title + "</b></a></div>";
    if (link.description !== undefined) {
        markup += "<div class='description'><a href='"+link.url+"'>" + link.description + "</a></div>";
    }
    markup += "</div></div>"
    return markup;
    //]]>
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
                    linkQuery: {q: term, loc: $("#autoquery-location").val()}, //search term
                    page_limit:100 // page size
                };
            },
        results:function (data, page) {
            // notice we return the value of more so Select2 knows if more results can be loaded
            for (x in data.result) {
                data.result[x].id = data.result[x].url;
            }
            data.result.unshift({'id':data.url, 'title':currEnteredData, 'url':'<?php echo @DOC_ROOT;?>
/search?search='+currEnteredData})
            return {results:data.result};
        }
    },

    formatResult:linkFormatResult, // omitted for brevity, see the source of this page

    formatSelection:linkFormatSelection // omitted for brevity, see the source of this page

    }).on('change', function(event){

        document.location.href = $(this).val();

    });



    jQuery(".select2-search input[type='text']").keyup(function(event){
        currEnteredData = jQuery(this).val();

    });

	jQuery("#search_form").submit(function(){
		jQuery("#autoquery").val(currEnteredData);		
	});	

});



</script>



<?php if (@OPTIONS_SEARCH_FIELD==0){?>
<form class="phpld-form headerSearch" action="<?php echo @DOC_ROOT;?>
/search/basic" method="get" id="search_form">
	<div class="phpld-columnar phpld-equalize" style="overflow: visible">
    	<div class="phpld-fbox-text float-left" style="margin-top:5px;">
        	<input name="search" maxlength="150"  style="width: 400px; margin-top: 2px; height: 25px;" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" />
    	</div>
    	<div class="phpld-fbox-button float-left " style="position:absolute; margin-top:7px;">  
        	<input class="button phpld-searchbutton" type="submit" value="GO" />
    	</div>
	</div>
</form>
<?php }elseif(@OPTIONS_SEARCH_FIELD==1){?>
 <form class="phpld-form headerSearch" action="<?php echo @DOC_ROOT;?>
/search" method="get" id="search_form">
	<div class="phpld-columnar phpld-equalize" style="overflow: visible">
     	<div class="phpld-fbox-text float-left" style="margin-top:5px;">
        	<input name="search" maxlength="150" id="autoquery" style="width: 400px;" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" />
    	</div>
    	<div class="phpld-fbox-button float-left " style="position:absolute; margin-top:7px;">  
        	<input class="button phpld-searchbutton" type="submit" value="GO" />
    	</div>
	</div>
</form>
<?php }elseif(@OPTIONS_SEARCH_FIELD==2){?>


<script type="text/javascript">

var servername = "<?php echo @DOC_ROOT;?>
"+"/Search/ajaxSearchLinks/";
var servernameLocation = "<?php echo @DOC_ROOT;?>
"+"/Search/ajaxSearchAddress/";
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
                data.result.unshift({'id':currEnteredData, 'title':currEnteredData, 'url':'<?php echo @DOC_ROOT;?>
/search?search='+currEnteredData})
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

<form class="phpld-form headerSearch" action="<?php echo @DOC_ROOT;?>
/search" method="get" id="search_form" class="phpld-form">
    <div class="phpld-columnar phpld-equalize" style="overflow: visible">
        <div class="phpld-fbox-text float-left" style="margin-top:5px;">
            <input name="search" maxlength="150" id="autoquery" style="width: 200px;" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" />
            <input name="location" maxlength="150" id="autoquery-location" style="width: 200px;" value="<?php if (!empty($_smarty_tpl->tpl_vars['search']->value)){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" />
        </div>
		
        <div class="phpld-fbox-button float-left ">
            <input class="button phpld-searchbutton" type="submit" value="GO" />
        </div>
    </div>
</form>




<?php }?><?php }} ?>