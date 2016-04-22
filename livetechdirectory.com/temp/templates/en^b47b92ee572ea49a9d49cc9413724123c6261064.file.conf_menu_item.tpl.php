<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:11:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_menu_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:912116914535a9738b102e0-40651230%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b47b92ee572ea49a9d49cc9413724123c6261064' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/conf_menu_item.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '912116914535a9738b102e0-40651230',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a9738b91cb4_37635341',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a9738b91cb4_37635341')) {function content_535a9738b91cb4_37635341($_smarty_tpl) {?><li id="menu-item-<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
" class="sortable menu-item menu-item-depth-<?php echo $_smarty_tpl->tpl_vars['page']->value['LEVEL'];?>
 menu-item-page menu-item-edit-inactive">
    <dl class="menu-item-bar">
        <dt class="menu-item-handle">
            <span class="item-title"><?php echo $_smarty_tpl->tpl_vars['page']->value['LABEL'];?>
</span>
            <span class="item-controls">
                <!--<span class="item-type">Страница</span>-->
                <a class="item-edit" id="edit-<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
" title="Edit menu item" href="javascript:void(null)" onclick="jQuery('#settings-<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
').toggle('fast')">Edit</a>
                <a class="item-remove" id="menu-item-<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
" title="Remove menu item" href="javascript:void(null)" onclick="if (confirm('Are you sure?'))mkMenuEditor.removeElement('<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
', event)">Remove</a>
            </span>
        </dt>
    </dl>
    <ul class="menu-item-transport"></ul>
    <div class="settings" id="settings-<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
">
        <form method="post">
            <input type="hidden" name="ID" value="<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
">
            <input type="hidden" name="action" value="saveMenuItem">
            <label>Label: </label>
            <input type="text" name="LABEL" value="<?php echo $_smarty_tpl->tpl_vars['page']->value['LABEL'];?>
" class="text"/>
            <label>URL: </label>
            <input type="text" name="URL" value="<?php echo $_smarty_tpl->tpl_vars['page']->value['URL'];?>
"  class="text"/>
            <div class="actions" style="margin-right:10px;">
                <input type="submit" name="submit" id="submit" value="Save" class="btn">
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
">
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $_smarty_tpl->tpl_vars['page']->value['ID'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['page']->value['PARENT'];?>
">
        </form>
    </div>
</li><?php }} ?>