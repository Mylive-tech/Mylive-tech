<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:01:38
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/listingRender.tpl" */ ?>
<?php /*%%SmartyHeaderCode:953231267535a94f26ad738-71548939%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '644ebb4cbd45c12fdcf01567877eaccf50b7b89a' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/listingRender.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '953231267535a94f26ad738-71548939',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'linkColumns' => 0,
    'widgetID' => 0,
    'linkStyle' => 0,
    'links' => 0,
    'gridClass' => 0,
    'LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a94f273b781_88958901',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a94f273b781_88958901')) {function content_535a94f273b781_88958901($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['linkColumns']->value==2){?>
    <?php if (isset($_smarty_tpl->tpl_vars["gridClass"])) {$_smarty_tpl->tpl_vars["gridClass"] = clone $_smarty_tpl->tpl_vars["gridClass"];
$_smarty_tpl->tpl_vars["gridClass"]->value = 'phpld-g50 woo'; $_smarty_tpl->tpl_vars["gridClass"]->nocache = null; $_smarty_tpl->tpl_vars["gridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["gridClass"] = new Smarty_variable('phpld-g50 woo', null, 0);?>
<?php }elseif($_smarty_tpl->tpl_vars['linkColumns']->value==3){?>
    <?php if (isset($_smarty_tpl->tpl_vars["gridClass"])) {$_smarty_tpl->tpl_vars["gridClass"] = clone $_smarty_tpl->tpl_vars["gridClass"];
$_smarty_tpl->tpl_vars["gridClass"]->value = 'phpld-g33 woo'; $_smarty_tpl->tpl_vars["gridClass"]->nocache = null; $_smarty_tpl->tpl_vars["gridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["gridClass"] = new Smarty_variable('phpld-g33 woo', null, 0);?>
<?php }elseif($_smarty_tpl->tpl_vars['linkColumns']->value==4){?>
    <?php if (isset($_smarty_tpl->tpl_vars["gridClass"])) {$_smarty_tpl->tpl_vars["gridClass"] = clone $_smarty_tpl->tpl_vars["gridClass"];
$_smarty_tpl->tpl_vars["gridClass"]->value = 'phpld-g25 woo'; $_smarty_tpl->tpl_vars["gridClass"]->nocache = null; $_smarty_tpl->tpl_vars["gridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["gridClass"] = new Smarty_variable('phpld-g25 woo', null, 0);?>
<?php }elseif($_smarty_tpl->tpl_vars['linkColumns']->value==5){?>
    <?php if (isset($_smarty_tpl->tpl_vars["gridClass"])) {$_smarty_tpl->tpl_vars["gridClass"] = clone $_smarty_tpl->tpl_vars["gridClass"];
$_smarty_tpl->tpl_vars["gridClass"]->value = 'phpld-g20 woo'; $_smarty_tpl->tpl_vars["gridClass"]->nocache = null; $_smarty_tpl->tpl_vars["gridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["gridClass"] = new Smarty_variable('phpld-g20 woo', null, 0);?>
<?php }else{ ?>
    <?php if (isset($_smarty_tpl->tpl_vars["gridClass"])) {$_smarty_tpl->tpl_vars["gridClass"] = clone $_smarty_tpl->tpl_vars["gridClass"];
$_smarty_tpl->tpl_vars["gridClass"]->value = 'phpld-gbox '; $_smarty_tpl->tpl_vars["gridClass"]->nocache = null; $_smarty_tpl->tpl_vars["gridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["gridClass"] = new Smarty_variable('phpld-gbox ', null, 0);?>
<?php }?>


<?php if ($_smarty_tpl->tpl_vars['linkColumns']->value>1){?>
    <script type="text/javascript">
            
    $(window).load(function() { 
            $('img').each(function(){
                $(this).attr('height', jQuery(this).outerHeight());
            });  
    });    

    var handler<?php echo $_smarty_tpl->tpl_vars['widgetID']->value;?>
 = null;   
    $(document).ready(function() {
            
        if($('#woogrid<?php echo $_smarty_tpl->tpl_vars['widgetID']->value;?>
 div.woo').length > 1){  
        //if(handler) handler.wookmarkClear();    
        handler<?php echo $_smarty_tpl->tpl_vars['widgetID']->value;?>
 = $('#woogrid<?php echo $_smarty_tpl->tpl_vars['widgetID']->value;?>
 div.woo');

            var options = {
                autoResize: true,  
                container: jQuery('#woogrid<?php echo $_smarty_tpl->tpl_vars['widgetID']->value;?>
'), // Optional, used for some extra CSS styling
                offset: 0 // Optional, the distance between grid items       
            };
        setInterval( function() { handler<?php echo $_smarty_tpl->tpl_vars['widgetID']->value;?>
.wookmark(options); } , 1000)
            
      }
      //setTimeout(setWook(), 1000);
    });
        
    </script> 
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars["i"])) {$_smarty_tpl->tpl_vars["i"] = clone $_smarty_tpl->tpl_vars["i"];
$_smarty_tpl->tpl_vars["i"]->value = 1; $_smarty_tpl->tpl_vars["i"]->nocache = null; $_smarty_tpl->tpl_vars["i"]->scope = 0;
} else $_smarty_tpl->tpl_vars["i"] = new Smarty_variable(1, null, 0);?>

<div class="phpld-grid listing-style-<?php echo $_smarty_tpl->tpl_vars['linkStyle']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['linkColumns']->value>1){?> id="woogrid<?php echo $_smarty_tpl->tpl_vars['widgetID']->value;?>
" <?php }?> style="position: relative;">
    <?php  $_smarty_tpl->tpl_vars['LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['links']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LINK']->key => $_smarty_tpl->tpl_vars['LINK']->value){
$_smarty_tpl->tpl_vars['LINK']->_loop = true;
?>
        <div class="<?php echo $_smarty_tpl->tpl_vars['gridClass']->value;?>
<?php if ($_smarty_tpl->tpl_vars['LINK']->value['FEATURED']){?> featured<?php }?>">
            <?php echo $_smarty_tpl->tpl_vars['LINK']->value->listing($_smarty_tpl->tpl_vars['linkStyle']->value);?>

        </div>
    <?php } ?>
</div>
<?php }} ?>