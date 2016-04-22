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
 
/**
 * Usefull function allowed to be executed only from admin area!!
 */

if (!defined ('IN_PHPLD_ADMIN'))
{
   die("!! ERROR !! You are not allowed to run this script!");
}

function SetNewPageStatus($id=0, $val=1)
{
   global $db, $tables;
   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

    //Update page status
    if ($db->Execute("UPDATE `{$tables['page']['name']}` SET `STATUS` = ".$db->qstr($val)." WHERE `ID` = ".$db->qstr($id))) {
       $output['status'] = 1;
    }
    else
       $output['errorMsg'] = $db->ErrorMsg();
   return $output;
}


/**
 * Set new status for a link
 * @param integer Link ID for link to be processed
 * @param integer Status value for link (2 = active, 1 = pending, 0 = inactive)
 * @param integer Check expriration time for payment
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function SetNewLinkStatus($id=0, $val=1, $checkPayment=1)
{
   global $db, $tables;

   $checkPayment = ($checkPayment == 1 ? 1 : 0);

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Process if payments are enabled
   if (PAY_ENABLE == 1 && $val == 2 && $checkPayment == 1)
   {
      $sql = "SELECT `ID`, `PAYED`, `STATUS`, `EXPIRY_DATE` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id);
      $data = $db->GetRow($sql);

      if ($data['PAYED'] > 0)
      {
         $sql = "SELECT `ID`, `QUANTITY`, `UM` FROM `{$tables['payment']['name']}` WHERE `ID` = ".$db->qstr($data['PAYED']);
         $pdata = $db->GetRow($sql);
         $exp_date = calculate_expiry_date(time(), $pdata['QUANTITY'], $pdata['UM']);
         if ($exp_date != 0 && $data['EXPIRY_DATE'] == '')
            $data['EXPIRY_DATE'] = gmdate ('Y-m-d H:i:s', $exp_date);
      }
      $data['STATUS'] = 2;

      //Update link status
      if (db_replace('link', $data, 'ID') > 0)
      {
         $output['status'] = 1;
         send_status_notifications($id); //Send status notification to link owner
      }
      else
         $output['errorMsg'] = $db->ErrorMsg();
   }
   else
   {
      //Update link status
      if ($db->Execute("UPDATE `{$tables['link']['name']}` SET `STATUS` = ".$db->qstr($val)." WHERE `ID` = ".$db->qstr($id)))
      {
         $output['status'] = 1;
         send_status_notifications($id); //Send status notification to link owner
      }
      else
         $output['errorMsg'] = $db->ErrorMsg();
   }

   return $output;
}

/**
 * Set new status for a reviewed link
 * @param integer Link ID for link to be processed
 * @param integer Status value for link (2 = active, 1 = pending, 0 = inactive)
 * @param integer Check expriration time for payment
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function SetNewReviewedLinkStatus($id=0, $val=1)
{
   global $db, $tables;
   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   $revied_changes = $db->GetRow("SELECT * FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
   $revied_changes['ID']     = $id;
   $revied_changes['STATUS'] = $val;

   $link = getFullLinkInfo($revied_changes['LINK_ID']);
   unset ($revied_changes['LINK_ID']);

   $submit_items = $db->GetAll("
	SELECT submit_item.*, item_status.STATUS, item_status.LINK_TYPE_ID 
	FROM `PLD_SUBMIT_ITEM` AS submit_item, `PLD_SUBMIT_ITEM_STATUS` AS item_status 
	WHERE item_status.ITEM_ID = submit_item.ID 
		AND item_status.LINK_TYPE_ID = '{$revied_changes['LINK_TYPE']}'
		AND item_status.STATUS = '2'
                AND (submit_item.TYPE = 'IMAGE' OR submit_item.TYPE = 'FILE')
	ORDER BY `ORDER_ID` ASC
    ");

   for ($i=0; $i<count($submit_items); $i++) {
       if ($revied_changes[$submit_items[$i]['FIELD_NAME']] == '') {
            $revied_changes[$submit_items[$i]['FIELD_NAME']] = '';
            if (file_exists(INSTALL_PATH.'/uploads/'.$link[$submit_items[$i]['FIELD_NAME']])) {
                unlink(INSTALL_PATH.'/uploads/'.$link[$submit_items[$i]['FIELD_NAME']]);
            }
            if (file_exists(INSTALL_PATH.'/uploads/thumb/'.$link[$submit_items[$i]['FIELD_NAME']])) {
                unlink(INSTALL_PATH.'/uploads/thumb/'.$link[$submit_items[$i]['FIELD_NAME']]);
            }
       } else {
            if (file_exists(INSTALL_PATH.'/uploads/'.$link[$submit_items[$i]['FIELD_NAME']])) {
                unlink(INSTALL_PATH.'/uploads/'.$link[$submit_items[$i]['FIELD_NAME']]);
            }
            if (file_exists(INSTALL_PATH.'/uploads/thumb/'.$link[$submit_items[$i]['FIELD_NAME']])) {
                unlink(INSTALL_PATH.'/uploads/thumb/'.$link[$submit_items[$i]['FIELD_NAME']]);
            }
            if (file_exists(INSTALL_PATH.'/uploads/reviews/'.$revied_changes[$submit_items[$i]['FIELD_NAME']])) {
                copy(INSTALL_PATH.'/uploads/reviews/'.$revied_changes[$submit_items[$i]['FIELD_NAME']], INSTALL_PATH.'/uploads/'.$revied_changes[$submit_items[$i]['FIELD_NAME']]);
                unlink(INSTALL_PATH.'/uploads/reviews/'.$revied_changes[$submit_items[$i]['FIELD_NAME']]);
            }
            if (file_exists(INSTALL_PATH.'/uploads/reviews/thumb/'.$revied_changes[$submit_items[$i]['FIELD_NAME']])) {
                copy(INSTALL_PATH.'/uploads/reviews/thumb/'.$revied_changes[$submit_items[$i]['FIELD_NAME']], INSTALL_PATH.'/uploads/thumb/'.$revied_changes[$submit_items[$i]['FIELD_NAME']]);
                unlink(INSTALL_PATH.'/uploads/reviews/thumb/'.$revied_changes[$submit_items[$i]['FIELD_NAME']]);
            }
       }
   }

   $where = " `ID` = ".$db->qstr($id);

   if ($db->AutoExecute($tables['link']['name'], $revied_changes, 'UPDATE', $where))
   {
      $linkModel = new Model_Link();
      $seoUrl = $linkModel->seoUrl($revied_changes, $revied_changes['ID']);
      $db->execute("UPDATE `{$tables['link']['name']}` SET `CACHE_URL` = '".$seoUrl."' WHERE ID = ".$revied_changes['ID']);
      
      $db->Execute("DELETE FROM `{$tables['additional_category']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
      $db->Execute("DELETE FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));

      $db->Execute("INSERT INTO `{$tables['additional_category']['name']}`
            SELECT * FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
      $db->Execute("INSERT INTO `{$tables['additional_link']['name']}` (`LINK_ID`, `TITLE`, `URL`)
            SELECT `LINK_ID`, `TITLE`, `URL` FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));

     $db->Execute("DELETE FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
     $db->Execute("DELETE FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));

      $output['status'] = 1;
      send_status_notifications($id, true, false); //Send status notification to link owner
      $db->Execute("DELETE FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id)); //Remove from reviews


   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Remove a link from database! This action can not be undone!
 * @param integer Link ID for link to be removed
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function RemoveLink($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Get all link details for status notification to link owner
   $data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
   //Delete link from reviews table
   $db->Execute("DELETE FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));

   $db->Execute("DELETE FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
   $db->Execute("DELETE FROM `{$tables['additional_category']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));

   $db->Execute("DELETE FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
   $db->Execute("DELETE FROM `{$tables['additional_link']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));

   $si = $db->GetAll("SELECT * FROM `{$tables['submit_item']['name']}` s LEFT JOIN `{$tables['submit_item_status']['name']}` ss
                        ON s.`ID` = ss.`ITEM_ID`
                        WHERE (s.`TYPE`='IMAGE' OR s.`TYPE`='FILE')
                        AND ss.`LINK_TYPE_ID` = ".$db->qstr($data['LINK_TYPE'])." AND
                        ss.`STATUS` = '2'");

   for ($i=0; $i<count($si); $i++) {
       $name = $data[$si[$i]['FIELD_NAME']];
       if (file_exists(INSTALL_PATH.'/uploads/'.$name)) {
            unlink(INSTALL_PATH.'/uploads/'.$name);
        }
        if (file_exists(INSTALL_PATH.'/uploads/thumb/'.$name)) {
            unlink(INSTALL_PATH.'/uploads/thumb/'.$name);
        }
   }

   //Delete link from links table
   if ($db->Execute("DELETE FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id)))
   {
      $output['status'] = 1;
      $data['STATUS'] = 0;
      send_status_notifications($data, false); //Send status notification to link owner
   }
   else
   {
      $output['errorMsg'] = $db->ErrorMsg();
   }

   return $output;
}

/**
 * Remove a category from database! This action can not be undone!
 * Notice: This will remove all subcategories, all links and all articles in this category and it's subcategories
 * @param integer category ID for category to be removed
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function RemoveCategoryAndContent($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Get all subcategories
   $allCategs   = get_sub_categories($id);
   $allCategs[] = $id;

   //Delete links from all subcategories and current category
   $sql = "DELETE FROM `{$tables['link']['name']}` WHERE `CATEGORY_ID` IN ('".implode ("', '", $allCategs)."')";
   if (!$db->Execute($sql))
   {
      //Error occured while removing links
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
      return $output;
   }

   //Delete all subcategories and current category
   $sql = "DELETE FROM `{$tables['category']['name']}` WHERE `ID` IN ('".implode ("', '", $allCategs)."')";
   if (!$db->Execute($sql))
   {
      //Error occured while removing category and it's subcategories
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
      return $output;
   }
   else
   {
      //Successfully removed category,subcategories,links and articles
      $output['status'] = 1;
   }

   //Free memory
   unset ($allCategs, $sql);

   return $output;
}

/**
 * Remove a category from database! This action can not be undone!
 * Notice: This will keep all subcategories with a (wrong) parent ID and all links with a (wrong) category ID
 * @param integer category ID for category to be removed
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function RemoveCategory($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Get all subcategories
   $allCategs   = get_sub_categories($id);
   $allCategs[] = $id;

   //Make links orphan from current category
   $sql = "UPDATE `{$tables['link']['name']}` SET `CATEGORY_ID` = '-1' WHERE `CATEGORY_ID` = ".$db->qstr($id);
   if (!$db->Execute($sql))
   {
      //Error occured while updating links
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
      return $output;
   }

   //Make subcategories orphan of current category
   $sql = "UPDATE `{$tables['category']['name']}` SET `PARENT_ID` = '-1' WHERE `PARENT_ID` = ".$db->qstr($id);;
   if (!$db->Execute($sql))
   {
      //Error occured while updating subcategories
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
      return $output;
   }

   //Delete category from table
   if ($db->Execute("DELETE FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($id)))
   {
      $output['status'] = 1;
   }
   else
   {
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
      return $output;
   }

   return $output;
}


function SetNewCommentStatus($id=0, $val=1)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Define correct status values
   $correctStatusValues = array (0, 1, 2);

   if (!in_array ($val, $correctStatusValues))
   {
      $output['errorMsg'] = _L('No valid status value');
      return $output;
   }

   //Update article status
   if ($db->Execute("UPDATE `{$tables['comment']['name']}` SET `STATUS` = ".$db->qstr($val)." WHERE `TYPE` = '2' AND `ID` = ".$db->qstr($id)))
   {
      $output['status'] = 1;
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Remove a comment from database! This action can not be undone!
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function RemoveComment($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';
 $data= $db->GetRow("SELECT * FROM `{$tables['comment']['name']}` WHERE `TYPE` = '2' AND `ID` = ".$db->qstr($id));
   //Delete comment from table
   if ($db->Execute("DELETE FROM `{$tables['comment']['name']}` WHERE `TYPE` = '2' AND `ID` = ".$db->qstr($id)))
   {
	

      $output['status'] = 1;
   }
   else
   {
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
      return $output;
   }

   return $output;
}


/**
 * Move link to another category
 * @param  integer Link ID for link to be moved
 * @param  integer Category ID where link should be moved
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function MoveLinkToCateg($linkID=0, $categID=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Move link to new category ID
   if ($db->Execute("UPDATE `{$tables['link']['name']}` SET `CATEGORY_ID` = ".$db->qstr($categID)." WHERE `ID` = ".$db->qstr($linkID)))
   {
      //Success
      $output['status'] = 1;
   }
   else
   {
      //Error occured
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
   }

   return $output;
}

/**
 * Remove a link review from database! (main link won't be removed) This action can not be undone!
 * @param integer ID for link review to be removed
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function RemoveLinkReview($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Delete link from reviews table
   if ($db->Execute("DELETE FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id)))
   {
      $db->Execute("DELETE FROM `{$tables['additional_category_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
      $db->Execute("DELETE FROM `{$tables['additional_link_review']['name']}` WHERE `LINK_ID` = ".$db->qstr($id));
      $output['status'] = 1;
      send_status_notifications($id, true, false, true); //Send status notification to link owner
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Make a link regular (not featured)
 * @param integer Link ID for link to be processed
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function MakeRegularLink($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Make link regular
   if ($db->Execute("UPDATE `{$tables['link']['name']}` SET `FEATURED` = ".$db->qstr(0)." WHERE `ID` = ".$db->qstr($id)))
      $output['status'] = 1;
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Make a link featured
 * @param integer Link ID for link to be processed
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function MakeFeaturedLink($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Make link featured
   if ($db->Execute("UPDATE `{$tables['link']['name']}` SET `FEATURED` = ".$db->qstr(1)." WHERE `ID` = ".$db->qstr($id)))
      $output['status'] = 1;
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Set new status for a category
 * @param integer Category ID for category to be processed
 * @param integer Status value for link (2 = active, 1 = pending, 0 = inactive)
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function SetNewCategoryStatus($id=0, $val=1)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

      //Update link status
      if ($db->Execute("UPDATE `{$tables['category']['name']}` SET `STATUS` = ".$db->qstr($val)." WHERE `ID` = ".$db->qstr($id)))
      {
         $output['status'] = 1;
         send_status_notifications($id); //Send status notification to link owner
      }
      else
         $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Set new payment status
 * @param integer Payment ID to be processed
 * @param integer Status value for payment (-1 = pending, 0 = uncleared, 1 = paid, 2 = canceled)
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function SetNewPaymentStatus($id=0, $val=-1)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Define correct status values
   $correctStatusValues = array (0, 1, 2, 3);

   if (in_array ($val, $correctStatusValues))
   {
      if ($val == 2)
      {
         //Update payment status (paid)
         if ($db->Execute("UPDATE `{$tables['payment']['name']}` SET `CONFIRMED` = ".$db->qstr($val).", `PAYED_TOTAL` = `TOTAL`, `CONFIRM_DATE`= NOW() WHERE `ID` = ".$db->qstr($id)))
         {
            $output['status'] = 1;
         }
         else
         {
            //Database error
            $output['errorMsg'] = $db->ErrorMsg();
         }

         return $output;
      }
      else
      {
         //Update payment status
         if ($db->Execute("UPDATE `{$tables['payment']['name']}` SET `CONFIRMED` = ".$db->qstr($val).", `PAYED_TOTAL` = '0.00', `CONFIRM_DATE`= NULL WHERE `ID` = ".$db->qstr($id)))
         {
            $output['status'] = 1;
         }
         else
         {
            //Database error
            $output['errorMsg'] = $db->ErrorMsg();
         }

         return $output;
      }
   }
   else
   {
      //Invalid status values
      $output['errorMsg'] = _L('Invalid payment status value');
   }

   return $output;
}

/**
 * Remove a payment listing from database! This action can not be undone!
 * @param integer Payment ID to be removed
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function RemovePayment($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Delete payment info
   if ($db->Execute("DELETE FROM `{$tables['payment']['name']}` WHERE `ID` = ".$db->qstr($id)))
   {
      $output['status'] = 1;
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Change parent category for a category
 * @param integer Category ID
 * @param integer New parent category ID
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function categChangeParent($categID=0, $parentID=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

    if ($parentID === false)
   {
      $output['errorMsg'] = _L('No valid parent category ID passed');
      return $output;
   }

   if ($categID <= 0)
   {
      $output['errorMsg'] = _L('No valid category ID passed');
      return $output;
   }

   if ($categID == $parentID)
   {
      $output['errorMsg'] = _L('Current category ID and new parent ID are the same for selected category').": ".$db->GetOne("SELECT `TITLE` FROM `{$tables['category']['name']}` WHERE `ID` = ".$db->qstr($categID));
      return $output;
   }

   //Change parent category
   if ($db->AutoExecute($tables['category']['name'], array ('PARENT_ID' => $parentID), 'UPDATE', "`ID` = ".$db->qstr($categID)))
   {
      $output['status'] = 1;
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Change category for a link
 * @param integer Link ID
 * @param integer Category ID
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function linkChangeCateg($linkID=0, $categID=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   if ($linkID <= 0)
   {
      $output['errorMsg'] = _L('No valid link ID passed');
      return $output;
   }

   if ($categID <= 0)
   {
      $output['errorMsg'] = _L('No valid category ID passed');
      return $output;
   }

   //Change category
   if ($db->AutoExecute($tables['link']['name'], array ('CATEGORY_ID' => $categID), 'UPDATE', "`ID` = ".$db->qstr($linkID)))
   {
      $output['status'] = 1;
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Change category for a link review
 * @param integer Link ID
 * @param integer Category ID
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function linkReviewChangeCateg($linkID=0, $categID=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   if ($linkID <= 0)
   {
      $output['errorMsg'] = _L('No valid link ID passed');
      return $output;
   }

   if ($categID <= 0)
   {
      $output['errorMsg'] = _L('No valid category ID passed');
      return $output;
   }
    //Change category
   if ($db->AutoExecute($tables['link_review']['name'], array ('CATEGORY_ID' => $categID), 'UPDATE', "`LINK_ID` = ".$db->qstr($linkID)))
   {
      $output['status'] = 1;
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Ban submitter IP of given link ID
 * @param integer Link ID to ban submitter IP
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function banLinkSubmitIP($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Get IP address
   $IP = $db->GetOne("SELECT `IPADDRESS` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));

   if (empty ($IP))
   {
      $output['errorMsg'] = _L('Could not determine IP address for link').": ".$db->GetOne("SELECT `TITLE` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
      return $output;
   }

   //Check if IP address was already banned
   $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['banlist']['name']}` WHERE `BAN_IP` = ".$db->qstr($IP));

   /*
   if ($count > 0)
   {
      $output['errorMsg'] = str_replace ('#IPADDRESS#', '<code>'.$IP.'</code>', _L('Domain #IPADDRESS# was already banned').'!');
      return $output;
   }
   */

   //Add IP address to banlist
   if ($db->AutoExecute($tables['banlist']['name'], array ('BAN_IP' => $IP), 'INSERT', false))
   {
      $output['status'] = 1;
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Ban submitter IP of given link ID
 * @param integer Link ID to ban submitter IP
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */

/**
 * Ban domain of given link ID
 * @param integer Link ID to ban domain
 * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
 */
function banDomain($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';

   //Get URL
   $URL = $db->GetOne("SELECT `URL` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));

   if (empty ($URL))
   {
      $output['errorMsg'] = _L('Could not determine URL for link').": ".$db->GetOne("SELECT `TITLE` FROM `{$tables['link']['name']}` WHERE `ID` = ".$db->qstr($id));
      return $output;
   }

   //Determine domain from URL
   $domain = parseDomain($URL);

   //Check if domain was already banned
   $count = $db->GetOne("SELECT COUNT(*) FROM `{$tables['banlist']['name']}` WHERE `BAN_DOMAIN` = ".$db->qstr($domain));

   /*
   if ($count > 0)
   {
      $output['errorMsg'] = str_replace ('#DOMAIN#', '<code>'.$domain.'</code>', _L('Domain #DOMAIN# was already banned').'!');
      return $output;
   }
   */

   //Add domain to banlist
   if ($db->AutoExecute($tables['banlist']['name'], array ('BAN_DOMAIN' => $domain), 'INSERT', false))
   {
      $output['status'] = 1;
   }
   else
      $output['errorMsg'] = $db->ErrorMsg();

   return $output;
}

/**
 * Calculate next sorting URL
 * @param array   Columns
 * @param integer Current paging item
 * @return array  Array with columns as key, full sorting URLs as values
 */
function GetColSortUrls($columns=null, $currItem=1)
{
   if (!is_array ($columns) || empty ($columns))
   {
      return false;
   }

   //Correct paging item
   $currItem = ($currItem < 0 ? 0 : intval ($currItem));

   //Determine category ID
   $categID = (!empty ($_REQUEST['c']) ? intval ($_REQUEST['c']) : '');
   $categID = ($categID < 0 ? 0 : $categID);

   //Determine search keywords
   $search = (!empty ($_REQUEST['search']) ? trim (urldecode ($_REQUEST['search'])) : '');

   //Determine if featured link
   $featured = (isset ($_REQUEST['f']) && $_REQUEST['f'] == 1  ? 1 : 0);

   $columnURLs = array ();
   //Loop through each column
   foreach ($columns as $key => $column)
   {
      //Determine next sort order
      if (SORT_FIELD != $key)
         $order = 'ASC';
      else
         $order = (SORT_ORDER == 'ASC' ? 'DESC' : 'ASC');

      //Build URL
      $columnURLs[$key] = "sort={$key}&amp;sort_order={$order}&amp;p={$currItem}".(!empty ($categID) ? "&amp;c={$categID}" : '').($featured == 1 ? "&amp;f=1" : '').(!empty ($search) ? "&amp;search=".urlencode ($search) : '');

      unset ($columns[$key], $column, $order); //Free memory
   }

   return $columnURLs;
}

 function SetNewLinkCommentStatus($id=0, $val=1)
 {
    global $db, $tables;
 
    $output = array ();
    $output['status']   = 0;
    $output['errorMsg'] = '';
 
    //Define correct status values
    $correctStatusValues = array (0, 1, 2);
 
    if (!in_array ($val, $correctStatusValues))
    {
       $output['errorMsg'] = _L('No valid status value');
       return $output;
    }
 
    //Update article status
    if ($db->Execute("UPDATE `{$tables['comment']['name']}` SET `STATUS` = ".$db->qstr($val)." WHERE `TYPE` = '1' AND `ID` = ".$db->qstr($id)))
    {
       $output['status'] = 1;
    }
    else
       $output['errorMsg'] = $db->ErrorMsg();
 
    return $output;
 }
 
 /**
  * Remove a comment from database! This action can not be undone!
  * @return array  Array with processed status (1 = success, 0 = error) and the DB error message
  */
function RemoveLinkComment($id=0)
{
   global $db, $tables;

   $output = array ();
   $output['status']   = 0;
   $output['errorMsg'] = '';
   $data= $db->GetRow("SELECT * FROM `{$tables['comment']['name']}` WHERE `TYPE` = '1' AND `ID` = ".$db->qstr($id));
   $db->Execute("UPDATE `{$tables['link']['name']}` SET `COMMENT_COUNT` = `COMMENT_COUNT` - 1 WHERE `ID` = ".$db->qstr($data['ITEM_ID']));
   //Delete comment from table
   if ($db->Execute("DELETE FROM `{$tables['comment']['name']}` WHERE `TYPE` = '1' AND `ID` = ".$db->qstr($id)))
   {
    

      $output['status'] = 1;
   }
   else
   {
      $output['status']   = 0;
      $output['errorMsg'] = $db->ErrorMsg();
      return $output;
   }

   return $output;
}

?>