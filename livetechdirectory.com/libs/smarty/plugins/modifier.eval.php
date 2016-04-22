<?php
function smarty_modifier_eval($string) 
{ 
global $smarty; 
ob_start(); 
$smarty->_eval('?>' . $string); 
$_contents = ob_get_contents(); 
ob_end_clean(); 
return $_contents; 
}
?>