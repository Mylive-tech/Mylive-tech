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
class PaymentController extends PhpldfrontController {
	public function indexAction() {
		$db = Phpld_Db::getInstance()->getAdapter();
		$tables = Phpld_Db::getInstance()->getTables();
		// Disable any caching by the browser
		disable_browser_cache();

		$id = $_REQUEST['id'];
		$id = (isset ($id) ? trim ($id) : 0);
		$id = preg_replace ('`(id[_]?)`', '', $id);
		$id = intval ($id);
		if ($id>0)
		{
			if (!empty ($_REQUEST['mode']) && $_REQUEST['mode'] == 'review')
			{
				$data = $db->GetRow("SELECT * FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$id);
				$data['ID'] = (!empty ($data['LINK_ID']) ? $data['LINK_ID'] : '');
				if (isset ($data['LINK_ID']))
					unset ($data['LINK_ID']);
			}
			else
				$data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$id);
			$link_type = $db->GetRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$data['LINK_TYPE']}'");
			if(DEBUG)
			{
				if($db->ErrorNo()>0)
					echo $db->ErrorMsg();
			}
		}

		if($_SESSION['PRIVACY'] != '1')
		{
			$data['OWNER_EMAIL']= 'Hidden for Privacy Reasons.';
		}

		if (empty ($data['ID']))
		{
			throw new Exception("Could not process link with ID=".$id);
		}

		$price = $link_type['PRICE'];
		$ext = $link_type['PAY_UM'];
		$sublength = $link_type['PAY_UM'];

		$SubscriptionEnabled = 0;

