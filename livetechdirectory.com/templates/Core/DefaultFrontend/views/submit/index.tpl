{strip}
    {if $smarty.const.DISABLE_SUBMIT == 1 && $is_admin neq 1}
    {include file="views/submit/closed.tpl" disablereason=$disablereason}
        {else}
        {if ($confirmed)}
        <div class="formPage">
            <div class="phpld-columnar">
                <div class="box info">
                    {l}The link's email address has been confirmed.{/l}<br />
                    {l}The link will be listed as soon as it is approved by a site administrator.{/l}
                </div>
            </div>
        </div>
        {/if}
        {if $smarty.const.REQUIRE_REGISTERED_USER == 1 and empty($regular_user_details.ID)}
        <div class="formPage">
            <div class="phpld-columnar">
                <h3>{l}Information{/l}</h3>
            </div>
            <div>
                {l}You must be logged in to submit a new link. <a href="{$smarty.const.DOC_ROOT}/user/login" title="{l}Login Here{/l}"> Login Here </a> {/l}
                <br />
                {l}No account yet?{/l} <a href="{$smarty.const.DOC_ROOT}/user/register" title="{l}Register new user{/l}">{l}Create one{/l}</a>
            </div>
        </div>
            {else}                            <div id="categ" style="display: none;">

                                        <br />

                                    {include file="views/submit/add_categs.tpl" }&nbsp;

                                    </div>
        <form method="post" action="" class="cmxform phpld-form" id="submitForm" name="submitForm" enctype="multipart/form-data" >
        <div class="formPage">


            {if $catid eq 0}
            {include file="views/submit/category.tpl" categoryID=$data.CATEGORY_ID parentID=$data.PARENT_ID}
                {elseif $linktypeid eq 0}
            {include file="views/submit/linktype.tpl"}
                {else}
                <input type="hidden" name="LINK_TYPE" value="{$linktypeid}" checked="true" />
            {/if}

            {if $linktypeid neq 0}
                <div class="info-box" >
                    {l}Fields marked with a {/l}<span class="phpld-required">*</span>{l} are required.{/l}
                </div>
                {foreach from=$submit_items item=item name=submit_items}

                    {assign var="FIELD_NAME" value=$item.FIELD_NAME}
                {* special submit items *}
                    {if $item.FIELD_NAME == 'TITLE'}
                        <div class="phpld-columnar phpld-equalize">
                            <div class="phpld-label float-left">{if $valid.rules[$item.FIELD_NAME].required==true}
                                <span class="phpld-required">*</span>{/if}{l}Title{/l}:
                            </div>
                            <div class="field float-left">
                                <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                    <input type="text" id="TITLE" name="TITLE" value="{$data.TITLE|escape|trim}" maxlength="{$smarty.const.TITLE_MAX_LENGTH}" class="text" />
                                </div>
                            </div>
                        </div>
                        {elseif $item.FIELD_NAME == 'CATEGORY_ID'}
                        
                        <div class="phpld-columnar phpld-equalize">
                            <div class="phpld-label float-left">{if $valid.rules[$item.FIELD_NAME].required==true}
                                <span class="phpld-required">*</span>{/if}{l}Category{/l}:
                            </div>
                            <div class="field float-left">
                                {if !$review_link}
                                    <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                        {$CategoryTitle}
                                    </div>
                                    <div id="inlineCategSelection">
                                    {* Load category selection *}
                                        <input type="hidden" id="CATEGORY_ID" name="CATEGORY_ID" value="{$CATEGORY_ID}" />
                                    </div>
                                {else}
                                
                                     {include file="views/submit/review_categ.tpl" categ_id=$CATEGORY_ID}&nbsp;
                                {/if}

                                {if $link_type_details.MULTIPLE_CATEGORIES > 0}
                                    {foreach from=$add_categs item=categ name=additional}
                                        {if $smarty.foreach.additional.index < $link_type_details.MULTIPLE_CATEGORIES}
                                            <div>
                                                <br />
                                                
                                             {include file="views/submit/add_categs.tpl" categ_id=$categ.CATEGORY_ID additional_categs=true}&nbsp;
                                            </div>
                                        {/if}
                                    {/foreach}

                                    
                                    <a href="#" class="formDelCateg" id="fileDelCateg" onclick="return false;" style="{if $link_type_details.MULTIPLE_CATEGORIES gt '1'  and !empty($add_categs)}{else}display: none{/if}">{l}remove{/l}</a>
                                    <div class="clear"></div>
                                    <br />
                                    <a href="#" id="plusCategLink" class="formSmall" style="border-width: 0;">+ {l}additional categ{/l}</a>
                                    {/if}
                            </div>
                        </div>

                        {elseif $item.TYPE eq 'TAGS'}
                            <link rel="stylesheet" type="text/css" href="{$smarty.const.DOC_ROOT}javascripts/select2/select2.css" />
                            <script type="text/javascript" src="{$smarty.const.DOC_ROOT}javascripts/select2/select2.js" ></script>
                            <script type="text/javascript">
                                    {literal}jQuery(document).ready(function(){{/literal}
                                    {assign var="i" value=0}
                                    {assign var="tagsCount" value=$allTags|count}
                                    jQuery("#{$item.FIELD_NAME}").select2(
                                        {literal}
                                        {
                                        tags: [{/literal}
                                        {foreach from=$allTags item="tag"}
                                            {assign var="i" value=$i+1}
                                            {literal}{{/literal}id:{$tag.ID}, text:'{$tag.TITLE}'{literal}}{/literal}{if $i<$tagsCount},{/if}
                                        {/foreach}
                                        {literal}]

                                        }
                                        {/literal}
                                    );
                                    {literal}});{/literal}
                            </script>
                            <div class="phpld-columnar phpld-equalize select2-submit">
                                <div class="phpld-label float-left">{if $valid.rules.OWNER_EMAIL.required==true}
                                    <span class="phpld-required">*</span>{/if}{l}Tags{/l}:
                                </div>
                                <div class="field float-left">
                                    <div class="phpld-fbox-text  {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                        <input type="text" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$data[$item.FIELD_NAME]|escape|trim}" size="40" />
                                    </div>
                                </div>

                            </div>
                        {elseif $item.FIELD_NAME == 'OWNER_NAME'}
                        {if $regular_user_details }
                            <input type="hidden" id="OWNER_NAME" name="OWNER_NAME" value="{$data.OWNER_NAME|escape|trim}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" />
                            {else}
                            <div class="phpld-columnar phpld-equalize">
                                <div class="phpld-label float-left">{if $valid.rules.OWNER_NAME.required==true}
                                    <span class="phpld-required">*</span>{/if}{l}Owner Name{/l}:
                                </div>
                                <div class="field float-left">
                                    <div class="phpld-fbox-text  {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                        <input type="text" id="OWNER_NAME" name="OWNER_NAME" value="{$data.OWNER_NAME|escape|trim}" maxlength="{$smarty.const.USER_NAME_MAX_LENGTH}" class="text" />
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {elseif $item.FIELD_NAME == 'OWNER_EMAIL'}
                        {if $regular_user_details }
                            <input type="hidden" id="OWNER_EMAIL" name="OWNER_EMAIL" value="{$data.OWNER_EMAIL|escape|trim}" />
                            {else}
                            <div class="phpld-columnar phpld-equalize">
                                <div class="phpld-label float-left">{if $valid.rules.OWNER_EMAIL.required==true}
                                    <span class="phpld-required">*</span>{/if}{l}Owner Email{/l}:
                                </div>
                                <div class="field float-left">
                                    <div class="phpld-fbox-text  {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                        <input type="text" id="OWNER_EMAIL" name="OWNER_EMAIL" value="{$data.OWNER_EMAIL|escape|trim}" maxlength="255" class="text" />
                                    </div>
                                    <div class="phpld-clearfix"></div>
                                    <p class="small">
                                                <span style="display:inline-block; float: left;width:20px;">
                                                    <input type="checkbox" name="OWNER_NEWSLETTER_ALLOW" checked="checked" />
                                                </span>
                                        &nbsp;
                                        <span style="display: block; float: left; margin: 2px 0 0 0;">{l}Allow site administrator to send me newsletters.{/l}</span>
                                    <div style="clear: both;"></div>
                                    </p>
                                </div>
                            </div>
                        {/if}
                        {elseif $item.FIELD_NAME == 'META_KEYWORDS'}
                        {if $link_type_details.SHOW_META eq 1}
                            {if $smarty.const.ENABLE_META_TAGS eq 1}
                                <div class="phpld-columnar phpld-equalize">
                                    <div class="phpld-label float-left">{l}META Keywords{/l}:</div>
                                    <div class="field float-left">
                                        <div class="phpld-fbox-text  {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                            <input type="text" id="META_KEYWORDS" name="META_KEYWORDS" value="{$data.META_KEYWORDS|escape|trim}" class="text" />
                                        </div>
                                        <div class="phpld-clearfix"></div>
                                        <p class="msg notice info">{l}Separate keywords by comma.{/l}</p>
                                    </div>
                                </div>
                            {/if}
                        {/if}
                        {elseif $item.FIELD_NAME == 'META_DESCRIPTION'}
                        {if $link_type_details.SHOW_META eq 1}
                            {if $smarty.const.ENABLE_META_TAGS eq 1}
                                <div class="phpld-columnar phpld-equalize">
                                    <div class="phpld-label float-left">{l}META Description{/l}:</div>
                                    <div class="smallDesc float-left">
                                        <div class="phpld-fbox-text  {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                            <textarea id="META_DESCRIPTION" name="META_DESCRIPTION" rows="6" cols="70" class="text" {formtool_count_chars name="META_DESCRIPTION" limit=$smarty.const.META_DESCRIPTION_MAX_LENGTH alert=true}>{$data.META_DESCRIPTION|trim|escape}</textarea>
                                        </div>
                                        <div class="phpld-clearfix"></div>
                                        <p class="limitDesc float-left">{l}Limit{/l}:
                                        <div class="phpld-fbox-text">
                                            <input type="text" name="META_DESCRIPTION_limit" class="limit_field" readonly="readonly" value="{$MetaDescriptionLimit}" /></p>
                                    </div>
                                </div>
                            </div>
                            {/if}
                        {/if}
			
                        {elseif $item.FIELD_NAME == 'RELEASE_DATE'}
                        {if $user_level eq 1}
                        <div class="phpld-columnar phpld-equalize">
                            <div class="phpld-label float-left">
                                {if $valid.rules[$item.FIELD_NAME].required==true}
                                    <span class="phpld-required">*</span>
                                {/if}
                                {l}{$item.NAME}{/l}:
                            </div>
                            <div class="smallDesc float-left">
                                <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                    <input type="text" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$data[$item.FIELD_NAME]|escape|trim}" size="40" class="text" />
                                </div>
                                <div class="phpld-clearfix"></div>
                            </div>
                        </div>
                        {/if}
                        {elseif $item.FIELD_NAME == 'THUMBNAIL_WIDTH_GRID'}
                    <input type="hidden" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$link_type_details.DEFAULT_THUMBNAIL_GRID}" />
                        {elseif $item.FIELD_NAME == 'THUMBNAIL_WIDTH_LIST'}
                    <input type="hidden" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="{$link_type_details.DEFAULT_THUMBNAIL_LIST}" />
                        {elseif $item.FIELD_NAME eq 'RECPR_URL'}
                    <div class="phpld-columnar phpld-equalize">
                        <div class="phpld-label float-left">
                            {if $valid.rules[$item.FIELD_NAME].required==true}
                                <span class="phpld-required">*</span>
                            {/if}{l}{$item.NAME}{/l}:
                        </div>
                        <div class="field float-left">
                            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                <input type="text" name="{$item.FIELD_NAME}" id="{$item.FIELD_NAME}" value="" size="40" maxlength="255" class="text" />
                            </div>

                            {if $smarty.const.SAME_DOMAIN_RECPR == 1}
                                <p class="small">{l}We require the Reciprocal to be on the same domain as the link you are submitting.{/l}</p>
                            {/if}
                            <div class="phpld-clearfix"></div>
                            <p class="small">{l}To validate the reciprocal link please include the{/l}<br />{l}following HTML code in the page at the URL{/l}<br />{l}specified above, before submiting this form{/l}:</p>
                            <div class="phpld-fbox-text {if $error_list.$FIELD_NAME}phpld-error{/if}">
                                <textarea name="RECPR_TEXT" rows="2" readonly="readonly" cols="50" class="text">&lt;a href="{$smarty.const.DEFAULT_RECPR_URL}"&gt;{$smarty.const.DEFAULT_RECPR_TITLE}&lt;/a&gt;</textarea>
                            </div>
                        </div>
                    </div>
                        {else}
                    {include file="views/submit/fields.tpl" item=$item valid=$valid data=$data}
                    {/if}
                {/foreach}

                {if $link_type_details.DEEP_LINKS > 0}
                <div class="phpld-columnar phpld-equalize">
                    <div class="phpld-label required">
                        <label>{l}Deep Links{/l}:</label>
                    </div>
                    <div class="field float-left">
                        <div id="additional_links">
                            <div >
                                <div  style="width:49%;float: left;">{l}Titles{/l}</div>
                                <div  style="width:49%;float: left;">{l}URLs{/l}</div>
                            </div>

                            {section name=i start=0 loop=$link_type_details.DEEP_LINKS}
                                <div>
                                    <div class="phpld-fbox-text" style="float: left;width: 45%;"><input type="text" name="ADD_LINK_TITLE[]" class="text" style="width: 160px;" value="{$add_links[i].TITLE}" /></div>
                                    <div class="phpld-fbox-text" style="float: left;width: 45%;"><input type="text" name="ADD_LINK_URL[]"   class="text" style="width: 160px;" value="{$add_links[i].URL}" /></div>
                                </div>
                            {/section}
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                {/if}

                {if $remove_link}
                <div class="phpld-columnar phpld-equalize">
                    <div class="phpld-label float-left">{l}Mark for removal{/l}:</div>
                    <div class="phpld-field float-left">
                        <input type="checkbox" id="MARK_REMOVE" name="MARK_REMOVE" /><span class="small">{l}Use with caution{/l}</span>
                    </div>
                </div>
                {/if}
                {if $smarty.const.VISUAL_CONFIRM eq 2}
                <div class="phpld-columnar phpld-equalize">
                    <div class="phpld-label float-left"><span class="phpld-required">*</span>
                        {l}DO THE MATH{/l}:
                    </div>
                    <div class="field float-left">
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
                    </div>
                </div>
                {/if}
                {if $smarty.const.VISUAL_CONFIRM eq 1}
                    {if $dont_show_captch neq 1}
                    <div class="phpld-columnar phpld-equalize">
                        <div class="phpld-label float-left">
                            <span class="phpld-required">*</span>
                            {l}Enter the code shown{/l}:
                        </div>
                        <div class="field float-left">
                            <div class="phpld-fbox-text {if $error_list.CAPTCHA}phpld-error{/if}">
                                {if $error_list.CAPTCHA}<p class="phpld-message">{$error_list.CAPTCHA}</p>{/if}
                                <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="{$imagehash}" />
                                <input class="required text" id="CAPTCHA" name="CAPTCHA" style="width:80px;" type="text" value="" size="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" maxlength="{$smarty.const.CAPTCHA_PHRASE_LENGTH}" class="text" />
                                <label for="CAPTCHA" id="captcha_validation" style="float: none; color: red; padding-left: .5em; "></label>
                                <div style="clear: both;"></div>
                                <p class="small">{l}This helps prevent automated registrations.{/l}</p>
                                <img src="{$smarty.const.DOC_ROOT}/captcha.php?imagehash={$imagehash}" class="captcha" alt="{l}Visual Confirmation Security Code{/l}" title="{l}Visual Confirmation Security Code{/l}" />
                            </div>
                        </div>
                    </div>
                    {/if}
                {/if}
                {if $regular_user_details and $regular_user_details.ACTIVE and $regular_user_details.EMAIL_CONFIRMED }
                <input type="hidden" id="AGREERULES" name="AGREERULES" value="1" checked="checked" />
                    {else}
                <div class="phpld-columnar phpld-equalize">
                    <div id="AGREERULES">
                        <div class="phpld-label float-left" style="width:20%">
                            <span class="phpld-required">*</span>
                            <a href="{$smarty.const.DOC_ROOT}/rules" title="Submission Rules" target='_blank'>{l}Submission Rules Agreement{/l}</a>:
                        </div>
                        <div class="field float-left" style="width:70%">
                            <div class="phpld-fbox-check float-left {if $error_list.AGREERULES}phpld-error{/if}">
                                {if $error_list.AGREERULES}<p class="phpld-message">{$error_list.AGREERULES}</p>{/if}
                                <input type="checkbox" id="AGREERULES" name="AGREERULES"{if $AGREERULES eq 'on'} checked="checked"{/if} />
                            </div>
                            <div class="phpld-label float-left">{l}I AGREE with the {/l}<a href="{$smarty.const.DOC_ROOT}/rules" title="Submission Rules" target='_blank'>{l}submission rules{/l}</a></div>
                        </div>
                    </div>
                </div>
                {/if}
            <div class="phpld-fbox-button">
                {if !$review_link and !$remove_link}
                    <div class="phpld-fbox-button">
                        
                        <input type="submit" name="continue" value="{l}Continue{/l}" onclick="this.value='Please Wait'"  class="button" />
                    </div>
                    {else}
                    <input type="hidden" name="id" value="{$data.ID}"/>
                    <input type="submit" name="edit" value="{l}Continue{/l}" class="button" />
                {/if}
            </div>
            {/if}
        <input type="hidden" name="formSubmitted" value="1" />
        </div>
        </form>
        {/if}
    {/if}
