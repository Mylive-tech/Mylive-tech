<?php
/**
 # ################################################################################
 # Project:   PHP Link Directory
 #
 # **********************************************************************
 # Copyright (C) 2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
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
 # @copyright      2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.0 Codename Transformer
 # ################################################################################
 */

class http_request
{
   /**
    * YOU MAY SET HERE SOME DEFAULT VARIABLES
    * It is suggested you pass them via an associative array with the class constructor
    * [variable] => [value]
    */


   /**
    * Full URL we are connecting to
    * @var string
    */
   var $url = '';

   /**
    * Port we are connecting to
    * Will change automatically for some known protocolls
    * @var integer
    */
   var $port = 80;

   /**
    * Default http request version ("HTTP/1.1" or "HTTP/1.0")
    * @var string
    */
   var $httpVersion =  'HTTP/1.1';

   /**
    * Type of HTTP request method (GET, POST, etc)
    * Currently only GET is fully supported
    * @var string
    */
   var $method = 'GET';

   /**
    * User agent to masquerade as
    * @var string
    */
   var $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0';

   /**
    * Maximum number of seconds to allow for connection
    * @var integer
    */
   var $connTimeout = 5;

   /**
    * Maximum number of seconds to allow request functions to execute/read
    * @var integer
    */
   var $readTimeout = 5;

   /**
    * Whether to allow redirects (done on 3xx status codes)
    * http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6.1.1
    * @var boolean
    */
   var $allowRedirects = true;

   /**
    * Maximum redirects allowed
    * @var integer
    */
   var $maxRedirects = 5;

   /**
    * Defined order of request methods they should be tried out
    * @var array
    */
   var $requestMethodsOrder = array ();

/************************* STOP EDITING HERE *************************/
/*********************************************************************/

   /**
    * Parsed URL into components
    * @var array
    */
   var $urlParts = array();

   /**
    * URL scheme (http, https, ftp, ftps, imap, pop3, etc.)
    * @var string
    */
   var $scheme = 'http';

   /**
    * Complete URL path including querystring (=after the question mark "?")
    * @var string
    */
   var $urlPath = '';

   /**
    * Clean hostname (just domain like "www.example.com")
    * @var string
    */
   var $hostName = '';

   /**
    * HTTP status code
    * @var string
    */
   var $responseCode = 0;

   /**
    * Response body
    * @var string
    */
   var $responseBody = null;

   /**
    * Request headers
    * @var array
    */
   var $requestHeaders = array();

   /**
    * Response headers
    * @var array
    */
   var $responseHeaders = array();

   /**
    * Current number of redirects
    * @var integer
    */
   var $redirects = 0;

   /**
    * Defined order of request methods
    * @var array
    */
   var $requestMethods = array ('curl_request', 'file_get_contents', 'request');

   /**
    * Wheter to use fopen
    * fopen is preffered for basic HTTP request because of "file_get_contents"
    * @var bool
    */
   var $allow_url_fopen = true;

   /**
    * Wheter to use PHP's built-in cURL
    * @var bool
    */
   var $curl = true;

   /**
    * Remaining length of the current chunk
    * @var integer
    */
   var $chunkLength = 0;

   /**
    * Hold last error Message
    * @var string
    */
   var $errorMsg = null;

   /**
    * HTTP Request class version
    * @var string
    */
    var $version = '1.0';




   /**
    * PHP 5 compatible wrapper for the constructor
    *
    * @param string The URL to fetch/access
    * @param array Associative array of parameters
    */
   function __construct($url, $params = array())
   {
      $this->http_request($url, $params);
   }

