<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/flashMessenger.tpl" */ ?>
<?php /*%%SmartyHeaderCode:118035775056548c5e730028-33589343%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1a1a846cc2e1354e9060410b622f06bdcb26fa97' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/flashMessenger.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118035775056548c5e730028-33589343',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'namespaces' => 0,
    'messages' => 0,
    'namespace' => 0,
    'm' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e745b11_09953545',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e745b11_09953545')) {function content_56548c5e745b11_09953545($_smarty_tpl) {?><?php if (!is_null($_smarty_tpl->tpl_vars['namespaces']->value)){?>
    <div class="phpld-column phpld-messages">
    <?php  $_smarty_tpl->tpl_vars["messages"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["messages"]->_loop = false;
 $_smarty_tpl->tpl_vars["namespace"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['namespaces']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["messages"]->key => $_smarty_tpl->tpl_vars["messages"]->value){
$_smarty_tpl->tpl_vars["messages"]->_loop = true;
 $_smarty_tpl->tpl_vars["namespace"]->value = $_smarty_tpl->tpl_vars["messages"]->key;
?>
        <?php if (count($_smarty_tpl->tpl_vars['messages']->value)>0){?>
            <?php  $_smarty_tpl->tpl_vars["m"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["m"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["m"]->key => $_smarty_tpl->tpl_vars["m"]->value){
$_smarty_tpl->tpl_vars["m"]->_loop = true;
?>
                <p class="box <?php echo $_smarty_tpl->tpl_vars['namespace']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['m']->value;?>
</p>
            <?php } ?>
        <?php }?>
    <?php } ?>
    </div>
<?php }?><?php }} ?>