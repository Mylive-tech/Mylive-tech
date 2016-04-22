<?php if(!defined('ABSPATH')) die('not allowed');

class mymail_placeholder {

	private $content;
	private $placeholder = array();
	private $rounds = 2;
	private $campaignID = NULL;
	private $subscriberID = NULL;
	private $replace_custom = true;
	private $social_services;


	public function __construct($content = '', $basic = NULL) {
		$this->content = $content;

		//hardcoded tags
		if(!is_array($basic)){
			$timestamp = current_time('timestamp');
			$basic = array(
				'year' => date('Y', $timestamp),
				'month' => date('m', $timestamp),
				'day' => date('d', $timestamp),
				'hour' => date('H', $timestamp),
				'minute' => date('m', $timestamp),
			);
		}

		$this->add($basic);
		$this->add(mymail_option('custom_tags', array()));
		$this->add(mymail_option('tags', array()));

		//mymail_add_tag('url', array( $this, 'urlencode_tags'));

	}


	public function __destruct() {
	}

	public function urlencode_tags($option, $fallback, $campaignID = NULL, $subscriberID = NULL){

		if($subscriber = mymail('subscribers')->get($subscriberID) && isset($subscriber->{$option})){
			$return = rawurlencode($subscriber->{$option});
		}else{
			$return = '{'.$option;
			if($fallback) $return .= '|'.$fallback;
			$return .= '}';
		}

		return $return;
	}

	public function set_content($content = '') {
		$this->content = $content;
	}

	public function set_campaign($id) {
		$this->campaignID = $id;
	}

	public function set_subscriber($id) {
		$this->subscriberID = $id;
	}

	public function replace_custom_tags($bool = true) {
		$this->replace_custom = $bool;
	}

	public function get_content($removeunused = true, $placeholders = array(), $relative_to_absolute = false ) {
		return $this->do_placeholder($removeunused, $placeholders, $relative_to_absolute );
	}


	public function clear_placeholder( ) {
		$this->placeholder = array();
	}


	public function add( $placeholder = array(), $brackets = true ) {
		if(empty($placeholder)) return false;
		foreach($placeholder as $key => $value){
			($brackets)
				? $this->placeholder['{'.$key.'}'] = $value
				: $this->placeholder[$key] = $value;
		}
	}

	public function do_placeholder($removeunused = true, $placeholders = array(), $relative_to_absolute = false, $round = 1 ) {


		$this->add($placeholders);

		$this->replace_dynamic($relative_to_absolute);

		foreach($this->placeholder as $search => $replace){
			$this->content = str_replace( $search, $replace, $this->content );
		}

		//embeded tags ( [tag] )
/*
		if($round == 1){
			if($count = preg_match_all( '#\{([a-z0-9-_]+):\[([a-z0-9-_]+)\]\|?([^\}]+)?\}#', $this->content, $hits_fallback )){

				for ( $i = 0; $i < $count; $i++ ) {

					$search = $hits_fallback[0][$i];
					$placeholder = '{'.$hits_fallback[1][$i].'}';
					$embeded_tags = $hits_fallback[2][$i];
					$fallback = $hits_fallback[3][$i];

					$this->content = str_replace( $search, $this->placeholder[$placeholder], $this->content );

				}

			}
		}
*/

		global $mymail_mytags;

		if(!empty($mymail_mytags) && $this->replace_custom){

			krsort($mymail_mytags);

			foreach($mymail_mytags as $tag => $replacecallback){

				if($count = preg_match_all( '#\{'.preg_quote($tag).':?([^\}|]+)?\|?([^\}]+)?\}#i', $this->content, $hits_fallback )){

					for ( $i = 0; $i < $count; $i++ ) {

						$search = $hits_fallback[0][$i];
						$option = $hits_fallback[1][$i];
						$fallback = $hits_fallback[2][$i];
						$replace = call_user_func_array($replacecallback, array($option, $fallback, $this->campaignID, $this->subscriberID));

						if(!empty($replace) || is_string($replace)){
							$this->content = str_replace( $search, ''.$replace, $this->content );
						}else{
							$this->content = str_replace( $search, ''.$fallback, $this->content );
						}

					}

				}

			}
		}

		if($count = preg_match_all( '#\{([a-z0-9-_]+)\|([^\}]+)\}#i', $this->content, $hits_fallback )){

			for ( $i = 0; $i < $count; $i++ ) {

				$search = $hits_fallback[0][$i];
				$placeholder = '{'.$hits_fallback[1][$i].'}';
				$fallback = $hits_fallback[2][$i];
				//use placeholder

				if ( !empty( $this->placeholder[$placeholder] ) ) {
					$this->content = str_replace( $search, $this->placeholder[$placeholder], $this->content );

				//use fallback
				} else if($removeunused && $round < $this->rounds){

					$this->content = str_replace( $search, $fallback, $this->content );

				}
			}

		}

		//do it twice to get tags inside tags ;)
		if($round < $this->rounds)	return $this->do_placeholder($removeunused, $placeholders, $relative_to_absolute, ++$round );

		$this->replace_embeds();

		//remove unused placeholders
		if($removeunused){

			preg_match_all('#(<style(>|[^<]+?>)([^<]+)<\/style>)#', $this->content, $styles);

			if($hasstyle = !empty($styles[0])){
				$this->content = str_replace( $styles[0], '%%%STYLEBLOCK%%%', $this->content );
			}

			$this->content = preg_replace('#\{([a-z0-9-_,;:| \[\]]+)\}#i','', $this->content);

			if($hasstyle){
				$search = explode('|', str_repeat('/%%%STYLEBLOCK%%%/|', count($styles[0])-1).'/%%%STYLEBLOCK%%%/');
				$this->content = preg_replace($search, $styles[0], $this->content, 1);
			}
		}

		return $this->content;

	}


