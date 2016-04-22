{capture name="title"}{l}Link Payment{/l}{/capture}
{capture assign="in_page_title"}{l}Link Payment{/l}{/capture}
{capture assign="description"}{l}Submit a new link to the directory{/l}{/capture}

{strip}
{if $paypal}
	<li class="group">{l}Processing Payment...{/l}</li>
	<li><form name="form" action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal">
		{if isset ($SubscriptionEnabled) and $SubscriptionEnabled eq 1 and $PAYMENT.SUBSCRIBED eq 1}
			<input type="hidden" name="cmd" value="_xclick-subscriptions" />
			{else}
			<input type="hidden" name="cmd" value="_xclick" />
		{/if}

		<input type="hidden" name="rm" value="2">
		<input type="hidden" name="business" value="{$smarty.const.PAYPAL_ACCOUNT}" />
		<input type="hidden" name="item_name" value="Link to {$URL|trim} for {$smarty.const.DIRECTORY_TITLE|escape|trim}" />
		<input type="hidden" name="item_number" value="{$ID}" />
		<input type="hidden" name="amount" value="{$PAYMENT.AMOUNT}" />
		<input type="hidden" name="quantity" value="{$PAYMENT.QUANTITY}" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="return" value="http://{$smarty.server.SERVER_NAME}{$smarty.const.DOC_ROOT}/payment/paid/?pid={$ID}" />
		<input type="hidden" name="cancel_return" value="http://{$smarty.server.SERVER_NAME}{$smarty.const.DOC_ROOT}/payment/cancel/?pid={$ID}" />
		<input type="hidden" name="notify_url" value="http://{$smarty.server.SERVER_NAME}{$smarty.const.DOC_ROOT}/payment/callback/?pid={$PAYMENT.ID}" />
		<input type="hidden" name="custom" value="{$PAYMENT.ID}" />
		<input type="hidden" name="no_note" value="1" />
		<input type="hidden" name="email" value="{$OWNER_EMAIL|escape|trim}" />
		<input type="hidden" name="currency_code" value="{$smarty.const.PAY_CURRENCY_CODE}" />

		{if isset ($SubscriptionEnabled) and $SubscriptionEnabled eq 1 and $PAYMENT.SUBSCRIBED eq 1}
			<input type="hidden" name="a3" value="{$PAYMENT.TOTAL}" />
			<input type="hidden" name="p3" value="{$Subscription.PERIOD}" />
			<input type="hidden" name="t3" value="{$Subscription.UM}" />
			<input type="hidden" name="src" value="1" />
			<input type="hidden" name="sra" value="1" />
		{/if}
	</form>
</li>
	<script type="text/javascript">{literal}
		function mvtopp () {
		var frm = document.getElementById("paypal");
		frm.submit();
		}
		window.onload = mvtopp;
		{/literal}
	</script>
{else}
	{if not $ID}
		{l}Invalid link id.{/l}
	{else}
	<li><form method="post" action="" id="submit_payment_form">
		<table border="0" class="formPage">
			{if $error}
				<tr>
					<td colspan="2" class="err">
						{l}An error occured while saving the link payment data.{/l}
					</td>
				</tr>
			{/if}
			<tr>
				<td class="label">{l}Title{/l}:</td>
				<td class="field">{$TITLE|escape|trim}</td>
			</tr>

			{if $URL|escape|trim}
				<tr>
					<td class="label">{l}URL{/l}:</td>
					<td class="field">{$URL|escape|trim}</td>
				</tr>
			{/if}

			{if $DESCRIPTION|escape|trim}
				<tr>
					<td class="label">{l}Description{/l}:</td>
					<td class="field">{$DESCRIPTION|escape|trim}</td>
				</tr>
			{/if}

			{if $OWNER_NAME|escape|trim}
				<tr>
					<td class="label">{l}Your Name{/l}:</td>
					<td class="field">{$OWNER_NAME|escape|trim}</td>
				</tr>
			{/if}

			{if $OWNER_EMAIL|escape:decentity|trim != ''}
				<tr>
					<td class="label">{l}Your Email{/l}:</td>
					<td class="field">{$OWNER_EMAIL|escape:decentity|trim}</td>
				</tr>
			{/if}

			{if $SubscriptionEnabled eq 1}
				<tr>
					<td class="label">{l}Subscribe{/l}:</td>
					<td class="field">
						{html_radios name="subscribe" options=$SubscribeOptions selected=$subscribe separator="<br />"}
					</td>
				</tr>
			{/if}

			<tr>
				<td class="label">{l}Unit price{/l}:</td>
				<td class="field">{$smarty.const.HTML_CURRENCY_CODE}{$price}  {if $SubscriptionEnabled eq 1}every {$SubscriptionPeriod} {$SubscriptionUm}{/if}</td>
			</tr>

			<tr>
				<td class="label">{l}Quantity{/l}</td>
				<td class="field">
					{if $SubLength ne 5}
						<input type="text" name="quantity" value="{$quantity}" size="10" maxlength="5" class="text" />
						{else}
						{l}n/a{/l}<input type="hidden" name="quantity" value="1" />
					{/if}
				</td>
			</tr>

			<tr>
				<td class="buttons" colspan="2" align="center">
					<input type="hidden" name="formSubmitted" value="1" />
					<input type="image" name="paypal" src="https://www.paypal.com/en_US/i/btn/x-click-but01.gif" alt="Make payments with PayPal - it's fast, free and secure!" onClick="this.submit();">
				</td>
			</tr>
		</table>
	</form></li>
	{/if} {*if ID*}
{/if} {*if !paypal*}
{/strip}