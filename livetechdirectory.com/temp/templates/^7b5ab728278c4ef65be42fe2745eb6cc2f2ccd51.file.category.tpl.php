<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 16:57:46
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/category.tpl" */ ?>
<?php /*%%SmartyHeaderCode:912238611535a940ae29222-87990285%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b5ab728278c4ef65be42fe2745eb6cc2f2ccd51' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/submit/category.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '912238611535a940ae29222-87990285',
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
  'unifunc' => 'content_535a940ae38ee3_55051557',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a940ae38ee3_55051557')) {function content_535a940ae38ee3_55051557($_smarty_tpl) {?><div class="phpld-columnar">
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