<?php if (!defined('ABSPATH')) die('not allowed');

use \InlineStyle\InlineStyle;


class mymail_mail {

	public $embed_images = true;
	public $headers = array();
	public $content = '';
	public $plaintext = '';
	public $subject = '';
	public $from;
	public $from_name;
	public $to;
	public $bcc;
	public $hash = '';
	public $reply_to;
	public $deliverymethod;
	public $dkim;
	public $bouncemail;
	public $baselink;
	public $add_tracking_image = true;
	public $errors = array();
	public $sent = false;
	public $pre_send = false;

	public $mailer;

	public $attachments = array();

	public $send_limit;
	public $sent_within_period = 0;
	public $sentlimitreached = false;

	private $campaignID = NULL;
	private $subscriberID = NULL;

	public $text = '';

	public $last_error = '';

	private $hostname = '';

	private static $_instance = null;

	public static function get_instance(){
		if (!isset(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct() {

		$this->deliverymethod = mymail_option('deliverymethod', 'simple');

		$this->dkim = mymail_option('dkim');

		require_once MYMAIL_DIR . 'classes/mail.helper.class.php';
		$this->mailer = new mymail_mail_helper( true );

		if ($this->deliverymethod == 'smtp') {

			$this->mailer->IsSMTP();

			$this->mailer->Host = mymail_option('smtp_host');
			$this->mailer->Port = mymail_option('smtp_port', 25);
			$this->mailer->Timeout = mymail_option('smtp_timeout', 10);
			$this->mailer->SMTPAuth = mymail_option('smtp_auth', false);
			if ( $this->mailer->SMTPAuth ) {
				$this->mailer->Username = mymail_option('smtp_user');
				$this->mailer->Password = mymail_option('smtp_pwd');
			}
			$this->mailer->SMTPSecure = mymail_option('smtp_secure', '');
			$this->mailer->SMTPKeepAlive = true;

			add_action('mymail_presend', array( &$this, 'pre_send'), 1, 10);
			add_action('mymail_dosend', array( &$this, 'do_send'), 1, 10);


		}else if ($this->deliverymethod == 'gmail') {

			$this->mailer->IsSMTP();

			$this->mailer->Host = 'smtp.googlemail.com';
			$this->mailer->Port = 587;
			$this->mailer->SMTPAuth = true;

			$this->mailer->Username = mymail_option('gmail_user');
			$this->mailer->Password = mymail_option('gmail_pwd');

			$this->mailer->SMTPSecure = 'tls';
			$this->mailer->SMTPKeepAlive = true;

			add_action('mymail_presend', array( &$this, 'pre_send'), 1, 10);
			add_action('mymail_dosend', array( &$this, 'do_send'), 1, 10);

		}else if ($this->deliverymethod == 'simple') {

			$method = mymail_option('simplemethod', 'sendmail');

			if($method == 'sendmail'){
				$this->mailer->Sendmail = mymail_option('sendmail_path');
				if(empty($this->mailer->Sendmail)) $this->mailer->Sendmail = '/usr/sbin/sendmail';
				$this->mailer->IsSendmail();
			}else if($method == 'qmail'){
				$this->mailer->IsQmail();
			}else{
				$this->mailer->IsMail();
			}

			add_action('mymail_presend', array( &$this, 'pre_send'), 1, 10);
			add_action('mymail_dosend', array( &$this, 'do_send'), 1, 10);

		}

		do_action('mymail_initsend', $this);


		if ($this->dkim) {
			$this->mailer->DKIM_selector = mymail_option('dkim_selector');
			$this->mailer->DKIM_domain = mymail_option('dkim_domain');

			$folder = MYMAIL_UPLOAD_DIR.'/dkim';

			$this->mailer->DKIM_private = $folder.'/'.mymail_option('dkim_private_hash').'.pem';
			$this->mailer->DKIM_passphrase = mymail_option('dkim_passphrase');
			$this->mailer->DKIM_identity = mymail_option('dkim_identity');
		}

		$this->from = mymail_option('from');
		$this->from_name = mymail_option('from_name');

		$this->send_limit = mymail_option('send_limit');

		$this->subscriber_errors = apply_filters('mymail_subscriber_errors', array(
			'SMTP Error: The following recipients failed',
			'The following From address failed',
			'Invalid address:',
			'SMTP Error: Data not accepted',
		));

		if(!get_transient('_mymail_send_period_timeout')){
			set_transient('_mymail_send_period_timeout', true, mymail_option('send_period')*3600);
		}else{
			$this->sent_within_period = get_transient('_mymail_send_period');

			if(!$this->sent_within_period) $this->sent_within_period = 0;

		}

		$this->sentlimitreached = $this->sent_within_period >= $this->send_limit;

		if($this->sentlimitreached){
			$msg = sprintf(__('Sent limit of %1$s reached! You have to wait %2$s before you can send more mails!', 'mymail'), '<strong>'.$this->send_limit.'</strong>', '<strong>'.human_time_diff(get_option('_transient_timeout__mymail_send_period_timeout')).'</strong>');
			mymail_notice($msg, 'error', false, 'dailylimit');

			$e = new Exception($msg, 1);
			$this->last_error = $e;
			$this->errors[] = $e;

		}else{

			mymail_remove_notice('dailylimit');

		}

		if ($this->mailer->Mailer == 'smtp') {
			$this->mailer->setAsSMTP();
		}

		$this->hostname = $this->serverHostname();


	}


	public function debug($level = 2, $output = 'mymail') {

		if ($this->mailer && $this->mailer->Mailer == 'smtp') {
			$this->mailer->SMTPDebug = $level; // 0 = off, 1 = commands, 2 = commands and data
			$this->mailer->Debugoutput = $output; // Options: "echo", "html" or "error_log";
		}
	}


	public function __destruct() {

		$this->close();
	}


	public function close() {
		if ($this->mailer && $this->mailer->Mailer == 'smtp') {
			$this->mailer->SmtpClose();
		}
	}


	public function __set($name, $value) {
		switch ($name) {
		case 'mailer':
			break;
		default:
			$this->{$name} = apply_filters("mymail_mail_set_{$name}", $value);
		}
	}


	public function __get($name) {
		if (isset($this->{$name})) {
			return $this->{$name};
		}
		return NULL;
	}

	public function set_campaign($id) {
		$this->campaignID = intval($id);
		$this->baselink = mymail()->get_base_link(intval($id));
	}

	public function set_subscriber($id) {
		$this->subscriberID = intval($id);
	}


	public function apply_raw_headers( $headers ) {
		if(empty($headers) || !$this->mailer) return;

		$headers = is_array($headers) ? $headers : explode("\n", $headers);
		foreach($headers as $header){
			if(empty($header)) continue;
			if(preg_match('#^from: ?(.*) (<([^ ]+)>)?#i', $header, $from)){
				$this->from_name = trim($from[1], '"');
				$this->from = isset($from[3]) ? trim($from[3]) : '';

			}elseif(preg_match('#^content-type#i', $header, $from)){
				continue;
			}else{
				$parts = array_map('trim', explode(':', $header));
				$this->add_header($parts[0], $parts[1]);
			}
		}
	}


	public function prepare_content( $inline = true ) {
		if ( empty( $this->content ) )
			return false;

		//strip all unwanted stuff from the content
		$this->strip_stuff();

		//fix for Yahoo background color FIXED!!
		// if(!strpos($this->content, 'body{background-image'))
		// 	$this->content = preg_replace('/body{background-color/','body,.bodytbl{background-color', $this->content, 1);


		//adding a inline width attribute to images for a bug in Apple Mail 7 with embeded images
		if($this->embed_images){
			preg_match_all('#(<img.*?)(width="(\d+)")(.*?>)#', $this->content, $images);
			foreach($images[0] as $i => $image){
				$oldstyle = '';
				$styleattr = '';
				if(preg_match('#style="([^"]*)"#', $image, $style)){
					$oldstyle = $style[1];
					$styleattr = $style[0];
				}
				$imgstr = str_replace($styleattr, '', $images[1][$i].'style="width:'.$images[3][$i].'px;'.$oldstyle.'" '.$images[2][$i].$images[4][$i]);
				$this->content = str_replace($image, $imgstr, $this->content);
			}
		}


		//custom styles
		$this->content = $this->add_mymail_styles($this->content);

		//Inline CSS
		if ( $inline )
			$this->content = $this->inline_style($this->content);

		$this->content = str_replace( array('%7B', '%7D') , array( '{', '}' ), $this->content );

		return $this->content;

	}


	public function inline_style($content){

		//get all style blocks
		preg_match_all('#(<style ?[^<]+?>([^<]+)<\/style>)#', $content, $originalstyles);

		//found!
		if(!empty($originalstyles[0])){

			@error_reporting(E_ERROR | E_PARSE);
			@ini_set('display_errors', '0');

			// strip media queries
			foreach($originalstyles[2] as $i => $styleblock){
				$mediaBlocks = $this->parseMediaBlocks($styleblock);
				foreach($mediaBlocks as $mediaBlock){
					$originalstyles[2][$i] = str_replace($mediaBlocks,'',$originalstyles[2][$i]);
				}

			}

			require MYMAIL_DIR.'classes/libs/InlineStyle/autoload.php';

			$htmldoc = new InlineStyle( $content );

			$htmldoc->applyStylesheet($originalstyles[2]);

			$html = $htmldoc->getHTML();

			//convert urlencode back for links with unallowed characters (only images)
			preg_match_all("/(src|background)=[\"'](.*)[\"']/Ui", $html, $urls);
			$urls = !empty($urls[2]) ? array_unique($urls[2]) : array();
			foreach($urls as $url){
				$html = str_replace($url, rawurldecode($url), $html);
			}
			$content = $html;

		}

		return $content;

	}


	public function add_mymail_styles($content){
		//custom styles
		global $mymail_mystyles;

		if($mymail_mystyles){
			//check for existing styles
			preg_match_all('#(<style ?[^<]+?>([^<]+)<\/style>)#', $content, $originalstyles);

			if(!empty($originalstyles[0])){
				foreach($mymail_mystyles as $style){
					$block = end($originalstyles[0]);
					$content = str_replace($block, $block.'<style type="text/css">'."\n".$style."\n".'</style>', $content);
				}
			}else{
				$content = str_replace('</head>', '<style type="text/css">'."\n".$style."\n".'</style></head>', $content);
			}

		}

		return $content;
	}


	private function parseMediaBlocks($css){

		$mediaBlocks = array();

		$start = 0;
		while (($start = strpos($css, "@media", $start)) !== false) {
		// stack to manage brackets
		$s = array();

		// get the first opening bracket
		$i = strpos($css, "{", $start);

			// if $i is false, then there is probably a css syntax error
			if ($i !== false) {
			// push bracket onto stack
				array_push($s, $css[$i]);

				// move past first bracket
				$i++;

				while (!empty($s)){
				// if the character is an opening bracket, push it onto the stack, otherwise pop the stack
					if ($css[$i] == "{"){

						array_push($s, "{");

					}elseif ($css[$i] == "}"){

						array_pop($s);
					}

					$i++;
				}

				// cut the media block out of the css and store
				$mediaBlocks[] = substr($css, $start, ($i + 1) - $start);

				// set the new $start to the end of the block
				$start = $i;
			}

		}

		return $mediaBlocks;
	}


	public function strip_stuff( ) {

		if ( empty( $this->content ) )
			return false;

		//with new template language

		$this->content = preg_replace( '#<(modules?|buttons|multi|single)([^>]*)>#', '', $this->content );
		$this->content = preg_replace( '#<\/(modules?|buttons|multi|single)>#', '', $this->content );

		//remove comments
		$this->content = preg_replace( '#<!-- (.*) -->\s*#', "", $this->content );

		return $this->content;

	}


	public function add_header( $key, $value ) {
		$this->headers[$key] = (string) $value;
	}


	public function set_headers( $header = array() ) {

		foreach($header as $key => $value){
			$this->add_header ($key, $value);
		}

		$this->mailer->clearCustomHeaders();

		foreach($this->headers as $key => $value){
			$this->mailer->addCustomHeader($key.':'.$value);
		}

	}



	/*----------------------------------------------------------------------*/
	/* Mail functions
	/*----------------------------------------------------------------------*/

	public function send_notification( $content, $headline = NULL, $replace = array(), $force = false, $file = 'notification.html') {

		if (is_null($headline)) $headline = $this->subject;

		$template = mymail_option('default_template');

		if ($template) {
			$template = mymail('template', $template, $file);
			$this->content = $template->get(true, true);
		}else {
			$this->content = $headline.'<br>'.$content;
		}

		$placeholder = mymail('placeholder', $this->content);

		$placeholder->add( array(
			'subject' => $this->subject,
			'preheader' => $headline,
			'headline' => $headline,
			'content' => $content,
		));

		$placeholder->add($replace);

		$this->content = $placeholder->get_content();

		$placeholder->set_content($this->subject);
		$this->subject = $placeholder->get_content();

		$this->prepare_content();
		$this->add_tracking_image = false;
		$this->embed_images = mymail_option('embed_images');

		$success = $this->send( $force );

		$this->close();
		return $success;

	}


	public function send( ) {

		$this->sent = false;

		if($this->sentlimitreached){
			return false;
		}

		//add some linebreaks to prevent "auto linebreaks" in UTF 8
		$this->content = str_replace('</tr>', "</tr>\n", $this->content);

		do_action('mymail_presend', $this);
		if(!$this->pre_send) return false;
		do_action('mymail_dosend', $this);

		if($this->sent){

			$this->sent_within_period++;
			$this->sentlimitreached = $this->sent_within_period >= $this->send_limit;
			set_transient( '_mymail_send_period', $this->sent_within_period);

		}

		return $this->sent;

	}


	public function do_send() {

		try {

			$this->sent = $this->mailer->Send();

		} catch ( _mymail_phpmailerException $e ) {

			$this->last_error = $e;
			$this->errors[] = $e;
			$this->sent = false;

		} catch ( mailerException $e ) {

			$this->last_error = $e;
			$this->errors[] = $e;
			$this->sent = false;

		} catch ( Exception $e ) {

 			$this->last_error = $e;
 			$this->errors[] = $e;
			$this->sent = false;

		}

	}


	public function reset() {

		$this->mailer->clearAllRecipients();
		$this->mailer->clearAttachments();
		$this->mailer->clearCustomHeaders();
		$this->mailer->clearReplyTos();

	}


	public function pre_send() {

		try {

			$this->last_error = NULL;

			// Empty out the values that may be set
			$this->reset();

			if(!is_array($this->to)) $this->to = array($this->to);

			foreach($this->to as $address){
				$this->mailer->AddAddress($address);
			}

			if($this->bcc){
				if(!is_array($this->bcc)) $this->bcc = array($this->bcc);
				foreach($this->bcc as $address){
					$this->mailer->addBCC($address);
				}
			}

			$this->mailer->Subject = htmlspecialchars_decode($this->subject);
			$this->mailer->SetFrom( $this->from, htmlspecialchars_decode($this->from_name), false);


			$this->mailer->IsHTML(true);

			if ( $this->embed_images ) {
				$this->content = $this->make_img_relative( $this->content );
				$this->mailer->MsgHTML( $this->content, trailingslashit( dirname(MYMAIL_UPLOAD_DIR) ) );
			}else{
				$this->mailer->Body = $this->mailer->normalizeBreaks( $this->content );
			}

			$this->mailer->AltBody = $this->mailer->normalizeBreaks( !empty($this->plaintext) ? $this->plaintext : mymail()->plain_text( $this->content ) );

			($this->bouncemail)
				? $this->mailer->ReturnPath = $this->mailer->Sender = $this->bouncemail
				: $this->mailer->ReturnPath = $this->mailer->Sender = $this->from;

			($this->reply_to)
				? $this->mailer->AddReplyTo($this->reply_to)
				: $this->mailer->AddReplyTo($this->from);


			//add the tracking image at the bottom
			if ($this->add_tracking_image){

				if(mymail('helper')->using_permalinks()){

					$tracking_url = trailingslashit($this->baselink) . $this->hash . '/';

				}else{

					$tracking_url = add_query_arg( array('k' => $this->hash), $this->baselink );

				}

				$this->mailer->Body = str_replace( '</body>', '<img src="' . $tracking_url . '" alt="" width="1" height="1"></body>', $this->mailer->Body );

			}

			$this->mailer->MessageID = sprintf('<%s@%s>',
				uniqid().'-'.$this->hash.'-'.$this->campaignID.'-'.mymail_option('ID'),
				$this->hostname);

			$this->add_header('X-Message-ID', $this->mailer->MessageID);

			$this->set_headers();

			if(is_array($this->attachments)){
				foreach($this->attachments as $name => $attachment){
					if(file_exists($attachment)) $this->mailer->AddAttachment($attachment, !is_int($name) ? $name : '');
				}

			}

			$this->pre_send = true;

		} catch ( _mymail_phpmailerException $e ) {

			$this->last_error = $e;
			$this->errors[] = $e;
			$this->pre_send = false;

		} catch ( mailerException $e ) {

			$this->last_error = $e;
			$this->errors[] = $e;
			$this->pre_send = false;

		} catch ( Exception $e ) {

 			$this->last_error = $e;
 			$this->errors[] = $e;
			$this->sent = false;

		}


	}


	public function is_user_error($error = NULL) {

		if(is_null($error)) $error = $this->last_error;
		if(empty($error)) return false;

		$errormsg = $error->getMessage();

		//check for subscriber error
		foreach($this->subscriber_errors as $subscriber_error){
			if(stripos($errormsg, $subscriber_error) !== false){
				return true;
			}
		}

		return false;

	}


	public function set_error($errors) {
		if(!is_array($errors)) $errors = array($errors);

		foreach($errors as $error){
			if(!is_array($error)) $error = array($error);
			foreach ($error as $e) {
				$this->last_error = new Exception($e, 1);
				$this->errors[] = $this->last_error;
			}
		}
	}


	public function get_errors($format = '') {

		$messages = array();
		if(!empty($this->errors)){

			foreach($this->errors as $e){
				$m = $e->getMessage();
				if(!empty($m)) $messages[] = $e->getMessage();
			}

		}

		switch($format){
			case 'ul':
			case 'ol':
				$html = '<'.$format.' class="mymail-mail-error">';
				foreach($messages as $msg){
					$html .= '<li>'.$msg.'</li>';
				}
				$html .= '</'.$format.'>';
				$return = $html;
				break;
			case 'array':
				$return = $messages;
				break;
			case 'object':
				$return = (object) $messages;
				break;
			case 'string':
				$return = $messages[0];
				break;
			case 'br':
				$format = '<br>';
			default:
				$html = '<span class="mymail-mail-error">';
				foreach($messages as $msg){
					$html .= $format.$msg."\n";
				}
				$html .= '</span>';
				$return = $html;
				break;
		}
		return $return;
	}

	public function make_img_relative( $html ) {
		$html = str_replace( ' src="'.trailingslashit( dirname(MYMAIL_UPLOAD_URI) ), ' src="', $html );
		return $html;
	}


	public function plain_text( $html ) {
		return $this->mailer->html2text( $html );
	}


	private function ServerHostname() {
		if ($this->Hostname != '') {
			$result = $this->Hostname;
		} elseif ($this->ServerVar('SERVER_NAME') != '') {
			$result = $this->ServerVar('SERVER_NAME');
		} else {
			$result = 'localhost.localdomain';
		}

		return $result;
	}

	private function ServerVar($varName) {
		global $HTTP_SERVER_VARS;
		global $HTTP_ENV_VARS;

		if(!isset($_SERVER)) {
			$_SERVER = $HTTP_SERVER_VARS;
			if(!isset($_SERVER['REMOTE_ADDR'])) {
				$_SERVER = $HTTP_ENV_VARS; // must be Apache
			}
		}

		if(isset($_SERVER[$varName])) {
			return $_SERVER[$varName];
		} else {
			return '';
		}
	}

}
