<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 class Phpld_Breadcrumbs
{
    protected $_items = array();

    public function add($label, $url = null)
    {
        array_push($this->_items, array('LABEL'=>$label, 'URL'=>$url));
    }

    public function assign($tpl, $name = 'BREADCRUMBS')
    {
        $tpl->assign($name, $this);
    }

    public function render()
    {
        $view = Phpld_View::getView();
        $view->assign('items', $this->_items);
        return $view->fetch('views/_shared/_placeholders/breadcrumbs.tpl');
    }

    public function __toString()
    {
        return $this->render();
    }
}