	public function share_service($url, $title = '' ) {

		$placeholders = array();

		$social = implode('|', apply_filters('mymail_share_services', array('twitter', 'facebook', 'google', 'linkedin')));

		if($count = preg_match_all('#\{(share:('.$social.') ?([^}]+)?)\}#i', $this->content, $hits)){

			for($i = 0; $i < $count; $i++){

				$service = $hits[2][$i];

				$url = !empty($hits[3][$i]) ? $hits[3][$i] : $url;

				$placeholders[$hits[1][$i]] = $this->get_social_service($service, $url, $title);

			}

		}

		$this->add($placeholders);

	}


	public function get_social_service( $service, $url, $title = '', $fallback = '' ) {

		//bit caching
		if(!$this->social_services) $this->social_services = mymail('helper')->social_services();

		if(!isset($this->social_services[$service])) return $fallback;

		$_url = str_replace(array('%title', '%url'), array(
			rawurlencode($title),
			rawurlencode($url)
		), $this->social_services[$service]['url']);

		$content = apply_filters('mymail_share_button_'.$service, '<img alt="'.esc_attr( sprintf(__('Share this on %s', 'mymail'), $this->social_services[$service]['name']) ).'" src="'.MYMAIL_URI . 'assets/img/share/share_'.$service.'.png" style="display:inline;display:inline !important;" />');

		return '<a href="'.$_url.'" class="social">'.$content.'</a>'."\n";


	}


