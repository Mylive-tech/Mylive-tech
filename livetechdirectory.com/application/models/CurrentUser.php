<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	
	
class Model_CurrentUser extends Model_User
{
    protected static $_instance;

    protected $_entity = null;

    /**
     * @static
     * @return Model_CurrentUser
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function isLoggedIn()
    {
        if (isset($_SESSION['phpld']['user'])) {
            return true;
        }
        return false;
    }

    public function getId()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return $_SESSION['phpld']['user']['id'];
    }

    public function getLevel()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        $data = $this->loadData();
        return $data['LEVEL'];
    }

    function getPermissions() {

        $data = $this->_db->cacheGetAll("SELECT `CATEGORY_ID` FROM `{$this->_tables['user_permission']['name']}` WHERE `USER_ID`=".$this->_db->qstr($this->getId()));
        return $data;
    }

    public function loadData()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        if (is_null($this->_entity)) {
            $row = $this->_db->getRow(
                'SELECT * FROM '.$this->_tables['user']['name'].' WHERE ID = '.$this->getId()
            );
            $this->_entity = $this->entity($row);
            return $this->_entity;
        } else {
            return $this->_entity;
        }
    }

    public function update($data, $where = null)
    {
        if (is_null($where))
        {
            $where = 'ID = '.$this->getId();
        }
        return parent::update($data, $where);
    }

    public function login($login, $password)
    {
        $tables = Phpld_Db::getInstance()->getTables();
        $db = Phpld_Db::getInstance()->getAdapter();

        $sql = "SELECT `ID`, `LOGIN`, `NAME`, `LEVEL`, `RANK`, `LANGUAGE` FROM `{$tables['user']['name']}` WHERE `LOGIN` = ".$db->qstr($login)." AND `PASSWORD` = ".$db->qstr(encrypt_password($password))." AND `ACTIVE` = '1'";
        $row = $db->GetRow($sql);


        if (is_array ($row) && !empty ($row))
        {
            //Don't break admin panel login data
            if (!isset ($_SESSION['phpld']))
            {
                $_SESSION['phpld'] = array();
            }

            //Create web user session
            $_SESSION['phpld']['user'] = array();

            //Load all user data into session

            foreach ($row as $field => $value)
            {
                $field = strtolower ($field);
                $_SESSION['phpld']['user'][$field] = $value;
            }

            if (isset($_SESSION['phpld']['user']['id'])) {
                $user_level = get_user_level($_SESSION['phpld']['user']['id']);
                if ($user_level == 1) {
                    //delete expired links & articles (not paid though) on admin login
                    //links
                    $HaveExpiredEmail_sql = "DELETE FROM `{$tables['link']['name']}`
                                    WHERE `OWNER_EMAIL_CONFIRMED` = '0'
                                    AND DATE_ADD(`DATE_ADDED`, INTERVAL ".WAIT_FOR_EMAIL_CONF." DAY) <= now()
                                    AND `PAYED` <> '1'";

                    $HaveExpiredEmail = $db->Execute($HaveExpiredEmail_sql);
                    //end of delete
                }
            }

            $_SESSION['user_language'] = $_SESSION['phpld']['user']['language'];

            if (isset($_POST['rememberMe'])) {
                $auth = array('user' => $_POST['user'], 'password' => $_POST['password']);
                $auth = serialize($auth);
                setcookie('auth', $auth, time() + 7 * 24 * 60 * 60, '/');
            } else {
                // unset the possibly seted cookie
                setcookie('auth', '', time() - 3600, '/');
            }


            // Update last login
            $user_id = $_SESSION['phpld']['user']['id'];
            $data['LAST_LOGIN']     = 'NOW()';
            $where = " `ID` = ".$db->qstr($user_id);
            $db->AutoExecute($tables['user']['name'], $data, 'UPDATE', $where);
            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        if (isset ($_SESSION['phpld']['user']))
        {
            //Clear session variable,just in case unset does not work
            $_SESSION['phpld']['user'] = array();
            //Completely destroy user session
            unset ($_SESSION['phpld']['user']);

            if (empty ($_SESSION['phpld']))
            {
                unset ($_SESSION['phpld']);
            }
        }

        if (isset ($_COOKIE[session_name ()]))
        {
            setcookie (session_name(), '', 0);
        }

        @ session_write_close ();
        //@ session_unset ();
        @ session_destroy ();//Fix IE Bug

    }
}