<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 18:29:32
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/paginator.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21397725315654ac8c729186-32055838%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4d868d0642d984c01003ffa9bbb0ea158fb22b23' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/_placeholders/paginator.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21397725315654ac8c729186-32055838',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MainPaging' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5654ac8c743240_09068306',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5654ac8c743240_09068306')) {function content_5654ac8c743240_09068306($_smarty_tpl) {?><?php if (!is_callable('smarty_function_paginate_prev')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.paginate_prev.php';
if (!is_callable('smarty_function_paginate_middle')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.paginate_middle.php';
if (!is_callable('smarty_function_paginate_next')) include '/home/mylivete/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.paginate_next.php';
?>
<?php if (!empty($_smarty_tpl->tpl_vars['MainPaging']->value)&&$_smarty_tpl->tpl_vars['MainPaging']->value['page_total']>1){?>
<div class="navig">
    <div class="mainPaging">
    
        <div class="pagingLinks">
            <?php echo smarty_function_paginate_prev(array('id'=>"MainPaging"),$_smarty_tpl);?>
 <?php echo smarty_function_paginate_middle(array('id'=>"MainPaging",'format'=>"page",'prefix'=>'','suffix'=>'','link_prefix'=>" ",'link_suffix'=>" ",'current_page_prefix'=>"[",'current_page_suffix'=>"]"),$_smarty_tpl);?>
 <?php echo smarty_function_paginate_next(array('id'=>"MainPaging"),$_smarty_tpl);?>

        </div>
    </div>

    Total records: <?php echo $_smarty_tpl->tpl_vars['MainPaging']->value['total'];?>

</div>
<?php }?>
<?php }} ?>