	public function replace_dynamic( $relative_to_absolute = false ) {

		//$placeholders = array();

		$pts = array_keys(get_post_types( array( 'public' => true ), 'objects' ));
		$pts = array_diff($pts, array( 'newsletter', 'attachment'));
		$pts = implode('|',$pts);

		$timeformat = get_option('time_format');
		$dateformat = get_option('date_format');

		$strip_shortcodes = apply_filters( 'mymail_strip_shortcodes', true );

		//placeholder images
		$ajaxurl = admin_url('admin-ajax.php');
		if($count = preg_match_all( '#<img(.*)src="'.$ajaxurl.'\?action=mymail_image_placeholder([^"]+)"(.*)>#', $this->content, $hits )){

			for ( $i = 0; $i < $count; $i++ ) {

				$search = $hits[0][$i];
				$pre_stuff = preg_replace('# height="(\d+)"#i', '', $hits[1][$i]);
				$post_stuff = preg_replace('# height="(\d+)"#i', '', $hits[3][$i]);
				$querystring = str_replace('&amp;', '&', $hits[2][$i]);

				parse_str($querystring, $query);

				if(isset($query['tag'])){

					$replace_to = mymail_cache_get( 'mymail_'.$querystring );

					if ( false === $replace_to ) {
						$parts = explode(':', trim($query['tag']));
						$width = isset($query['w']) ? intval($query['w']) : NULL;
						//$height = isset($query['h']) ? intval($query['h']) : NULL;
						$factor = isset($query['f']) ? intval($query['f']) : 1;

						$post_type = str_replace('_image', '', $parts[0]);

						$extra = explode('|', $parts[1]);
						$term_ids = explode(';', $extra[0]);
						$fallback_id = isset($extra[1]) ? intval($extra[1]) : mymail_option('fallback_image');

						$post_id = intval(array_shift($term_ids));

						if($post_id < 0){

							$post = $this->get_last_post( abs($post_id)-1, $post_type, $term_ids );

						}else if($post_id > 0){

							$post = get_post($post_id);

							if($relative_to_absolute) continue;

						}

						if(!$relative_to_absolute){

							$thumb_id = ($post->post_type == 'attachment') ? $post->ID : get_post_thumbnail_id($post->ID);

							$org_src = wp_get_attachment_image_src( $thumb_id, 'full');

							if(empty($org_src) && $fallback_id){

								$org_src = wp_get_attachment_image_src( $fallback_id, 'full');

							}

							if(!empty($org_src) && $org_src[1] && $org_src[2]){

								$img = mymail('helper')->create_image(NULL, $org_src[0], $width);
								$asp = $org_src[1]/$org_src[2];
								$height = $width/$asp;

								$replace_to = '<img '.$pre_stuff.'src="'.$img['url'].'" height="'.round($height/$factor).'"'.$post_stuff.'>';

								mymail_cache_set( 'mymail_'.$querystring, $replace_to );

							}else if(!empty($org_src[0])){

								$replace_to = '<img '.$pre_stuff.'src="'.$org_src[0].'" '.$post_stuff.'>';

								mymail_cache_set( 'mymail_'.$querystring, $replace_to );

							}
						}else{

							$replace_to = str_replace('tag='.$query['tag'], 'tag='.$post_type.'_image:'.$post->ID, $search);

						}

					}

					if($replace_to) $this->content = str_replace( $search, $replace_to, $this->content );

				}
			}
		}

		if(!$relative_to_absolute){

			//absolute posts
			if($count = preg_match_all('#\{(('.$pts.')_([^}]+):([\d]+))\}#i', $this->content, $hits)){

				for($i = 0; $i < $count; $i++){
					$post = get_post($hits[4][$i]);
					if(!$post->post_excerpt){
						if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
							$content = explode($matches[0], $post->post_content, 2);
							$post->post_excerpt = trim($content[0]);
						}
					}

					$post->post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

					if($post){

						$what = $hits[3][$i];
						if(strpos($what, 'author_') !== false){
							$author = get_user_by( 'id', $post->post_author );
						}else if(strpos($what, 'meta') !== false){
							preg_match('#meta\[(.*)\]#i', $what, $metakey);
							if(!isset($metakey[1])) continue;
							$metakey = trim($metakey[1]);
							$metavalue = get_post_meta( $post->ID, $metakey, true );
							if(is_null($metavalue)) continue;
							$what = 'meta';
						}

						switch($what){
							case 'link':
							case 'permalink':
								$replace_to = get_permalink($post->ID);
								break;
							case 'author_name':
								$replace_to = $author->data->display_name;
								break;
							case 'author_nicename':
								$replace_to = $author->data->user_nicename;
								break;
							case 'author_email':
								$replace_to = $author->data->user_email;
								break;
							case 'author_url':
								$replace_to = $author->data->user_url;
							break;
							case 'date':
							case 'date_gmt':
							case 'modified':
							case 'modified_gmt':
								$replace_to = date($dateformat, strtotime($post->{'post_'.$what}));
								break;
							case 'time':
								$what = 'date';
							case 'time_gmt':
								$what = isset($what) ? $what : 'date_gmt';
							case 'modified_time':
								$what = isset($what) ? $what : 'modified';
							case 'modified_time_gmt':
								$what = isset($what) ? $what : 'modified_gmt';
								$replace_to = date($timeformat, strtotime($post->{'post_'.$what}));
								break;
							case 'excerpt':
								$replace_to = (!empty($post->{'post_excerpt'}) ? $post->{'post_excerpt'} : wp_trim_words($post->{'post_content'}));
								$replace_to = ($strip_shortcodes) ? strip_shortcodes( $replace_to ) : do_shortcode( $replace_to );
								break;
							case 'content':
								$replace_to = wpautop($post->{'post_content'});
								$replace_to = ($strip_shortcodes) ? strip_shortcodes( $replace_to ) : do_shortcode( $replace_to );
								break;
							case 'meta':
								$replace_to = maybe_unserialize($metavalue);
								break;
							case 'category':
								$categories = array_map('get_cat_name', array_values($post->{'post_category'}));
								$replace_to = implode(', ', $categories);
								break;
							case 'twitter':
							case 'facebook':
							case 'google':
							case 'linkedin':
								$replace_to = $this->get_social_service($what, get_permalink($post->ID), get_the_title($post->ID));
								break;
							case 'image':
								$replace_to = '['.(sprintf(__('use the tag %s as url in the editbar', 'mymail'), '"'.$hits[1][$i].'"')).']';
								break;
							default:
								$replace_to = isset($post->{'post_'.$what})
									? $post->{'post_'.$what}
									: $post->{$what};
						}

					}else{
						$replace_to = '';
					}

					$this->content = str_replace( $hits[0][$i], $replace_to, $this->content );
					//$placeholders[$hits[1][$i]] = $replace_to;
				}

			}

		}

