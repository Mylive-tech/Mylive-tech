<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 

 
 
 
 
	
class Phpld_Paginator
{
    protected $_perPage;
    protected $_pageRange;
    protected $_currentPage;
    protected $_totalCount;
    protected $_urlVal = 'p';
    protected $_currentItem = 'p';

    public function __construct($perPage = null, $pageRange = null){

        if (is_null($perPage)) {
            $perPage   = (PAGER_LPP       && PAGER_LPP       > 0 ? intval (PAGER_LPP)       : 10);
        }

        if (is_null($pageRange)) {
            $pageRange   = (PAGER_GROUPINGS && PAGER_GROUPINGS > 0 ? intval (PAGER_GROUPINGS) : 20);
        }

        $this->_perPage = $perPage;
        $this->_pageRange = $pageRange;
        $this->_resolveCurrentPage();
    }

    public function setUrlVar($var)
    {
        $this->_urlVal = $var;
    }

    public function assign(&$tpl, $count)
    {
        $view = Phpld_View::getView();
        $name = 'MainPaging';
        $this->_totalCount = $count;
        SmartyPaginate :: connect($name); // Connect Paging
        if ($this->_totalCount < 2)
        {
            SmartyPaginate :: disconnect($name);
            SmartyPaginate :: reset     ($name);
        }

        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace("/\?p=[0-9]+/", "", $url);

        SmartyPaginate :: setPrevText    ('Previous' , $name);
        SmartyPaginate :: setNextText    ('Next'     , $name);
        SmartyPaginate :: setFirstText   ('First'    , $name);
        SmartyPaginate :: setLastText    ('Last'     , $name);
        SmartyPaginate :: setTotal       ($this->_totalCount     , $name);
        SmartyPaginate :: setUrlVar      ($this->_urlVal         , $name);
        SmartyPaginate :: setUrl         ($url, $name);
        SmartyPaginate :: setCurrentItem ($this->_currentItem    , $name);
        SmartyPaginate :: setLimit       ($this->_perPage        , $name);
        SmartyPaginate :: setPageLimit   ($this->_pageRange      , $name);
        SmartyPaginate :: assign         ($view                   , $name, $name);


        $tpl->assign('PAGINATOR', Phpld_View::getView()->fetch('views/_shared/_placeholders/paginator.tpl'));
    }

    protected function _resolveCurrentPage(){
        // Determine current index
        $current_item = (isset($_REQUEST['p']) ? $_REQUEST['p'] : 0);
        //$current_item--;
        // Determine page
        $page         = $current_item;
        $this->_currentItem = $current_item;
        $this->_currentPage = $page;
        
    }

    public function getLimit()
    {
        $limit = ' LIMIT '.($this->getOffset()).', '.$this->_perPage;
        return $limit;
    }

    public function getPerPage()
    {
        return $this->_perPage;
    }

    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Calculates number of items to skip to get items for current page
     */
    public function getOffset()
    {
	if($this->_currentPage > 0)
	    return $this->_currentPage - 1;
	else
	     return $this->_currentPage;
    }
}
