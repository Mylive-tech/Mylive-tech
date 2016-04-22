<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>PHP Link Directory Transformer v{$smarty.const.CURRENT_VERSION} - Installation</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="robots" content="noindex,nofollow" />
        <link rel="stylesheet" type="text/css" href="install.css" />
        <script type="text/javascript">
            {literal}
      var DOC_ROOT = '{$smarty.const.DOC_ROOT}';
            {/literal}
        </script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery.validate.js"></script>
        <script type="text/javascript" src="{$smarty.const.DOC_ROOT}/javascripts/jquery/jquery.field.min.js"></script>
        {include file="install/validation.tpl"}
    </head>
    <body>
        {include file="install/messages.tpl"}
        <!-- Main -->
        <div id="wrap" class="install-page">

            <div id="header">
                <div id="header-title"><span>PHP Link Directory</span></div>
            </div>

            <form method="post" action="" id="install_form" name="install_form">

                {$smarty.capture.form_error}
                {$smarty.capture.message}

                <div id="install">

                    {if empty($step) or $step le 1}
                        <table class="formPage lang-select">
                            <tbody>
                                <tr>
                                    <td class="label"><label>{l}Language{/l}</label></td>
                                    <td class="smallDesc">
                                        {html_options options=$languages selected=$language name="language"}
                                        <p class="description help">{l}Select the language for the installation process.{/l}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="install_new">This is a new installation</label></td>
                                    <td class="smallDesc">
                                        <input type="radio" id="goto_admin" name="install" value="new"{if $install eq 'new' or !$goto} checked="checked"{/if} />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="install_upgrate">This is an upgrade</label></td>
                                    <td class="smallDesc">
                                    <input type="radio" id="goto_dir" name="install" value="upgrate"{if $install eq 'upgrate'} checked="checked"{/if} />
                                </td>
                        </tr>
                            </tbody>
                        </table>

                    {elseif $step eq 2}
                        <div id="thank">{l}Thank you for choosing PHP Link Directory. PHP Link Directory was developed to help create and maintain a link directory and exchange. Many features are already implemented and more are being developed all the time, keep up-to-date by visiting the PHP Link Directory {/l}<a target="_blank" href="http://www.phplinkdirectory.com">{l}homepage{/l}</a>.</div>

                        <table class="formPage">
                            <thead>
                                <tr>
                                    <th colspan="2">{$title|escape|trim}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$req item="item"}
                                    <tr class="{cycle values="odd,even"} valid-{if $item.fatal}fatal{elseif $item.ok}ok{else}no{/if}">
                                        <td class="label">{$item.req}</td>
                                        <td class="smallDesc nowrap">{$item.txt}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>

                        {if $fatal eq 1}
                            <div class="error">{l}At least one fatal error was found. Please correct the reported error(s) and refresh this page or restart the installer in order to continue with the installation process.{/l}</div>
                        {/if}

                    {elseif $step eq 3}
                        <table class="formPage">
                            <thead>
                                <tr>
                                    <th colspan="2">{$title|escape|trim}</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="{cycle values="odd,even"}">
                                    <td class="label required">
                                        <label for="db_host">{l}Database Server{/l}</label>
                                    </td>
                                    <td class="smallDesc">
                                        <input type="text" class="required" id="db_host" name="db_host" value="{$db_host}" maxlength="100" />
                                        {*validate form="install" id="v_db_host" page="3" message=$smarty.capture.field_required*}
                                        <p class="description help">{l}The database server can be in the form of a hostname, such as db1.myserver.com, or as an IP-address, such as 192.168.0.1, or localhost.{/l}</p>
                                    </td>
                                </tr>
                                <tr class="{cycle values="odd,even"}">
                                    <td class="label required"><label for="db_name">{l}Database Name{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="text" class="required" id="db_name" name="db_name" value="{$db_name}" maxlength="100" />
                                        {*validate form="install" id="v_db_name" page="3" message=$smarty.capture.field_required*}
                                        <p class="description help">{l}The database used to hold the data. An example database name is 'phplinkd'.{/l}</p>
                                    </td>
                                </tr>
                                <tr class="{cycle values="odd,even"}">
                                    <td class="label required"><label for="db_user">{l}Username{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="text" class="required" id="db_user" name="db_user" value="{$db_user}" maxlength="100" />
                                        {*validate form="install" id="v_db_user" page="3" message=$smarty.capture.field_required*}
                                        <p class="description help">{l}The username used to connect to the database server. An example username is 'mysql_10'.{/l}</p>
                                        <p class="notice">{l}Note: Create and Drop permissions {/l}<b>{l}are required{/l}</b>{l} at this point of the installation procedure{/l}.</p>
                                    </td>
                                </tr>
                                <tr class="{cycle values="odd,even"}">
                                    <td class="label"><label for="db_password">{l}Password{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="password" id="db_password" name="db_password" value="{$db_password}" maxlength="100" class="text" />
                                        <p class="description help">{l}The password is used together with the username, which forms the database user account.{/l}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    {elseif $step eq 4}
                        <table class="formPage">
                            <thead>
                            <tr>
                                <th colspan="2">{$title|escape|trim}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$link_types item="type"}
                                <tr>
                                    <td>{$type.NAME}</td>
                                    <td>
                                        <select name="TYPE[{$type.ID}]">
                                            {foreach from=$new_link_types item="new_type"}
                                                <option value="{$new_type.ID}">{$new_type.NAME}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    {elseif $step eq 5}
                        <table class="formPage">
                            <thead>
                                <tr>
                                    <th colspan="2">{$title|escape|trim}</th>
                                </tr>
                                <tr class="notice">
                                    <th colspan="2" class="notice"><p>{l}Populate database with configuration settings and other optional data{/l}.</p><p>{l}In case you are upgrading, all your existing data is automatically altered if needed{/l}.</p></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="label required"><label for="populate_config">{l}Populate configurations{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="checkbox" id="populate_config" name="populate_config" value="1" checked="checked" readonly="readonly" disabled="disabled" />
                                        <p class="info">{l}Always updated automatically{/l}!</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="populate_emailtpl">{l}Add email templates{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="checkbox" id="populate_emailtpl" name="populate_emailtpl" value="1"{if $populate_emailtpl} checked="checked"{/if} />
                                        <p class="info">{l}If you are updating, this option will add only email templates that are not associated to your notification system{/l}.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="associate_emailtpl">{l}Associate email templates to notfication system{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="checkbox" id="associate_emailtpl" name="associate_emailtpl" value="1"{if $associate_emailtpl} checked="checked"{/if} />
                                        <p class="info">{l}Works only if you check the above option and new email templates are added{/l}.</p>
                                        <p class="info">{l}You can skip this option and manually associate notification emails{/l}.</p>
                                    </td>
                                </tr>
				
                            </tbody>
                        </table>

                    {elseif $step eq 6}
                        <table class="formPage">
                            <thead>
                                <tr>
                                    <th colspan="2">{$title|escape|trim}</th>
                                </tr>
                                <tr class="notice">
                                    <th colspan="2" class="notice">{l}Create an administrative user for the phpLinkDirectory.{/l}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="label required"><label for="admin_user">{l}Administrator username{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="text" id="admin_user" name="admin_user" value="{$admin_user}" maxlength="100" class="text" />
                                        {*validate form="install" id="v_admin_user" page="4" message=$smarty.capture.invalid_username*}
                                        <p class="description help">{l}The username used to access the administrative pages of phpLinkDirectory. The user name must have minimum 4 characters, maximum 10 characters and must contain only letters and digits.{/l}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="admin_name">{l}Administrator name{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="text" class="required" id="admin_name" name="admin_name" value="{$admin_name}" maxlength="100" />
                                        {*validate form="install" id="v_admin_name" page="4" message=$smarty.capture.field_required*}
                                        <p class="description help">{l}The name of the administrative user. This name will be used in the {/l}<i>{l}From{/l}:</i>{l} field when sending emails{/l}.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="admin_password">{l}Administrator password{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="password" id="admin_password" name="admin_password" value="" maxlength="100" class="admin_password" />
                                        {*validate form="install" id="v_admin_password" page="4" message=$smarty.capture.invalid_password*}
                                        <p class="description help">{l}The password is used together with the username to access the administrative pages of phpLinkDirectory. The password must have minimum 6 characters and maximum 10 characters.{/l}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="admin_passwordc">{l}Confirm administrator password{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="password" id="admin_passwordc" name="admin_passwordc" value="" maxlength="100" class="text" />
                                        {*validate form="install" id="v_admin_passwordc" page="4"  message=$smarty.capture.password_not_match*}
                                        <p class="description help">{l}To verify that the password was typed correctly please enter it again.{/l}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label required"><label for="admin_email">{l}Administrator Email{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="text" class="email" id="admin_email" name="admin_email" value="{$admin_email}" maxlength="255" />
                                        <p class="description help">{l}Administrative email address. This email address will be used for notifications regarding the system.{/l}</p>
                                    </td>
                                </tr>
				<tr>
                                    <td class="label required"><label for="paypal">{l}Paypal Email{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="text" class="text" id="paypal" name="paypal" value="{$paypal}" maxlength="255" />
                                        <p class="description help">{l}Your paypal email address. This email address will be used for payments via paypal.{/l}</p>
                                    </td>
                                </tr>
				<tr>
                                    <td class="label required"><label for="domain">{l}Domain{/l}</label></td>
                                    <td class="smallDesc">
                                        <input type="text" class="text" id="domain" name="domain" value="http://{$domain}/" maxlength="255" />
                                        <p class="description help">{l}The complete url to the phpLD installation trailing slash must be used.{/l}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    {elseif $step eq 7}
                        <h1>{l}Thank you for choosing phpLinkDirectory.{/l}</h1>

                        <br />

                        <div class="notice">
                            <h3>{l}YOU MUST DELETE THE FOLLOWING FILE(S) BEFORE CONTINUING:{/l} <code>{$smarty.const.DOC_ROOT}/install/index.php</code></h3>
                            <h3>{l}YOU MUST DROP WRITING PERMISSIONS TO FOLLOWING FILE(S) BEFORE CONTINUING:{/l} <code>{$smarty.const.DOC_ROOT}/include/config.php</code></h3>
                        </div>

                        <br />

                        <table class="formPage">
                        {literal}<!--{/literal}
                           <thead>
                              <tr>
                                 <th colspan="2">{$title|escape|trim}</th>
                              </tr>
                           </thead>
                    {literal}-->{/literal}
                    <tbody>
                        <tr>
                            <td class="label required"><label for="goto_admin">Redirect to admin control panel</label></td>
                            <td class="smallDesc">
                                <input type="radio" id="goto_admin" name="goto" value="admin"{if $goto eq 'admin'} checked="checked"{/if} />
                            </td>
                        </tr>
                        <tr>
                            <td class="label required"><label for="goto_dir">Redirect to your directory page</label></td>
                            <td class="smallDesc">
                                <input type="radio" id="goto_dir" name="goto" value="dir"{if !$goto or $goto eq 'dir' or $goto eq 'directory'} checked="checked"{/if} />
                            </td>
                        </tr>
                        <tr>
                            <td class="label required"><label for="goto_install">Restart installation</label></td>
                            <td class="smallDesc">
                                <input type="radio" id="goto_install" name="goto" value="install"{if $goto eq 'install'} checked="checked"{/if} />
                            </td>
                        </tr>
                    </tbody>
                </table>
            {/if}

            <div class="formButtons">
                {if $fatal ne '1'}
                    {if $btn_next}
                        <input type="submit" id="next" name="next" value="{l}Next{/l}" title="{l}Go to next step{/l}" accesskey="n" class="button" />
                    {/if}
                {/if}

                {if $btn_back}
                    <input type="submit" id="back" name="back" value="{l}Back{/l}" title="{l}Go to previous step{/l}" accesskey="b" class="button" />
                {/if}

                {if $btn_finish}
                    <input type="submit" id="complete" name="complete" value="{l}Complete Installation{/l}" title="{l}Complete Installation{/l}" accesskey="n" class="button" />
                {/if}
            </div>

        </div>
        <input type="hidden" name="formSubmitted" value="1" />
    </form>


    <!-- Footer -->
    <div id="footer">PHP Link Directory Phoenix v{$smarty.const.CURRENT_VERSION}, Copyright &copy; 2004-{php}echo date('Y');{/php} NetCreated.</div>
    <!-- /Footer -->
</div>
<!-- /Main -->
</body>
</html>