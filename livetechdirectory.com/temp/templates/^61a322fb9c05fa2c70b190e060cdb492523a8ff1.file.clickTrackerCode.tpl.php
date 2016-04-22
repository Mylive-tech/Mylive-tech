<?php /* Smarty version Smarty-3.1.12, created on 2015-11-24 16:12:14
         compiled from "/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/clickTrackerCode.tpl" */ ?>
<?php /*%%SmartyHeaderCode:68849493556548c5e4cc9f9-91213236%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '61a322fb9c05fa2c70b190e060cdb492523a8ff1' => 
    array (
      0 => '/home/mylivete/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/clickTrackerCode.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '68849493556548c5e4cc9f9-91213236',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_56548c5e4d1827_03554744',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56548c5e4d1827_03554744')) {function content_56548c5e4d1827_03554744($_smarty_tpl) {?><script type="text/javascript">
    /* <![CDATA[ */
    var root = '<?php echo @DOC_ROOT;?>
';
    
    var a = document.getElementsByTagName("a");
    for(i = 0; i< a.length; i++)
        if(a[i].id != '')
            a[i].onclick = count_link;
    function count_link(event) {
        i = new Image();
        i.src= root+'/cl.php?id='+this.id;
        return true;
    }

    
    /* ]]> */
</script><?php }} ?>