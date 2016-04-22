<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:26
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/flashMessenger.tpl" */ ?>
<?php /*%%SmartyHeaderCode:601690047535a93bae4e499-75381756%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b659a4da03f52228ee7bb93572f26b1f9daba2b' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/flashMessenger.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '601690047535a93bae4e499-75381756',
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
  'unifunc' => 'content_535a93bae7db81_60191707',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93bae7db81_60191707')) {function content_535a93bae7db81_60191707($_smarty_tpl) {?><?php if (!is_null($_smarty_tpl->tpl_vars['namespaces']->value)){?>
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