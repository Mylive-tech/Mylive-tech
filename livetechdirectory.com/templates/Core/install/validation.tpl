{literal}
<script type="text/javascript">
jQuery(function($) {
	$(document).ready(function(){
	
	$("#install_form").validate({
			debug: false,
			errorElement: "label",
			errorClass: "validation_error",
			{/literal}{$validators}{literal}
	});
	
	jQuery.validator.addMethod("password_match", function(value, element) { 
		var p1 = $("input[name='admin_password']").getValue();
		var p2 = $("input[name='admin_passwordc']").getValue();
		if (p1 == p2)
			return true;
		else
			return false;
	}, {/literal}"{escapejs}{l}Password confirmation does not match. Please type again.{/l}{/escapejs}"{literal});
	
	jQuery.validator.addMethod("adminuser", function(value, element) { 
		var v = $(element).val();
		var reg = new RegExp("^\\w{4,25}$");
		var m = v.match(reg);
		return m;
	}, {/literal}"{escapejs}{l}Invalid username. Please see the field help for more details.{/l}{/escapejs}"{literal});
	
	jQuery.extend(jQuery.validator.messages, {
	{/literal}
        required: 		"{escapejs}{l}This field is required.{/l}{/escapejs}",
        url: 					"{escapejs}{l}Invalid URL.{/l}{/escapejs}",
        email:				"{escapejs}{l}Invalid email address format.{/l}{/escapejs}",
        admin_password: "{escapejs}{l}Invalid password. Please see the field help for more details.{/l}{/escapejs}",
        admin_passwordc: "{escapejs}{l}Password confirmation does not match. Please type again.{/l}{/escapejs}"
   {literal}
});

        $.ajaxSetup( {
            async: false
         } );

	});
});
</script>
{/literal}