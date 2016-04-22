<form method="post" action="" id="contact_listing" class="phpld-form">
    <input type="hidden" name="formSubmitted" value="1" />
   
    <div class="phpld-columnar phpld-equalize">

     <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Name{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="NAME" name="NAME" value="{$NAME|escape|trim}" size="40" class="text" />
            </div>
        </div>


        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Email{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="EMAIL" name="EMAIL" value="{$EMAIL|escape|trim}" size="40" class="text" />
            </div>
        </div>
 <div class="phpld-fbox-text">
            <label><sup class="phpld-required"></sup>{l}Phone Number{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="PHONE" name="PHONE" value="{$PHONE|escape|trim}" size="40" class="text" />
            </div>
        </div>
       
        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Message{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <textarea id="MESSAGE" name="MESSAGE" rows="7" cols="60" class="text">{$MESSAGE|escape|trim}</textarea>
		
            </div>
        </div>

        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>{l}Enter the code shown{/l}:</label>
            <div class="phpld-fbox-text float-left">
                <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="{$imagehash}" />
                <input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" maxlength="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" class="text" />
                <br /><br />
                <p class="small">{l}This helps prevent automated registrations.{/l}</p>
                <img src="{$smarty.const.DOC_ROOT}/captcha.php?imagehash={$imagehash}" class="captcha" alt="{l}Visual Confirmation Security Code{/l}" title="{l}Visual Confirmation Security Code{/l}" />
            </div>
        </div>

        <div class="phpld-fbox-button">
            <div class="float-right">
                <input type="submit" id="submit" name="edit" value="{l}Continue{/l}" class="btn" />
            </div>
        </div>
    </div>
</form>
