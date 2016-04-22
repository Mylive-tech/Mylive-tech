<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 15:21:52
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/sendpassword.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17028322885655d2104e7858-05667438%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '47e8a323b12defa7f91bc4e2ab94663c64cdc2ab' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/user/sendpassword.tpl',
      1 => 1395373392,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17028322885655d2104e7858-05667438',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LOGIN' => 0,
    'EMAIL' => 0,
    'imagehash' => 0,
    'error_list' => 0,
    'errorKey' => 0,
    'errorItem' => 0,
    'DO_MATH_N1' => 0,
    'DO_MATH_N2' => 0,
    'DO_MATH' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5655d2105176a7_64447359',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5655d2105176a7_64447359')) {function content_5655d2105176a7_64447359($_smarty_tpl) {?><form method="post" action="" id="submit_form" class="phpld-form">
    <div class="formPage">
        <h3>Recover Password</h3>
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">Login</div>
            <div class="phpld-fbox-text float-left">
                <input type="text" name="LOGIN" value="<?php echo $_smarty_tpl->tpl_vars['LOGIN']->value;?>
" size="20" maxlength="<?php echo @USER_LOGIN_MAX_LENGTH;?>
" class="text" />
            </div>
        </div>
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-label float-left">Email</div>
            <div class="phpld-fbox-text float-left">
                <input type="text" name="EMAIL" value="<?php echo $_smarty_tpl->tpl_vars['EMAIL']->value;?>
" size="20" maxlength="255" class="text" />
            </div>
        </div>
        <?php if (@VISUAL_CONFIRM==1){?>
            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left">
                    Confirmation code
                    <p class="small">This helps prevent automated registrations.</p>
                </div>
                <div class="phpld-fbox-text float-left">
                    <input id="CAPTCHA" name="CAPTCHA" type="text" value="" size="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" maxlength="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" class="text" />
                </div>
                <div class="phpld-fbox-text float-left">
                    <input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" />
                    <img src="<?php echo @DOC_ROOT;?>
/captcha.php?imagehash=<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" class="captcha" alt="Visual Confirmation Security Code" title="Visual Confirmation Security Code" />

                </div>
            </div>
        <?php }?>
        <?php if (@VISUAL_CONFIRM==2){?>
             <div class="phpld-columnar phpld-equalize">
                <div class="phpld-label float-left"><span class="phpld-required">*</span>DO THE MATH:</div>
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
<br/>
                                        <?php }else{ ?>
                                        <?php echo $_smarty_tpl->tpl_vars['errorItem']->value;?>
<br/>
                                    <?php }?>
                                <?php }?>
                            <?php } ?>
                        </span>
                    <font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;float: left;"><?php echo $_smarty_tpl->tpl_vars['DO_MATH_N1']->value;?>
 + <?php echo $_smarty_tpl->tpl_vars['DO_MATH_N2']->value;?>
 = </font><br/><input type="text" id="DO_MATH" name="DO_MATH" value='<?php echo $_smarty_tpl->tpl_vars['DO_MATH']->value;?>
' class="text" style="width: 60px;"/>
                    <br/>
                    <br/>
                </div>
            </div>
        <?php }?>
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-fbox-button">
                <div class="float-right">
                    <input type="submit" name="login" value="Submit" class="button" />
                </div>
            </div>
        </div>
    </div>
</form><?php }} ?>