    /**
     * Constructor
     *
     * Sets up the object
     * @param string The URL to fetch/access
     * @param array Associative array of parameters
     * @return bool TRUE on success, FALSE on error
     */
   function http_request($url, $params = array())
   {
      //Make sure URL is not encoded
      $url = urldecode ($url);

      //Clean and load URL
      $url = str_replace (array ("\r\n", "\r", "\n", "\t", "\0", "\x0B") , "", $url);
      $url = preg_replace ('#[\s]+#i', '', $url);

      if (strlen ($url) > 0 && !preg_match ('#^(http[s]?|ftp[s]?|imap[s]?|pop3[s]?)?:\/\/#i', $url))
      {
         $url = 'http://' . $url;
      }
      $this->hostName = $this->parseDomain($url);

      if (empty ($this->hostName))
      {
         $this->errorMsg = 'No URL available to get contents';
         return false;
      }

      //Load settings
      if (is_array ($params) && !empty ($params))
      {
         //Loop through parameters
         foreach ($params as $key => $value)
         {
            //Load parameter
            $this->{$key} = $value;
         }
      }

      //Check if "allow_url_fopen" is enabled
      $this->allow_url_fopen = (!ini_get ('allow_url_fopen') ? false : true);

      //Check if cURL is available
      $this->curl = (!extension_loaded ('curl') || !function_exists ('curl_init') ? false : true);

      //Check if one of the request methods is available (fopen or cURL)
      if (!$this->allow_url_fopen && !$this->curl)
      {
         $this->errorMsg = "Your PHP configuration does not allow requesting URL contents! You need to enable either >>allow_url_fopen<< or compile PHP with the >>libcurl<< library.";

         //Stop and exit
         return false;
      }

      //Define default request methods
      $defaultRequestMethods = array ();
      //Allow cURL request if cURL library is available
      if ($this->curl)
      {
         $defaultRequestMethods['curl_request'] = 'curl_request';
      }
      //Allow "file_get_contents" and basic socket connection
      //if "allow_url_fopen" wrappers are enabled
      if ($this->allow_url_fopen)
      {
         $defaultRequestMethods['file_get_contents'] = 'file_get_contents';
         $defaultRequestMethods['request']           = 'request';
      }

      //Reset order of request methods
      $this->requestMethods = array();

      //Select request methods order
      if (!is_array ($this->requestMethodsOrder) || empty ($this->requestMethodsOrder))
      {
         $this->requestMethods = $defaultRequestMethods;
      }
      else
      {
         foreach ($this->requestMethodsOrder as $method => $function)
         {
            if (in_array ($function, $defaultRequestMethods))
            {
               $this->requestMethods[$method] = $function;
            }
         }
      }

      if (!is_array ($this->requestMethods) || empty ($this->requestMethods))
      {
         $this->errorMsg = 'No valid request methods defined. Using default order instead.';
         $this->requestMethods = $defaultRequestMethods;
      }

      //Set URL
      $this->url = $url;

      $this->method = strtoupper ($this->method);

      //Parse URL
      $this->urlParts = parse_url ($this->url);

      $this->scheme = strtolower (isset ($this->urlParts['scheme']) && $this->urlParts['scheme'] != $this->scheme ? $this->urlParts['scheme'] : $this->scheme);
      $this->port = (isset ($this->urlParts['port']) && $this->urlParts['port'] != $this->port ? intval ($this->urlParts['port']) : $this->port);
      $standardPort = $this->getStandardPort($this->scheme);
      $this->port = (!empty ($standardPort) && $standardPort != $this->port ? $standardPort : $this->port);
      $this->port = $this->port % 65536;

      $this->urlPath = (!empty ($this->urlParts['path']) ? trim ($this->urlParts['path']) : '/').(!empty ($this->urlParts['query']) ? '?'.trim($this->urlParts['query']) : '');

      //Add default headers
      $this->addHeader('User-Agent', $this->userAgent);
      $this->addHeader('Connection', 'close');

      //Use gzip encoding if possible
      //Avoid gzip encoding if using multibyte functions
      if ('HTTP/1.1' == $this->httpVersion && extension_loaded ('zlib') && 0 == (2 & ini_get ('mbstring.func_overload')))
      {
         $this->addHeader('Accept-Encoding', 'gzip');
      }

      //Start fetching the URL
      return $this->fetch();
   }

   /**
    * Starts requesting URL content by using the different methods until an error occurs,
    * no more methods available or it stops automatically on success
    *
    * @return bool TRUE on success, FALSE on error
    */
   function fetch()
   {
      //Ceil through each request method
      //stop on success, but continue if function returns error or content is empty
      foreach ($this->requestMethods as $method => $function)
      {
         $getContent = $this->$function();

         if ($getContent !== false && !empty ($this->responseBody))
         {
            //Request successfull, cleanup some elements and stop
            if (is_array ($this->responseHeaders) && !empty ($this->responseHeaders))
            {
               //Loop through each response header and remove empty elements
               foreach ($this->responseHeaders as $key => $value)
               {
                  if (strlen ($key) < 1 && isset ($this->responseHeaders[$key]))
                  {
                     unset ($this->responseHeaders[$key]);
                  }
                  unset ($key, $value);
               }
            }

            //Clean response body
            $this->responseBody = (is_string ($this->responseBody) ? trim ($this->responseBody) : $this->responseBody);

            //Return with success
            return true;
         }

         //Reset response
         $this->responseCode    = 0;
         $this->responseBody    = null;
         $this->responseHeaders = array();
         unset ($getContent);
      }

      return false;
   }

