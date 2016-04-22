{* Error and confirmation messages *}
{include file="messages.tpl"}
<style type="text/css" media="screen">
    @import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/datatable_jui.css";
    @import "{$smarty.const.TEMPLATE_ROOT}/themes/smoothness/jquery-ui-1.7.2.custom.css";
    {literal}
    #content-main img{
        margin: 0px;
    }
    label {
        margin: 5px 0;
    }
    .logoWrapper {
        padding:0 !important;
        margin:0 !important;
        background-color: #fff;
        border: 1px solid #ccc;
        display: inline-block;
    }

    .logoWrapper img {
        border: 1px solid #ccc !important;

    }

    table.logoPreview span{
        padding: 5px;
    }
    table.logoPreview td{
        vertical-align: middle;
        text-align: center;
        padding: 5px;
    }
    {/literal}
</style>

<script>
    {literal}
    jQuery(function() {
        jQuery( "#widthSlider" ).slider({
            'min' : 20,
            'max' : 400,
            'value' : {/literal}{$logoOptions.widthValue}{literal},
            stop: function( event, ui ) {
                var src = jQuery('.logoWrapper img' ).attr('src');
                src = src.replace(/width=([0-9]+)/g, 'width='+ui.value);
                jQuery('.logoWrapper img' ).attr('src', src);
            },
            slide: function( event, ui ) {
                jQuery('#widthLabel').html(ui.value+'px');
                jQuery('#widthValue').val(ui.value);
            }
        });

        jQuery( "#marginTopSlider" ).slider({
            'min' : 0,
            'max' : 200,
            'value' : {/literal}{$logoOptions.marginTopValue}{literal},
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-top', ui.value);
                jQuery('#marginTopValue' ).val(ui.value);
                jQuery('#marginTopLabel' ).html(ui.value+'px');
            }
        });
        jQuery( "#marginRightSlider" ).slider({
            'min' : 0,
            'max' : 200,
            'value' : {/literal}{$logoOptions.marginRightValue}{literal},
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-right', ui.value);
                jQuery('#marginRightValue' ).val(ui.value);
                jQuery('#marginRightLabel' ).html(ui.value+'px');
            }
        });
        jQuery( "#marginBottomSlider" ).slider({
            'min' : 0,
            'max' : 200,
            'value' : {/literal}{$logoOptions.marginBottomValue}{literal},
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-bottom', ui.value);
                jQuery('#marginBottomValue' ).val(ui.value);
                jQuery('#marginBottomLabel' ).html(ui.value+'px');
            }
        });
        jQuery( "#marginLeftSlider" ).slider({
            'min' : 0,
            'max' : 200,
            'value' : {/literal}{$logoOptions.marginLeftValue}{literal},
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-left', ui.value);
                jQuery('#marginLeftValue' ).val(ui.value);
                jQuery('#marginLeftLabel' ).html(ui.value+'px');
            }
        });

        jQuery('.logoWrapper img' ).css('margin-top', {/literal}{$logoOptions.marginTopValue}{literal});
        jQuery('.logoWrapper img' ).css('margin-right', {/literal}{$logoOptions.marginRightValue}{literal});
        jQuery('.logoWrapper img' ).css('margin-bottom', {/literal}{$logoOptions.marginBottomValue}{literal});
        jQuery('.logoWrapper img' ).css('margin-left', {/literal}{$logoOptions.marginLeftValue}{literal});
    });
    {/literal}
</script>

<div class="block">
    <form method="post" id="submit_form" enctype="multipart/form-data">
        <table class="formPage">
            <thead>
            <tr>
                <th colspan="2">{if !empty($conf_categs.$conf_group)}{$conf_categs.$conf_group|escape|trim}{else}{l}Configuration options{/l}{/if}</th>
            </tr>
            </thead>
            <tbody>
            {if !empty($currentLogo)}
            <input type="hidden" name="logo_options[marginTopValue]" id="marginTopValue" value="{$logoOptions.marginTopValue}">
            <input type="hidden" name="logo_options[marginRightValue]" id="marginRightValue" value="{$logoOptions.marginRightValue}">
            <input type="hidden" name="logo_options[marginBottomValue]" id="marginBottomValue" value="{$logoOptions.marginBottomValue}">
            <input type="hidden" name="logo_options[marginLeftValue]" id="marginLeftValue" value="{$logoOptions.marginLeftValue}">
            <input type="hidden" name="logo_options[widthValue]" id="widthValue" value="{$logoOptions.widthValue}">
                <tr class="{cycle values='odd,even'}">
                    <td  class="smallDesc" colspan="2">
                        <a href="{$smarty.const.DOC_ROOT}/conf_logo.php?act=clear" onclick="return confirm('Are you sure?')"><img src="{$smarty.const.TEMPLATE_ROOT}/images/cross.png" border="0"/></a>
                        <table class="logoPreview" style="width: 600px; float:left;">
                            <tbody>
                                <tr>
                                    <td colspan="3"><span id="marginTopLabel">{$logoOptions.marginTopValue}px</span></td>
                                </tr>
                                <tr>
                                    <td><span id="marginLLeftLabel">{$logoOptions.marginLeftValue}px</span></td>
                                    <td>
                                        <div class="logoWrapper">
                                            <img src="{$smarty.const.FRONT_DOC_ROOT}logo_thumbnail.php?pic=/{$currentLogo}{if !empty($logoOptions.widthValue)}&width={$logoOptions.widthValue}{/if}" />
                                        </div>
                                    </td>
                                    <td><span id="marginRightLabel">{$logoOptions.marginRightValue}px</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><span id="marginBottomLabel">{$logoOptions.marginBottomValue}px</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3">&larr;<span id="widthLabel">{$logoOptions.widthValue}px</span>&rarr;</td>
                                </tr>
                            </tbody>
                        </table>

                        <div style="padding: 15px;width:300px; float: left;">
                            <h2>Width: </h2>
                            <div id="widthSlider"></div>
                            <h2>Margins: </h2>
                            <label>Top:</label><div id="marginTopSlider"></div>
                            <label>Right:</label><div id="marginRightSlider"></div>
                            <label>Bottom:</label><div id="marginBottomSlider"></div>
                            <label>Left:</label><div id="marginLeftSlider"></div>
                        </div>
                    </td>
                </tr>
            {/if}
                <tr class="{cycle values='odd,even'}">
                    <td  class="label{if $row.REQUIRED eq 1} required{/if}">
                        <label for="logo">Logo file:</label>
                    </td>
                    <td  class="smallDesc">
                        <input type="file" id="logo" name="logo" />
                        <div class="description">
                            "<a href="{$smarty.const.DOC_ROOT}/conf_settings.php?c=1&r=1">Site name</a>" will be used as logo, if it not uploaded.
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><input type="submit" id="send-conf-submit" name="save" value="{l}Save{/l}" alt="{l}Save form{/l}" title="{l}Save settings{/l}" class="button" /></td>
            </tr>
            </tfoot>
        </table>
        <input type="hidden" name="formSubmitted" value="1" />
        <input type="hidden" name="submit_session" value="{$submit_session}" />
    </form>
</div>