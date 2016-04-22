<?php /* Smarty version Smarty-3.1.12, created on 2014-10-03 18:03:23
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/details/tellfriend.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1498932136542ee4ebbce600-25583933%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0144a205c96b22f4343fdb009f1d4f52f2e1408c' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/details/tellfriend.tpl',
      1 => 1392374246,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1498932136542ee4ebbce600-25583933',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'EMAIL' => 0,
    'FRIEND_EMAIL' => 0,
    'MESSAGE' => 0,
    'error_list' => 0,
    'errorKey' => 0,
    'errorItem' => 0,
    'DO_MATH_N1' => 0,
    'DO_MATH_N2' => 0,
    'DO_MATH' => 0,
    'dont_show_captch' => 0,
    'imagehash' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542ee4ebc7d095_52684298',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542ee4ebc7d095_52684298')) {function content_542ee4ebc7d095_52684298($_smarty_tpl) {?><form method="post" action="" id="tell_friend" class="phpld-form">
    <input type="hidden" name="formSubmitted" value="1" />
    <div class="phpld-columnar phpld-equalize">
        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>Your Email:</label>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="EMAIL" name="EMAIL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['EMAIL']->value, ENT_QUOTES, 'UTF-8', true));?>
" size="40" class="text" />
            </div>
        </div>

        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>Friend's Email:</label>
            <div class="phpld-fbox-text float-left">
                <input type="text" id="FRIEND_EMAIL" name="FRIEND_EMAIL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['FRIEND_EMAIL']->value, ENT_QUOTES, 'UTF-8', true));?>
" size="40" class="text" />
            </div>
        </div>

        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>Friend's Email:</label>
            <div class="phpld-fbox-text float-left">
                <textarea id="MESSAGE" name="MESSAGE" rows="7" cols="60" class="text"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['MESSAGE']->value, ENT_QUOTES, 'UTF-8', true));?>
</textarea>
            </div>
        </div>

        <?php if (@VISUAL_CONFIRM==2){?>
    <div class="phpld-fbox-text">
        <label><sup class="phpld-required">*</sup>DO THE MATH:</label>
        <div class="phpld-fbox-text float-left">
                <span style="color: red;">
                    <?php  $_smarty_tpl->tpl_vars['errorItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['errorItem']->_loop = false;
 $_smarty_tpl->tpl_vars['errorKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['error_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['errorItem']->key => $_smarty_tpl->tpl_vars['errorItem']->value){
$_smarty_tpl->tpl_vars['errorItem']->_loop = true;
 $_smarty_tpl->tpl_vars['errorKey']->value = $_smarty_tpl->tpl_vars['errorItem']->key;
?>
                        <?php if ($_smarty_tpl->tpl_vars['errorKey']->value=='DO_MATH'){?>
                            <?php if (is_array($_smarty_tpl->tpl_vars['errorItem']->value)){?>
                                <?php echo $_smarty_tpl->tpl_vars['errorItem']->value['remote'];?>

                                <?php }else{ ?>
                                <?php echo $_smarty_tpl->tpl_vars['errorItem']->value;?>

                            <?php }?>
                        <?php }?>
                    <?php } ?>
                </span>
            <font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;"><?php echo $_smarty_tpl->tpl_vars['DO_MATH_N1']->value;?>
 + <?php echo $_smarty_tpl->tpl_vars['DO_MATH_N2']->value;?>
 = </font><input type="text" id="DO_MATH" name="DO_MATH" value='<?php echo $_smarty_tpl->tpl_vars['DO_MATH']->value;?>
' class="text" style="width: 60px; float: right;"/>
            
            
        </div>
    </div>
<?php }?>

<?php if (@VISUAL_CONFIRM==1){?>
    <?php if ($_smarty_tpl->tpl_vars['dont_show_captch']->value!=1){?>
        <div class="phpld-fbox-text">
            <label><sup class="phpld-required">*</sup>Enter the code shown:</label>
            <div class="phpld-fbox-text float-left">
                <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" />
                <input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" maxlength="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" class="text" />
                
                <p class="small">This helps prevent automated registrations.</p>
                <img src="<?php echo @DOC_ROOT;?>
/captcha.php?imagehash=<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" class="captcha" alt="Visual Confirmation Security Code" title="Visual Confirmation Security Code" />
            </div>
        </div>
    <?php }?>
<?php }?>  

        <div class="phpld-fbox-button">
            <div class="float-right">
                <input type="submit" id="submit" name="edit" value="Continue" class="btn" />
            </div>
        </div>
    </div>
</form>
<?php }} ?>