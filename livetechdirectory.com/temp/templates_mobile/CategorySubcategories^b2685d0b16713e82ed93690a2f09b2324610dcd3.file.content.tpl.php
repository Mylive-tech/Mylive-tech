<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 19:18:20
         compiled from "/home/mylive5/public_html/livetechdirectory.com/application/widgets/CategorySubcategories/templates/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:315046317535ab4fc4802b0-89471311%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b2685d0b16713e82ed93690a2f09b2324610dcd3' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/application/widgets/CategorySubcategories/templates/content.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '315046317535ab4fc4802b0-89471311',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'subcategories' => 0,
    'catgridClass' => 0,
    'cat' => 0,
    'subcategory' => 0,
    'scat' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535ab4fc55eef8_97128756',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535ab4fc55eef8_97128756')) {function content_535ab4fc55eef8_97128756($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['subcategories']->value->countWithoutLimit()>0){?>
    <?php if (@CATS_PER_ROW==2){?>
        <?php if (isset($_smarty_tpl->tpl_vars["catgridClass"])) {$_smarty_tpl->tpl_vars["catgridClass"] = clone $_smarty_tpl->tpl_vars["catgridClass"];
$_smarty_tpl->tpl_vars["catgridClass"]->value = 'phpld-g50 phpld-gl'; $_smarty_tpl->tpl_vars["catgridClass"]->nocache = null; $_smarty_tpl->tpl_vars["catgridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["catgridClass"] = new Smarty_variable('phpld-g50 phpld-gl', null, 0);?>
    <?php }elseif(@CATS_PER_ROW==3){?>
        <?php if (isset($_smarty_tpl->tpl_vars["catgridClass"])) {$_smarty_tpl->tpl_vars["catgridClass"] = clone $_smarty_tpl->tpl_vars["catgridClass"];
$_smarty_tpl->tpl_vars["catgridClass"]->value = 'phpld-g33 phpld-gl'; $_smarty_tpl->tpl_vars["catgridClass"]->nocache = null; $_smarty_tpl->tpl_vars["catgridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["catgridClass"] = new Smarty_variable('phpld-g33 phpld-gl', null, 0);?>
    <?php }elseif(@CATS_PER_ROW==4){?>
        <?php if (isset($_smarty_tpl->tpl_vars["catgridClass"])) {$_smarty_tpl->tpl_vars["catgridClass"] = clone $_smarty_tpl->tpl_vars["catgridClass"];
$_smarty_tpl->tpl_vars["catgridClass"]->value = 'phpld-g25 phpld-gl'; $_smarty_tpl->tpl_vars["catgridClass"]->nocache = null; $_smarty_tpl->tpl_vars["catgridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["catgridClass"] = new Smarty_variable('phpld-g25 phpld-gl', null, 0);?>
    <?php }elseif(@CATS_PER_ROW==5){?>
        <?php if (isset($_smarty_tpl->tpl_vars["catgridClass"])) {$_smarty_tpl->tpl_vars["catgridClass"] = clone $_smarty_tpl->tpl_vars["catgridClass"];
$_smarty_tpl->tpl_vars["catgridClass"]->value = 'phpld-g20 phpld-gl'; $_smarty_tpl->tpl_vars["catgridClass"]->nocache = null; $_smarty_tpl->tpl_vars["catgridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["catgridClass"] = new Smarty_variable('phpld-g20 phpld-gl', null, 0);?>
    <?php }else{ ?>
        <?php if (isset($_smarty_tpl->tpl_vars["catgridClass"])) {$_smarty_tpl->tpl_vars["catgridClass"] = clone $_smarty_tpl->tpl_vars["catgridClass"];
$_smarty_tpl->tpl_vars["catgridClass"]->value = 'phpld-gbox '; $_smarty_tpl->tpl_vars["catgridClass"]->nocache = null; $_smarty_tpl->tpl_vars["catgridClass"]->scope = 0;
} else $_smarty_tpl->tpl_vars["catgridClass"] = new Smarty_variable('phpld-gbox ', null, 0);?>
    <?php }?>
    <div class="phpld-grid categories">
        <?php  $_smarty_tpl->tpl_vars['cat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['subcategories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cat']->key => $_smarty_tpl->tpl_vars['cat']->value){
$_smarty_tpl->tpl_vars['cat']->_loop = true;
?>
            <div class="<?php echo $_smarty_tpl->tpl_vars['catgridClass']->value;?>
 ">
                <div class="phpld-gbox">
                    <h4><a href="<?php echo $_smarty_tpl->tpl_vars['cat']->value->getUrl();?>
" <?php if ($_smarty_tpl->tpl_vars['cat']->value['NEW_WINDOW']==1){?>target="blank"<?php }?>  title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat']->value['TITLE'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cat']->value['TITLE'], ENT_QUOTES, 'UTF-8', true);?>
</a><?php if (@CATS_COUNT){?><span class="phpld-gray">(<?php echo $_smarty_tpl->tpl_vars['cat']->value['COUNT'];?>
)</span><?php }?></h4>
                    
                    <?php if (isset($_smarty_tpl->tpl_vars["subcategory"])) {$_smarty_tpl->tpl_vars["subcategory"] = clone $_smarty_tpl->tpl_vars["subcategory"];
$_smarty_tpl->tpl_vars["subcategory"]->value = $_smarty_tpl->tpl_vars['cat']->value['SUBCATS']; $_smarty_tpl->tpl_vars["subcategory"]->nocache = null; $_smarty_tpl->tpl_vars["subcategory"]->scope = 0;
} else $_smarty_tpl->tpl_vars["subcategory"] = new Smarty_variable($_smarty_tpl->tpl_vars['cat']->value['SUBCATS'], null, 0);?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['subcategory']->value)){?>
                        <div>
                            <?php  $_smarty_tpl->tpl_vars['scat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['scat']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['subcategory']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['scategs']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['scat']->key => $_smarty_tpl->tpl_vars['scat']->value){
$_smarty_tpl->tpl_vars['scat']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['scat']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['scategs']['iteration']++;
?>
                                <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['scategs']['iteration']<@CATS_PREVIEW){?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['scat']->value->getUrl();?>
" <?php if ($_smarty_tpl->tpl_vars['scat']->value['NEW_WINDOW']==1){?>target="blank"<?php }?>  title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['scat']->value['TITLE'], ENT_QUOTES, 'UTF-8', true);?>
" class="phpld-gray" ><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['scat']->value['TITLE'], ENT_QUOTES, 'UTF-8', true);?>
</a>
                                    <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['scategs']['iteration']<@CATS_PREVIEW-1){?>
                                        ,
                                    <?php }?>
                                <?php }?>    

                            <?php } ?>
                            ... 
                        </div> 
                    <?php }?>
                </div>
            </div>
        <?php } ?>
    </div>
<?php }?><?php }} ?>