   /**
    * Request URL content via "file_get_contents()" function
    * Could be the best method, but most of the config options are not implemented (ie maximum redirects)
    *
    * @return bool TRUE on success, FALSE on error
    */
   function file_get_contents()
   {
      $storeTimeout = @ ini_get ('default_socket_timeout');

      //Set timeout
      if ($this->connTimeout)
      {
         @ ini_set ('default_socket_timeout', $this->connTimeout);
      }

      //Get requested content
      $responseContent = @ file_get_contents ($this->scheme.'://'.$this->hostName.($this->port != 80 ? ':'.$this->port : '').$this->urlPath);

      //Set back to default socket timeout
      @ ini_set ('default_socket_timeout', $storeTimeout);

      if ($responseContent === false)
      {
         //Error occured
         return false;
      }

      $responseCode = 200;

      //Store response headers if available
      if (is_array ($http_response_header) && !empty ($http_response_header))
      {
         foreach ($http_response_header as $key => $header)
         {
            $header = trim ($header);

            //Extract HTTP response code
            if (preg_match ('#^HTTP(.)*#i', $header))
            {
               $responseCode = $this->extractResponseCode($header);
            }

            $this->processHeader($header);
            unset ($http_response_header[$key], $header);
         }
      }

      $this->responseCode = $responseCode;
      $this->responseBody = $responseContent;
      unset ($responseContent, $responseCode, $storeTimeout);

      return true;
   }

   /**
    * Request URL content via PHP's built-in cURL functions
    * As long as "file_get_contents" could have a little problem with passing options,
    * this method is preffered. It's fast and it does most of the job automatically and good.
    *
    * @return bool TRUE on success, FALSE on error
    */
   function curl_request()
   {
      $curlVersion = @ curl_version();

      $cResource = @ curl_init();

      //Set cURL request URL
      @ curl_setopt ($cResource, CURLOPT_URL, $this->scheme.'://'.$this->hostName.$this->urlPath);

      //Set basic options
      @ curl_setopt ($cResource, CURLOPT_RETURNTRANSFER, 1);
      @ curl_setopt ($cResource, CURLOPT_FRESH_CONNECT, true);
      @ curl_setopt ($cResource, CURLOPT_ENCODING, '');//Accept all supported encodings

      //Set HTTP options
      @ curl_setopt ($cResource, CURLOPT_HTTP_VERSION, ('HTTP/1.0' == $this->httpVersion ? CURL_HTTP_VERSION_1_0 : CURL_HTTP_VERSION_1_1));
      @ curl_setopt ($cResource, CURLOPT_USERAGENT, $this->userAgent);
      @ curl_setopt ($cResource, CURLOPT_PORT, (preg_match ('#^[\d]+$#', $this->port) ? intval ($this->port) : 80));
      if ('POST' == strtoupper ($this->method))
      {
         @ curl_setopt ($cResource, CURLOPT_POST, true);
      }

      //Set timeout options
      @ curl_setopt ($cResource, CURLOPT_CONNECTTIMEOUT, $this->connTimeout);
      @ curl_setopt ($cResource, CURLOPT_TIMEOUT, $this->readTimeout);

      //Set redirect options
      @ curl_setopt ($cResource, CURLOPT_FOLLOWLOCATION, $this->allowRedirects);
      @ curl_setopt ($cResource, CURLOPT_MAXREDIRS, $this->maxRedirects);

      //Response header storage - uses callback function
      @ curl_setopt ($cResource, CURLOPT_HEADERFUNCTION, array ($this, 'read_curl_header'));

      //Get content
      $responseContent = trim (@ curl_exec ($cResource));
      $info = curl_getinfo ($cResource);

      @ curl_close ($cResource);

      if ($responseContent === false)
      {
         //Error occured
         $this->errorMsg = @ curl_error ($cResource);
         return false;
      }

      //Get response code
      $responseCode = 200;
      if (is_array ($this->responseHeaders) && !empty ($this->responseHeaders))
      {
         foreach ($this->responseHeaders as $key => $header)
         {
            $header = trim ($header);

            //Extract HTTP response code
            if (preg_match ('#^HTTP(.)*#i', $header))
            {
               $responseCode = $this->extractResponseCode($header);
            }
         }
      }

      $this->responseCode = $responseCode;
      $this->responseBody = $responseContent;
      unset ($responseContent, $cResource);

      return true;
   }

