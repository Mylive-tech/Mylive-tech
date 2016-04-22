<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$typeTitle} Manager</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ROOT}/files/fileuploader.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ROOT}/files/media_manager.css" />
    <script src="../javascripts/jquery-1.3.2.min.js"></script>
    <script type="text/javascript">
   	{literal}jQuery.noConflict();
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
	       url:"{/literal}{$smarty.const.DOC_ROOT}/admin-delete-media.php{literal}?type="+type+"&file_name="+file_name,
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
{/literal} 
    </script>

</head>
<body>
{strip}
<table style="width:100%;" class="media_manager" id="media_manader">
    {foreach from=$file_list item=file name=for_media}
    <tr {if $smarty.foreach.for_media.iteration % 2 eq '0'}class="odd"{/if} id="{$file.FILE_NAME}">
        <td><img src="{$smarty.const.DOC_ROOT}/../uploads/media{$file.FILE_PATH}" height="30px" />
        </td>
        <td>{$file.FILE_NAME}
        </td>
	<td>{if $file.TYPE EQ 'image'}Width: <input type="text" id="image_width_{$file.ID}" value="{$file.IMAGE_WIDTH}" style="width:30px;border:1px #00000;" />{/if}</td>
	<td>{if $file.TYPE EQ 'image'}Height: <input type="text" id="image_height_{$file.ID}" value="{$file.IMAGE_HEIGHT}" style="width:30px;border:1px #00000;"/>{/if}</td>
        <td style="text-align:center;"><a href="#" onclick="send_to_editor('{$smarty.const.DOC_ROOT}/../uploads/media{$file.FILE_PATH}','{$file.ID}');return false;">Post</a>
	&nbsp;
<a href="#" onclick="remove_media('image','{$file.FILE_PATH}');return false;">Remove</a></td>
    </tr>
    {/foreach}
</table>
{if !empty($MainPaging) and $MainPaging.page_total > 1}
			   <div class="navig">
			      <div class="mainPaging">
			         {* Display Paging Links *}
			         <div class="pagingLinks">
			            {paginate_prev id="MainPaging"} {paginate_middle id="MainPaging" format="page" prefix="" suffix="" link_prefix=" " link_suffix=" " current_page_prefix="[" current_page_suffix="]"} {paginate_next id="MainPaging"}
			         </div>
			      </div>
			
			      {l}Total records:{/l} {$MainPaging.total}
			   </div>
			{/if}
<br/><br/>
<div id="imageuploadajaxcontainer">
</div>
{literal}
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
				       {/literal}
				       admin: "1"
				       {literal}
				       },
                            onComplete: function(id, fileName, responseJSON)
                            {
                                if(responseJSON['success']==true)
                                {
				
                                    jQuery("#media_manader").prepend('<tr id="'+responseJSON['filename']+'"><td ><img src="{/literal}{$smarty.const.DOC_ROOT}/../uploads/media{literal}'+responseJSON['file_path']+'" height="30px" /></td><td>'+responseJSON['filename']+'</td><td>Width: <input type="text" id="image_width_'+responseJSON['ID']+'" value="'+responseJSON['IMAGE_WIDTH']+'" style="width:30px;border:1px #00000;" /></td><td>Height: <input type="text" id="image_height_'+responseJSON['ID']+'" value="'+responseJSON['IMAGE_HEIGHT']+'" style="width:30px;border:1px #00000;"/></td><td style="text-align:center;"><a href="#"  onclick="send_to_editor(\'{/literal}{$smarty.const.DOC_ROOT}/../uploads/media'+responseJSON['file_path']+'{literal}\',\''+responseJSON['ID']+'\');return false;">Post</a>&nbsp;<a href="#" onclick="remove_media(\'image\',\''+responseJSON['file_path']+'\');return false;">Remove</a></td></tr>');
                                }
                            }
				 });
}
window.onload = createUploader;



</script>
{/literal} 
{/strip}
</body>
</html>