		//relative posts without options
		if($count = preg_match_all('#\{(('.$pts.')_([^}]+):-([\d]+))\}#i', $this->content, $hits)){

			for($i = 0; $i < $count; $i++){

				$offset = $hits[4][$i]-1;
				$post_type = $hits[2][$i];
				$post = $this->get_last_post( $offset, $post_type );

				if($post){

					$what = $relative_to_absolute ? '_relative_to_absolute' : $hits[3][$i];
					if(strpos($what, 'author_') !== false){
						$author = get_user_by( 'id', $post->post_author );
					}else if(strpos($what, 'meta') !== false){
						preg_match('#meta\[(.*)\]#i', $what, $metakey);
						if(!isset($metakey[1])) continue;
						$metakey = trim($metakey[1]);
						$metavalue = get_post_meta( $post->ID, $metakey, true );
						if(is_null($metavalue)) continue;
						$what = 'meta';
					}

					switch($what){
						case '_relative_to_absolute':
							$replace_to = '{'.$post_type.'_'.$hits[3][$i].':'.$post->ID.'}';
							break;
						case 'link':
						case 'permalink':
							$replace_to = get_permalink($post->ID);
							break;
						case 'author_name':
							$replace_to = $author->data->display_name;
							break;
						case 'author_nicename':
							$replace_to = $author->data->user_nicename;
							break;
						case 'author_email':
							$replace_to = $author->data->user_email;
							break;
						case 'author_url':
							$replace_to = $author->data->user_url;
							break;
						case 'date':
						case 'date_gmt':
						case 'modified':
						case 'modified_gmt':
							$replace_to = date($dateformat, strtotime($post->{'post_'.$what}));
							break;
						case 'time':
							$what = 'date';
						case 'time_gmt':
							$what = isset($what) ? $what : 'date_gmt';
						case 'modified_time':
							$what = isset($what) ? $what : 'modified';
						case 'modified_time_gmt':
							$what = isset($what) ? $what : 'modified_gmt';
							$replace_to = date($timeformat, strtotime($post->{'post_'.$what}));
							break;
						case 'excerpt':
							$replace_to = (!empty($post->{'post_excerpt'}) ? $post->{'post_excerpt'} : wp_trim_words($post->{'post_content'}));
							$replace_to = ($strip_shortcodes) ? strip_shortcodes( $replace_to ) : do_shortcode( $replace_to );
							break;
						case 'content':
							$replace_to = ($post->{'post_content'});
							$replace_to = ($strip_shortcodes) ? strip_shortcodes( $replace_to ) : do_shortcode( $replace_to );
							break;
						case 'meta':
							$replace_to = maybe_unserialize($metavalue);
							break;
						case 'category':
							$categories = array_map('get_cat_name', array_values($post->{'post_category'}));
							$replace_to = implode(', ', $categories);
							break;
						case 'twitter':
						case 'facebook':
						case 'google':
						case 'linkedin':
							$replace_to = $this->get_social_service($what, get_permalink($post->ID), get_the_title($post->ID));
							break;
						case 'image':
							$replace_to = '['.(sprintf(__('use the tag %s as url in the editbar', 'mymail'), '"'.$hits[1][$i].'"')).']';
							break;
						default:
							$replace_to = isset($post->{'post_'.$what})
								? $post->{'post_'.$what}
								: $post->{$what};
					}

				}else{
					$replace_to = '';
				}

				$this->content = str_replace( $hits[0][$i], $replace_to, $this->content );
				//$placeholders[$hits[1][$i]] = $replace_to;
			}

		}


