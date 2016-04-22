<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	

class CheckReciprocalLinks extends Task {

	public $description 	   = 'Check Reciprocal Links';
	public $run_freq			= 0;


	public function __construct($db) {
   	parent::__construct($db);
   	
	}
	
	public function get_total_num() {
		global $tables;
   	$total_num = $this->db->GetOne("SELECT COUNT(`ID`) FROM `{$tables['link']['name']}` WHERE `RECPR_URL` IS NOT NULL");

   	return $total_num;
	}
	
	private function check_recpr($data) {
		global $db, $tables;
		
		// SAME_DOMAIN_RECPR, RECPR_NEED_200_OK RECPR_CHECK_NOFOLLOW

   	//Check for reciprocal link field
   	if (!isset ($data['RECPR_URL'])) {
      	//No reciprocal link available
    		//Reciprocal link validation failed
    		return -1;
    	}

   	$data['RECPR_URL'] = trim ($data['RECPR_URL']);

   	//Check if reciprocal link is not empty
   	if (empty ($data['RECPR_URL'])) {
      	//Reciprocal link field empty
      	//Reciprocal link validation failed
      	return -1;
   	}
   	
		$sets = $db->GetAssoc("SELECT `ID`, `VALUE` FROM `{$tables['config']['name']}` WHERE `ID` IN ('ENABLE_THREE_WAY', 'THREE_WAY_LINK_URL', 'SITE_URL', 'THREE_WAY_LINK_TITLE', 'SITE_NAME',  'SAME_DOMAIN_RECPR', 'RECPR_NEED_200_OK', 'RECPR_CHECK_NOFOLLOW', 'RECPR_CHECK_DOMAIN')");

		$default_recpr_link_url   = ($sets['ENABLE_THREE_WAY'] == 1 ? $sets['THREE_WAY_LINK_URL']  : $sets['SITE_URL']);
   	$default_recpr_link_title = ($sets['ENABLE_THREE_WAY'] == 1 ? $sets['THREE_WAY_LINK_TITLE'] : $sets['SITE_NAME']);
   	
		if($sets['SAME_DOMAIN_RECPR'] == 1) {
			//Check if submitted URL is from the same domain as the recpr URL
			$submitDomain = parseDomain(!empty ($data['URL']) ? $data['URL'] : '');
			$recprDomain  = parseDomain(!empty ($data['RECPR_URL']) ? $data['RECPR_URL'] : '');
			if ($submitDomain != $recprDomain) {
   			//Submitted URL is not of the same domain as the recpr URL
   			return 0;
			}
			unset ($submitDomain, $recprDomain);
		}
		
   	//Load HTTP request class
   	require_once 'libs/request/request.class.php';

   	$fallBack = false;

   	//If HTTP request class exists,
   	//use it over the default content browser
   	if (class_exists ('http_request')) {
      	//Define request options
      	$options = array();
      	//Default http request version ("HTTP/1.1" or "HTTP/1.0")
      	$options['httpVersion']          = 'HTTP/1.1';
      	//Type of HTTP request method (GET, POST, etc)
      	//Currently only GET is fully supported
      	$options['method']               = 'GET';
      	//User agent to masquerade as
      	$options['userAgent']            = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0';
      	//Maximum number of seconds to allow for connection
      	$options['connTimeout']          = 10;
     		 //Maximum number of seconds to allow request functions to execute/read
     		 $options['readTimeout']          = 10;
     		 //Whether to allow redirects (done on 3xx status codes)
      	$options['allowRedirects']       = true;
      	//Maximum redirects allowed (works only if "allowRedirects" is TRUE)
      	$options['maxRedirects']         = 5;
      	//Define request methods in order they should be tried out
      	//NULL or empty array will reset to defaults

      	//-->I am not sure if "file_get_contents" could timeout
      	//-->on neverending redirects or how to set these options
      	$options['requestMethodsOrder']  = array ( 'curl_request'      => 'curl_request', //use cURL (fast and reliable)
                                                 									'file_get_contents' => 'file_get_contents', //use "file_get_contents()" fast
                                                 									'request'           => 'request', //use basic content request
                                                 								);

     		 //Initialize HTTP request class
      	$http_request = new http_request($data['RECPR_URL'], $options);

      	if ($http_request === false) {
        		 //Could not fetch URL or and error occured
         	//Reciprocal link validation failed
         	return -1;
      	}
      	else {
         	//Get Content
         	$responseContent = $http_request->getResponseBody();
         	$responseContent = (is_string ($responseContent) && !empty ($responseContent) ? $responseContent : '');

         	//If no response content, fall back to "get_url()"
         	if (empty ($responseContent)) {
            	$fallBack = true;
         	}

         	$responseCode = ($http_request->getResponseCode() ? $http_request->getResponseCode() : 0);
         	$responseHeaders = $http_request->getResponseHeader();
         }
      } else {
      	$fallBack = true;
   	}
   	
   	if ($fallBack) {
     		 //HTTP request class is not available, or failed
      	$return = get_url($data['RECPR_URL'], URL_CONTENT);

      	//Get response status/code
      	$responseStr = (is_string ($return['response']) && !empty ($return['response']) ? $return['response'] : '');

      	//Extract response code (should be numeric)
      	preg_match ("#^(HTTP[\s]*/[\d\.\d]+[\s]*)([\d]+)#i", $responseStr, $matches);

      	//Check for matches
      	if (isset ($matches[2]) && !empty ($matches[2])) {
         	//Make sure response code is an integer value
         	$responseCode = intval ($matches[2]);
      	} else {
        	 	//Could not extract response code
         	$responseCode = 0;
      	}

      	//Get response headers
      	$responseHeaders = (isset ($return['headers']) && is_array ($return['headers']) ? $return['headers'] : array());
      	//Provide also lowercase response headers
      	foreach ($responseHeaders as $headerName => $headerValue) {
         	$responseHeaders[strtolower($headerName)] = $headerValue;
      	}

     	 	//Get Content
     	 	$getContent = (is_string ($return['content']) && !empty ($return['content']) ? $return['content'] : '');

     		 //Get correct content, optionally decode content
      	if (isset ($responseHeaders['Content-Encoding'])) {
         	//Decode any content-encoding (gzip or deflate) if needed
         	switch (strtolower ($responseHeaders['Content-Encoding'])) {
            	//Handle gzip encoding
            	case 'gzip':
               	$responseContent = gz_decode($getContent);
               	//If error occured, try again with a very simple decoder
               	if ($responseContent === false || empty ($responseContent)) {
                  	$responseContent = decode_gzip($getContent);
               	}
               	break;

           		 //Handle deflate encoding
            	case 'deflate':
               	$responseContent = decode_deflate($getContent);
               	break;

            	//No or undetermined content encoding
            	default:
               	$responseContent = $getContent;
               	break;
         	}
         } else {
         	//Content was not encoded or encoding was not determined
        		 //Use as is
         	$responseContent = $getContent;
      	}

      	unset ($getContent, $return);
   	}

   	//Check whether the response code is successful
  	 	//If response code was not determined, this step is skipped
   	if (isset($sets['RECPR_NEED_200_OK']) && $sets['RECPR_NEED_200_OK'] == 1 && $responseCode > 0) {
      	$resType = floor ($responseCode / 100);
     		 //Define success code prefixes
     		 //1XX, 2XX, etc
      	$successResTypes = array (1, 2);

      	//Success codes: 1XX, 2XX
      	//Not sure about 3XX
      	if (!in_array ($resType, $successResTypes)) {
         	return 0;
      	}

      	unset ($successResTypes);
      }

   	//Remove all kind of white-space chars
   	$responseContent = strip_white_space($responseContent);

   	if (empty ($responseContent)) {
      	//Could not fetch URL
      	//Possible library missing or an error occured
      	//Reciprocal link validation failed
     	 return -1;
   	}

   	if (isset($sets['RECPR_CHECK_NOFOLLOW']) && $sets['RECPR_CHECK_NOFOLLOW'] == 1) {
      	//Define array where META tags are stored
      	$metaTags = array();

      	//Define META tags that allow and use "nofollow",
      	//Don't need other tags
      	$robotTags = array('robots'    , //All robots/crawlers/SEs
                           				  'googlebot' , //Google crawler
                          					 /*'msnbot'*/
                  				 			);

     		 //Get META tags
      	preg_match_all ('/<[\s]*meta[\s]*(name|http-equiv)[\s]*=[\s]*["\']?([^>"\']*)["\']?[\s]*content[\s]*=[\s]*["\']?([^>"\']*)["\']?[\s]*[\/]?[\s]*>/si', $responseContent, $matches);

      	//Check if META tags available
      	if (!empty ($matches) && is_array ($matches) && count ($matches[2]) == count ($matches[3])) {
         	//Loop through each found META tag
         	foreach ($matches[2] as $key => $value) {
           	 	//Determine META tag
            	$value = clean_string_paranoia($value);

            	//Keep only "robots" META tags
           		if (in_array (strtolower ($value), $robotTags)) {
               	$metaTags[$value] = clean_string_paranoia($matches[3][$key]);
            	}
         	}
      	}

      	if (is_array ($metaTags) && !empty ($metaTags)) {
         	//Loop through each found "robots" tags
         	foreach ($metaTags as $tagName => $tagValue) {
            	//Check for global "nofollow" attributes
            	if (preg_match ('#nofollow#Ui', $tagValue)) {
              	 	//Found the "nofollow" attribute in META tags,
              		 //Reciprocal link validation failed
               	return 0;
            	}
         	}

        		 //Free memory
         	unset ($metaTags, $tagName, $tagValue);
         }
      }

   	//Define URL to look for
   	$recprURL = (isset($sets['RECPR_CHECK_DOMAIN']) && $sets['RECPR_CHECK_DOMAIN'] != 1 ? $default_recpr_link_url : parseDomain($default_recpr_link_url));

   	//Look for reciprocal in any link tag
   	preg_match_all ("`<(\s*)a([^>]*)".trim($recprURL)."([^>]*)>(.*)<\/a>`Ui", $responseContent, $matches);

   	//First check if matches array was created
   	if (!is_array ($matches) || empty ($matches)) {
     		 //No URL matches
      	//Reciprocal link validation failed
      	return 0;
   	}
   	
   	//Set internal pointer of array to its first element
   	reset ($matches);

 	 	 //Check if URL exists
   	if (!is_array ($matches[key ($matches)]) || empty ($matches[key ($matches)])) {
      	//URL was not found
      	//Reciprocal link validation failed
      	return 0;
   	}

   	//Check for "nofollow" attribute in link tag
   	if (isset($sets['RECPR_CHECK_NOFOLLOW']) && $sets['RECPR_CHECK_NOFOLLOW'] == 1) {
      	foreach ($matches as $m => $match) {
         	foreach ($match as $key => $value) {
            	//If anything matching <rel="nofollow"> is found, validation fails
            	if (preg_match ('`rel[\s]*=[\s]*("|\')?[\s]*nofollow[\s]*("|\')`Ui', $value)) {
               	//Found the "nofollow" attribute,
               	//Reciprocal link validation failed
               	return 0;
            	}
            	unset ($match[$key]);
         	}
         	unset ($matches[$m]);
      	}
   	}

   	//Free memory
   	unset ($matches, $responseContent, $responseHeaders);

   	return 1;
	}

	
	public function do_task() {   	global $db, $tables;
   	$count_links = $this->settings['RECIPROCAL_LINKS_PER_ITERATION'];
	if(!is_int($count_links))
		$count_links = 1;
		
   	$link = $db->GetRow("SELECT * FROM `{$tables['link']['name']}` WHERE `RECPR_URL` IS NOT NULL LIMIT {$this->done_num}, {$count_links}");
   	
   	$result = $this->check_recpr($link);
   	
   	if ($result < 1) {
   		// Reciprocal Link invalid or not exists
   		if ($this->settings['EXPIRED_STATUS']) {
   			$new_status = ($this->settings['EXPIRED_STATUS'] == 'Inactive') ? 0 : 1;
   			$db->Execute("UPDATE `{$tables['link']['name']}` SET `STATUS` = '{$new_status}', RECPR_LAST_CHECKED = NOW() WHERE `ID` = '{$link['ID']}'"); 
   		}
   		if ($this->settings['SEND_OWNER_NOTIFICATION']) {
   			send_status_notifications($link['ID']);
   		}
   	}

   	$result = $db->Execute("UPDATE `{$tables['link']['name']}` SET `STATUS` = '{$new_status}' WHERE `ID` = '{$link['ID']}'");
   	
   	$this->done_num+=$count_links;
   	
   	return $result;

	}




}

?>