<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:11:41
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_logo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1362842632535a974d190b41-60006353%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a219ec4610c6de08d2e26063c17fc021828667ec' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_logo.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1362842632535a974d190b41-60006353',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logoOptions' => 0,
    'conf_group' => 0,
    'conf_categs' => 0,
    'currentLogo' => 0,
    'row' => 0,
    'submit_session' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a974d40dc97_01827052',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a974d40dc97_01827052')) {function content_535a974d40dc97_01827052($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.cycle.php';
?>
<?php echo $_smarty_tpl->getSubTemplate ("messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<style type="text/css" media="screen">
    @import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/datatable_jui.css";
    @import "<?php echo @TEMPLATE_ROOT;?>
/themes/smoothness/jquery-ui-1.7.2.custom.css";
    
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
    
</style>

<script>
    
    jQuery(function() {
        jQuery( "#widthSlider" ).slider({
            'min' : 20,
            'max' : 400,
            'value' : <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['widthValue'];?>
,
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
            'value' : <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginTopValue'];?>
,
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-top', ui.value);
                jQuery('#marginTopValue' ).val(ui.value);
                jQuery('#marginTopLabel' ).html(ui.value+'px');
            }
        });
        jQuery( "#marginRightSlider" ).slider({
            'min' : 0,
            'max' : 200,
            'value' : <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginRightValue'];?>
,
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-right', ui.value);
                jQuery('#marginRightValue' ).val(ui.value);
                jQuery('#marginRightLabel' ).html(ui.value+'px');
            }
        });
        jQuery( "#marginBottomSlider" ).slider({
            'min' : 0,
            'max' : 200,
            'value' : <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginBottomValue'];?>
,
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-bottom', ui.value);
                jQuery('#marginBottomValue' ).val(ui.value);
                jQuery('#marginBottomLabel' ).html(ui.value+'px');
            }
        });
        jQuery( "#marginLeftSlider" ).slider({
            'min' : 0,
            'max' : 200,
            'value' : <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginLeftValue'];?>
,
            slide: function( event, ui ) {
                jQuery('.logoWrapper img' ).css('margin-left', ui.value);
                jQuery('#marginLeftValue' ).val(ui.value);
                jQuery('#marginLeftLabel' ).html(ui.value+'px');
            }
        });

        jQuery('.logoWrapper img' ).css('margin-top', <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginTopValue'];?>
);
        jQuery('.logoWrapper img' ).css('margin-right', <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginRightValue'];?>
);
        jQuery('.logoWrapper img' ).css('margin-bottom', <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginBottomValue'];?>
);
        jQuery('.logoWrapper img' ).css('margin-left', <?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginLeftValue'];?>
);
    });
    
</script>

<div class="block">
    <form method="post" id="submit_form" enctype="multipart/form-data">
        <table class="formPage">
            <thead>
            <tr>
                <th colspan="2"><?php if (!empty($_smarty_tpl->tpl_vars['conf_categs']->value[$_smarty_tpl->tpl_vars['conf_group']->value])){?><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['conf_categs']->value[$_smarty_tpl->tpl_vars['conf_group']->value], ENT_QUOTES, 'UTF-8', true));?>
<?php }else{ ?>Configuration options<?php }?></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($_smarty_tpl->tpl_vars['currentLogo']->value)){?>
            <input type="hidden" name="logo_options[marginTopValue]" id="marginTopValue" value="<?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginTopValue'];?>
">
            <input type="hidden" name="logo_options[marginRightValue]" id="marginRightValue" value="<?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginRightValue'];?>
">
            <input type="hidden" name="logo_options[marginBottomValue]" id="marginBottomValue" value="<?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginBottomValue'];?>
">
            <input type="hidden" name="logo_options[marginLeftValue]" id="marginLeftValue" value="<?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginLeftValue'];?>
">
            <input type="hidden" name="logo_options[widthValue]" id="widthValue" value="<?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['widthValue'];?>
">
                <tr class="<?php echo smarty_function_cycle(array('values'=>'odd,even'),$_smarty_tpl);?>
">
                    <td  class="smallDesc" colspan="2">
                        <a href="<?php echo @DOC_ROOT;?>
/conf_logo.php?act=clear" onclick="return confirm('Are you sure?')"><img src="<?php echo @TEMPLATE_ROOT;?>
/images/cross.png" border="0"/></a>
                        <table class="logoPreview" style="width: 600px; float:left;">
                            <tbody>
                                <tr>
                                    <td colspan="3"><span id="marginTopLabel"><?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginTopValue'];?>
px</span></td>
                                </tr>
                                <tr>
                                    <td><span id="marginLLeftLabel"><?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginLeftValue'];?>
px</span></td>
                                    <td>
                                        <div class="logoWrapper">
                                            <img src="<?php echo @FRONT_DOC_ROOT;?>
logo_thumbnail.php?pic=/<?php echo $_smarty_tpl->tpl_vars['currentLogo']->value;?>
<?php if (!empty($_smarty_tpl->tpl_vars['logoOptions']->value['widthValue'])){?>&width=<?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['widthValue'];?>
<?php }?>" />
                                        </div>
                                    </td>
                                    <td><span id="marginRightLabel"><?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginRightValue'];?>
px</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><span id="marginBottomLabel"><?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['marginBottomValue'];?>
px</span></td>
                                </tr>
                                <tr>
                                    <td colspan="3">&larr;<span id="widthLabel"><?php echo $_smarty_tpl->tpl_vars['logoOptions']->value['widthValue'];?>
px</span>&rarr;</td>
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
            <?php }?>
                <tr class="<?php echo smarty_function_cycle(array('values'=>'odd,even'),$_smarty_tpl);?>
">
                    <td  class="label<?php if ($_smarty_tpl->tpl_vars['row']->value['REQUIRED']==1){?> required<?php }?>">
                        <label for="logo">Logo file:</label>
                    </td>
                    <td  class="smallDesc">
                        <input type="file" id="logo" name="logo" />
                        <div class="description">
                            "<a href="<?php echo @DOC_ROOT;?>
/conf_settings.php?c=1&r=1">Site name</a>" will be used as logo, if it not uploaded.
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2"><input type="submit" id="send-conf-submit" name="save" value="Save" alt="Save form" title="Save settings" class="button" /></td>
            </tr>
            </tfoot>
        </table>
        <input type="hidden" name="formSubmitted" value="1" />
        <input type="hidden" name="submit_session" value="<?php echo $_smarty_tpl->tpl_vars['submit_session']->value;?>
" />
    </form>
</div><?php }} ?>