<?php /* Smarty version Smarty-3.1.12, created on 2014-04-30 16:25:17
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/contact/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1740572420536123ed794ec5-20651476%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd1ce87d55f6b33e66e67c76dc1f562adaf628266' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/contact/index.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1740572420536123ed794ec5-20651476',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'NAME' => 0,
    'EMAIL' => 0,
    'SUBJECT' => 0,
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
  'unifunc' => 'content_536123ed838e54_47422963',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_536123ed838e54_47422963')) {function content_536123ed838e54_47422963($_smarty_tpl) {?><li class="group">Contact Us</li><li><?php if (@DISABLE_CONTACT_FORM==1){?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
             jQuery("#submitForm input, #submitForm select, #submitForm textarea, #submitForm checkbox, #submitForm radio , #submitForm submit").attr("disabled", true);   
            });
    </script>
<?php }?><form method="post" action="" id="submitForm" class="phpld-form"><div class="contactPage"><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Name:</div><div class="phpld-fbox-text float-left"><input type="text" id="NAME" name="NAME" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
" size="40" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Your Mail:</div><div class="phpld-fbox-text float-left"><input type="text" id="EMAIL" name="EMAIL" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['EMAIL']->value, ENT_QUOTES, 'UTF-8', true));?>
" size="40" maxlength="255" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Subject:</div><div class="phpld-fbox-text float-left"><input type="text" id="SUBJECT" name="SUBJECT" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['SUBJECT']->value, ENT_QUOTES, 'UTF-8', true));?>
" size="40" maxlength="255" class="text" /></div></div><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>Message:</div><div class="phpld-fbox-text float-left"><textarea id="MESSAGE" name="MESSAGE" rows="10" cols="50" class="text"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['MESSAGE']->value, ENT_QUOTES, 'UTF-8', true));?>
</textarea></div></div><?php if (@VISUAL_CONFIRM==2){?><div class="phpld-columnar phpld-equalize"><div class="phpld-label float-left"><span class="phpld-required">*</span>DO THE MATH:</div><div class="phpld-fbox-text float-left"><span style="color: red;"><?php  $_smarty_tpl->tpl_vars['errorItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['errorItem']->_loop = false;
 $_smarty_tpl->tpl_vars['errorKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['error_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['errorItem']->key => $_smarty_tpl->tpl_vars['errorItem']->value){
$_smarty_tpl->tpl_vars['errorItem']->_loop = true;
 $_smarty_tpl->tpl_vars['errorKey']->value = $_smarty_tpl->tpl_vars['errorItem']->key;
?><?php if ($_smarty_tpl->tpl_vars['errorKey']->value=='DO_MATH'){?><?php if (is_array($_smarty_tpl->tpl_vars['errorItem']->value)){?><?php echo $_smarty_tpl->tpl_vars['errorItem']->value['remote'];?>
<br/><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['errorItem']->value;?>
<br/><?php }?><?php }?><?php } ?></span><font style="font-weight: bold; font-size: 14pt; color: red; margin-right: 10px;"><?php echo $_smarty_tpl->tpl_vars['DO_MATH_N1']->value;?>
 + <?php echo $_smarty_tpl->tpl_vars['DO_MATH_N2']->value;?>
 = </font><input type="text" id="DO_MATH" name="DO_MATH" value='<?php echo $_smarty_tpl->tpl_vars['DO_MATH']->value;?>
' class="text" style="width: 60px;"/><br/><br/></div></div><?php }?><?php if (@VISUAL_CONFIRM==1){?><?php if ($_smarty_tpl->tpl_vars['dont_show_captch']->value!=1){?><div><div class="phpld-columnar phpld-equalize"><div class="phpld-label"><span class="phpld-required">*</span>Enter the code shown:</div><div class="phpld-fbox-text float-left"><input id="IMAGEHASH" name="IMAGEHASH" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" /><input class="required text" id="CAPTCHA" name="CAPTCHA" type="text" value="" size="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
" maxlength="<?php echo @CAPTCHA_PHRASE_LENGTH;?>
"/><label for="CAPTCHA" id="captcha_validation" style="float: none; color: red; padding-left: .5em; "></label><div style="clear: both;"></div><p class="small">This helps prevent automated registrations.</p><img src="<?php echo @DOC_ROOT;?>
/captcha.php?imagehash=<?php echo $_smarty_tpl->tpl_vars['imagehash']->value;?>
" class="captcha" alt="Visual Confirmation Security Code" title="Visual Confirmation Security Code" /></div></div></div><?php }?><?php }?><div class="phpld-columnar phpld-equalize"><div class="phpld-fbox-button"><div class="float-right"><input type="submit" id="continue" name="continue" value="Continue" class="button" /></div></div></div><input type="hidden" name="formSubmitted" value="1" /></div></form></li><?php }} ?>