<div class="phpld-clearfix"></div>
    {literal}
    <script type="text/javascript">
    //<![CDATA[

            jQuery(document).ready(function(){

                jQuery("input[name=choicemade]").click(function(){

                   // alert('asdasd');
                    //update_form(); // albert: seems useless, we are making a refresh when the user changes the link type
                    var ts = jQuery('input[name=LINK_TYPE]:checked').val()
                    var lt = getUrlVars()["LINK_TYPE"];
                    if (lt) {
                        var url = location.href.replace("LINK_TYPE="+lt, "LINK_TYPE=" + ts);
                    } else {
                        if (location.href.search(/\?/) == -1) {
                            var url = location.href + "?LINK_TYPE="+ts;
                        } else {
                            var url = location.href + "&LINK_TYPE="+ts;
                        }
                    }
                    location.href= url;
                });

                function getUrlVars()
                {
                    var vars = [], hash;
                    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                    for(var i = 0; i < hashes.length; i++)
                    {
                        hash = hashes[i].split('=');
                        vars.push(hash[0]);
                        vars[hash[0]] = hash[1];
                    }
                    return vars;
                }

                var maxCats = {/literal}{if $link_type_details.MULTIPLE_CATEGORIES == ''}1{else}{$link_type_details.MULTIPLE_CATEGORIES}{/if}{literal};
                var numCats = {/literal}1 + {$add_categs|@count}{literal};

                jQuery("#addCateg").click(function(){
                    if (numCats >= maxCats){
                        jQuery('#plusCategLink').hide();
                    }
                });

                jQuery("#plusCategLink").click(function(){

                    if (numCats < maxCats) {
                        jQuery("#categ").clone(true).insertBefore("#fileDelCateg").attr('id','').show();
                        jQuery("#fileDelCateg").show();
                        numCats++;
                    }
                    if (numCats == maxCats)
                        jQuery(this).hide();

                    return false;
                });

                jQuery("#fileDelCateg").click(function(){

                    if (numCats > 1) {
                        jQuery(this).prev().remove();
                        numCats--;
                    }
                    if (numCats < maxCats) {
                        jQuery("#plusCategLink").show();
                    }
                    if (numCats == 1)
                        jQuery(this).hide();

                    return false;
                });
            });


            Array.prototype.in_array = function(p_val) {
                for(var i = 0, l = this.length; i < l; i++) {
                    if(this[i] == p_val) {
                        return true;
                    }
                }
                return false;
            }
        //]]>
        </script>
    {/literal}
{/strip}