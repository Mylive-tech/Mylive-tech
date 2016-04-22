<?

/**
 * Smarty plugin
 * 
 * @package Smarty
 * @subpackage phpld
 */
function smarty_function_widget_zone($params, $template)
{
    
    $res = '';
    $layoutType = Phpld_Layout::getCurrent();
    $widgets =  $layoutType->getWidgets();
    $layout_settings = $layoutType->getLayoutSettings();
    foreach($widgets[$params['name']] as $k => $widget)
    {
        
        $template->assign('ID', $widget['ID']);
        $template->assign('SHOW_TITLE', (!isset($widget['SETTINGS']['SHOW_TITLE']) or strtolower($widget['SETTINGS']['SHOW_TITLE']) == 'yes'));
	$template->assign('DISPLAY_IN_BOX', (!isset($widget['SETTINGS']['DISPLAY_IN_BOX']) or strtolower($widget['SETTINGS']['DISPLAY_IN_BOX']) == 'yes'));
        $template->assign('WIDGET_HEADING', $layout_settings['widgetheading']['selected']);
        $template->assign('TITLE', $widget['SETTINGS']['TITLE']);
        $template->assign('CONTENT', $widget['CONTENT']);
        $res .=  $template->fetch('views/_shared/widget.tpl', $id);
    }
  
    return $res;

}
?>