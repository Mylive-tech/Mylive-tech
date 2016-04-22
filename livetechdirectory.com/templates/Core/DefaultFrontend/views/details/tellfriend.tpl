<form method="post" action="" id="tell_friend" class="phpld-form">
    <input type="hidden" name="formSubmitted" value="1" />
    <div class="phpld-columnar phpld-equalize">
        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Your Email{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="EMAIL" name="EMAIL" value="{$EMAIL|escape|trim}" size="40" class="text" />
            </div>
        </div>

        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Friend's Email{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="FRIEND_EMAIL" name="FRIEND_EMAIL" value="{$FRIEND_EMAIL|escape|trim}" size="40" class="text" />
            </div>
        </div>

        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Friend's Email{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <textarea id="MESSAGE" name="MESSAGE" rows="7" cols="60" class="text">{$MESSAGE|escape|trim}</textarea>
            </div>
        </div>

        {if $smarty.const.VISUAL_CONFIRM eq 2}
    <div class="phpld-fbox-text">
        <label><sup class="phpld-required">*</sup>{l}DO THE MATH{/l}:</label>
        <div class="phpld-fbox-text float-left">
                <span style="color: red;">
                    {foreach name=errorList from=$error_list key=errorKey item=errorItem}
                        {if $errorKey == 'DO_MATH'}
                            {if is_array($errorItem)}
                                {$errorItem.remote}
                                {else}
                                {$errorItem}
                            {/if}
                        {/if}
                    {/foreach}
                </span>
            <font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;">{$DO_MATH_N1} + {$DO_MATH_N2} = </font><input type="text" id="DO_MATH" name="DO_MATH" value='{$DO_MATH}' class="text" style="width: 60px; float: right;"/>
            
            
        </div>
    </div>
{/if}

{if $smarty.const.VISUAL_CONFIRM eq 1}
    {if $dont_show_captch neq 1}
        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Enter the code shown{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="{$imagehash}" />
                <input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" maxlength="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" class="text" />
                
                <p class="small">{l}This helps prevent automated registrations.{/l}</p>
                <img src="{$smarty.const.DOC_ROOT}/captcha.php?imagehash={$imagehash}" class="captcha" alt="{l}Visual Confirmation Security Code{/l}" title="{l}Visual Confirmation Security Code{/l}" />
            </div>
        </div>
    {/if}
{/if}  

        <div class="phpld-fbox-button">
            <div class="float-right">
                <input type="submit" id="submit" name="edit" value="{l}Continue{/l}" class="btn" />
            </div>
        </div>
    </div>
</form>
