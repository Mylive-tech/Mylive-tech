<div>
    <fieldset>
        <h3>{l}Reviews{/l}</h3>
        <div class="phpld-grid">
        {foreach from=$comments item=comment name=items}
            <div class="phpld-gbox comment">
                <div>
                    <div class="author float-left">{$comment.USER_NAME}:</div>
                    <div class="date float-right">{$comment.DATE_ADDED}</div>
                </div>
                <div class="phpld-clearfix"></div>
                <div class="descr">{$comment.COMMENT|nl2br}</div>
            </div>
        {foreachelse}
            <h4>{l}No Reviews Yet{/l}.</h4>
        {/foreach}
        </div>
        {if $comm_posted eq 1 && $needs_approval_msg eq 1 }
            <div class="box info">{l}Comment posted and awaiting admin approval{/l}.</div>
        {elseif $comm_posted eq 1}
            <div class="box success">{l}Comment posted{/l}.</div>
        {/if}
        {if ($smarty.const.REQUIRE_REGISTERED_USER_LINK_COMMENT eq 1 && !empty($regular_user_details)) || $smarty.const.REQUIRE_REGISTERED_USER_LINK_COMMENT eq 0}
            <br>
            <form id="commentForm" method="post" action="{$smarty.const.DOC_ROOT}/listing/comment/{$LINK.ID}" class="phpld-form">
                <div class="phpld-columnar phpld-equalize">
                    <div class="phpld-label float-left">
                        <label for="comment"><b>{l}Leave your comment{/l}:</b></label>
                    </div>
                    <div class="phpld-fbox-text float-left">
                        <textarea id="comment" name="comment" rows="4" cols="60" class="text">{$COMMENT}</textarea>
                    {if $commentError}<div class="box error">{l}You need to enter comment before Submit{/l}.</div>{/if}
                </div>
            </div>
            <div class="phpld-columnar phpld-equalize">
                {if $smarty.const.VISUAL_CONFIRM_LINK_COMMENTS eq 1 && $smarty.const.VISUAL_CONFIRM eq 1}
                    {if $rights.editLink neq 1 && $rights.addLink neq 1 && $rights.delLink neq 1}
                        <div class="phpld-label float-left">
                            <span class="phpld-required">*</span>{l}Enter the code shown{/l}:
                        </div>
                        <div class="phpld-fbox-text float-left">
                            <input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" maxlength="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" class="text" /><br><br>
                        </div>                        
                        {if $img_verification_error}
                            <span class="box error">
                                <font color=red>{l}Verification error, please enter the code again{/l}</font>
                            </span><br>
                        {/if}
                        <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="{$imagehash}" />
                        <img src="{$smarty.const.DOC_ROOT}/captcha.php?imagehash={$imagehash}" class="captcha" alt="{l}Visual Confirmation Security Code{/l}" title="{l}Visual Confirmation Security Code{/l}" />
                        <br /><br />

                    {/if}
                {/if}
                {if $smarty.const.VISUAL_CONFIRM_LINK_COMMENTS eq 1 && $smarty.const.VISUAL_CONFIRM eq 2}
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
                <input type="hidden" name="formSubmitted" value="1" />
                <div class="phpld-clearfix"></div>
                <div class="phpld-fbox-button">
                    <input type="submit" value="{l}Submit{/l}" class="button">
                </div>
            </div>

        </form>
    {else}
        <br><a href="{$smarty.const.DOC_ROOT}/login" class="phpld-button">{l}You must be logged in to leave a Comment{/l}.</a><br>
    {/if}
</fieldset>
</div>      