<?php /* Smarty version Smarty-3.1.12, created on 2014-12-19 07:56:05
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/validation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8900834495493da15855b67-95820932%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7ae4e4df5f7def474f1d04b003ea255d9c9ba157' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/validation.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8900834495493da15855b67-95820932',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'validation_messages' => 0,
    'k' => 0,
    'i' => 0,
    'form_id' => 0,
    'validators' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5493da158a4454_41523090',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5493da158a4454_41523090')) {function content_5493da158a4454_41523090($_smarty_tpl) {?>

    <script type="text/javascript">
            jQuery(document).ready(function(){
    
    <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['validation_messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['i']->key;
?>
            <?php if ($_smarty_tpl->tpl_vars['k']->value=='maxlength'||$_smarty_tpl->tpl_vars['k']->value=='minlength'||$_smarty_tpl->tpl_vars['k']->value=='rangelength'||$_smarty_tpl->tpl_vars['k']->value=='range'||$_smarty_tpl->tpl_vars['k']->value=='max'||$_smarty_tpl->tpl_vars['k']->value=='min'){?>
                                jQuery.validator.messages.<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
 = $.format("<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
");
            <?php }else{ ?>
                                jQuery.validator.messages.<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
 = "<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
";
        <?php }?>
    <?php } ?>
    

                    function fireEvent(obj, evt) {
                            var fireOnThis = obj;
                            if (document.createEvent) {
                                    var evObj = document.createEvent('MouseEvents');
                                    evObj.initEvent(evt, true, false);
                                    fireOnThis.dispatchEvent(evObj);
                            } else if (document.createEventObject) {
                                    fireOnThis.fireEvent('on'+evt);
                            }
                    }

                    //valid obj isntantiated in main.tpl
                    valid_obj.<?php echo $_smarty_tpl->tpl_vars['form_id']->value;?>
 = {
                                    debug: false,
                                    onKeyUp: true,
                                    onfocusout: false,
                                    errorElement: "span",
                                    errorClass: "errForm",
                                    submitHandler: function(form) {
                                            // do other stuff for a valid form
                                            if (jQuery("#<?php echo $_smarty_tpl->tpl_vars['form_id']->value;?>
").valid()) {
                                                    form.submit();
                                            }
                                    },

    <?php echo $_smarty_tpl->tpl_vars['validators']->value;?>

                    };

                    jQuery("#<?php echo $_smarty_tpl->tpl_vars['form_id']->value;?>
").validate(valid_obj.<?php echo $_smarty_tpl->tpl_vars['form_id']->value;?>
);

                    var selects = jQuery("#<?php echo $_smarty_tpl->tpl_vars['form_id']->value;?>
").find("select");
                    var crt;

                    jQuery.each(selects, function() {
                        crt = this.id;
                        if(typeof(valid_obj.<?php echo $_smarty_tpl->tpl_vars['form_id']->value;?>
.rules[crt]) !== 'undefined') {
                            jQuery("#"+crt).change(function() {
                              jQuery(this).valid();
                            });
                        }
                    });
            });
    </script>
<?php }} ?>