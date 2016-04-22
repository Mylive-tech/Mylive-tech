<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
* File:     block.escapejs.php
* Type:     block
* Name:     escapejs
* Purpose:  
* -------------------------------------------------------------
*/
function smarty_block_escapejs($params, $content, &$smarty)
{
    return str_replace("'", "\'", $content);
}
?>
