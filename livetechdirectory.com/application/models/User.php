<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_User extends Phpld_Model_Abstract
{
    protected $_entityClass = 'Model_User_Entity';

    protected $_modelTable = 'user';

    /**
     * @param $isUser
     * @return Model_User_Entity
     */
    public function getUser($isUser)
    {
        $row = $this->_db->getRow('SELECT * FROM '.$this->_tables['user']['name'].' WHERE ID = '.$isUser);
        return $this->entity($row);
    }

	/**
	 * Gets user from the database, by a speciffic login
	 * @param $login string User Login
	 * @return Model_User
	 */
	public function getUserByLogin($login)
	{
		$row = $this->_db->getRow('SELECT * FROM '.$this->_tables['user']['name'].' WHERE LOGIN = ?', array($login));
		return $this->entity($row);
	}

	public function activate($uid, $key)
	{
		$this->_db->Execute('UPDATE '.$this->_tables['user']['name'].' SET `EMAIL_CONFIRMED`=1, `CONFIRM`=null, ACTIVE=1
			WHERE ID=? AND CONFIRM=?', array($uid, $key));
		return $this->_db->GetOne('SELECT count(1) FROM '.$this->_tables['user']['name'].
			' WHERE ID=? AND `EMAIL_CONFIRMED`=1 AND `ACTIVE`=1', array($uid));
	}

}