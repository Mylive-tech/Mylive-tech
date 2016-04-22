<?php

class wfGrant
{
	public $select = false;
	public $update = false;
	public $insert = false;
	public $delete = false;
	public $alter = false;
	public $create = false;
	public $drop = false;

	public static function get()
	{
		static $instance;
		if ($instance === null) {
			$instance = new self;
		}
		return $instance;
	}
	
	private function __construct()
	{
		global $wpdb;
		$rows = $wpdb->get_results("SHOW GRANTS FOR current_user()", ARRAY_N);
		
		foreach ($rows as $row) {
			preg_match("/GRANT (.+) ON (.+) TO/", $row[0], $matches);
			foreach (explode(",", $matches[1]) as $permission) {
				$permission = str_replace(" ", "_", trim(strtolower($permission)));
				if ($permission === 'all_privileges') {
					foreach ($this as $key => $value) {
						$this->$key = true;
					}
					break 2;
				}
				$this->$permission = true;
			}
		}
	}
}

class wfDiagnostic
{
	protected $minVersion = array(
		'PHP' => '5.2.4',
		'cURL' => '1.0',
	);

	protected $description = array(
		'Filesystem' => array(
			'isTmpReadable' => 'Checking if web server can read from <code>~/plugins/wordfence/tmp</code>',
			'isTmpWritable' => 'Checking if web server can write to <code>~/plugins/wordfence/tmp</code>',
			'testWfCache'   => 'Checking if web server can write to <code>~/wp-content/wfcache</code>',
		),
		'MySQL' => array(
			'userCanDelete' => 'Checking if MySQL user has <code>DELETE</code> privilege',
			'userCanInsert' => 'Checking if MySQL user has <code>INSERT</code> privilege',
			'userCanSelect' => 'Checking if MySQL user has <code>SELECT</code> privilege',
			'userCanCreate' => 'Checking if MySQL user has <code>CREATE TABLE</code> privilege',
			'userCanAlter'  => 'Checking if MySQL user has <code>ALTER TABLE</code> privilege',
			'userCanDrop'   => 'Checking if MySQL user has <code>DROP</code> privilege',
			'userCanTruncate'   => 'Checking if MySQL user has <code>TRUNCATE</code> privilege',
		),
		'PHP' => array(
			'phpVersion' => 'PHP version >= PHP 5.2.4<br><em> (<a href="https://wordpress.org/about/requirements/" target="_blank">Minimum version required by WordPress</a>)</em>',
			'hasOpenSSL' => 'Checking for OpenSSL support',
			'hasCurl'    => 'Checking for cURL support',
		),
		'Connectivity' => array(
			'connectToServer1' => 'Connecting to Wordfence servers (http)',
			'connectToServer2' => 'Connecting to Wordfence servers (https)',
		),
//		'Configuration' => array(
//			'howGetIPs' => 'How does get IPs',
//		),
	);

	protected $results = array();

	public function __construct()
	{
		foreach ($this->description as $title => $tests) {
			$this->results[$title] = array();
			foreach ($tests as $name => $description) {
				$result = $this->$name();

				if (is_bool($result)) {
					$result = array(
						'test'    => $result,
						'message' => $result ? 'OK' : 'FAIL',
					);
				}

				$result['label'] = $description;

				$this->results[$title][] = $result;
			}
		}
	}

	public function getResults()
	{
		return $this->results;
	}

	public function isTmpReadable() {
		return is_readable(WORDFENCE_PATH . 'tmp');
	}

	public function isTmpWritable() {
		return is_writable(WORDFENCE_PATH . 'tmp');
	}

	public function userCanInsert() {
		return wfGrant::get()->insert;
	}

	public function testWfCache() {
		$result = wfCache::cacheDirectoryTest();
		return array(
			'test' => $result === false,
			'message' => is_string($result) ? $result : 'OK'
		);
	}

	public function userCanDelete() {
		return wfGrant::get()->delete;
	}

	public function userCanSelect() {
		return wfGrant::get()->select;
	}

	public function userCanCreate() {
		return wfGrant::get()->create;
	}

	public function userCanDrop() {
		return wfGrant::get()->drop;
	}

	public function userCanTruncate() {
		return wfGrant::get()->drop && wfGrant::get()->delete;
	}

	public function userCanAlter() {
		return wfGrant::get()->alter;
	}

	public function phpVersion()
	{
		return array(
			'test' => version_compare(phpversion(), $this->minVersion['PHP'], '>='),
			'message'  => phpversion(),
		);
	}

	public function hasOpenSSL() {
		return is_callable('openssl_open');
	}

	public function hasCurl() {
		if (!is_callable('curl_version')) {
			return false;
		}
		$version = curl_version();
		return array(
			'test' => version_compare($version['version'], $this->minVersion['cURL'], '>='),
			'message'  => $version['version'],
		);
	}

	public function connectToServer1() {
		return $this->_connectToServer('http');
	}

	public function connectToServer2() {
		return $this->_connectToServer('https');
	}

	public function _connectToServer($protocol) {
		$cronURL = admin_url('admin-ajax.php');
		$cronURL = preg_replace('/^(https?:\/\/)/i', '://noc1.wordfence.com/scanptest/', $cronURL);
		$cronURL .= '?action=wordfence_doScan&isFork=0&cronKey=47e9d1fa6a675b5999999333';
		$cronURL = $protocol . $cronURL;
		$result = wp_remote_post($cronURL, array(
			'timeout' => 10, //Must be less than max execution time or more than 2 HTTP children will be occupied by scan
			'blocking' => true, //Non-blocking seems to block anyway, so we use blocking
			// This causes cURL to throw errors in some versions since WordPress uses its own certificate bundle ('CA certificate set, but certificate verification is disabled')
			// 'sslverify' => false,
			'headers' => array()
			));
		if( (! is_wp_error($result)) && $result['response']['code'] == 200 && strpos($result['body'], "scanptestok") !== false){
			return true;
		}

		ob_start();
		if(is_wp_error($result)){
			echo "wp_remote_post() test to noc1.wordfence.com failed! Response was: " . $result->get_error_message() . "<br />\n";
		} else {
			echo "wp_remote_post() test to noc1.wordfence.com failed! Response was: " . $result['response']['code'] . " " . $result['response']['message'] . "<br />\n";
			echo "This likely means that your hosting provider is blocking requests to noc1.wordfence.com or has set up a proxy that is not behaving itself.<br />\n";
			echo "This additional info may help you diagnose the issue. The response headers we received were:<br />\n";
			foreach($result['headers'] as $key => $value){
				echo "$key => $value<br />\n";
			}
		}

		return array(
			'test' => false,
			'message' => ob_get_clean()
		);
	}

	public function howGetIPs()
	{
		$howGet = wfConfig::get('howGetIPs', false);
		if ($howGet) {
			if (empty($_SERVER[$howGet])) {
				return array(
					'test' => false,
					'message' => 'We cannot read $_SERVER[' . $howGet . ']',
				);
			}
			return array(
				'test' => true,
				'message' => $howGet,
			);
		}
		foreach (array('HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR') as $test) {
			if (!empty($_SERVER[$test])) {
				return array(
					'test' => false,
					'message' => 'Should be: ' . $test
				);
			}
		}
		return array(
			'test' => true,
			'message' => 'REMOTE_ADDR',
		);
	}
}

