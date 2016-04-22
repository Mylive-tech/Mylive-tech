<?php

class Ip2City {

	//maxmind doesn't provide a zip version so I've uploaded it to bitbucket (updated weekly)
	public $zip = 'http://rxa.li/geoipcity';
	public $renewinhours = 168; //(24*7)
	private $zipfile;
	private $dbfile;
	private $gi;
	private $renew = false;
	
	public function __construct() {
	
		require_once MYMAIL_DIR . 'classes/libs/geoipcity.inc.php';
		
		$this->zipfile = MYMAIL_UPLOAD_DIR.'/GeoIPCity.dat.zip';
		$this->dbfile = MYMAIL_UPLOAD_DIR.'/GeoIPCity.dat';
		
		if(mymail_option('cities_db') && mymail_option('cities_db') != $this->dbfile){
			$this->dbfile = mymail_option('cities_db');
		}else if (!file_exists($this->dbfile) || !mymail_option('cities') ) {
			add_action('shutdown', array( &$this, 'renew' ));
		}
		
		if(file_exists($this->dbfile)){
			$this->gi = new mymail_CityIP($this->dbfile);
			if(!mymail_option('cities')){
				mymail_update_option('cities', filemtime($this->dbfile));
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
	
	
		//prevent some errors
		$error = ini_get("error_reporting");
		error_reporting(E_ERROR);
		$record = $this->gi->geoip_record_by_addr($ip);
		error_reporting($error);
		
		if (is_null($part)) {
			if(isset($record->city)) $record->city = utf8_encode(trim($record->city));
			return $record;
		}else{
			return isset($record->{$part}) ? utf8_encode($record->{$part}) : false;
		}
		
	}


	public function renew($force = false) {

		if(time()-mymail_option('cities') > 3600*$this->renewinhours || $force || $this->renew){
		
			global $wp_filesystem;
			
			mymail_require_filesystem();
			
			@set_time_limit(120);
	
			mymail_update_option('cities', time());
			
			$zip = wp_remote_get( $this->zip, array('timeout' => 120, 'sslverify' => false) );
	
			if ( is_wp_error( $zip ) || $zip['response']['code'] != 200 ) {
				if (file_exists($this->dbfile) ) {
					@touch($this->dbfile);
					mymail_update_option('cities', filemtime($this->dbfile));
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
				if(basename($file) == 'GeoIPCity.dat' && $file != dirname($this->dbfile) . '/GeoIPCity.dat'){
					if ( !$wp_filesystem->move( $file, $this->dbfile, FS_CHMOD_FILE) ) {
						return new WP_Error('write_file', 'error saving file');
					}
					$wp_filesystem->delete( dirname($file) );
					break;
				}
			}
			
			if (!file_exists($this->dbfile) ) {
				mymail_update_option( 'trackcities' , false );
				return new WP_Error('file_missing', 'file is missing');
			}
	
			mymail_update_option('cities', filemtime($this->dbfile));
	
			$this->gi = new mymail_CityIP($this->dbfile);
	
			return $this->dbfile;
		}
		
		return false;

	}


	public function remove() {

		mymail_update_option('cities', 0);

		global $wp_filesystem;
		mymail_require_filesystem();

		return $wp_filesystem->delete( $this->dbfile );

	}
	

	public function get_real_ip() {
		return mymail_get_ip();
	}


}


?>