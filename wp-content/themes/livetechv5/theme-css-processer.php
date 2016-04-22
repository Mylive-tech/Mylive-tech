<?php
header("Content-type: text/css; charset=utf-8");
header('Cache-control: must-revalidate');

$absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $absolute_path[0] . 'wp-load.php';
require_once($wp_load);

ob_start();
flatsome_custom_css();
$buffer = ob_get_clean();

$buffer = str_replace(array('<!-- Custom CSS Codes --><style type="text/css"> ','</style>'), '', $buffer);

echo $buffer;
?>