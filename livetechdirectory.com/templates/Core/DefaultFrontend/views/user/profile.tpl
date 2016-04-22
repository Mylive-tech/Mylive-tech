{strip}
<form method="post" action="" enctype="multipart/form-data" name="profile_form" id="submit_form" class="phpld-form">
{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="Registration Information" titleheading=$titleheading}
    <div class="formPage">
        {if $profileUpdate}
            <div>
                <h3>{l}Profile updated{/l}</h3>
            </div>
        {/if}
        {if $user_registration eq 1}
            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left">
                    <span class="phpld-required">*</span>
                    {l}Username{/l}:
                </div>
                <div class="phpld-fbox-text float-left">
                    <input type="text" name="LOGIN" value="{$user.LOGIN}" size="20" maxlength="{$smarty.const.USER_LOGIN_MAX_LENGTH}" class="text" />
                </div>
            </div>
        {/if}
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">
                <span class="phpld-required">*</span>
                {l}Name{/l}:
            </div>
            <div class="phpld-fbox-text float-left">
                <input type="text" name="NAME" value="{$user.NAME}" size="20" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
            </div>
        </div>

        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">
                {l}Password{/l}:
            </div>
            <div class="phpld-fbox-text float-left">
                <input type="password" name="PASSWORD" id="PASSWORD" value="" size="20" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
            </div>
        </div>

        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">
                {l}Confirm Password{/l}:
            </div>
            <div class="phpld-fbox-text float-left">
                <input type="password" name="PASSWORDC" id="PASSWORDC" value="" size="20" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
            </div>
        </div>

        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">
                <span class="phpld-required">*</span>
                {l}Language{/l}:
            </div>
            <div class="phpld-fbox-select float-left">
                {html_options options=$languages selected=$user.LANGUAGE name="LANGUAGE"}
            </div>
        </div>

        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">
                <span class="phpld-required">*</span>
                {l}Email{/l}:
            </div>
            <div class="phpld-fbox-text float-left">
                <input type="text" name="EMAIL" value="{$user.EMAIL|escape}" size="20" maxlength="255" class="text" />
            </div>
        </div>
        {if $user_registration eq 0 AND $smarty.const.ALLOW_AUTHOR_INFO eq 1}
            <div>
                <h3>{l}Optional Info For Author Page{/l}</h3>
            </div>
            <!-- author avatar related -->

            {if $ALLOW_AVATARS eq 1}

            {/if}
            <!-- end -->
        {* if user has links posted*}
            {if $hasLinks > 0}
                <div class="phpld-columnar phpld-equalize">
                    <div class="phpld-fbox-check float-left">
                        <input type="checkbox" name="OWNER_NEWSLETTER_ALLOW" {if $OWNER_NEWSLETTER_ALLOW >= 1}checked="checked"{/if} />
                    </div>
                    <div class="phpld-label float-left">
                        <p class="small">Allow site administrator to send me newsletters.</p>
                    </div>
                </div>
            {/if}


            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left">
                    {l}Info{/l}:
                </div>
                <div class="phpld-fbox-text float-left">
                    <textarea name="INFO" id="INFO" rows="3" cols="10" class="text" {formtool_count_chars name="INFO" limit="255" alert=true}>{$user.INFO|escape|trim}</textarea>
                    <div class="limitDesc float-left">{l}Limit{/l}:
                                        
                    <input type="text" name="INFO_limit" cols="4" class="limit_field" readonly="readonly" value="{$InfoLimit|trim}" /></div>
                </div>
                
                
  
            </div>

            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left">
                    {l}Website Name{/l}:
                </div>
                <div class="phpld-fbox-text float-left">
                    <input type="text" name="WEBSITE_NAME" value="{$user.WEBSITE_NAME|escape|trim}" size="40" maxlength="255" class="text"/>
                </div>
            </div>
            <div class="phpld-clearfix"></div>
            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left">
                    {l}Website URL{/l}:
                </div>
                <div class="phpld-fbox-text float-left">
                    <input type="text" name="WEBSITE" value="{if !$user.WEBSITE}http://{else}{$user.WEBSITE|escape|trim}{/if}" size="40" maxlength="255" class="text"/>
                </div>
            </div>
        {/if}

        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-fbox-button">
                <input type="submit" name="send" value="Submit" class="button" />
            </div>
        </div>
    </div>
    <input type="hidden" name="formSubmitted" value="1" />
</form>
{/strip}