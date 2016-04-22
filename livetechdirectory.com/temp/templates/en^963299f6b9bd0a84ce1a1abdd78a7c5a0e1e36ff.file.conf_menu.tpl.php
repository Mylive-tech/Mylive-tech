<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:11:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:832230775535a9738b9c014-52323389%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '963299f6b9bd0a84ce1a1abdd78a7c5a0e1e36ff' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_menu.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '832230775535a9738b9c014-52323389',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pages' => 0,
    'page' => 0,
    'menuPages' => 0,
    'idMenu' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a9738bcbf20_25780649',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a9738bcbf20_25780649')) {function content_535a9738bcbf20_25780649($_smarty_tpl) {?><script type="text/javascript" src="<?php echo @FRONT_DOC_ROOT;?>
javascripts/nav-editor.js"></script>
<div class="pages availPagesCol">
    <h3>Available pages</h3>
    <ul class="menu menu-to-edit" id="avaiable-pages">
        <li class="menu-item menu-item-page">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                <span class="item-title">Custom URL </span>
                <span class="item-controls">
                    <button class="btn" onclick="jQuery('#addCustomUrlForm').toggle()">+</button>
                </span>
                <form method="post" id="addCustomUrlForm" action="" class="form-stacked" style="display: none;">
                    <input type="hidden" name="action" value="addPage">
                    <fieldset>
                        <label>Label: </label>
                        <input type="text" name="LABEL" value=""  class="text"/>
                        <label>URL: </label>
                        <input type="text" name="URL" value=""  class="text"/>
                        <div class="actions" style="margin-right:10px;">
                            <input type="submit" name="submit" id="submit" value="Add" class="btn">
                        </div>
                    </fieldset>
                </form>
                </dt>
            </dl>
        </li>

        <?php  $_smarty_tpl->tpl_vars['page'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['page']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['pages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['page']->key => $_smarty_tpl->tpl_vars['page']->value){
$_smarty_tpl->tpl_vars['page']->_loop = true;
?>
        <li class="menu-item menu-item-page">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                    <span class="item-title"><?php echo $_smarty_tpl->tpl_vars['page']->value['LABEL'];?>
</span>
                        <span class="item-controls">
                            <form method="post" method="post">
                                <input type="hidden" name="action" value="addPage">
                                <input type="hidden" name="LABEL" value="<?php echo $_smarty_tpl->tpl_vars['page']->value['LABEL'];?>
" />
                                <input type="hidden" name="URL" value="<?php echo $_smarty_tpl->tpl_vars['page']->value['URL'];?>
" />
                                <button type="submit" value="submit" class="btn">+</button>
                            </form>
                        </span>
                </dt>
            </dl>
        </li>
        <?php } ?>
    </ul>
</div>
<div class="menu menuPagesCol">
    <h3>Menu</h3>
    <ul class="menu menu-to-edit" id="menu-to-edit">
        <?php echo $_smarty_tpl->tpl_vars['menuPages']->value;?>

    </ul>
    <div class="actions">
        <button onclick="mkMenuEditor.saveMenu('<?php echo $_smarty_tpl->tpl_vars['idMenu']->value;?>
', event)" class="btn">Save</button>
    </div>
</div><?php }} ?>