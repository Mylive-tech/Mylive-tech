<?php /* Smarty version Smarty-3.1.12, created on 2014-07-15 11:19:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/submit/category.tpl" */ ?>
<?php /*%%SmartyHeaderCode:176207027553c50e38169cb5-92598806%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '44d83804ff6ad3d96b5051ebaf3612aeb2af25e8' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/MobileFormat/views/submit/category.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '176207027553c50e38169cb5-92598806',
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
  'unifunc' => 'content_53c50e3817fd74_77284145',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53c50e3817fd74_77284145')) {function content_53c50e3817fd74_77284145($_smarty_tpl) {?>

    <div id="hiddenModalContent">
        
        <?php echo $_smarty_tpl->getSubTemplate ("views/_shared/category_select.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('selected'=>$_smarty_tpl->tpl_vars['categoryID']->value,'selected_parent'=>$_smarty_tpl->tpl_vars['parentID']->value), 0);?>

    </div>
    <br />
    <div style="clear: both;"></div>
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
    
<?php }} ?>