   /**
    * Basic URL request
    *
    * @return bool TRUE on success, FALSE on error
    */
   function request()
   {
      $host = $this->hostName;

      if (strcasecmp ($this->sheme, 'https') == 0 && function_exists ('file_get_contents') && extension_loaded ('openssl'))
      {
         $host = 'ssl://' . $host;
      }

      //Open socket connection
      if (!http_socket::fsockconnect($host))
      {
         return false;
      }

      if (!$this->fp)
      {
         $this->errorMsg = "({$errorNo}) ". trim ($errorStr);
         return false;
      }
      else
      {
         //Build request
         $request = $this->method . ' ' . $this->urlPath . ' ' . $this->httpVersion . "\r\n";

         if ('POST' != $this->method && 'PUT' != $this->method)
         {
            $this->removeHeader('Content-Type');
         }
         else
         {
            if (empty ($this->requestHeaders['Content-Type']))
            {
                //Default content-type
                $this->addHeader('Content-Type', 'application/x-www-form-urlencoded');
            }
            elseif ('multipart/form-data' == $this->requestHeaders['Content-Type'])
            {
                $this->addHeader('Content-Type', 'multipart/form-data; boundary=' . 'HTTP_Request_' . md5 (uniqid ('request') . microtime()));
            }
         }

         if ('HTTP/1.1' == $this->httpVersion)
         {
            $this->addHeader('Host', $host);
         }

        //Request Headers
        if (is_array ($this->requestHeaders) && !empty ($this->requestHeaders))
        {
            foreach ($this->requestHeaders as $name => $value)
            {
                $request .= $name . ': ' . $value . "\r\n";
            }
        }

         //If no POST/PUT data add a final CRLF
         if ('POST' != $this->method && 'PUT' != $this->_method)
         {
            $request .= "\r\n";
         }

         //Write request data
         http_socket::write($request);

         //Set timeout period on stream
         http_socket::setTimeout($this->readTimeout);

         //Processes the HTTP response
         if (!http_socket::process())
         {
            return false;
         }

         //Check if URL is redirecting
         if ($this->allowRedirects
            && $this->redirects <= $this->maxRedirects
            && $this->getResponseCode() > 300
            && $this->getResponseCode() < 399
            && !empty ($this->responseHeaders['location']))
         {
            $redirect = $this->responseHeaders['location'];

            if (preg_match('#^http[s]?:\/\/#i', $redirect))
            {
               $this->addHeader('Host', $this->parseDomain($redirect));
            }
            elseif ($redirect{0} == '/')
            {
               $this->urlPath = $redirect;
            }
            elseif (substr ($redirect, 0, 3) == '../' || substr ($redirect, 0, 2) == './')
            {
               if (substr ($this->urlPath, -1) == '/')
               {
                  $redirect = $this->urlPath . $redirect;
               }
               else
               {
                  $redirect = dirname ($this->urlPath) . '/' . $redirect;
               }
               $redirect = $this->parseDomain($redirect);
               $this->urlPath = $redirect;
            }
            else
            {
               if (substr ($this->urlPath, -1) == '/')
               {
                  $redirect = $this->urlPath . $redirect;
               }
               else
               {
                  $redirect = dirname ($this->urlPath) . '/' . $redirect;
               }
               $this->urlPath = $redirect;
            }

            //Increment redirects counter
            $this->redirects++;

            //Request new location
            return ($newHttpRequest = &$this->http_request($redirect));
         }
         elseif ($this->allowRedirects && $this->redirects > $this->maxRedirects)
         {
            $this->errorMsg = 'Too many redirects';
            return false;
         }

         http_socket::fsockdisconnect();
      }

      return true;
   }

