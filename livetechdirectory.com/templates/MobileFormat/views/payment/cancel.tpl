{capture name="title"}{l}Link Payment{/l}{/capture}
{capture assign="in_page_title"}{l}Link Payment{/l}{/capture}
{capture assign="description"}{l}Submit a new link to the directory{/l}{/capture}
{strip}
{if empty ($ID)}
{l}Invalid link id.{/l}
{else}
	<li><form method="post" action="" id="submit_payment_form">
		<table border="0" class="formPage">
			<tr>
				<td colspan="2" class="err">
				{l}The payment was canceled.{/l}
				</td>
			</tr>
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

			{if $URL|escape|trim != ''}
				<tr>
					<td class="label">{l}URL{/l}:</td>
					<td class="field">{$URL|escape|trim}</td>
				</tr>
			{/if}

			{if $DESCRIPTION|escape|trim != ''}
				<tr>
					<td class="label">{l}Description{/l}:</td>
					<td class="field">{$DESCRIPTION|escape|trim}</td>
				</tr>
			{/if}

			{if $OWNER_NAME|escape|trim != ''}
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
				{assign var="um" value="`$SubLength`"}
				<td class="field">{$smarty.const.HTML_CURRENCY_CODE}{$price}  {if $SubscriptionEnabled eq 1}every {$SubscriptionPeriod} {$SubscriptionUm}{/if}</td>
			</tr>
		</table>
	</form></li>
{/if}
</strip>