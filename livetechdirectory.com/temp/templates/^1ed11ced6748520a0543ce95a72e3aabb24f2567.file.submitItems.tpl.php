<?php /* Smarty version Smarty-3.1.12, created on 2014-04-27 21:20:12
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/submitItems.tpl" */ ?>
<?php /*%%SmartyHeaderCode:911286733535d748c8c7f90-94241048%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1ed11ced6748520a0543ce95a72e3aabb24f2567' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_listings/_placeholders/submitItems.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '911286733535d748c8c7f90-94241048',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SUBMIT_ITEMS' => 0,
    'item' => 0,
    'LINK' => 0,
    'tag' => 0,
    'items' => 0,
    'group_image_details' => 0,
    'group_image' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535d748cb53627_99402723',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535d748cb53627_99402723')) {function content_535d748cb53627_99402723($_smarty_tpl) {?><div class="submiItems">
    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['SUBMIT_ITEMS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value['IS_DETAIL']=='1'){?>
        <div class="phpld-grid phpld-equalize submitItem">
            <div class="phpld-label float-left"><?php echo $_smarty_tpl->tpl_vars['item']->value['NAME'];?>
:</div>
            <div class="smallDesc float-left">

                <?php if ($_smarty_tpl->tpl_vars['item']->value['TYPE']=='FILE'){?>
                    <a href="<?php echo @DOC_ROOT;?>
/uploads/<?php echo $_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
</a>
                <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='BOOL'){?>
                    <?php if (trim(htmlspecialchars($_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']], ENT_QUOTES, 'UTF-8', true))==1){?>Yes<?php }else{ ?>No<?php }?>
                <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='DROPDOWN'){?>
                    <?php echo $_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>

                <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='TAGS'){?>
                    <?php  $_smarty_tpl->tpl_vars["tag"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["tag"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LINK']->value->getTags(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["tag"]->key => $_smarty_tpl->tpl_vars["tag"]->value){
$_smarty_tpl->tpl_vars["tag"]->_loop = true;
?>
                        <span class="label"><?php echo $_smarty_tpl->tpl_vars['tag']->value['TITLE'];?>
</span>
                    <?php } ?>

                <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='MULTICHECKBOX'){?>
                    <?php if (isset($_smarty_tpl->tpl_vars["items"])) {$_smarty_tpl->tpl_vars["items"] = clone $_smarty_tpl->tpl_vars["items"];
$_smarty_tpl->tpl_vars["items"]->value = explode(",",$_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]); $_smarty_tpl->tpl_vars["items"]->nocache = null; $_smarty_tpl->tpl_vars["items"]->scope = 0;
} else $_smarty_tpl->tpl_vars["items"] = new Smarty_variable(explode(",",$_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']]), null, 0);?>
                    <ul class="multicheckbox">
                        <?php  $_smarty_tpl->tpl_vars["item"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["item"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["item"]->key => $_smarty_tpl->tpl_vars["item"]->value){
$_smarty_tpl->tpl_vars["item"]->_loop = true;
?>
                            <li><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</li>
                        <?php } ?>
                    </ul>
		      <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='VIDEO'){?> 
 <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/flowplayer-3.2.6.min.js"></script>
        <!--    <object type="application/x-shockwave-flash" data="player_flv_maxi.swf" width="352" height="288">
                 <param name="movie" value="player_flv_maxi.swf" />
                 <param name="FlashVars" value="flv=uploads/<?php echo $_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
&showfullscreen=1&autoplay=1&showstop=1&showvolume=1&showtime=1" />
            </object> -->

	    	<!-- this A tag is where your Flowplayer will be placed. it can be anywhere -->
		<a href="<?php echo @SITE_URL;?>
uploads/<?php echo $_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
"
			 style="display:block;width:520px;height:330px"  
			 id="player"> </a> 
	
		<!-- this will install flowplayer inside previous A- tag. -->
		<script>
			flowplayer("player", "<?php echo @SITE_URL;?>
flowplayer-3.2.7.swf", {
    clip:  {
        autoPlay: false,
        autoBuffering: true
    }
	});	</script>
         
           
                <?php }elseif($_smarty_tpl->tpl_vars['item']->value['TYPE']=='URL_IMAGE'){?>
                    <img src="<?php echo @DOC_ROOT;?>
/uploads/<?php echo $_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>
" />
     
	  	<?php }else{ ?>
          <?php echo $_smarty_tpl->tpl_vars['LINK']->value[$_smarty_tpl->tpl_vars['item']->value['FIELD_NAME']];?>

                <?php }?>

            </div>
        </div>
        <?php }?>
    <?php } ?>
 <?php if (!empty($_smarty_tpl->tpl_vars['group_image_details']->value)){?>
  <link rel="stylesheet" type="text/css" href="<?php echo @DOC_ROOT;?>
/templates/<?php echo @TEMPLATE;?>
/style/jcarousel.css" />
 <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/jquery/jquery.jcarousel.min.js"></script>
 <script type="text/javascript" src="<?php echo @DOC_ROOT;?>
/javascripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo @DOC_ROOT;?>
/javascripts/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<script type="text/javascript">

jQuery(document).ready(function() {
jQuery('#mycarousel').jcarousel({
        visible: 4
    });
jQuery("a.image_list").fancybox();
});

</script>
       
	  
          	<ul id="mycarousel" class="jcarousel-skin-tango">
              		<?php  $_smarty_tpl->tpl_vars['group_image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group_image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['group_image_details']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group_image']->key => $_smarty_tpl->tpl_vars['group_image']->value){
$_smarty_tpl->tpl_vars['group_image']->_loop = true;
?>
              			<li>
				    <a href="<?php echo @DOC_ROOT;?>
/uploads/<?php echo $_smarty_tpl->tpl_vars['group_image']->value['IMAGE'];?>
" class="image_list" rel="image_list" >
				    <img src="<?php echo @DOC_ROOT;?>
/uploads/thumb/<?php echo $_smarty_tpl->tpl_vars['group_image']->value['IMAGE'];?>
" border="0" style="border: 1px dotted grey;width:100px;"  />
				    </a>
				 </li>
              		<?php } ?>
              </ul>
    
	<?php }?>
</div><?php }} ?>