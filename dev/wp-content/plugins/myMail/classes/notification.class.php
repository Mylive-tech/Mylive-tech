<?php if (!defined('ABSPATH')) die('not allowed');


class mymail_notification {

	private $message;
	private $template;
	private $file;
	private $to;
	private $subject;
	private $headline;
	private $preheader;
	private $attachments;
	private $replace;
	private $requeue = true;
	private $debug = false;

	private static $_instance = null;

	public static function get_instance($template, $file){
		if (!isset(self::$_instance)){
			self::$_instance = new self();
		}
		self::$_instance->reset();
		self::$_instance->template($template);
		self::$_instance->file($file);

		return self::$_instance;
	}

	private function __construct() {

		add_filter('mymail_notification_to', array( &$this, 'filter'), 1, 4);
		add_filter('mymail_notification_subject', array( &$this, 'filter'), 1, 4);
		add_filter('mymail_notification_file', array( &$this, 'filter'), 1, 4);
		add_filter('mymail_notification_headline', array( &$this, 'filter'), 1, 4);
		add_filter('mymail_notification_preheader', array( &$this, 'filter'), 1, 4);
		add_filter('mymail_notification_replace', array( &$this, 'filter'), 1, 4);
		add_filter('mymail_notification_attachments', array( &$this, 'filter'), 1, 4);

	}


	public function init() {


	}

	public function reset() {
		$this->message = NULL;
		$this->template = NULL;
		$this->file = NULL;
		$this->to = NULL;
		$this->subject = NULL;
		$this->headline = NULL;
		$this->preheader = NULL;
		$this->attachments = array();
		$this->replace = array();
		$this->requeue = true;
		$this->debug = false;
	}

	public function add($timestamp = NULL, $args = array()) {

		$now = time();

		$defaults = array(
			'subscriber_id' => NULL,
			'template' => $this->template,
		);

		if(is_null($timestamp)){
			$timestamp = $now;
		}elseif(is_array($timestamp)){
			$args = $timestamp;
			$timestamp = $now;
		}


		$args = $this->set_options($args);

		$options = wp_parse_args( $args, $defaults );

		$subscriber_id = intval($options['subscriber_id']);

		//send now
		if($timestamp <= $now){
			//sendnow
			$result = $this->send(intval($subscriber_id), $options);

			//queue it if there was a problem
			if(is_wp_error( $result )){
				if($this->requeue) $this->add($now+360, $options);
				return false;
			}

			return true;

		}else{

			unset($options['subscriber_id']);
			if(!$subscriber_id){
				$options['to'] = $this->to;
			}

			return mymail('queue')->add(array(
				'campaign_id' => 0,
				'subscriber_id' => $subscriber_id,
				'timestamp' => $timestamp,
				'priority' => 5,
				'ignore_status' => 1,
				'options' => $options,
			));
		}


	}

	public function filter($content, $template, $subscriber, $options) {

		$filter = str_replace('mymail_notification_', '', current_filter());

		switch($template.'_'.$filter){

			//new subscriber
			case 'new_subscriber_to':
				return explode(',', mymail_option('subscriber_notification_receviers'));

			case 'new_subscriber_subject':
				return __('A new user has subscribed to your newsletter!','mymail');

			case 'new_subscriber_file':
				return mymail_option('subscriber_notification_template');

			case 'new_subscriber_replace':
				return array(
					'preheader' => (($subscriber->fullname ? $subscriber->fullname.' - ' : '').$subscriber->email),
					'notification' => '',
				);






			//confirmation
			case 'confirmation_to':
				$form = mymail('form')->get($options['form']);
				return $subscriber->email;

			case 'confirmation_subject':
				$form = mymail('form')->get($options['form']);
				return $form['text']['subscription_subject'];

			case 'confirmation_file':
				$form = mymail('form')->get($options['form']);
				return isset($form['template']) ? $form['template'] : null;

			case 'confirmation_headline':
				$form = mymail('form')->get($options['form']);
				return $form['text']['subscription_headline'];

			case 'confirmation_replace':
				if(isset($options['form'])){
					$form = mymail('form')->get($options['form']);
					$form_id = $form['id'];
				}else{
					$form_id = NULL;
				}

				$link = mymail('subscribers')->get_confirm_link($subscriber->ID, $form_id);

				return wp_parse_args(array(
					'link' => '<a href="'.htmlentities($link).'">'. $form['text']['subscription_link'].'</a>',
					'linkaddress' => $link,
				), $content);

			case 'confirmation_attachments':
				$form = mymail('form')->get($options['form']);
				if(isset($form['vcard'])){
					$filename = !empty($form['vcard_filename']) ? $form['vcard_filename'] : 'vCard.vcf';
					$content[] = MYMAIL_UPLOAD_DIR.'/'.$filename;
				}
				return $content;

			//test mail
			case 'test_subject':
				return __('MyMail Test Email', 'mymail');

			default:

				return apply_filters("mymail_notification_{$template}_{$filter}", $content, $subscriber, $options);
		}

	}

