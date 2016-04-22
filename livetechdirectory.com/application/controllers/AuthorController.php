<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
/**
 # ################################################################################
 # Project:   PHP Link Directory
 #
 # **********************************************************************
 # Copyright (C) 2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 #
 # This software is for use only to those who have purchased a license.
 # A license must be purchased for EACH installation of the software.
 #
 # By using the software you agree to the terms:
 #
 #    - You may not redistribute, sell or otherwise share this software
 #      in whole or in part without the consent of the the ownership
 #      of PHP Link Directory. Please contact david@david-duval.com
 #      if you need more information.
 #
 #    - You agree to retain a link back to http://www.phplinkdirectory.com/
 #      on all pages of your directory if you purchased any of our "link back" 
 #      versions of the software.
 #
 #
 # In some cases, license holders may be required to agree to changes
 # in the software license before receiving updates to the software.
 # **********************************************************************
 #
 # For questions, help, comments, discussion, etc., please join the
 # PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
 #
 # @link           http://www.phplinkdirectory.com/
 # @copyright      2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.1.0 Phoenix Release
 # ################################################################################
 */	
class AuthorController extends PhpldfrontController
{
    public function indexAction()
    {
        $idUser = $this->getParam('idUser');
        $user = new Model_User();
        $user =  $user->getUser($idUser);
    
        $db = Phpld_Db::getInstance()->getAdapter();
        $tables = Phpld_Db::getInstance()->getTables();
        $user = $db->GetRow("SELECT *, DATE_FORMAT(REGISTRATION_DATE, '%M %d, %Y %r') AS `DAT`,DATE_FORMAT(LAST_LOGIN, '%M %d, %Y %r') AS `DAT2` FROM `{$tables['user']['name']}` WHERE `ID` = ".$db->qstr($idUser));
      
        if (is_null($user)) {
            throw new Phpld_Exception_NotFound();
        }
        
        $sorter = new Phpld_Sorter();

        if (EMAIL_CONFIRMATION == 1) {
            $email_conf = " AND `OWNER_EMAIL_CONFIRMED` = '1' ";
        }

        $user_where = 'AND OWNER_ID = '.$idUser;

        $expire_where = "AND (`EXPIRY_DATE` >= ".$db->DBDate(time())." OR `EXPIRY_DATE` IS NULL)";
        $linksCollection = new Phpld_Model_Collection(Model_Link_Entity);

       
        $query = "SELECT *, l.ID AS ID, l.DESCRIPTION AS DESCRIPTION FROM `{$tables['link']['name']}` l,`{$tables['link_type']['name']}` t WHERE  l.LINK_TYPE=t.ID and (l.`STATUS` = '2') {$email_conf} {$expire_where} {$user_where} ORDER BY ".$sorter->getOrder();
       
	$links = $db->CacheGetAll($query);
        $linksCollection->setElements($links);
        $linksCollection->setCountWithoutLimit($db->getOne("SELECT FOUND_ROWS() as count"));
        
        $user['RELATED'] = "";
        foreach ($linksCollection as $value)
            {
                $link = $value->getUrl();
                $user['RELATED'] .= "<li><a class=\"special\" href=\"{$link}\">{$value['TITLE']}</a></li>\n";
            }

        if($user['RELATED'])
            $user['RELATED'] = "<ul>\n{$user['RELATED']}</ul>\n";
        
        
        
        $this->view->assign($user);

    
    }
}