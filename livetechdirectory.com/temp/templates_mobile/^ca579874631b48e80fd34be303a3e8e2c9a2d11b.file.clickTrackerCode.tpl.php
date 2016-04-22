<?php /* Smarty version Smarty-3.1.12, created on 2014-04-25 17:19:03
         compiled from "/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/clickTrackerCode.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1907818326535a9907bbcee6-13838848%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ca579874631b48e80fd34be303a3e8e2c9a2d11b' => 
    array (
      0 => '/home/mylive5/public_html/livetechdirectory.com/templates/Core/DefaultFrontend/views/_shared/clickTrackerCode.tpl',
      1 => 1386917016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1907818326535a9907bbcee6-13838848',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535a9907bc6875_94861456',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a9907bc6875_94861456')) {function content_535a9907bc6875_94861456($_smarty_tpl) {?><script type="text/javascript">
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