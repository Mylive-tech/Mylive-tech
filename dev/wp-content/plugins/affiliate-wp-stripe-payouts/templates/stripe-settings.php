<?php
$affiliate_id  = affwp_get_affiliate_id();
$user_id       = get_current_user_id();
?>
<style>
#affwp-payout-options-wrap legend { 
	display:block; font-size: 120%; 
	line-height: 1; font-weight: bold; 
	width: 100%; 
	margin: 0 0 21px 0; 
	padding: 0;
}
#affwp-payout-options-wrap label {
	font-weight: bold;
	display:block; 
	position: relative; 
	line-height: 100%; 
	font-size: 95%; 
	margin: 0 0 5px; 
}
#affwp-payout-options-wrap label:after { 
	display: block; 
	visibility: hidden; 
	float: none; clear: both; 
	height: 0; 
	text-indent: -9999px; 
	content: "."; 
}
#affwp-payout-options-wrap span.affwp-description { 
	color: #666; 
	font-size: 80%; 
	display: block; 
	margin: 0 0 5px; 
}
#affwp-payout-options-wrap input.affwp-input, 
#affwp_checkout_form_wrap textarea.affwp-input { 
	display:block; width: 80%; 
}
</style>
<h4><?php _e( 'Payout Settings', 'affwp-stripe-payouts' ); ?></h4>

<div id="affwp-payout-options-wrap">

	<p><?php _e( 'Use this form to attach a debit card to your affiliate account. All affiliate earnings will be deposited directly onto this card. <strong>U.S. affiliates only.</strong>', 'affwp-stripe-payouts' ); ?></p>

	<?php affiliate_wp()->templates->get_template_part( 'card', 'form' ); ?>

</div>