   /**
    * Processes a HTTP response
    * This extracts response code, response headers and content body (automatically decoded)
    *
    * @return bool TRUE on success, FALSE on error
    */
   function process()
   {
      $responseBody = '';
      $returnBody   = '';

      do
      {
         $line = http_socket::readLine();
         if (sscanf ($line, 'HTTP/%s %s', $httpVersion, $returnCode) != 2)
         {
            $this->errorMsg = 'Malformed response';
            return false;
         }
         else
         {
            $this->httpVersion  = ('HTTP/1.1' != 'HTTP/' . $httpVersion ? 'HTTP/1.0' : 'HTTP/1.1');
            $this->responseCode = intval ($returnCode);
         }

         while ('' !== ($header = http_socket::readLine()))
         {
            $this->processHeader($header);
         }
      } while (100 == $this->responseCode);

      // If response body is present, read it and decode
      $chunked = isset ($this->responseHeaders['transfer-encoding']) && ('chunked' == $this->responseHeaders['transfer-encoding']);

      //Read data
      while (!http_socket::eof())
      {
         if ($chunked)
            $data = http_socket::readChunked();
         else
            $data = http_socket::read(4096);

         if ('' != $data)
         {
            $responseBody .= $data;
         }
      }

      //Check if content is encoded
      $contentEncoding = (!empty ($this->responseHeaders['content-encoding']) ? trim ($this->responseHeaders['content-encoding']) : '');

      //Decode any content-encoding (gzip or deflate) if needed
      switch (strtolower ($contentEncoding))
      {
         //Handle gzip encoding
         case 'gzip':
            $returnBody = $this->gz_decode($responseBody);

            //If error occured, try again with a very simple decoder
            if ($content === false || empty ($content))
            {
               $returnBody = $this->decode_gzip($responseBody);
            }
            break;

         //Handle deflate encoding
         case 'deflate':
            $returnBody = $this->decode_deflate($responseBody);
            break;

         //No or undetermined content encoding
         default :
            $returnBody = $responseBody;
            break;
      }

      if (empty ($returnBody))
      {
         $this->errorMsg = 'Nothing to read';
         return false;
      }

      //Save content body
      $this->responseBody = $returnBody;
      unset ($returnBody, $responseBody, $line, $chunked, $gzipped);

      return true;
   }

   /**
    * Returns the body of the response
    *
    * @return mixed response body, FALSE if not set
    */
   function getResponseBody()
   {
      return isset ($this->responseBody) ? $this->responseBody : false;
   }

   function read_curl_header($curl, $headers)
   {
      $this->processHeader(trim ($headers));
      return strlen ($headers);
   }

   /**
    * Return standard port number for known protocols
    *
    * @param string The protocol to lookup
    * @return integer Port number (NULL for no match or error)
    */
   function getStandardPort($scheme='http')
   {
      switch (strtolower ($scheme))
      {
            case 'http':
               return 80;
            case 'https':
               return 443;
            case 'ftp':
               return 21;
            case 'imap':
               return 143;
            case 'imaps':
               return 993;
            case 'pop3':
               return 110;
            case 'pop3s':
               return 995;
            default:
               return NULL;
      }
      return NULL;
   }

   /**
    * Return the response code
    *
    * @return mixed Response code, FALSE if not set
    */
   function getResponseCode()
   {
      return (isset ($this->responseCode) ? $this->responseCode : false);
   }

   /**
    * Returns either the named header or all if no name given
    *
    * @param string Header name to return, do not set to get all headers
    * @return mixed Either the value of $headername (false if header is not present) or an array of all headers
    */
   function getResponseHeader($headername = null)
   {
      if (!isset ($headername))
      {
         return (is_array ($this->responseHeaders) ? $this->responseHeaders : array());
      }
      else
      {
         return (isset ($this->responseHeaders[$headername]) ? $this->responseHeaders[$headername] : false);
      }
   }

   /**
    * Add a request header
    *
    * @param string Header name
    * @param string Header value
    */
   function addHeader($name, $value)
   {
      $this->requestHeaders[$name] = $value;
   }

   /**
    * Remove a request header
    *
    * @param string Header name to remove
    */
   function removeHeader($name)
   {
      if (isset ($this->requestHeaders[$name]))
      {
         unset ($this->requestHeaders[$name]);
      }
   }

   /**
    * Process the response headers
    *
    * @param  string  HTTP header
    */
   function processHeader($header)
   {
      list ($headerName, $headerValue) = explode (':', $header, 2);
      $headerName  = trim ($headerName);
      $headerValue = trim ($headerValue);

      $headerNameLowercase = strtolower ($headerName);

      $this->responseHeaders[$headerName] = $headerValue;
      $this->responseHeaders[$headerNameLowercase] = $headerValue;
    }

