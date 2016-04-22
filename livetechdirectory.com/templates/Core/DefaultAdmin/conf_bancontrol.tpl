{* Error and confirmation messages *}
{include file="messages.tpl"}

{include file=$smarty.const.ADMIN_TEMPLATE|cat:"/validation.tpl" form_id="submit_form" validators=$validators}

{strip}
<div class="block">
   <!-- Database Action Links -->
   <ul class="page-action-list">
      <li><a href="{$smarty.const.DOC_ROOT}/conf_bancontrol.php#banip" title="{l}Jump to IP banning{/l}" class="button"><span class="jump">{l}Jump to IP banning{/l}</span></a></li>
      <li><a href="{$smarty.const.DOC_ROOT}/conf_bancontrol.php#bandomain" title="{l}Jump to domain banning{/l}" class="button"><span class="jump">{l}Jump to domain banning{/l}</span></a></li>
      <li><a href="{$smarty.const.DOC_ROOT}/conf_bancontrol.php#banemail" title="{l}Jump to email banning{/l}" class="button"><span class="jump">{l}Jump to email banning{/l}</span></a></li>
      <li><a href="{$smarty.const.DOC_ROOT}/conf_bancontrol.php#about" title="{l}Jump to banning information{/l}" class="button"><span class="jump">{l}Jump to banning information{/l}</span></a></li>
   </ul>
   <!-- /Database Action Links -->
</div>

{if $posted}
   <div class="success block">
      {l}Banlist updated.{/l}
   </div>
{/if}