		//relative posts with options
		if($count = preg_match_all('#\{(('.$pts.')_([^}]+):-([\d]+);([0-9;,]+))\}#i', $this->content, $hits)){

			for($i = 0; $i < $count; $i++){

				$search = $hits[0][$i];
				$offset = $hits[4][$i]-1;
				$post_type = $hits[2][$i];
				$term_ids = explode(';', trim($hits[5][$i]));
				$post = $this->get_last_post( $offset, $post_type, $term_ids  );

				if($post){

					$what = $relative_to_absolute ? '_relative_to_absolute' : $hits[3][$i];
					if(strpos($what, 'author_') !== false){
						$author = get_user_by( 'id', $post->post_author );
					}else if(strpos($what, 'meta') !== false){
						preg_match('#meta\[(.*)\]#i', $what, $metakey);
						if(!isset($metakey[1])) continue;
						$metakey = trim($metakey[1]);
						$metavalue = get_post_meta( $post->ID, $metakey, true );
						if(is_null($metavalue)) continue;
						$what = 'meta';
					}

					switch($what){
						case '_relative_to_absolute':
							$replace_to = '{'.$post_type.'_'.$hits[3][$i].':'.$post->ID.'}';
							break;
						case 'link':
						case 'permalink':
							$replace_to = get_permalink($post->ID);
							break;
						case 'author_name':
							$replace_to = $author->data->display_name;
							break;
						case 'author_nicename':
							$replace_to = $author->data->user_nicename;
							break;
						case 'author_email':
							$replace_to = $author->data->user_email;
							break;
						case 'author_url':
							$replace_to = $author->data->user_url;
							break;
						case 'date':
						case 'date_gmt':
						case 'modified':
						case 'modified_gmt':
							$replace_to = date($dateformat, strtotime($post->{'post_'.$what}));
							break;
						case 'time':
							$what = 'date';
						case 'time_gmt':
							$what = isset($what) ? $what : 'date_gmt';
						case 'modified_time':
							$what = isset($what) ? $what : 'modified';
						case 'modified_time_gmt':
							$what = isset($what) ? $what : 'modified_gmt';
							$replace_to = date($timeformat, strtotime($post->{'post_'.$what}));
							break;
						case 'excerpt':
							$replace_to = (!empty($post->{'post_excerpt'}) ? $post->{'post_excerpt'} : wp_trim_words($post->{'post_content'}));
							$replace_to = ($strip_shortcodes) ? strip_shortcodes( $replace_to ) : do_shortcode( $replace_to );
							break;
						case 'content':
							$replace_to = ($post->{'post_content'});
							$replace_to = ($strip_shortcodes) ? strip_shortcodes( $replace_to ) : do_shortcode( $replace_to );
							break;
						case 'meta':
							$replace_to = maybe_unserialize($metavalue);
							break;
						case 'category':
							$categories = array_map('get_cat_name', array_values($post->{'post_category'}));
							$replace_to = implode(', ', $categories);
							break;
						case 'twitter':
						case 'facebook':
						case 'google':
						case 'linkedin':
							$replace_to = $this->get_social_service($what, get_permalink($post->ID), get_the_title($post->ID));
							break;
						case 'image':
							$replace_to = '['.(sprintf(__('use the tag %s as url in the editbar', 'mymail'), '"'.$hits[1][$i].'"')).']';
							break;
						default:
							$replace_to = isset($post->{'post_'.$what})
								? $post->{'post_'.$what}
								: $post->{$what};
					}

				}else{
					$replace_to = '';
				}

				$this->content = str_replace( $search, $replace_to, $this->content );
				//$placeholders[$hits[1][$i]] = $replace_to;
			}

		}