   /**
    * Extract HTTP response code from a header
    *
    * @param string HTTP header (0-zero on error)
    */
   function extractResponseCode($responseHeader)
   {
      $responseHeader = trim ($responseHeader);

      //Extract response code (should be numeric)
      preg_match ("#^(HTTP[\s]*/[\d\.\d]+[\s]*)([\d]+)#i", $responseHeader, $matches);

      //Check for matches
      if (isset ($matches[2]) && !empty ($matches[2]))
      {
         //Make sure response code is an integer value
         return intval ($matches[2]);
      }

      return 0;
   }

   /**
    * Parse URL and return its domain name (keep "www.")
    * @param string The URL to be parsed
    * @return string Domain name of URL
    */
   function parseDomain($url)
   {
      $url = trim ($url);

      if (empty ($url))
         return false;

      $output = parse_url ($url);

      if (!isset ($output['host']))
         return false;

      $pattern = array();
      //Remove http: and https: prefix
      $pattern[] = '#^http[s]?:#i';
      //Remove ftp: prefix
      $pattern[] = '#^ftp[s]?:#i';
      //Remove mailto: prefix
      $pattern[] = '#^mailto:#i';
      //Remove any dots as prefix
      $pattern[] = '#^\.#';
      //Remove any dots as suffix
      $pattern[] = '#\.$#';
      //Remove any unaccepted character
      $pattern[] = '#[^\w\d-\.]#i';

      $output['host'] = preg_replace ($pattern, '', $output['host']);

      return trim ($output['host']);
   }

   /**
    * Decode a gzip encoded message (when Content-encoding = gzip)
    * Currently requires PHP with zlib support
    * For details on the DEFLATE compression algorithm see (RFC 1951):
    * http://www.faqs.org/rfcs/rfc1951
    *
    * @author Aaron G.
    * @link   http://www.php.net/manual/en/function.gzencode.php#44470
    *
    * @param  string String to be decoded
    * @return string Decoded string
    */
   function gz_decode($data)
   {
      if (!function_exists ('gzinflate'))
      {
         //"gzinflate" function does not exist
         return false;
      }

      $len = strlen ($data);

      if ($len < 18 || strcmp (substr ($data, 0, 2), "\x1f\x8b"))
      {
         //Not GZIP format (See RFC 1952)
         return null;
      }

      //Compression method
      $method = ord (substr ($data, 2, 1));
      //Flags
      $flags  = ord (substr ($data, 3, 1));

      if ($flags & 31 != $flags)
      {
         //Reserved bits are set -- NOT ALLOWED by RFC 1952
         return null;
      }

      //NOTE: $mtime may be negative (PHP integer limitations)
      $mtime = unpack ("V", substr ($data, 4, 4));
      $mtime = $mtime[1];
      $xfl   = substr ($data,8,1);
      $os    = substr ($data,8,1);
      $headerlen = 10;
      $extralen  = 0;
      $extra     = "";

      if ($flags & 4)
      {
         //2-byte length prefixed EXTRA data in header
         if ($len - $headerlen - 2 < 8)
         {
            //Invalid format
            return false;
         }

         $extralen = unpack ("v", substr ($data, 8, 2));
         $extralen = $extralen[1];

         if ($len - $headerlen - 2 - $extralen < 8)
         {
            //Invalid format
            return false;
         }

         $extra = substr ($data, 10, $extralen);
         $headerlen += 2 + $extralen;
      }

      $filenamelen = 0;
      $filename    = "";

      if ($flags & 8)
      {
         //C-style string file NAME data in header
         if ($len - $headerlen - 1 < 8)
         {
            //Invalid format
            return false;
         }

         $filenamelen = strpos (substr ($data, 8 + $extralen), chr(0));

         if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8)
         {
            //Invalid format
            return false;
         }

         $filename = substr ($data, $headerlen, $filenamelen);
         $headerlen += $filenamelen + 1;
      }

      $commentlen = 0;
      $comment    = "";

      if ($flags & 16)
      {
         //C-style string COMMENT data in header
         if ($len - $headerlen - 1 < 8)
         {
            //Invalid format
            return false;
         }

         $commentlen = strpos (substr ($data, 8 + $extralen + $filenamelen), chr(0));

         if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8)
         {
            //Invalid header format
            return false;
         }

