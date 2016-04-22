<?php /* Smarty version Smarty-3.1.12, created on 2015-11-25 10:58:02
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/category.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17588770245655943a192221-58568503%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5fcf22f6de27e1006d21d730153e85eb232cbce9' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/category.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17588770245655943a192221-58568503',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categoryID' => 0,
    'parentID' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5655943a19aa30_21439798',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5655943a19aa30_21439798')) {function content_5655943a19aa30_21439798($_smarty_tpl) {?><div class="phpld-columnar">
    <h3 style="width:100%;">Step One Choose a Category:</h3>
    <div id="hiddenModalContent">
        
        <?php echo $_smarty_tpl->getSubTemplate ("views/_shared/category_select.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('selected'=>$_smarty_tpl->tpl_vars['categoryID']->value,'selected_parent'=>$_smarty_tpl->tpl_vars['parentID']->value), 0);?>

    </div>
    <br />
    <center>
        <div class="phpld-fbox-button">
            <input type="button" class="button" id="ok" value="Go To Step Two" onclick="closeCategSelectModal(self);" style="padding:1px 10px;" />
        </div>
    </center>

    
        <script type="text/javascript">
            function closeCategSelectModal(el) {
                    //el.parent.tb_remove();
                    //destroyCatTree();
                    //jQuery("#selectCategOk").hide();
                    //jQuery("#toggleCategTree").hide();
                    var cur_categ = jQuery("#CATEGORY_ID").val();
                    document.location.href = "submit?c=" + cur_categ;
            }
        </script>
    
</div><?php }} ?>