<form method="post" action="{$smarty.const.DOC_ROOT}/conf_bancontrol.php" id="submit_form">
<div id="banip" class="block">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Ban from submit page{/l}</th>
      </tr>
      <tr>
         <td colspan="2" class="info notice">{l}This feature allows you to stop certain IP addresses from accessing the submit page.{/l}</td>
      </tr>
   </thead>
   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="BAN_IP">{l}Ban IP{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="BAN_IP" name="BAN_IP" value="{$BAN_IP}" maxlength="15" class="text" />
            <p class="notice">{$wildcardIpTxt}</p>
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="UNBAN_IP">{l}Unban IP{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$IP_List selected=$UNBAN_IP name="UNBAN_IP" id="UNBAN_IP"}
         </td>
      </tr>
   </tbody>
   <tfoot>
      <tr>
         <td><input type="reset" id="reset-ip-banning" name="reset" value="{l}Reset{/l}" alt="{l}Reset IP banning settings{/l}" title="{l}Reset IP banning settings{/l}" class="button" /></td>
         <td><input type="submit" id="save-ip-banning" name="save" value="{l}Save{/l}" alt="{l}Save IP banning settings{/l}" title="{l}Save IP banning settings{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
</div>

<div id="bandomain" class="block">
<table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Ban from link submission{/l}</th>
      </tr>
      <tr>
         <td colspan="2" class="info notice">{l}This feature allows you to stop certain domains from being submitted to your link directory.{/l}</td>
      </tr>
   </thead>
   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="BAN_DOMAIN">{l}Ban Domain{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="BAN_DOMAIN" name="BAN_DOMAIN" value="{$BAN_DOMAIN}" size="40" class="text" />
            <p class="notice">{$wildcardDomainTxt}</p>
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="UNBAN_DOMAIN">{l}Unban Domain{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$Domain_List selected=$UNBAN_DOMAIN name="UNBAN_DOMAIN" id="UNBAN_DOMAIN"}
         </td>
      </tr>
   </tbody>
   <tfoot>
      <tr>
         <td><input type="reset" id="reset-domain-banning" name="reset" value="{l}Reset{/l}" alt="{l}Reset domain banning settings{/l}" title="{l}Reset domain banning settings{/l}" class="button" /></td>
         <td><input type="submit" id="save-domain-banning" name="save" value="{l}Save{/l}" alt="{l}Save domain banning settings{/l}" title="{l}Save domain banning settings{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
</div>

<div id="banword" class="block">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Ban word from submit page{/l}</th>
      </tr>
      <tr>
         <td colspan="2" class="info notice">{l}This feature allows you to stop words in the submit page.{/l}</td>
      </tr>
   </thead>
   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="BAN_WORD">{l}Ban Words{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="BAN_WORD" name="BAN_WORD" value="{$BAN_WORD}" class="text" />
            <p class="notice">{l}You can add multiple words at a time by separating them with a space{/l}.</p>
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="UNBAN_WORDS">{l}Unban Words{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$WORD_List selected=$UNBAN_WORD name="UNBAN_WORD" id="UNBAN_WORD"}
         </td>
      </tr>
   </tbody>
   <tfoot>
      <tr>
         <td><input type="reset" id="reset-words-banning" name="reset" value="{l}Reset{/l}" alt="{l}Reset words banning settings{/l}" title="{l}Reset words banning settings{/l}" class="button" /></td>
         <td><input type="submit" id="save-words-banning" name="save" value="{l}Save{/l}" alt="{l}Save words banning settings{/l}" title="{l}Save words banning settings{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
</div>

<div id="banemail" class="block">
   <table class="list">
   <thead>
      <tr>
         <th colspan="2">{l}Ban email addresses from submitting and registering{/l}</th>
      </tr>
      <tr>
         <td colspan="2" class="info notice">{l}This feature allows you to stop certain email addresses from submitting links and registering new useraccounts.{/l}</td>
      </tr>
   </thead>
   <tbody>
      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="BAN_EMAIL">{l}Ban Email{/l}:</label></td>
         <td class="smallDesc">
            <input type="text" id="BAN_EMAIL" name="BAN_EMAIL" value="{$BAN_EMAIL}" maxlength="255" class="text" />
            <p class="notice">{$wildcardEmailTxt}</p>
         </td>
      </tr>

      <tr class="{cycle values="odd,even"}">
         <td class="label"><label for="UNBAN_EMAIL">{l}Unban Email{/l}:</label></td>
         <td class="smallDesc">
            {html_options options=$Email_List selected=$UNBAN_EMAIL name="UNBAN_EMAIL" id="UNBAN_EMAIL"}
         </td>
      </tr>
   </tbody>
   <tfoot>
      <tr>
         <td><input type="reset" id="reset-email-banning" name="reset" value="{l}Reset{/l}" alt="{l}Reset email banning settings{/l}" title="{l}Reset email banning settings{/l}" class="button" /></td>
         <td><input type="submit" id="save-email-banning" name="save" value="{l}Save{/l}" alt="{l}Save email banning settings{/l}" title="{l}Save email banning settings{/l}" class="button" /></td>
      </tr>
   </tfoot>
   </table>
</div>
<input type="hidden" name="formSubmitted" value="1" />
</form>

<div id="about" class="info block">
   <table class="list info-tbl">
   <thead>
      <tr>
         <th colspan="2">{l}IP banning{/l}:</th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <td class="label">{l}Valid IP{/l}</td><td class="smallDesc">123.123.123.123</td>
      </tr>
      <tr class="thead">
         <th colspan="2">{l}Notice: To avoid certain IP banning problems, the following range of IP addresses are currently kept out of the banning system.{/l}</th>
      </tr>
      <tr class="notice">
         <td colspan="2" class="notice">
            <p><a href="http://rfc.net/rfc1918.html" title="RFC 1918 - Address Allocation for Private Internets" target="_blank">RFC 1918 - Address Allocation for Private Internets</a></p>
            <p>{l}The Internet Assigned Numbers Authority (IANA) has reserved the following three blocks of the IP address space for private internets, please do not ban them:{/l}</p>
         </td>
      </tr>
      <tr>
         <td class="label"><a href="http://rfc.net/rfc1918.html#s10.0.0.0" title="RFC 1918 - Address Allocation for Private Internets" target="_blank">10.0.0.0</a></td>
         <td class="smallDesc">10.255.255.255  (10/8 prefix)</td>
      </tr>
      <tr>
         <td class="label"><a href="http://rfc.net/rfc1918.html#s172.16.0.0" title="RFC 1918 - Address Allocation for Private Internets" target="_blank">172.16.0.0</a></td>
         <td class="smallDesc">172.31.255.255  (172.16/12 prefix)</td>
      </tr>
      <tr>
         <td class="label"><a href="http://rfc.net/rfc1918.html#s192.168.0.0" title="RFC 1918 - Address Allocation for Private Internets" target="_blank">192.168.0.0</a></td>
         <td class="smallDesc">192.168.255.255 (192.168/16 prefix)</td>
      </tr>
      <tr class="thead">
         <th colspan="2">{l}The following example of an IP is ALWAYS your personal IP number of the current computer you are running.{/l}</th>
      </tr>
      <tr>
         <td class="label">{l}Local IP{/l}</td>
         <td class="smallDesc">127.0.0.1</td>
      </tr>

      <tr class="thead">
         <th colspan="2">{l}Domain banning:{/l}</th>
      </tr>
      <tr>
         <td rowspan="2" class="label">{l}Valid domain name{/l}</td>
         <td class="smallDesc">domain.com</td>
      </tr>
      <tr>
         <td class="smallDesc">subdomain.domain.com</td>
      </tr>

      <tr>
         <td rowspan="2" class="label">{l}Invalid domain name{/l}</td>
         <td class="smallDesc"><span class="important">www.</span>domain.com</td>
      </tr>
      <tr>
         <td class="smallDesc"><span class="important">www.</span>domain.domain.com<span class="important">/index.php?foo=bar&amp;foobar=milk</span></td>
      </tr>
   </tbody>
   </table>
</div>
{/strip}