         $comment = substr ($data, $headerlen, $commentlen);
         $headerlen += $commentlen + 1;
      }

      $headercrc = "";

      if ($flags & 1)
      {
         //2-bytes (lowest order) of CRC32 on header present
         if ($len - $headerlen - 2 < 8)
         {
            //Invalid format
            return false;
         }

         $calccrc   = crc32 (substr ($data, 0, $headerlen)) & 0xffff;
         $headercrc = unpack ("v", substr($data, $headerlen, 2));
         $headercrc = $headercrc[1];

         if ($headercrc != $calccrc)
         {
            //Bad header CRC
            return false;
         }

         $headerlen += 2;
      }

      //GZIP FOOTER - These be negative due to PHPs limitations
      $datacrc = unpack ("V", substr ($data, -8, 4));
      $datacrc = $datacrc[1];
      $isize   = unpack ("V", substr ($data, -4));
      $isize   = $isize[1];

      //Perform the decompression:
      $bodylen = $len - $headerlen - 8;

      if ($bodylen < 1)
      {
         //This should never happen - IMPLEMENTATION BUG!
         return null;
      }

      $body = substr ($data, $headerlen, $bodylen);
      $data = "";

      if ($bodylen > 0)
      {
         switch ($method)
         {
            case 8:
               //Currently the only supported compression method:
               $data = gzinflate ($body);
               break;
            default:
               //Unknown compression method
               return false;
         }
      }
      else
      {
         //I'm not sure if zero-byte body content is allowed.
         //Allow it for now...  Do nothing...
      }

      // Verifiy decompressed size and CRC32:
      // NOTE: This may fail with large data sizes depending on how
      //      PHPs integer limitations affect strlen() since $isize
      //      may be negative for large sizes.
      if ($isize != strlen ($data) || crc32 ($data) != $datacrc)
      {
         //Bad format! Length or CRC doesnt match!
         return false;
      }

      return trim ($data);
   }

   /**
    * Decode a gzip encoded message (when Content-encoding = gzip)
    * Currently requires PHP with zlib support
    * For details on the DEFLATE compression algorithm see (RFC 1951):
    * http://www.faqs.org/rfcs/rfc1951
    *
    * @param  string String to be decoded
    * @return string Decoded string
    */
   function decode_gzip($body)
   {
      if (function_exists ('gzinflate'))
      {
         $body = substr ($body, 10);
         //Inflate a deflated string
         return trim (gzinflate ($body));
      }
      else
      {
         //Function does not exist
         return false;
      }
   }

   /**
    * Decode a zlib deflated message (when Content-encoding = deflate)
    * Currently requires PHP with zlib support
    * For details on the ZLIB compression algorithm see (RFC 1950):
    * http://www.faqs.org/rfcs/rfc1950
    *
    * @param  string String to be decoded
    * @return string Decoded string
    */
   function decode_deflate($body)
   {
      if (function_exists ('gzuncompress'))
      {
         //Uncompress the zlib compressed string
         return trim (gzuncompress ($body));
      }
      else
      {
         //Function does not exist
         return false;
      }
   }
}
//End of "http_request" class


class http_socket extends http_request
{
   /**
    * Socket file pointer
    * @var resource
    */
   var $fp = null;

   /**
    * Whether the socket is persistent (default is FALSE)
    * @var boolean
    */
   var $persistent = false;

   /**
    * Connect to the specified port
    * If already connected, it disconnects and connects again
    *
    * @param string  $hostName Host name
    * @param array   $options     See options for "stream_context_create"
    *
    * @return bool TRUE on success, FALSE on error
    */
   function fsockconnect($hostName, $options = null)
   {
      if (is_resource ($this->fp))
      {
         @ fclose($this->fp);
         $this->fp = null;
      }

      $this->connTimeout = ($this->connTimeout !== NULL ? $this->connTimeout : 5);

      $fsockFunction = (!$this->persistent ? 'fsockopen' : 'pfsockopen');

      if (is_array ($options) && function_exists ('stream_context_create'))
      {
         $context = @ stream_context_create ($options);
         $fp = @ $fsockFunction($hostName, $this->port, $errorNo, $errorStr, $this->connTimeout, $context);
      }
      else
      {
         if ($this->connTimeout)
         {
            $fp = @ $fsockFunction($hostName, $this->port, $errorNo, $errorStr, $this->connTimeout);
         }
         else
         {
            $fp = @ $fsockFunction($hostName, $this->port, $errorNo, $errorStr);
         }
      }

      if (!$fp)
      {
         $this->errorMsg = "({$errorNo}) {$errorStr}";
         return false;
      }

      $this->fp = $fp;
      return true;
    }