	public function template($template) {
		$this->template = $template;
	}

	public function file($file) {
		$this->file = $file;
	}

	public function to($to) {
		$this->to = $to;
	}

	public function subject($subject) {
		$this->subject = $subject;
	}

	public function attachments($attachments) {
		$this->attachments = is_array($attachments) ? $attachments : array($attachments);
	}

	public function replace($replace) {
		$this->replace = is_array($replace) ? $replace : array($replace);
	}

	public function requeue($requeue) {
		$this->requeue = $requeue;
	}

	public function debug($bool = true) {
		$this->debug = (bool) $bool;
	}

	public function send($subscriber_id, $options) {

		$template = $options['template'];

		$this->apply_options($options);
		if($subscriber_id && $subscriber = mymail('subscribers')->get($subscriber_id, true)){
			$userdata = mymail('subscribers')->get_userdata($subscriber);
			$this->to = $subscriber->email;
		}else{
			$subscriber = null;
		}

		ob_start();

		if(method_exists($this, 'template_'.$template))
			call_user_func(array( $this, 'template_'.$template), $subscriber, $options);

		$output = ob_get_contents();

		ob_end_clean();

		//hook for custom templates
		ob_start();

		do_action("mymail_notification_{$template}", $subscriber, $options);

		$output2 = ob_get_contents();

		ob_end_clean();

		$this->message = !empty($output2) ? $output2 : $output;

		if(empty($this->message)) return new WP_Error('notification_error', 'no content');

		$this->to = 		apply_filters("mymail_notification_to", $this->to, $template, $subscriber, $options);
		$this->subject = 	apply_filters("mymail_notification_subject", $this->subject, $template, $subscriber, $options);
		$this->file = 		apply_filters("mymail_notification_file", $this->file, $template, $subscriber, $options);
		$this->headline = 	apply_filters("mymail_notification_headline", $this->headline, $template, $subscriber, $options);
		$this->preheader = 	apply_filters("mymail_notification_preheader", $this->preheader, $template, $subscriber, $options);

		$this->replace = 	apply_filters("mymail_notification_replace", $this->replace, $template, $subscriber, $options);

		if(!isset($this->file) || empty($this->file)) $this->file = 'notification.html';

		$mail = mymail('mail');

		$mail->to = $this->to;
		$mail->subject = $this->subject;
		$mail->reply_to = mymail_option('reply_to', false);

		$mail->attachments = apply_filters("mymail_notification_attachments", $this->attachments, $template, $subscriber, $options);

		$t = mymail('template', NULL, $this->file);
		$raw = $t->get(true, true);

		$placeholder = mymail('placeholder', $raw);

		if($subscriber){
			$mail->hash = $subscriber->hash;
			$placeholder->add($userdata);
			$placeholder->add( array(
				'emailaddress' => $subscriber->email,
				'hash' => $subscriber->hash,
			));
		}

		$placeholder->add( array(
			'subject' => $this->subject,
			'preheader' => $this->preheader,
			'headline' => $this->headline,
			'content' => $this->message,
		));

		$placeholder->add($this->replace);

		$mail->content = $placeholder->get_content();

		$placeholder->set_content($mail->subject);
		$mail->subject = $placeholder->get_content();

		$mail->prepare_content();
		$mail->add_tracking_image = false;
		$mail->embed_images = mymail_option('embed_images');
		if($this->debug) $mail->debug();

		$result = $mail->send();

		if($result && !is_wp_error( $result )){
			return true;
		}

		if(is_wp_error( $result )) return $result;

		if($mail->is_user_error()){
			return new WP_Error('user_error', $mail->last_error->getMessage());
		}

		if($mail->last_error){
			return new WP_Error('notification_error', $mail->last_error->getMessage());
		}

		return new WP_Error('notification_error', __('unknown', 'mymail'));


	}



