(function ( $ ) {
 	var settings;
    $.fn.category_cycle = function( options ) {
    	settings = $.extend({
            count           : 25,
            hierarchy       : {},
            relationships   : {},
            parents         : {},
            archive_display : 'subcategories',
            breadcrumbs     : false,
            breadcrumbs_h   : false
        }, options );
    	return this.each(function() {
    		if ( $(this).data('category_cycle') == 'init' )  return false;

	    	var $_this  = $(this);
	    	var $_elems = $_this.find('.category_cycle');
	        $_this.data('elems', $_elems);
	        $_this.data('active_category', 0);
	        filter_hierarchy($_elems, 0);

	        $_this.find('.open_category').click(function(event) {
	        	var catid = $(this).data('catid');
	        	var title = $(this).data('title');
	        	$_this.data('active_category', catid);
	        	filter_hierarchy($_elems, catid, title);
	        });
	        $('.next-grid-layout').click(function(event) {
	        	var parent      = settings.breadcrumbs.find('.cat_title').last().data('parent');
	        	var active_page = $('#nav_layout_cycle .activeSlide').data('page');
	        	var count_page  = $('#nav_layout_cycle a').length;
	        	if( (active_page+1) == count_page  )
	        		active_page = 0;
	        	else
	        		active_page++;

	        	$('#nav_layout_cycle .activeSlide').removeClass('activeSlide');
	        	$('#nav_layout_cycle a').eq(active_page).addClass('activeSlide');

	        	var filter = get_filter ($_elems, parent);
    			$_elems.filter( filter ).slice( (active_page*settings.count), (active_page*settings.count)+settings.count ).show();

	        	return false;
	        });
	        $('.previous-grid-layout').click(function(event) {
	        	var parent      = settings.breadcrumbs.find('.cat_title').last().data('parent');
	        	var active_page = $('#nav_layout_cycle .activeSlide').data('page');
	        	var count_page  = $('#nav_layout_cycle a').length;
	        	
	        	if( (active_page-1) < 0  )
	        		active_page = count_page-1;
	        	else
	        		active_page--;

	        	$('#nav_layout_cycle .activeSlide').removeClass('activeSlide');
	        	$('#nav_layout_cycle a').eq(active_page).addClass('activeSlide');

	        	var filter = get_filter ($_elems, parent);
    			$_elems.filter( filter ).slice( (active_page*settings.count), (active_page*settings.count)+settings.count ).show();

	        	return false;
	        });
	        $('#nav_layout_cycle').on('click', 'a', function(event) {
	        	var parent      = settings.breadcrumbs.find('.cat_title').last().data('parent');
	        	var active_page = $(this).data('page');
	        	$('#nav_layout_cycle .activeSlide').removeClass('activeSlide');
	        	$('#nav_layout_cycle a').eq(active_page).addClass('activeSlide');

	        	var filter = get_filter ($_elems, parent);
    			$_elems.filter( filter ).slice( (active_page*settings.count), (active_page*settings.count)+settings.count ).show();
	        	return false;
	        });
	        settings.breadcrumbs.on('click', '.cat_title', function(event) {
	        	var parent = $(this).data('parent');
	        	$(this).nextAll().remove();
	        	$_this.data('active_category', parent);
	        	var filter = filter_hierarchy($_elems, parent);
	        });

	        $_this.data('category_cycle', 'init');
	    });
    };
    function get_filter (elems, parent, title) {
    	var filter = '';
    	elems.hide();
    	
    	if(parent == 0){
    		settings.breadcrumbs.find('*').not(settings.breadcrumbs_h).remove();
	    	$.each(settings.hierarchy, function(index, val) {
	    		if(typeof settings.parents[index] != 'undefined'){
		    		if(filter != '')
		    			filter += ', ';
		    		filter += "#category_"+index;
	    		}
	    	});
	    }else {
	    	if(typeof title != 'undefined' && title != '')
	    		settings.breadcrumbs.append('<span class="sep"> → </span><span data-parent="'+parent+'" class="cat_title">'+title+'</span>');

	    	if( typeof settings.hierarchy[parent] != 'undefined' && settings.archive_display != ''){
		    	$.each( settings.hierarchy[parent], function(index, val) {
		    		if(filter != '')
		    			filter += ', ';
		    		filter += "#category_"+val;
		    	});
	    	}
	    }

	    if( ( settings.archive_display == 'both' || settings.archive_display == '' || typeof settings.hierarchy[parent] == 'undefined' || settings.hierarchy[parent].length == 0) && parent != 0 ){
		    if( typeof settings.relationships[parent] != 'undefined'){
		    	$.each(settings.relationships[parent], function(index, val) {
		    		if(filter != '')
		    			filter += ', ';
		    		filter += "#product_"+val;
		    	});
		    }
	    }
	    return filter;
    }
    function filter_hierarchy (elems, parent, title) { 
    	var filter = get_filter (elems, parent, title);
	    elems.filter( filter ).slice( 0, settings.count ).show();
	    if(elems.filter( filter ).length > settings.count){
	    	var count = elems.filter( filter ).length;
	    	var pages = Math.ceil( count/settings.count );
	    	$('.previous-next-toggles').show();
	    	var nav = '';
	    	for (var i = 1; i <= pages; i++) {
	    		nav += '<a href="#" data-page="'+(i-1)+'">'+i+'</a>';
	    	};
	    	$('#nav_layout_cycle').html(nav);
	    	$('#nav_layout_cycle a').first().addClass('activeSlide');
	    }else{
	    	$('.previous-next-toggles').hide();
	    	$('#nav_layout_cycle').html('');
	    }
    }

    function pagination (elems, parent, offset) {
    	// body...
    }
 
}( jQuery ));