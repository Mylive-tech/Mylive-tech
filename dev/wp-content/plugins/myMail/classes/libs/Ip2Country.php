<?php

class Ip2Country {

	//maxmind doesn't provide a zip version so I've uploaded it to bitbucket (updated weekly)
	public $zip = 'http://rxa.li/geoipv6';
	public $renewinhours = 168; //(24*7)
	private $zipfile;
	private $dbfile;
	private $gi;
	private $renew = false;

	public function __construct() {
	
		require_once MYMAIL_DIR . 'classes/libs/geoip.inc.php';
		
		$this->zipfile = MYMAIL_UPLOAD_DIR.'/GeoIPv6.dat.zip';
		$this->dbfile = MYMAIL_UPLOAD_DIR.'/GeoIPv6.dat';
		
		if(mymail_option('countries_db') && mymail_option('countries_db') != $this->dbfile){
			$this->dbfile = mymail_option('countries_db');
		}else if (!file_exists($this->dbfile) || !mymail_option('countries') ) {
			add_action('shutdown', array( &$this, 'renew' ));
		}
		
		if(file_exists($this->dbfile)){
			$this->gi = new mymail_GeoIP($this->dbfile);
			if(!mymail_option('countries')){
				mymail_update_option('countries', filemtime($this->dbfile));
			}
		}
	}


	public function country($code) {
		return (isset($this->gi->GEOIP_COUNTRY_CODE_TO_NUMBER[strtoupper($code)])) ? $this->gi->GEOIP_COUNTRY_NAMES[$this->gi->GEOIP_COUNTRY_CODE_TO_NUMBER[strtoupper($code)]] : $code;
	}


	public function get_countries() {

		$rawcountries = $this->gi->GEOIP_COUNTRY_NAMES;
		$countries = array();
		foreach ($rawcountries as $key => $country) {
			if (!$key) continue;
			$countries[$this->gi->GEOIP_COUNTRY_CODES[$key]] = $country;
		}

		return $countries;
	}


	public function get($ip, $part = NULL) {

		//append two semicollons for ipv4 addresses
		if(strlen($ip) <= 15) $ip = '::'.$ip;
		
		//prevent some errors
		$error = ini_get("error_reporting");
		error_reporting(E_ERROR);
		
		if (!is_null($part)) {
			if (method_exists( $this->gi, 'geoip_country_'.$part.'_by_addr_v6')) {
				return call_user_func(array($this->gi, 'geoip_country_'.$part.'_by_addr_v6'), $ip);
			}else {
				return false;
			}
		}
		$return = (object) array(
			'id' => call_user_func(array($this->gi, 'geoip_country_ip_by_addr_v6'), $ip),
			'code' => call_user_func(array($this->gi, 'geoip_country_code_by_addr_v6'), $ip),
			'country' => call_user_func(array($this->gi, 'geoip_country_name_by_addr_v6'), $ip),
		);
		
		error_reporting($error);
		
		return $return;
	}


	public function renew($force = false) {
	
		if(time()-mymail_option('countries') > 3600*$this->renewinhours || $force || $this->renew){

			global $wp_filesystem;
			
			mymail_require_filesystem();
			
			@set_time_limit(120);
	
			mymail_update_option('countries', time());
			
			$zip = wp_remote_get( $this->zip, array('timeout' => 120, 'sslverify' => false) );
			
			if ( is_wp_error( $zip ) || $zip['response']['code'] != 200 ) {
				if (file_exists($this->dbfile) ) {
					@touch($this->dbfile);
					mymail_update_option('countries', filemtime($this->dbfile));
					return $this->dbfile;
				}
				return $zip;
			}
			
			if( $zip['headers']['content-type'] != 'application/zip' ) return new WP_Error('wrong_filetype', 'wrong file type');
			
			if (!is_dir( dirname($this->dbfile) )) wp_mkdir_p( dirname($this->dbfile) ) ;
	
			if ( !$wp_filesystem->put_contents( $this->zipfile, $zip['body'], FS_CHMOD_FILE) ) {
				return new WP_Error('write_file', 'error saving file');
			}
			
			if ( !unzip_file($this->zipfile, dirname($this->dbfile)) ) {
				return new WP_Error('unzip_file', 'error unzipping file');
			}else if ( !$wp_filesystem->delete( $this->zipfile )) {
				return new WP_Error('delete_file', 'error deleting old file');
			}
			
			$files = list_files(dirname($this->dbfile), 2);
			
			foreach($files as $file){
				if(basename($file) == 'GeoIPv6.dat' && $file != dirname($this->dbfile) . '/GeoIPv6.dat'){
					if ( !$wp_filesystem->move( $file, $this->dbfile, FS_CHMOD_FILE) ) {
						return new WP_Error('write_file', 'error saving file');
					}
					$wp_filesystem->delete( dirname($file) );
					break;
				}
			}
			
			if (!file_exists($this->dbfile) ) {
				mymail_update_option( 'trackcountries' , false );
				return new WP_Error('file_missing', 'file is missing');
			}
	
			mymail_update_option('countries', filemtime($this->dbfile));
	
			$this->gi = new mymail_GeoIP($this->dbfile);
	
			return $this->dbfile;
		}

		return false;
		
	}


	public function remove() {

		mymail_update_option('countries', 0);

		global $wp_filesystem;
		mymail_require_filesystem();

		return $wp_filesystem->delete( $this->dbfile );

	}


	public function get_real_ip() {
		return mymail_get_ip();
	}


}


?>