{* Error and confirmation messages *}
{include file="messages.tpl"}
<script type="text/javascript" src="{$smarty.const.FRONT_DOC_ROOT}javascripts/colorpicker/farbtastic.js"></script>
<link rel="stylesheet" href="{$smarty.const.FRONT_DOC_ROOT}javascripts/colorpicker/farbtastic.css" type="text/css" />
<script type="text/javascript">
    {literal}
    jQuery(document).ready(function(){
        jQuery('#colorPicker')
                .farbtastic(function(color){
            jQuery('.backgroundPreview .preview').css('background-color', color);
            jQuery('#bgcolor').val(color);
        });

        jQuery('.template-backgrounds li').click(function(){
            jQuery('.backgroundPreview .preview').css('background-image', 'url("'+jQuery(this).attr('data')+'")')
            jQuery('#pattern').val(jQuery(this).attr('data'));
            jQuery('.template-backgrounds li').removeClass('active');
            jQuery(this).addClass('active');
        });

        jQuery('.template-backgrounds li[data="'+jQuery('#pattern').val()+'"]').addClass('active');
        jQuery('.backgroundPreview .preview').css('background-image', 'url("'+jQuery('#pattern').val()+'")')
        jQuery('.backgroundPreview .preview').css('background-color', jQuery('#bgcolor').val());
    });
    {/literal}
</script>
<div class="block">
    <div class="listHeader">Background template</div>
    <form method="post" id="submit_form">
        <input type="hidden" name="bgcolor" id="bgcolor" value="{$bgcolor}">
        <input type="hidden" name="pattern" id="pattern" value="{$pattern}">

    <div class="backgroundPreview">
        <p id="colorPicker"></p>
        <div class="preview"></div>
    </div>

    <ul class="template-backgrounds">
    {foreach from=$backgrounds item=background}
        <li data="{$smarty.const.FRONT_DOC_ROOT}templates/{$smarty.const.TEMPLATE}/images/backgrounds/{$background.filename}" style="background-image: url('{$smarty.const.FRONT_DOC_ROOT}templates/{$smarty.const.TEMPLATE}/images/backgrounds/{$background.filename}')">
        </li>
    {/foreach}
    </ul>
        <div style="clear:both">
        <input type="submit" value="Save">
        </div>
    </form>
</div>