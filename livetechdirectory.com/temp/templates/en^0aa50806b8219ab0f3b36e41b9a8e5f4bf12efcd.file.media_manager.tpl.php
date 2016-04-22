<?php /* Smarty version Smarty-3.1.12, created on 2014-05-12 13:36:28
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/media_manager.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17640472725370ce5c35ce72-26062460%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0aa50806b8219ab0f3b36e41b9a8e5f4bf12efcd' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultAdmin/media_manager.tpl',
      1 => 1387383446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17640472725370ce5c35ce72-26062460',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'typeTitle' => 0,
    'file_list' => 0,
    'file' => 0,
    'MainPaging' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5370ce5c4758f2_65981482',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5370ce5c4758f2_65981482')) {function content_5370ce5c4758f2_65981482($_smarty_tpl) {?><?php if (!is_callable('smarty_function_paginate_prev')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.paginate_prev.php';
if (!is_callable('smarty_function_paginate_middle')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.paginate_middle.php';
if (!is_callable('smarty_function_paginate_next')) include '/home/mylive5/public_html/livetechdirectory.com/libs/Smarty3/plugins/function.paginate_next.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $_smarty_tpl->tpl_vars['typeTitle']->value;?>
 Manager</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?php echo @TEMPLATE_ROOT;?>
/files/fileuploader.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo @TEMPLATE_ROOT;?>
/files/media_manager.css" />
    <script src="../javascripts/jquery-1.3.2.min.js"></script>
    <script type="text/javascript">
   	jQuery.noConflict();
    function send_to_editor(h,file_id) {
	var h_width = jQuery('#image_width_'+file_id).val();
	var h_height = jQuery('#image_height_'+file_id).val();
	if(h_height)
	   var h_height_str = 'height="'+h_height+'"';
	   else
	   var h_height_str = '';
	if(h_width)
	   var h_width_str = 'width="'+h_width+'"';
	else
	   var h_width_str = '';
        var h_send = '<span><img src="'+h+'" '+h_height_str+' '+h_width_str+' /></span>';
        window.frames.parent.tb_remove();
        window.frames.parent.tinyMCE.execCommand('mceInsertContent', true, h_send);
} 
    function remove_media(type,file_name)
    {
       
        if(confirm('Are you sure you want to delete this media?')){
        jQuery.ajax({
	       url:"<?php echo @DOC_ROOT;?>
/admin-delete-media.php?type="+type+"&file_name="+file_name,
	       type:"get",
	       dataType:"json",
	       success:mediaDeleteSuccess
	    });
      }
    }
   function mediaDeleteSuccess(data){
        if(data.message=='success')
           {
            document.getElementById(data.file_name).style.display='none';
            }
    }
 
    </script>

</head>
<body>
<table style="width:100%;" class="media_manager" id="media_manader"><?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['file']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['file_list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['for_media']['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
$_smarty_tpl->tpl_vars['file']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['for_media']['iteration']++;
?><tr <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['for_media']['iteration']%2=='0'){?>class="odd"<?php }?> id="<?php echo $_smarty_tpl->tpl_vars['file']->value['FILE_NAME'];?>
"><td><img src="<?php echo @DOC_ROOT;?>
/../uploads/media<?php echo $_smarty_tpl->tpl_vars['file']->value['FILE_PATH'];?>
" height="30px" /></td><td><?php echo $_smarty_tpl->tpl_vars['file']->value['FILE_NAME'];?>
</td><td><?php if ($_smarty_tpl->tpl_vars['file']->value['TYPE']=='image'){?>Width: <input type="text" id="image_width_<?php echo $_smarty_tpl->tpl_vars['file']->value['ID'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['file']->value['IMAGE_WIDTH'];?>
" style="width:30px;border:1px #00000;" /><?php }?></td><td><?php if ($_smarty_tpl->tpl_vars['file']->value['TYPE']=='image'){?>Height: <input type="text" id="image_height_<?php echo $_smarty_tpl->tpl_vars['file']->value['ID'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['file']->value['IMAGE_HEIGHT'];?>
" style="width:30px;border:1px #00000;"/><?php }?></td><td style="text-align:center;"><a href="#" onclick="send_to_editor('<?php echo @DOC_ROOT;?>
/../uploads/media<?php echo $_smarty_tpl->tpl_vars['file']->value['FILE_PATH'];?>
','<?php echo $_smarty_tpl->tpl_vars['file']->value['ID'];?>
');return false;">Post</a>&nbsp;<a href="#" onclick="remove_media('image','<?php echo $_smarty_tpl->tpl_vars['file']->value['FILE_PATH'];?>
');return false;">Remove</a></td></tr><?php } ?></table><?php if (!empty($_smarty_tpl->tpl_vars['MainPaging']->value)&&$_smarty_tpl->tpl_vars['MainPaging']->value['page_total']>1){?><div class="navig"><div class="mainPaging"><div class="pagingLinks"><?php echo smarty_function_paginate_prev(array('id'=>"MainPaging"),$_smarty_tpl);?>
 <?php echo smarty_function_paginate_middle(array('id'=>"MainPaging",'format'=>"page",'prefix'=>'','suffix'=>'','link_prefix'=>" ",'link_suffix'=>" ",'current_page_prefix'=>"[",'current_page_suffix'=>"]"),$_smarty_tpl);?>
 <?php echo smarty_function_paginate_next(array('id'=>"MainPaging"),$_smarty_tpl);?>
</div></div>Total records: <?php echo $_smarty_tpl->tpl_vars['MainPaging']->value['total'];?>
</div><?php }?><br/><br/><div id="imageuploadajaxcontainer"></div>
        <div id="file-uploader-admin">
	 <noscript>
	    <p>Please enable JavaScript to use file uploader.</p>
	    <!-- or put a simple form for upload here -->
	 </noscript>
        </div>
	<script src="../javascripts/fileuploader.js" type="text/javascript"></script>
<script>
function createUploader(){

var uploader = new qq.FileUploader({
			      element: document.getElementById('file-uploader-admin'),
			      action: '../media_manager_upload.php',
                              allowedExtensions: ['jpeg','jpg','gif','png'],
			       params: {
				       admin: "1"
				       },
                            onComplete: function(id, fileName, responseJSON)
                            {
                                if(responseJSON['success']==true)
                                {
				
                                    jQuery("#media_manader").prepend('<tr id="'+responseJSON['filename']+'"><td ><img src="<?php echo @DOC_ROOT;?>
/../uploads/media'+responseJSON['file_path']+'" height="30px" /></td><td>'+responseJSON['filename']+'</td><td>Width: <input type="text" id="image_width_'+responseJSON['ID']+'" value="'+responseJSON['IMAGE_WIDTH']+'" style="width:30px;border:1px #00000;" /></td><td>Height: <input type="text" id="image_height_'+responseJSON['ID']+'" value="'+responseJSON['IMAGE_HEIGHT']+'" style="width:30px;border:1px #00000;"/></td><td style="text-align:center;"><a href="#"  onclick="send_to_editor(\'<?php echo @DOC_ROOT;?>
/../uploads/media'+responseJSON['file_path']+'\',\''+responseJSON['ID']+'\');return false;">Post</a>&nbsp;<a href="#" onclick="remove_media(\'image\',\''+responseJSON['file_path']+'\');return false;">Remove</a></td></tr>');
                                }
                            }
				 });
}
window.onload = createUploader;



</script>

</body>
</html><?php }} ?>