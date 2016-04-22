{strip}
{if $smarty.const.DISABLE_CONTACT_FORM eq 1}    
{literal}
    <script type="text/javascript">
        jQuery(document).ready(function($){
             jQuery("#submitForm input, #submitForm select, #submitForm textarea, #submitForm checkbox, #submitForm radio , #submitForm submit").attr("disabled", true);   
            });
    </script>
{/literal}
{/if}
<form method="post" action="" id="submitForm" class="phpld-form">
    <div class="contactPage">
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left"><span class="phpld-required">*</span>{l}Name{/l}:</div>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="NAME" name="NAME" value="{$NAME|escape|trim}" size="40" class="text" />
            </div>
        </div>

        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left"><span class="phpld-required">*</span>{l}Your Mail{/l}:</div>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="EMAIL" name="EMAIL" value="{$EMAIL|escape|trim}" size="40" maxlength="255" class="text" />
            </div>
        </div>

        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left"><span class="phpld-required">*</span>{l}Subject{/l}:</div>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="SUBJECT" name="SUBJECT" value="{$SUBJECT|escape|trim}" size="40" maxlength="255" class="text" />
            </div>
        </div>
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left"><span class="phpld-required">*</span>{l}Message{/l}:</div>
            <div class="phpld-fbox-text float-left">
                <textarea id="MESSAGE" name="MESSAGE" rows="10" cols="50" class="text">{$MESSAGE|escape|trim}</textarea>
            </div>
        </div>


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
                    <font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;">{$DO_MATH_N1} + {$DO_MATH_N2} = </font><input type="text" id="DO_MATH" name="DO_MATH" value='{$DO_MATH}' class="text" style="width: 60px; float: right;"/>
                    <br/>
                    <br/>
                </div>
            </div>
        {/if}

        {if $smarty.const.VISUAL_CONFIRM eq 1}
            {if $dont_show_captch neq 1}
                <div>
                    <div class="phpld-columnar phpld-equalize">
                        <div class="phpld-label"><span class="phpld-required">*</span>{l}Enter the code shown{/l}:</div>
                        <div class="phpld-fbox-text float-left">
                            <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="{$imagehash}" />
                            <input class="required text" id="CAPTCHA" name="CAPTCHA" type="text" value="" size="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" maxlength="{$smarty.const.CAPTCHA_PHRASE_LENGTH}"/>
                            <label for="CAPTCHA" id="captcha_validation" style="float: none; color: red; padding-left: .5em; "></label>
                            <div style="clear: both;"></div>
                            <p class="small">{l}This helps prevent automated registrations.{/l}</p>
                            <img src="{$smarty.const.DOC_ROOT}/captcha.php?imagehash={$imagehash}" class="captcha" alt="{l}Visual Confirmation Security Code{/l}" title="{l}Visual Confirmation Security Code{/l}" />
                        </div>
                    </div>
                </div>
            {/if}
        {/if}
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-fbox-button">
                <div class="float-right">
                    <input type="submit" id="continue" name="continue" value="{l}Continue{/l}" class="button" />
                </div>
            </div>
        </div>
        <input type="hidden" name="formSubmitted" value="1" />
    </div>
</form>
{/strip}