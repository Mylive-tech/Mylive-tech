jQuery(document).ready(function(jQuery) {
	
	//fix for the missing placeholder feature in IE < 10
	if(!placeholderIsSupported()){
		
		jQuery('body')
		.on('focus.mymail', 'form.mymail-form input[placeholder]', function(){
			var el = jQuery(this);
			if (el.val() == el.attr("placeholder"))
				el.val("");
		})
		.on('blur.mymail', 'form.mymail-form input[placeholder]', function(){
			var el = jQuery(this);
			if (el.val() == "")
				el.val(el.attr("placeholder"));
				
		})
		.on('submit.mymail', 'form.mymail-form', function(){
			var form = jQuery(this),
				inputs = form.find('input[placeholder]');
			
				
			jQuery.each(inputs, function(){
				var el = jQuery(this);
				if (el.val() == el.attr("placeholder"))
					el.val("");
			});
			
		})
		
		jQuery('form.mymail-form').find('input[placeholder]').trigger('blur.mymail');
	
	}
	
	function placeholderIsSupported() {
		var test = document.createElement('input');
		return ('placeholder' in test);
	}
	
});
	