		if(!$relative_to_absolute){
			if($count = preg_match_all('#\{(tweet:([^}|]+)\|?([^}]+)?)\}#i', $this->content, $hits)){

				for($i = 0; $i < $count; $i++){
					$search = $hits[0][$i];
					$tweet = $this->get_last_tweet($hits[2][$i], $hits[3][$i]);
					$this->content = str_replace( $search, $tweet, $this->content );
					//$placeholders[$hits[1][$i]] = $tweet;
				}

			}
		}

	}


	private function get_last_post( $offset = 0, $post_type = 'post', $term_ids = false ) {

		$cachekey = 'get_lp_'.md5(serialize(func_get_args()));

		$post = mymail_cache_get( $cachekey );

		if ( false !== $post ) return $post;

		$args = array(
			'numberposts' => 1,
			'post_type' => $post_type,
			'offset' => $offset,
		);

		if(is_array($term_ids)){

			$tax_query = array();

			$taxonomies = get_object_taxonomies( $post_type, 'names' );

			for($i = 0; $i < count($term_ids); $i++){
				if(empty($term_ids[$i])) continue;
				$tax_query[] = array(
					'taxonomy' => $taxonomies[$i],
					'field' => 'id',
					'terms' => explode(',', $term_ids[$i]),
				);
			}

			if(!empty($tax_query)){
				$tax_query['relation'] = 'AND';
				$args = wp_parse_args( $args, array('tax_query' => $tax_query));
			}

		}

		$post = get_posts( $args );

		if(!$post) return false;

		$post = $post[0];

		if(!$post->post_excerpt){
			if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
				$content = explode($matches[0], $post->post_content, 2);
				$post->post_excerpt = trim($content[0]);
			}
		}

		$post->post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

		$post->post_content = apply_filters( 'the_content', $post->post_content );

		mymail_cache_set( $cachekey, $post );

		return $post;
	}

	private function replace_embeds() {

	//TODO

/*
		require_once( ABSPATH . WPINC . '/class-oembed.php' );
		$oembed = _wp_oembed_get_object();

		if(preg_match_all('#<iframe.*?src="([^"]+)".*?>.*?<\/iframe>#', $this->content, $iframes)){

			foreach($iframes[0] as $i => $iframe){
				$width = NULL;
				$height = NULL;
				$src = $iframes[1][$i];
				if(preg_match('#width="([^"]+)"#', $iframe, $match)) $width = $match[1];
				if(preg_match('#height="([^"]+)"#', $iframe, $match)) $height = $match[1];
				if(preg_match('#youtube\.com/embed/([a-zA-Z0-9]+)$#',$src, $id)){
					$src = 'http://img.youtube.com/vi/'.$id[1].'/maxresdefault.jpg';
				}
				$this->content = str_replace($iframe, $width.' '.$height.' '.$src, $this->content);
			}
		}
*/
	}

	private function get_last_tweet( $username, $fallback = '' ) {

		if ( false === ( $tweet = get_transient( 'mymail_tweet_'.$username ) ) ) {

			$token = mymail_option('twitter_token');
			$token_secret = mymail_option('twitter_token_secret');
			$consumer_key = mymail_option('twitter_consumer_key');
			$consumer_secret = mymail_option('twitter_consumer_secret');

			if(!$token || !$token_secret || !$consumer_key || !$consumer_secret){

				//old method - not working since May 7th 2013
				$response = wp_remote_get('http://api.twitter.com/1/statuses/user_timeline/'.$username.'.json?exclude_replies=1&include_rts=1&count=1&include_entities=1');

			} else {

				require_once MYMAIL_DIR . 'classes/libs/twitter.class.php';

				$twitter = new twitter_api_class($token, $token_secret, $consumer_key, $consumer_secret);

				if(is_numeric($username)){
					$method = 'statuses/show/'.$username;

					$args = array();
				}else{
					$method = 'statuses/user_timeline';

					$args = array(
						'screen_name' => $username,
						'count' => 1,
						'include_rts' => false,
						'exclude_replies' => true,
						'include_entities' => true,

					);
				}

				$response = $twitter->query($method, $args);

			}

			if(is_wp_error($response)) return $fallback;
			$data = $response;

			if(isset($data->errors)) return $fallback;
			if(isset($data->error)) return $fallback;

			$tweet = (is_array($data)) ? $data[0] : $data;

			if(!isset($tweet->text)) return $fallback;

			if($tweet->entities->hashtags){
				foreach($tweet->entities->hashtags as $hashtag) {
					$tweet->text = str_replace('#'.$hashtag->text, '#<a href="https://twitter.com/search/%23'.$hashtag->text.'">'.$hashtag->text.'</a>', $tweet->text);

				}
			}
			if($tweet->entities->urls){
				foreach($tweet->entities->urls as $url) {
					$tweet->text = str_replace($url->url, '<a href="'.$url->url.'">'.$url->display_url.'</a>', $tweet->text);

				}
			}

			//$tweet->text = preg_replace('/(http|https|ftp|ftps)\:\/\/([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*))?/','<a href="\0">\2</a>', $tweet->text);
			//$tweet->text = preg_replace('/(^|\s)#(\w+)/','\1#<a href="https://twitter.com/search/%23\2">\2</a>',$tweet->text);
			$tweet->text = preg_replace('/(^|\s)@(\w+)/','\1@<a href="https://twitter.com/\2">\2</a>', $tweet->text);

			set_transient( 'mymail_tweet_'.$username , $tweet, 60*mymail_option('tweet_cache_time') );
		}

		return $tweet->text;
	}



}
?>
