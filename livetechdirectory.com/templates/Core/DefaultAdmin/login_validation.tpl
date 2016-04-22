{literal}
<script type="text/javascript">
jQuery(function($) {
	$(document).ready(function(){
	
	var first_check = true;
	
	$("#login_failed").hide();
	
	$("#login-form").validate({
			debug: false,
			onkeyup: false,
			errorClass: "loginError",
			errorElement: "div",
			rules: {
					user: {
						required: true
					},
					pass: {
						required: true
					}
			},
			
			messages: {
				
			},
			
		
	});
	
	$("#login-form").submit(function(event) { 
	
	if (!event.force_it) {
		$.ajax({
  				url: "login_validation.php",
  				type: "post",
  				data: ({
  					action: "check_login",
          		user: function() { return $("input[name='user']").val(); },
          		password: function() { return $("input[name='pass']").getValue(); },
  				}),
  				cache: false,
 			 	success: function(response){
 			 		if (response == '1') {
 			 			$("#login_failed").hide();
 			 			var event = jQuery.Event("submit");
						event.force_it = "1";
 			 			$("#login-form").trigger(event);

    				}
    				else
    					$("#login_failed").show();

  					}
				}); 
			first_check = false;
	}
	
	if ($("#login_failed").is(":hidden") && !first_check && $("#login-form").valid()) 
		return true;
	else
		return false;

	});
	
	function check_auth() {
		$.ajax({
  				url: "login_validation.php",
  				type: "post",
  				data: ({
  					action: "check_login",
          		user: function() { return $("input[name='user']").val(); },
          		password: function() { return $("input[name='pass']").getValue(); },
  				}),
  				cache: false,
 			 	success: function(response){
 			 		if (response == '1') {
 			 			$("#login_failed").hide();
 			 			return true;
    				}
    				else
    					$("#login_failed").show();
    					return false;
  					}
				});  
	}
	
	jQuery.validator.addMethod("is_not_equal", function(value, element) { 
		var p1 = $("input[name='SYMBOLIC_ID']").getValue();
		var p2 = $("input[name='PARENT_ID']").getValue();
		if (p1 != p2)
			return true;
		else
			return false;
	}, {/literal}"{escapejs}{l}Please select a category.{/l}{/escapejs}"{literal});

        $.ajaxSetup( {
            async: false
         } );

	});
});
</script>
{/literal}