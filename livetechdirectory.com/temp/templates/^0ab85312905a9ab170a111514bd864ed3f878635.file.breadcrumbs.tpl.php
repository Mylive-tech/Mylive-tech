<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:26
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/breadcrumbs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1402210324535a93badf81d0-59935684%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0ab85312905a9ab170a111514bd864ed3f878635' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/breadcrumbs.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1402210324535a93badf81d0-59935684',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'items' => 0,
    'count' => 0,
    'crumb' => 0,
    'i' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a93bae49313_90745137',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93bae49313_90745137')) {function content_535a93bae49313_90745137($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars["count"])) {$_smarty_tpl->tpl_vars["count"] = clone $_smarty_tpl->tpl_vars["count"];
$_smarty_tpl->tpl_vars["count"]->value = count($_smarty_tpl->tpl_vars['items']->value); $_smarty_tpl->tpl_vars["count"]->nocache = null; $_smarty_tpl->tpl_vars["count"]->scope = 0;
} else $_smarty_tpl->tpl_vars["count"] = new Smarty_variable(count($_smarty_tpl->tpl_vars['items']->value), null, 0);?>
<?php if (isset($_smarty_tpl->tpl_vars["i"])) {$_smarty_tpl->tpl_vars["i"] = clone $_smarty_tpl->tpl_vars["i"];
$_smarty_tpl->tpl_vars["i"]->value = 1; $_smarty_tpl->tpl_vars["i"]->nocache = null; $_smarty_tpl->tpl_vars["i"]->scope = 0;
} else $_smarty_tpl->tpl_vars["i"] = new Smarty_variable(1, null, 0);?>
<?php if ($_smarty_tpl->tpl_vars['count']->value>0){?>
    <div class="breadcrumbs">
    <?php  $_smarty_tpl->tpl_vars["crumb"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["crumb"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["crumb"]->key => $_smarty_tpl->tpl_vars["crumb"]->value){
$_smarty_tpl->tpl_vars["crumb"]->_loop = true;
?>
        <span>
            <?php if (!empty($_smarty_tpl->tpl_vars['crumb']->value['URL'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['crumb']->value['URL'];?>
"><?php }?>
                <?php echo $_smarty_tpl->tpl_vars['crumb']->value['LABEL'];?>

            <?php if (!empty($_smarty_tpl->tpl_vars['crumb']->value['URL'])){?></a><?php }?>

            <?php if ($_smarty_tpl->tpl_vars['i']->value<$_smarty_tpl->tpl_vars['count']->value){?>
                <span class="divider">/</span>
            <?php }?>
        </span>
        <?php if (isset($_smarty_tpl->tpl_vars["i"])) {$_smarty_tpl->tpl_vars["i"] = clone $_smarty_tpl->tpl_vars["i"];
$_smarty_tpl->tpl_vars["i"]->value = $_smarty_tpl->tpl_vars['i']->value+1; $_smarty_tpl->tpl_vars["i"]->nocache = null; $_smarty_tpl->tpl_vars["i"]->scope = 0;
} else $_smarty_tpl->tpl_vars["i"] = new Smarty_variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);?>
    <?php } ?>
    </div>
<?php }?><?php }} ?>