{literal}
<script type="text/javascript">
jQuery(function($) {
	$(document).ready(function(){
	
	var first_check = true;

	$("#login-form").validate({
			debug: false,
			onkeyup: false,
			errorClass: "loginError",
			errorElement: "div",
			rules: {
					LOGIN: {
						required: true
					},
					EMAIL: {
						required: true,
						email: true
					}
			},
	});
	$.ajaxSetup( {
            async: false
         } );
	});

});
</script>
{/literal}