<li class="group">{l}Recover Password{/l}</li>
<li>
<form method="post" action="" id="submit_form" class="phpld-form">
    <div class="formPage">
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Login{/l}</div>
            <div class="phpld-fbox-text float-left">
                <input type="text" name="LOGIN" value="{$LOGIN}" size="20" maxlength="{$smarty.const.USER_LOGIN_MAX_LENGTH}" class="text" />
            </div>
        </div>
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">{l}Email{/l}</div>
            <div class="phpld-fbox-text float-left">
                <input type="text" name="EMAIL" value="{$EMAIL}" size="20" maxlength="255" class="text" />
            </div>
        </div>
        {if $smarty.const.VISUAL_CONFIRM}
            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left">
                    {l}Confirmation code{/l}
                    <p class="small">This helps prevent automated registrations.</p>
                </div>
                <div class="phpld-fbox-text float-left">
                    <input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" maxlength="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" class="text" />
                </div>
                <div class="phpld-fbox-text float-left">
                    <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="{$imagehash}" />
                    <img src="{$smarty.const.DOC_ROOT}/captcha.php?imagehash={$imagehash}" class="captcha" alt="{l}Visual Confirmation Security Code{/l}" title="{l}Visual Confirmation Security Code{/l}" />

                </div>
            </div>
        {/if}
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-fbox-button">
                <div class="float-right">
                    <input type="submit" name="login" value="Submit" class="button" />
                </div>
            </div>
        </div>
    </div>
</form></li>