   /**
    * Disconnect - Close socket
    *
    * @access public
    * @return bool TRUE on success, FALSE on error
    */
   function fsockdisconnect()
   {
      if (!is_resource ($this->fp))
      {
         $this->errorMsg = 'Cannot close socket because not connected!';
         return false;
      }

      @ fclose($this->fp);
      $this->fp = null;
      return true;
   }

   /**
    * Sets the timeout value on socket
    *
    * @param integer Number of seconds to allow reading
    * @return bool TRUE on success, FALSE on error
    */
   function setTimeout($seconds)
   {
      if (!is_resource($this->fp))
      {
         $this->errorMsg = 'Cannot set timeout because not connected!';
         return false;
      }

      return @ stream_set_timeout ($this->fp, $seconds);
   }

   /**
    * Tests for end-of-file on a socket descriptor.
    *
    * @return bool
    */
   function eof()
   {
      return (is_resource ($this->fp) && feof ($this->fp));
   }

   /**
    * Get a specified line of data
    *
    * @param integer The number of bytes to read from the socket
    * @return mixed bytes of data from the socket, FALSE on error
    */
   function gets($size)
   {
      if (!is_resource ($this->fp))
      {
         $this->errorMsg = 'Cannot read because not connected!';
         return false;
      }

      return @ fgets ($this->fp, $size);
   }

   /**
    * Read a specified amount of data
    *
    * @param integer The number of bytes to read from the socket
    * @return mixed bytes of data from the socket, FALSE on error
    */
   function read($size = 4096)
   {
      if (!is_resource ($this->fp))
      {
         $this->errorMsg = 'Cannot read because not connected!';
         return false;
      }

      return @ fread ($this->fp, $size);
   }

   /**
    * Read until reached end of the socket or a newline
    *
    * @return mixed All available data up to a newline,FALSE on error
    */
   function readLine()
   {
      if (!is_resource ($this->fp))
      {
         $this->errorMsg = 'Cannot read because not connected!';
         return false;
      }

      $line = '';
      $timeout = time() + $this->readTimeout;

      while (!feof ($this->fp) && (!$this->readTimeout || time() < $timeout))
      {
         $line .= @ fgets ($this->fp, 2048);
         if (substr ($line, -1) == "\n")
         {
            return trim ($line);
         }
      }

      return $line;
   }

   /**
    * Read until the socket closes, or until there is no more data in the PHP buffer
    *
    * @return string All data until the socket closes
    */
   function readAll()
   {
      if (!is_resource ($this->fp))
      {
         $this->errorMsg = 'Cannot read because not connected!';
         return false;
      }

      $data = '';
      while (!feof ($this->fp))
      {
         $data .= @ fread ($this->fp, 2048);
      }

      return $data;
   }

   /**
    * Read a part of response body encoded with chunked Transfer-Encoding
    *
    * @return string
    */
   function readChunked()
   {
      if (0 == $this->chunkLength)
      {
         $line = http_socket::readLine();

         if (preg_match ('#^([0-9a-f]+)#i', $line, $matches))
         {
            $this->chunkLength = hexdec ($matches[1]);

            //Chunk with zero length indicates the end
            if (0 == $this->chunkLength)
            {
               http_socket::readAll(); // make this an eof()
               return '';
            }
         }
         elseif (feof ($this->fp))
         {
            return '';
         }
      }

      $data = http_socket::read($this->chunkLength);
      $this->chunkLength -= strlen ($data);

      if (0 == $this->chunkLength)
      {
         http_socket::readLine();
      }

      return $data;
   }

   /**
    * Write a specified amount of data.
    *
    * @param string  Data to write.
    * @param integer Amount of data to write at once (NULL= all at once)
    * @return mixed TRUE on success or an error object otherwise
    */
   function write($data, $blocksize = null)
   {
      if (!is_resource ($this->fp))
      {
         $this->errorMsg = 'Cannot write because not connected!';
         return false;
      }

      if (is_null ($blocksize) && substr (PHP_OS, 0, 3) != 'WIN')
      {
         return @ fwrite ($this->fp, $data);
      }
      else
      {
         if (is_null ($blocksize))
         {
            $blocksize = 1024;
         }

         $pos  = 0;
         $size = strlen ($data);

         while ($pos < $size)
         {
            $written = @ fwrite ($this->fp, substr ($data, $pos, $blocksize));

            if ($written === false)
            {
               return false;
            }
            $pos += $written;
         }

         return $pos;
      }
   }

}
//End of "http_request" class
?>