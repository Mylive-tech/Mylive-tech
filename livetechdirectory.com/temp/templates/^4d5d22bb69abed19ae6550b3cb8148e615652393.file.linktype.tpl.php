<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 10:58:16
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/linktype.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20321355956559448a46f88-74539329%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4d5d22bb69abed19ae6550b3cb8148e615652393' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/linktype.tpl',
      1 => 1386991090,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20321355956559448a46f88-74539329',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link_types' => 0,
    'link_type_id' => 0,
    'linktypeid' => 0,
    'link_type' => 0,
    'payment_um' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56559448a679d4_67499171',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56559448a679d4_67499171')) {function content_56559448a679d4_67499171($_smarty_tpl) {?><div class="phpld-columnar">
    <h3>Step Two Choose a Link Type:</h3>
    <div>
        <?php if ($_GET['LINK_TYPE']=='undefined'){?>
            <div class="box error">
                You must choose a link type to proceed
            </div>
        <?php }?>
        <?php  $_smarty_tpl->tpl_vars['link_type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['link_type']->_loop = false;
 $_smarty_tpl->tpl_vars['link_type_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['link_types']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['link_type']->key => $_smarty_tpl->tpl_vars['link_type']->value){
$_smarty_tpl->tpl_vars['link_type']->_loop = true;
 $_smarty_tpl->tpl_vars['link_type_id']->value = $_smarty_tpl->tpl_vars['link_type']->key;
?>
            <div class="phpld-columnar phpld-equalize">
                <div class="phpld-fbox-check float-left">
                    <input type="radio" name="LINK_TYPE" value="<?php echo $_smarty_tpl->tpl_vars['link_type_id']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['link_type_id']->value==$_smarty_tpl->tpl_vars['linktypeid']->value){?>checked<?php }?>/>
                </div>
                <div class="float-left phpld-fbox-text">
                    <?php echo $_smarty_tpl->tpl_vars['link_type']->value['NAME'];?>
&nbsp;-&nbsp;
                    <?php if ($_smarty_tpl->tpl_vars['link_type']->value['PRICE']>0){?>
                        <?php echo @HTML_CURRENCY_CODE;?>
<?php echo $_smarty_tpl->tpl_vars['link_type']->value['PRICE'];?>
 / <?php echo $_smarty_tpl->tpl_vars['payment_um']->value[$_smarty_tpl->tpl_vars['link_type']->value['PAY_UM']];?>

                    <?php }else{ ?>
                        free
                    <?php }?>
                    <p class="small"><?php echo $_smarty_tpl->tpl_vars['link_type']->value['DESCRIPTION'];?>
</p>
                </div>
            </div>
        <?php } ?>
        <div class="phpld-columnar">
            <center>
                <div class="phpld-fbox-button">
                    <input type="button" class='button' name="choicemade" value="Go To Step Three" />
                </div>
            </center>
        </div>
    </div>
</div>
<?php }} ?>