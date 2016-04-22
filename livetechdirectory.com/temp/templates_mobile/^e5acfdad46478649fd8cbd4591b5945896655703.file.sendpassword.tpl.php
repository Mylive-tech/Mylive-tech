<?php /* Smarty version Smarty-3.1.12, created on 2014-08-06 23:53:36
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/sendpassword.tpl" */ ?>
<?php /*%%SmartyHeaderCode:154934891953e2c0004e03d1-37138066%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5acfdad46478649fd8cbd4591b5945896655703' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/user/sendpassword.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '154934891953e2c0004e03d1-37138066',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LOGIN' => 0,
    'EMAIL' => 0,
    'imagehash' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53e2c00050c338_14568040',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53e2c00050c338_14568040')) {function content_53e2c00050c338_14568040($_smarty_tpl) {?><li class="group">Recover Password</li>
<li>
<form method="post" action="" id="submit_form" class="phpld-form">
    <div class="formPage">
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
        <?php if (@VISUAL_CONFIRM){?>
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
        <div class="phpld-columnar phpld-equalize">
            <div class="phpld-fbox-button">
                <div class="float-right">
                    <input type="submit" name="login" value="Submit" class="button" />
                </div>
            </div>
        </div>
    </div>
</form></li><?php }} ?>