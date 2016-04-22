<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Model_Link_Entity extends Phpld_Model_Entity
{
    const PURPOSE_LISTING = 1;
    const PURPOSE_DETAILS = 2;

    const STYLE_LIST = "list";
    const STYLE_GRID = "grid";

    const TYPE_LINK = 1;
    const TYPE_BUSINESS = 2;
    const TYPE_ARTICLE = 3;
    const TYPE_PICTURE = 4;
    const TYPE_VIDEO = 5;
    const TYPE_COUPON = 6;

    protected $_modelClass = 'Model_Link';

    protected $_urlPrefix = 'listing';

    /**
     * @var Model_Link_Handler_Abstract
     */
    protected $_handler = null;


    public function __construct($options = array(), $model = null)
    {
        parent::__construct($options, $model);

        switch ($options['LINK_TYPE']) {
            case self::TYPE_VIDEO:
                $this->_handler = new Model_Link_Handler_Video($this);
                break;

            case self::TYPE_COUPON:
                $this->_handler = new Model_Link_Handler_Coupon($this);
                break;

            default:
                $this->_handler = new Model_Link_Handler_Default($this);
                break;
        }
    }

 public function getUrl($prefix = true)
    {
         $seoLinkType       = $this->_model->getSeoLinkType();
         $seoUrlExtension   = $this->_model->getSeoUrlExtension();
         $seoCategoryName   = $this->_model->getSeoCategoryName();
         $category          = $this->_model->getMainCategory($this['ID']);
         $linkTypeById      = strtolower(str_replace(' ', '-',$this->_model->getLinkTypeById($this['ID'])));
         
        if($seoLinkType == 1 && $seoCategoryName == 1){
            //$seoUrlExt = (self::SEO_OUTPUT_EXT == '' ? $this['CACHE_URL'] : $this['CACHE_URL'].self::SEO_OUTPUT_EXT); 
            return $href   = FRONT_DOC_ROOT.'/'.$linkTypeById.$category->getUrl(false).$this['CACHE_URL'].$seoUrlExtension;
        }elseif ($seoLinkType == 1) {
            return $href   = FRONT_DOC_ROOT.'/'.$linkTypeById.'/'.$this['CACHE_URL'].$seoUrlExtension;
        }elseif ( $seoCategoryName == 1 ) {
            return $href   = FRONT_DOC_ROOT.$category->getUrl(false).$this['CACHE_URL'].$seoUrlExtension;
        }else{
            if(!empty($seoUrlExtension)){
                return FRONT_DOC_ROOT.'/'.$this['CACHE_URL'].$seoUrlExtension;
            }else{
                return FRONT_DOC_ROOT.'/'.($prefix == true ? $this->_urlPrefix : '').'/'.$this['CACHE_URL'];
            }
        }
}


    public function sgetUrl($prefix = true)
    {
	
	$seoLinkType       = $this->_model->getSeoLinkType();
         $seoUrlExtension   = $this->_model->getSeoUrlExtension();
         $seoCategoryName   = $this->_model->getSeoCategoryName();
         $category          = $this->_model->getMainCategory($this['ID']);
         $linkTypeById      = strtolower(str_replace(' ', '-',$this->_model->getLinkTypeById($this['ID'])));

        if($seoLinkType == 1 && $seoCategoryName == 1){
            //$seoUrlExt = (self::SEO_OUTPUT_EXT == '' ? $this['CACHE_URL'] : $this['CACHE_URL'].self::SEO_OUTPUT_EXT); 
            return $href   = '/'.$linkTypeById.$category->getUrl(false).$this['CACHE_URL'].$seoUrlExtension;
        }elseif ($seoLinkType == 1) {
            return $href   = '/'.$linkTypeById.'/'.$this['CACHE_URL'].$seoUrlExtension;
        }elseif ( $seoCategoryName == 1 ) {
            return $href   = $category->getUrl(false).$this['CACHE_URL'].$seoUrlExtension;
        }else{
            if(!empty($seoUrlExtension)){
                return '/'.$this['CACHE_URL'].$seoUrlExtension;
            }else{
                return '/'.($prefix == true ? $this->_urlPrefix : '').'/'.$this['CACHE_URL'];
            }
        }
    }

    public function getSubmitItems($idType = null)
    {
        if (is_null($idType)) {
            $idType = $this['LINK_TYPE'];
        }
        return $this->_model->getSubmitItems($idType);
    }

    public function getTags()
    {
        return $this->_model->getTags($this['ID']);
    }

    public function getCategories()
    {
        return $this->_model->getCategories($this['ID']);
    }

    public function getLinkTemplate($purpose, $style="list")
    {
        $template = null;
        switch ($purpose) {
            case self::PURPOSE_LISTING:
                $template = $this['LIST_TEMPLATE'];
                break;

            case self::PURPOSE_DETAILS:
                $template = $this['DETAILS_TEMPLATE'];
                break;
        }


        if (empty($template)) {
            $template = 'default.tpl';
        }

        return 'views/_listings/'.$style.'/'.$template;
    }

    /**
     * @param text $type Purpose of this rendering - for listing or for details page
     * @return mixed
     */
    public function render($purpose, $style="list")
    {
        $view = Phpld_View::getView();
        //$view->template_dir = INSTALL_PATH.'templates/'.USE_TEMPLATE.'/views/_listings';
        $view->assign('LINK', $this);
        $this->_handler->_assignPlaceholders($purpose, $style);
        return $view->fetch($this->getLinkTemplate($purpose, $style));
    }

    public function listing($style="list")
    {
        return $this->render(self::PURPOSE_LISTING, $style);
    }

    public function details()
    {
        return $this->render(self::PURPOSE_DETAILS, 'details');
    }

    public function getHandler()
    {
        return $this->_handler;
    }
}