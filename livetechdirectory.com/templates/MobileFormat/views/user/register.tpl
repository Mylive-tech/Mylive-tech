{strip}
<li class="group">{include file="views/_shared/_placeholders/pagetitle.tpl" PAGETITLE="Registration Information" titleheading=$titleheading}</li>
<li>
<form method="post" action="" enctype="multipart/form-data" name="profile_form" id="submit_form" class="phpld-form">

{*ANONYMOUS is 0 by default. This value redefines with checkbox field below if it's enabled*}
<input type="hidden" name="ANONYMOUS" value="0" />
<div class="formPage">
   {if $profileUpdate}
   <div>
        <h3>{l}Profile updated{/l}</h3>
   </div>
   {/if}
   <div class="phpld-columnar phpld-equalize">
       <div class="phpld-label float-left">
            <span class="phpld-required">*</span>
                {l}Login{/l}:
       </div>
          <div class="phpld-fbox-text float-left">
         <input type="text" name="LOGIN" value="{$LOGIN}" size="20" maxlength="{$smarty.const.USER_LOGIN_MAX_LENGTH}" class="text" />
      </div>
    </div>

   <div class="phpld-columnar phpld-equalize">
      <div class="phpld-label float-left">
          <span class="phpld-required">*</span>
            {l}Password{/l}:
      </div>
      <div class="phpld-fbox-text float-left">
         <input type="password" name="PASSWORD" id="PASSWORD" value="" size="20" maxlength="{$smarty.const.USER_PASSWORD_MAX_LENGTH}" class="text" />
      </div>
    </div>
     
    <div class="phpld-columnar phpld-equalize">
       <div class="phpld-label float-left">
          <span class="phpld-required">*</span>
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
            {html_options options=$languages selected=$LANGUAGE name="LANGUAGE"}
        </div>
   </div>
    
   <div class="phpld-columnar phpld-equalize">
      <div class="phpld-label float-left">
          <span class="phpld-required">*</span>
             {l}Email{/l}:
      </div>
      <div class="phpld-fbox-text float-left">
         <input type="text" name="EMAIL" value="{$EMAIL|escape}" size="20" maxlength="255" class="text" />
      </div>
   </div>
    {if $smarty.const.ALLOW_ANONYMOUS}
       <div class="phpld-columnar phpld-equalize">
          <div class="phpld-label float-left">
              <span>*</span>
                 {l}Anonymous{/l}:
          </div>
           <div class="phpld-fbox-check float-left">
             <input type="checkbox" name="ANONYMOUS" value="1" />
          </div>
       </div>
    {/if}
{if $user_registration eq 0 AND $ALLOW_AUTHOR_INFO eq 1}
   <div>
         <h3>{l}Optional Info For About Page{/l}</h3>
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
            <textarea name="INFO" rows="3" cols="37" class="text" {formtool_count_chars name="INFO" limit="255" alert=true}>{$INFO|escape|trim}</textarea>
        </div>
    </div>
      
      <div class="phpld-columnar phpld-equalize">
        <div class="phpld-label float-left">
            {l}Limit{/l}:
        </div>
        <div class="phpld-fbox-text float-left">
          <input type="text" name="INFO_limit" size="4" class="limit_field" readonly="readonly" value="{$InfoLimit|trim}" />
        </div>
      </div>
   <div class="phpld-clearfix"></div>
   <div class="phpld-columnar phpld-equalize">
      <div class="phpld-label float-left">
          {l}Website Name{/l}:
      </div>
      <div class="phpld-fbox-text float-left">
         <input type="text" name="WEBSITE_NAME" value="{$WEBSITE_NAME|escape|trim}" size="40" maxlength="255" class="text"/>
      </div>
   </div>
     <div class="phpld-clearfix"></div>  
      <div class="phpld-columnar phpld-equalize">
        <div class="phpld-label float-left">
            {l}Website URL{/l}:
        </div>
      <div class="phpld-fbox-text float-left">
         <input type="text" name="WEBSITE" value="{if !$WEBSITE}http://{else}{$WEBSITE|escape|trim}{/if}" size="40" maxlength="255" class="text"/>
      </div>
   </div>
   <div>
         <h3></h3>
    </div>
{/if}
         {if $smarty.const.VISUAL_CONFIRM eq 2}
             <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left">
                    <span class="phpld-required">*</span>
                    {l}DO THE MATH{/l}:
                </div>
		<div class="phpld-error float-left">
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
			<font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;">{$DO_MATH_N1} + {$DO_MATH_N2} = </font><input type="text" id="DO_MATH" name="DO_MATH" value='{$DO_MATH}' class="text" style="width: 60px;"/>
		<br/>
		<br/>
	       </div>
              </div>
	{/if}
   {if $smarty.const.VISUAL_CONFIRM eq 1}
    <div class="phpld-columnar phpld-equalize">
      <div class="phpld-label float-left">
            <span class="phpld-required">*</span>{l}Confirmation code{/l}
            <p class="small">{l}This helps prevent automated registrations.{/l}</p>
       </div>
         <div class="phpld-fbox-text float-left">
            <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="{$imagehash}" />
            <input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" maxlength="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" class="text" />
         </div>
            <div class="phpld-fbox-text float-left">
            <img src="{$smarty.const.DOC_ROOT}/captcha.php?imagehash={$imagehash}" class="captcha" alt="{l}Visual Confirmation Security Code{/l}" title="{l}Visual Confirmation Security Code{/l}" />
            
         </div>
    </div>
   {/if}
   <div>&nbsp;</div>
      <div class="phpld-columnar phpld-equalize">
      <div class="phpld-fbox-button">
          <input type="submit" name="send" value="Register" class="button" />
      </div>
   </div>
</div>
<input type="hidden" name="formSubmitted" value="1" />
</form></li>
{/strip}