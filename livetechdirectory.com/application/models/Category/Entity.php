<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_Category_Entity extends Phpld_Model_Entity
{
    protected $_urlPrefix = 'category';

    public function getUrl($prefix = true)
    {
        if (!empty($this['URL'])) {
            $href = $this['URL'];
        } else {
            $href = ($prefix ? FRONT_DOC_ROOT : '').'/'.$this['CACHE_URL'];
        }
        return $href;
    }

	public function sgetUrl($prefix = true)
    {
        if (!empty($this['URL'])) {
            $href = $this['URL'];
        } else {
            $href = ($prefix ? '/' : '').$this['CACHE_URL'];
        }
        return $href;
    }

    public function getSubcategories($idUser = null)
    {
        $categoriesModel = new Model_Category();
        return $categoriesModel->getCategories($this['ID'], $idUser);
    }

    public function getParent()
    {
        $categoriesModel = new Model_Category();
        if ($this['PARENT_ID'] == 0) {
            return null;
        } else {
            return $categoriesModel->getCategory($this['PARENT_ID']);
        }
    }

    public function getLinks($where = false, $offset = 0, $count = null, $sort = null)
    {
        return $this->_model->getLinks($this['ID'], $where, $offset, $count, $sort);
    }
    
    public function logHit()
    {
        return $this->_model->logHit($this['ID']);
    }

    public function getLocations()
    {
        $links = $this->getLinks();
        $locations = array();
        $locationsCollection = new Phpld_Model_Collection(Model_Location_Entity);
        foreach ($links as $link) {
            if (!empty($link['CITY']) && !empty($link['STATE'])) {
                $locations[] = array('CITY'=>$link['CITY'], 'STATE'=>$link['STATE']);
            }
        }
        $locationsCollection->setElements($locations);
        return $locationsCollection;
    }
}