	private function set_options($options) {
		$params = array('to', 'subject');
		foreach($params as $key){
			if(!is_null($this->{$key})){
				$options[$key] = $this->{$key};
			}
		}

		return $options;
	}

	private function apply_options($options) {
		if(is_array($options)){
			foreach($options as $key => $value){
				if(method_exists($this, $key)){
					$this->{$key}($value);
				}
			}
		}
	}



	//Templates

	private function template_basic($subscriber, $options) {

	}

	private function template_confirmation($subscriber, $options) {

		$form = mymail('form')->get($options['form']);

		echo nl2br($form['text']['subscription_text']);

	}

	private function template_welcome_mail($subscriber, $options) {

		$response = wp_remote_get( 'http://rxa.li/mymailwelcomemail' );
		if( is_wp_error( $response ) ) {
			return false;
		}
		echo $response['body'];

	}

	private function template_test($subscriber, $options) {

		global $mymail_options;
?>
		<style type="text/css">.settings td{border-top:1px solid #ccc;} pre{line-height:16px;word-wrap:break-word;word-break:break-all;white-space:pre-wrap;font-size:11px;}</style>
		<table width="100%;" cellpadding="0" cellspacing="0" class="settings">

		<?php
			$i = 0;
		foreach($mymail_options as $key => $option){

			if($option == '') continue;
			if($key && preg_match('#_pwd|_key|apikey|_secret#', $key)) $option = '******';
			if(is_bool($option)) $option = $option ? 'true' : 'false';
		?>
			<tr><td width="20%" valign="top"><b><pre><?php echo $key ?></pre></b></td><td width="5%">&nbsp;</td><td width="75%" valign=""><pre><?php echo trim(print_r($option, true)) ?></pre></td></tr>

		<?php } ?>


		</table>
<?php

	}


	private function template_new_subscriber($subscriber, $options) {

		$custom_fields = mymail()->get_custom_fields();

?>
		<table style="width:100%;table-layout:fixed">
			<tr>
			<td width="80" valign="top" align="right">
				<a href="<?php echo admin_url('edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber->ID) ?>">
					<img src="<?php echo mymail('subscribers')->get_gravatar_uri($subscriber->email, 80) ?>" width="80" height="80">
				</a>
			</td>
			<td width="24">&nbsp;</td>
			<td valign="top">
				<h4><?php printf(__('%s has joined', 'mymail'), '<a href="'.admin_url('edit.php?post_type=newsletter&page=mymail_subscribers&ID='.$subscriber->ID).'">'.(($subscriber->fullname) ? $subscriber->fullname.' - ' : '').$subscriber->email.'</a>') ?></h4>
				<?php mymail('subscribers')->output_referer($subscriber->ID) ?>
				<table style="width:100%;table-layout:fixed">

				<?php foreach($custom_fields as $id => $field) { ?>
					<tr><td height="20" style="border-top:1px solid #ccc;height:30px"><strong><?php echo $field['name'] ?>:</strong> <?php echo $subscriber->{$id} ?></td></tr>
				<?php } ?>

					<tr><td height="30" style="border-top:1px solid #ccc;height:30px"><strong><?php _e('Lists', 'mymail') ?>:</strong>
					<?php $lists = mymail('subscribers')->get_lists($subscriber->ID);
						foreach ($lists as $i => $list) { ?>
							<a href="<?php echo admin_url('edit.php?post_type=newsletter&page=mymail_lists&ID='.$list->ID) ?>"><?php echo $list->name ?></a><?php if($i+1 < count($list)) echo ', ' ?>
					<?php } ?>
					</td></tr>

				</table>


		<?php if(($loc = mymail_ip2City()) != 'unknown') : ?>

			<table style="width:100%;table-layout:fixed">
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>
						<img src="https://maps.googleapis.com/maps/api/staticmap?markers=<?php echo $loc->latitude ?>,<?php echo $loc->longitude ?>&zoom=4&size=276x200&visual_refresh=true&sensor=false" width="276" heigth="200">
					</td>
					<td>
						<img src="https://maps.googleapis.com/maps/api/staticmap?markers=<?php echo $loc->latitude ?>,<?php echo $loc->longitude ?>&zoom=8&size=276x200&visual_refresh=true&sensor=false" width="276" heigth="200">
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
			</table>

		<?php endif; ?>
			</td>
			</tr>
		</table>

<?php

	}



}

?>
