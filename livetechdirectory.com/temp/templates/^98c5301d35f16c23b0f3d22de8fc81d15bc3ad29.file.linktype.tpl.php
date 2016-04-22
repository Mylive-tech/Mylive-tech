<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:48:00
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/linktype.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1969315225535abbf0bd3404-66347165%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98c5301d35f16c23b0f3d22de8fc81d15bc3ad29' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/linktype.tpl',
      1 => 1386991090,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1969315225535abbf0bd3404-66347165',
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
  'unifunc' => 'content_535abbf0c25ca4_77805740',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535abbf0c25ca4_77805740')) {function content_535abbf0c25ca4_77805740($_smarty_tpl) {?><div class="phpld-columnar">
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