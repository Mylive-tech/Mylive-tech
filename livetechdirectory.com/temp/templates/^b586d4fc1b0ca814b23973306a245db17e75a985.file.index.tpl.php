<?php /* Smarty version Smarty-3.1.12, created on 2014-05-14 08:21:15
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/payment/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2445216195373277b263376-15316623%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b586d4fc1b0ca814b23973306a245db17e75a985' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/payment/index.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2445216195373277b263376-15316623',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'paypal' => 0,
    'SubscriptionEnabled' => 0,
    'PAYMENT' => 0,
    'URL' => 0,
    'ID' => 0,
    'OWNER_EMAIL' => 0,
    'Subscription' => 0,
    'error' => 0,
    'TITLE' => 0,
    'DESCRIPTION' => 0,
    'OWNER_NAME' => 0,
    'SubscribeOptions' => 0,
    'subscribe' => 0,
    'price' => 0,
    'SubscriptionPeriod' => 0,
    'SubscriptionUm' => 0,
    'SubLength' => 0,
    'quantity' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5373277b75afb6_00120463',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5373277b75afb6_00120463')) {function content_5373277b75afb6_00120463($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/modifier.escape.php';
if (!is_callable('smarty_function_html_radios')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.html_radios.php';
?><?php $_smarty_tpl->_capture_stack[0][] = array("title", null, null); ob_start(); ?>Link Payment<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->_capture_stack[0][] = array('default', "in_page_title", null); ob_start(); ?>Link Payment<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
<?php $_smarty_tpl->_capture_stack[0][] = array('default', "description", null); ob_start(); ?>Submit a new link to the directory<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php if ($_smarty_tpl->tpl_vars['paypal']->value){?><h3>Processing Payment...</h3><form name="form" action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal"><?php if (isset($_smarty_tpl->tpl_vars['SubscriptionEnabled']->value)&&$_smarty_tpl->tpl_vars['SubscriptionEnabled']->value==1&&$_smarty_tpl->tpl_vars['PAYMENT']->value['SUBSCRIBED']==1){?><input type="hidden" name="cmd" value="_xclick-subscriptions" /><?php }else{ ?><input type="hidden" name="cmd" value="_xclick" /><?php }?><input type="hidden" name="rm" value="2"><input type="hidden" name="business" value="<?php echo @PAYPAL_ACCOUNT;?>
" /><input type="hidden" name="item_name" value="Link to <?php echo trim($_smarty_tpl->tpl_vars['URL']->value);?>
 for <?php echo trim(htmlspecialchars(@DIRECTORY_TITLE, ENT_QUOTES, 'UTF-8', true));?>
" /><input type="hidden" name="item_number" value="<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" /><input type="hidden" name="amount" value="<?php echo $_smarty_tpl->tpl_vars['PAYMENT']->value['AMOUNT'];?>
" /><input type="hidden" name="quantity" value="<?php echo $_smarty_tpl->tpl_vars['PAYMENT']->value['QUANTITY'];?>
" /><input type="hidden" name="no_shipping" value="1" /><input type="hidden" name="return" value="http://<?php echo $_SERVER['SERVER_NAME'];?>
<?php echo @DOC_ROOT;?>
/payment/paid/?pid=<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" /><input type="hidden" name="cancel_return" value="http://<?php echo $_SERVER['SERVER_NAME'];?>
<?php echo @DOC_ROOT;?>
/payment/cancel/?pid=<?php echo $_smarty_tpl->tpl_vars['ID']->value;?>
" /><input type="hidden" name="notify_url" value="http://<?php echo $_SERVER['SERVER_NAME'];?>
<?php echo @DOC_ROOT;?>
/payment/callback/?pid=<?php echo $_smarty_tpl->tpl_vars['PAYMENT']->value['ID'];?>
" /><input type="hidden" name="custom" value="<?php echo $_smarty_tpl->tpl_vars['PAYMENT']->value['ID'];?>
" /><input type="hidden" name="no_note" value="1" /><input type="hidden" name="email" value="<?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['OWNER_EMAIL']->value, ENT_QUOTES, 'UTF-8', true));?>
" /><input type="hidden" name="currency_code" value="<?php echo @PAY_CURRENCY_CODE;?>
" /><?php if (isset($_smarty_tpl->tpl_vars['SubscriptionEnabled']->value)&&$_smarty_tpl->tpl_vars['SubscriptionEnabled']->value==1&&$_smarty_tpl->tpl_vars['PAYMENT']->value['SUBSCRIBED']==1){?><input type="hidden" name="a3" value="<?php echo $_smarty_tpl->tpl_vars['PAYMENT']->value['TOTAL'];?>
" /><input type="hidden" name="p3" value="<?php echo $_smarty_tpl->tpl_vars['Subscription']->value['PERIOD'];?>
" /><input type="hidden" name="t3" value="<?php echo $_smarty_tpl->tpl_vars['Subscription']->value['UM'];?>
" /><input type="hidden" name="src" value="1" /><input type="hidden" name="sra" value="1" /><?php }?></form><script type="text/javascript">
		function mvtopp () {
		var frm = document.getElementById("paypal");
		frm.submit();
		}
		window.onload = mvtopp;
		</script><?php }else{ ?><?php if (!$_smarty_tpl->tpl_vars['ID']->value){?>Invalid link id.<?php }else{ ?><form method="post" action="" id="submit_payment_form"><table border="0" class="formPage"><?php if ($_smarty_tpl->tpl_vars['error']->value){?><tr><td colspan="2" class="err">An error occured while saving the link payment data.</td></tr><?php }?><tr><td class="label">Title:</td><td class="field"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['TITLE']->value, ENT_QUOTES, 'UTF-8', true));?>
</td></tr><?php if (trim(htmlspecialchars($_smarty_tpl->tpl_vars['URL']->value, ENT_QUOTES, 'UTF-8', true))){?><tr><td class="label">URL:</td><td class="field"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['URL']->value, ENT_QUOTES, 'UTF-8', true));?>
</td></tr><?php }?><?php if (trim(htmlspecialchars($_smarty_tpl->tpl_vars['DESCRIPTION']->value, ENT_QUOTES, 'UTF-8', true))){?><tr><td class="label">Description:</td><td class="field"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['DESCRIPTION']->value, ENT_QUOTES, 'UTF-8', true));?>
</td></tr><?php }?><?php if (trim(htmlspecialchars($_smarty_tpl->tpl_vars['OWNER_NAME']->value, ENT_QUOTES, 'UTF-8', true))){?><tr><td class="label">Your Name:</td><td class="field"><?php echo trim(htmlspecialchars($_smarty_tpl->tpl_vars['OWNER_NAME']->value, ENT_QUOTES, 'UTF-8', true));?>
</td></tr><?php }?><?php if (trim(smarty_modifier_escape($_smarty_tpl->tpl_vars['OWNER_EMAIL']->value, 'decentity'))!=''){?><tr><td class="label">Your Email:</td><td class="field"><?php echo trim(smarty_modifier_escape($_smarty_tpl->tpl_vars['OWNER_EMAIL']->value, 'decentity'));?>
</td></tr><?php }?><?php if ($_smarty_tpl->tpl_vars['SubscriptionEnabled']->value==1){?><tr><td class="label">Subscribe:</td><td class="field"><?php echo smarty_function_html_radios(array('name'=>"subscribe",'options'=>$_smarty_tpl->tpl_vars['SubscribeOptions']->value,'selected'=>$_smarty_tpl->tpl_vars['subscribe']->value,'separator'=>"<br />"),$_smarty_tpl);?>
</td></tr><?php }?><tr><td class="label">Unit price:</td><td class="field"><?php echo @HTML_CURRENCY_CODE;?>
<?php echo $_smarty_tpl->tpl_vars['price']->value;?>
  <?php if ($_smarty_tpl->tpl_vars['SubscriptionEnabled']->value==1){?>every <?php echo $_smarty_tpl->tpl_vars['SubscriptionPeriod']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['SubscriptionUm']->value;?>
<?php }?></td></tr><tr><td class="label">Quantity</td><td class="field"><?php if ($_smarty_tpl->tpl_vars['SubLength']->value!=5){?><input type="text" name="quantity" value="<?php echo $_smarty_tpl->tpl_vars['quantity']->value;?>
" size="10" maxlength="5" class="text" /><?php }else{ ?>n/a<input type="hidden" name="quantity" value="1" /><?php }?></td></tr><tr><td class="buttons" colspan="2" align="center"><input type="hidden" name="formSubmitted" value="1" /><input type="image" name="paypal" src="https://www.paypal.com/en_US/i/btn/x-click-but01.gif" alt="Make payments with PayPal - it's fast, free and secure!" onClick="this.submit();"></td></tr></table></form><?php }?> <?php }?> <?php }} ?>