<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:56:35
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1459951815535a93c3837305-83705137%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7694e77cdd551ba0c95f1f469082b88e0e7529a2' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/menu.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1459951815535a93c3837305-83705137',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'm' => 0,
    'l' => 0,
    'mk' => 0,
    'k' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a93c38df658_92066781',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a93c38df658_92066781')) {function content_535a93c38df658_92066781($_smarty_tpl) {?><?php if (isset($_smarty_tpl->tpl_vars['m']->value['menu'])&&!empty($_smarty_tpl->tpl_vars['m']->value['menu'])&&is_array($_smarty_tpl->tpl_vars['m']->value['menu'])){?>
   <ul class="code1 closed" style="display: none" id="<?php echo $_smarty_tpl->tpl_vars['m']->value['label'];?>
">
   <?php  $_smarty_tpl->tpl_vars['l'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['l']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['m']->value['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['l']->key => $_smarty_tpl->tpl_vars['l']->value){
$_smarty_tpl->tpl_vars['l']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['l']->key;
?>
      <?php if (isset($_smarty_tpl->tpl_vars['l']->value['menu'])&&!empty($_smarty_tpl->tpl_vars['l']->value['menu'])&&is_array($_smarty_tpl->tpl_vars['l']->value['menu'])){?>
         <li class="menu-item has-submenu" title="<?php echo $_smarty_tpl->tpl_vars['l']->value['label'];?>
"><?php echo $_smarty_tpl->tpl_vars['l']->value['label'];?>
</li>
         <?php echo $_smarty_tpl->getSubTemplate ((@ADMIN_TEMPLATE).("/menu.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('m'=>$_smarty_tpl->tpl_vars['l']->value), 0);?>

      <?php }elseif(isset($_smarty_tpl->tpl_vars['l']->value)&&!empty($_smarty_tpl->tpl_vars['l']->value)&&is_array($_smarty_tpl->tpl_vars['l']->value)){?>
         <?php if ($_smarty_tpl->tpl_vars['l']->value['disabled']){?>
            <!--<li class=" menu-item disabled" title="Menu item disabled"><?php echo $_smarty_tpl->tpl_vars['l']->value['label'];?>
</li>-->
         <?php }else{ ?>
            <li class="menu-item"><a href="<?php echo $_smarty_tpl->tpl_vars['l']->value['url'];?>
<?php if (strpos($_smarty_tpl->tpl_vars['l']->value['url'],'?')!==false){?>&amp;r=1<?php }else{ ?>?r=1<?php }?>" title="<?php echo $_smarty_tpl->tpl_vars['l']->value['label'];?>
"><?php echo $_smarty_tpl->tpl_vars['l']->value['label'];?>
</a></li>
         <?php }?>
      <?php }else{ ?>
         <li><a href="<?php echo $_smarty_tpl->tpl_vars['mk']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
.php?r=1" title="<?php echo $_smarty_tpl->tpl_vars['l']->value;?>
" class="menu-item"><?php echo $_smarty_tpl->tpl_vars['l']->value;?>
</a></li>
      <?php }?>
   <?php } ?>
   </ul>
<?php }?><?php }} ?>