// Original JavaScript by Chris Coyier
	// Updated October 2010 by Stewart Heckenberg & Chris Coyier
 
	$(".home li.home").removeClass("home").addClass("current_page_item");
    $("#content").append("<img src='/ajax-loader.gif' id='ajax-loader' />");
	
	$("#s").focus(function() {
        if ($(this).val() == "Search...") {
            $(this).val("");
        }
    });
    
    if ($(".widget_categories li").length%2 != 0) {
        $(".widget_categories ul").append("<li><a>&nbsp;</a></li>");
    }
    
    if ($(".widget_tag_cloud a").length%2 != 0) {
        $(".widget_tag_cloud").append("<a>&nbsp;</a>");
    }
	
	// The reason this JavaScript is in footer.php instead of its own file is basically the next line.
	var 
	    $mainContent     = $("#primary"),
	    $ajaxSpinner     = $("#ajax-loader"),
	    $searchInput     = $("#s"),
	    $allLinks        = $("a"),
	    $el;
 
	$('a:urlInternal').live('click', function(e) { 
	
		$el = $(this);
	
		if ((!$el.hasClass("comment-reply-link")) && ($el.attr("id") != 'cancel-comment-reply-link')) { 		
			var path = $(this).attr('href').replace(base, '');
			$.address.value(path);
			$(".current_page_item").removeClass("current_page_item");
			$allLinks.removeClass("current_link");
			$el.addClass("current_link").parent().addClass("current_page_item");
			return false;
		}
		
		// Default action (go to link) prevented for comment-related links (which use onclick attributes)
		e.preventDefault();
		
	});  
	
	$('#searchform').submit(function() {  
		var s = $searchInput.val();
		if (s) {
			var query = '/?s=' + s;
			$.address.value(query);  
		}
		return false;
	});  
 
	$.address.change(function(event) {  
		if (event.value) {
			$ajaxSpinner.fadeIn();
			$mainContent
				.empty()
				.load(base + event.value + ' .entry-content', function() {
					$ajaxSpinner.fadeOut();
					$mainContent.fadeIn();
				});  
		} 
 
		var current = location.protocol + '//' + location.hostname + location.pathname;
		if (base + '/' != current) {
			var diff = current.replace(base, '');
			location = base + '/#' + diff;
		}
	}); 