		if (PAY_ENABLE_SUBSCRIPTION == 1 && $ext != 5)
		{
			$SubscriptionEnabled = 1;
			$Subscription = array ();
			if ($ext == 2)
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 3;
				$sublength = 2;
			}
			elseif ($ext == 3)
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 6;
				$sublength = 3;
			}
			elseif ($ext == 4)
			{
				$Subscription['UM']     = _L('Year');
				$Subscription['PERIOD'] = 1;
				$sublength = 4;
			}
			else
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 1;
				$sublength = 1;
			}
			$this->view->assign('Subscription', $Subscription);
		}

		$validators = array(
			'rules' => array(
				'quantity' => array(
					'digits' => true,
					'min' => '1'
				)
			)
		);

		$vld = json_custom_encode($validators);
		$this->view->assign('validators', $vld);
		$validator = new Validator($validators);

		$this->view->assign('SubLength', $sublength);


		if (empty ($_REQUEST['submit']))
		{
			if (!empty ($_SERVER['HTTP_REFERER']))
				$_SESSION['return'] = $_SERVER['HTTP_REFERER'];
		}
		else
		{
			//JQuery validation related - server side.
			$validator = new Validator($validators);
			$validator_res = $validator->validate($_POST);

			if (empty($validator_res))
			{
				$link_price = $db->GetOne("SELECT `PRICE` FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$data['LINK_TYPE']}'");
				$pay_data = array ();
				$pay_id   = $db->GenID($tables['payment']['name'].'_SEQ');
				$pay_data['ID']        = $pay_id;
				$pay_data['LINK_ID']   = $data['ID'];
				$pay_data['IPADDRESS'] = (!empty ($client_info['IP']) ? $client_info['IP'] : get_client_ip());
				$pay_data['QUANTITY']  = $_REQUEST['quantity'];
				$pay_data['AMOUNT']    = $link_price;
				$pay_data['TOTAL']     = (int)$pay_data['QUANTITY'] * (float)$pay_data['AMOUNT'];
				//$pay_data['PAYED_TOTAL'] = (int)$pay_data['QUANTITY'] * (float)$pay_data['AMOUNT'];
				//$pay_data['PAYED_QUANTITY'] = $_REQUEST['quantity'];
				$pay_data['UM']        = $sublength;
				$pay_data['PAY_DATE']  = gmdate ('Y-m-d H:i:s');
				$pay_data['CONFIRMED'] = -1;
				$pay_data['SUBSCRIBED'] = (isset ($_REQUEST['subscribe']) && $_REQUEST['subscribe'] == 1 && $SubscriptionEnabled == 1 ? 1 : 0);
				$result = db_replace('payment', $pay_data, 'ID');
				if ($result > 0)
				{
					$action = 'paypal';
					$this->view->assign('PAYMENT', $pay_data);
				}
				else
				{
					if(DEBUG)
						echo $db->ErrorMsg();
					$this->view->assign('error', true);
				}
			}
		}
		$quantity  = (!empty ($_REQUEST['quantity']) && preg_match ('`^[\d]+$`', $_REQUEST['quantity']) ? intval ($_REQUEST['quantity']) : 1);
		$subscribe = ($_REQUEST['subscribe'] == 1 ? 1 : 0);

		$this->view->assign('quantity' , $quantity);
		$this->view->assign('subscribe', $subscribe);
		$this->view->assign('price'    , $price);

		$path   = array ();
		$path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME)     , 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
		$path[] = array ('ID' => '0', 'TITLE' => _L('Link Payment'), 'TITLE_URL' => ''      , 'DESCRIPTION' => _L('Submit a new link to the directory '));
		$this->view->assign('path', $path);

		$this->view->assign('SubscribeOptions', array(0 => _L('No'), 1 => _L('Yes')) );

		$this->view->assign($data);
		$this->view->assign('payment_um', $payment_um);
		$this->view->assign('SubscriptionEnabled', $SubscriptionEnabled);
		$this->view->assign('SubscriptionPeriod', $Subscription['PERIOD']);
		$this->view->assign('SubscriptionUm', $Subscription['UM']);

		//Clean whitespace
		$this->view->load_filter('output', 'trimwhitespace');

		if ($action == 'paypal')
		{
			$this->view->assign("paypal", true);
		}
	}

	public function paidAction()
	{
		$db = Phpld_Db::getInstance()->getAdapter();
		$tables = Phpld_Db::getInstance()->getTables();
		// Disable any caching by the browser
		disable_browser_cache();

		$id=intval($_REQUEST['pid']);

		if ($id>0)
		{
			if (!empty ($_REQUEST['mode']) && $_REQUEST['mode'] == 'review')
			{
				$data = $db->GetRow("SELECT * FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$id);
				$data['ID'] = (!empty ($data['LINK_ID']) ? $data['LINK_ID'] : '');
				if (isset ($data['LINK_ID']))
					unset ($data['LINK_ID']);
			}
			else
				$data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$id);
			$link_type = $db->GetRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$data['LINK_TYPE']}'");
			if(DEBUG)
			{
				if($db->ErrorNo()>0)
					echo $db->ErrorMsg();
			}
		}

		if($_SESSION['PRIVACY'] != '1')
		{
			$data['OWNER_EMAIL']= 'Hidden for Privacy Reasons.';
		}

		if (empty ($data['ID']))
		{
			throw new Exception("Could not process link with ID=".$id);
		}

		$price = $link_type['PRICE'];
		$ext = $link_type['PAY_UM'];
		$sublength = $link_type['PAY_UM'];

		$SubscriptionEnabled = 0;

		if (PAY_ENABLE_SUBSCRIPTION == 1 && $ext != 5)
		{
			$SubscriptionEnabled = 1;
			$Subscription = array ();
			if ($ext == 2)
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 3;
				$sublength = 2;
			}
			elseif ($ext == 3)
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 6;
				$sublength = 3;
			}
			elseif ($ext == 4)
			{
				$Subscription['UM']     = _L('Year');
				$Subscription['PERIOD'] = 1;
				$sublength = 4;
			}
			else
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 1;
				$sublength = 1;
			}
			$this->view->assign('Subscription', $Subscription);
		}

		$validators = array(
			'rules' => array(
				'quantity' => array(
					'digits' => true,
					'min' => '1'
				)
			)
		);

		$vld = json_custom_encode($validators);
		$this->view->assign('validators', $vld);
		$validator = new Validator($validators);

		$this->view->assign('SubLength', $sublength);

		$this->view->assign('quantity' , $quantity);
		$this->view->assign('subscribe', $subscribe);
		$this->view->assign('price'    , $price);
		$this->view->assign('action'   , $action);

		$path   = array ();
		$path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME)     , 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
		$path[] = array ('ID' => '0', 'TITLE' => _L('Link Payment'), 'TITLE_URL' => ''      , 'DESCRIPTION' => _L('Submit a new link to the directory '));
		$this->view->assign('path', $path);

		$this->view->assign('SubscribeOptions', array(0 => _L('No'), 1 => _L('Yes')) );

		$this->view->assign($data);
		$this->view->assign('payment_um', $payment_um);
		$this->view->assign('SubscriptionEnabled', $SubscriptionEnabled);
		$this->view->assign('SubscriptionPeriod', $Subscription['PERIOD']);
		$this->view->assign('SubscriptionUm', $Subscription['UM']);

		//Clean whitespace
		$this->view->load_filter('output', 'trimwhitespace');
	}

	public function cancelAction()
	{
		$db = Phpld_Db::getInstance()->getAdapter();
		$tables = Phpld_Db::getInstance()->getTables();
		// Disable any caching by the browser
		disable_browser_cache();

		$id=intval($_REQUEST['pid']);
		if ($id>0)
		{
			if (!empty ($_REQUEST['mode']) && $_REQUEST['mode'] == 'review')
			{
				$data = $db->GetRow("SELECT * FROM `{$tables['link_review']['name']}` WHERE `LINK_ID` = ".$id);
				$data['ID'] = (!empty ($data['LINK_ID']) ? $data['LINK_ID'] : '');
				if (isset ($data['LINK_ID']))
					unset ($data['LINK_ID']);
			}
			else
				$data = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `ID` = ".$id);
			$link_type = $db->GetRow("SELECT * FROM `{$tables['link_type']['name']}` WHERE `ID` = '{$data['LINK_TYPE']}'");
			if(DEBUG)
			{
				if($db->ErrorNo()>0)
					echo $db->ErrorMsg();
			}
		}

		if($_SESSION['PRIVACY'] != '1')
		{
			$data['OWNER_EMAIL']= 'Hidden for Privacy Reasons.';
		}

		if (empty ($data['ID']))
		{
			throw new Exception("Could not process link with ID=".$id);
		}

		$price = $link_type['PRICE'];
		$ext = $link_type['PAY_UM'];
		$sublength = $link_type['PAY_UM'];

		$SubscriptionEnabled = 0;

		if (PAY_ENABLE_SUBSCRIPTION == 1 && $ext != 5)
		{
			$SubscriptionEnabled = 1;
			$Subscription = array ();
			if ($ext == 2)
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 3;
				$sublength = 2;
			}
			elseif ($ext == 3)
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 6;
				$sublength = 3;
			}
			elseif ($ext == 4)
			{
				$Subscription['UM']     = _L('Year');
				$Subscription['PERIOD'] = 1;
				$sublength = 4;
			}
			else
			{
				$Subscription['UM']     = _L('Month');
				$Subscription['PERIOD'] = 1;
				$sublength = 1;
			}
			$this->view->assign('Subscription', $Subscription);
		}

		$validators = array(
			'rules' => array(
				'quantity' => array(
					'digits' => true,
					'min' => '1'
				)
			)
		);

		$vld = json_custom_encode($validators);
		$this->view->assign('validators', $vld);
		$validator = new Validator($validators);

		$this->view->assign('SubLength', $sublength);

		$this->view->assign('quantity' , $quantity);
		$this->view->assign('subscribe', $subscribe);
		$this->view->assign('price'    , $price);
		$this->view->assign('action'   , $action);

		$path   = array ();
		$path[] = array ('ID' => '0', 'TITLE' => _L(SITE_NAME)     , 'TITLE_URL' => DOC_ROOT, 'DESCRIPTION' => SITE_DESC);
		$path[] = array ('ID' => '0', 'TITLE' => _L('Link Payment'), 'TITLE_URL' => ''      , 'DESCRIPTION' => _L('Submit a new link to the directory '));
		$this->view->assign('path', $path);

		$this->view->assign('SubscribeOptions', array(0 => _L('No'), 1 => _L('Yes')) );

		$this->view->assign($data);
		$this->view->assign('payment_um', $payment_um);
		$this->view->assign('SubscriptionEnabled', $SubscriptionEnabled);
		$this->view->assign('SubscriptionPeriod', $Subscription['PERIOD']);
		$this->view->assign('SubscriptionUm', $Subscription['UM']);

		//Clean whitespace
		$this->view->load_filter('output', 'trimwhitespace');
	}

	/**
	 * This action is accessed by PayPal upon completed transaction.
	 * It checks PayPal for transaction results
	 */
	public function callbackAction()
	{
		$paypal_host = 'www.paypal.com';
		//$paypal_host = 'www.sandbox.paypal.com';
		$paypal_path = '/cgi-bin/webscr';
		$ipn_data = array ();

		$pid = intval($_GET['pid']);
		$post_string = '';
		foreach ($_POST as $field => $value)
		{
			$ipn_data[$field] = $value;
			$post_string     .= $field.'='.urlencode ($value).'&';
		}
		$post_string .= "cmd=_notify-validate"; // append ipn command

		$ipn_response="";
		// Open the connection to paypal
	//	$fp = @ fsockopen ($paypal_host, '80', $err_num, $err_str, 30);
	// paypal changed things up once more
	    $fp = @ fsockopen ('ssl://'.$paypal_host, 443, $err_num, $err_str, 30);
		if (!$fp)
		{
			// Could not open the connection.
			// If loggin is on, the error message will be in the log.
			$last_error = "fsockopen error no. {$errnum}: {$errstr}";
			log_ipn_results(false, $ipn_data, $last_error, $ipn_response, $pid);
			exit();
		}
		else
		{
			// Post the data back to paypal
			fputs ($fp, "POST {$paypal_path} HTTP/1.1\n");
			fputs ($fp, "Host: {$paypal_host}\n");
			fputs ($fp, "Content-type: application/x-www-form-urlencoded\n");
			fputs ($fp, "Content-length: ".strlen ($post_string)."\n");
			fputs ($fp, "Connection: close\n\n");
			fputs ($fp, $post_string."\n\n");

			// Loop through the response from the server and append to variable
			while (!feof ($fp))
				$ipn_response .= fgets ($fp, 1024);

			// Close connection
			fclose ($fp);
		}

		if (preg_match("/VERIFIED/i", $ipn_response))
		{
			// Valid IPN transaction.
			$last_error=null;
			log_ipn_results(true, $ipn_data, $last_error, $ipn_response, $pid);
			exit();
		}
		else
		{
			// Invalid IPN transaction.  Check the log for details.
			$last_error = 'IPN Validation Failed.';
			log_ipn_results(false, $ipn_data, $last_error, $ipn_response, $pid);
			exit();
		}
	}
}
