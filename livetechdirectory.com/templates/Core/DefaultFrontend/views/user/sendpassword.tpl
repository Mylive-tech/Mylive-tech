<form method="post" action="" id="submit_form" class="phpld-form">
    <div class="formPage">
        <h3>{l}Recover Password{/l}</h3>
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
        {if $smarty.const.VISUAL_CONFIRM eq 1}
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
        {if $smarty.const.VISUAL_CONFIRM eq 2}
             <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left"><span class="phpld-required">*</span>{l}DO THE MATH{/l}:</div>
                <div class="phpld-fbox-text float-left">
                        <span style="color: red;">
                            {foreach name=errorList from=$error_list key=errorKey item=errorItem}
                                {if $errorKey == 'DO_MATH'}
                                    {if is_array($errorItem)}
                                        {$errorItem.remote}<br/>
                                        {else}
                                        {$errorItem}<br/>
                                    {/if}
                                {/if}
                            {/foreach}
                        </span>
                    <font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;float: left;">{$DO_MATH_N1} + {$DO_MATH_N2} = </font><br/><input type="text" id="DO_MATH" name="DO_MATH" value='{$DO_MATH}' class="text" style="width: 60px;"/>
                    <br/>
                    <br/>
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
</form>