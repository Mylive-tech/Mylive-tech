<?php if(!defined('ABSPATH')) die('not allowed');


class MyMail_Signup_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'mymail_signup', // Base ID
		'(MyMail) '.	__('Newsletter Signup Form', 'mymail'), // Name
			array( 'description' => __( 'Sign Up form for the newsletter', 'mymail' ), ) // Args
		);
	}

 	public function form( $instance ) {
		// outputs the options form on admin
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Newsletter sign up', 'mymail') : $instance['title'], $instance, $this->id_base);
		$text_before = apply_filters('widget_text_before', empty($instance['text_before']) ? '' : $instance['text_before'], $instance, $this->id_base);
		$form = apply_filters('widget_form', empty($instance['form']) ? 0 : $instance['form'], $instance, $this->id_base);
		$text_after = apply_filters('widget_text_after', empty($instance['text_after']) ? '' : $instance['text_after'], $instance, $this->id_base);
		$on_homepage = empty($instance['on_homepage']) ? '' : $instance['on_homepage'];

		$forms = mymail('forms')->get_all();

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'mymail' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<label for="<?php echo $this->get_field_id( 'form' ); ?>"><?php _e( 'Form', 'mymail' ); ?>:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'form' ); ?>" name="<?php echo $this->get_field_name( 'form' ); ?>" >
		<?php foreach($forms as $id => $f){ ?>
			<option value="<?php echo $f->ID ?>"<?php if($form == $f->ID) echo " selected"?>><?php echo '#'.$f->ID.' '.$f->name ?></option>
		<?php } ?>
		</select>
		<a href="edit.php?post_type=newsletter&page=mymail_forms&new"><?php _e('add form', 'mymail'); ?></a>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'text_before' ); ?>"><?php _e( 'Text before the form', 'mymail' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'text_before' ); ?>" name="<?php echo $this->get_field_name( 'text_before' ); ?>" type="text" value="<?php echo esc_attr( $text_before ); ?>" />
		<label for="<?php echo $this->get_field_id( 'text_after' ); ?>"><?php _e( 'Text after the form', 'mymail' ); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'text_after' ); ?>" name="<?php echo $this->get_field_name( 'text_after' ); ?>" type="text" value="<?php echo esc_attr( $text_after ); ?>" />
		</p>
		<p>
		<label><input id="<?php echo $this->get_field_id( 'on_homepage' ); ?>" name="<?php echo $this->get_field_name( 'on_homepage' ); ?>" type="checkbox" value="1" <?php checked( $on_homepage ); ?> /> <?php _e( 'Show form on newsletter homepage as well', 'mymail' ); ?></label>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['text_before'] = ( $new_instance['text_before'] );
		$instance['form'] = strip_tags( $new_instance['form'] );
		$instance['text_after'] = ( $new_instance['text_after'] );
		$instance['on_homepage'] = isset( $new_instance['on_homepage'] );

		return $instance;
	}

	public function widget( $args, $instance ) {
		global $post;
		// outputs the content of the widget
		extract( $args );

		if($post && mymail_option('homepage') == $post->ID && empty($instance['on_homepage'])) return false;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$text_before = apply_filters( 'widget_text_before', isset($instance['text_before']) ? $instance['text_before'] : false);
		$form_id = apply_filters( 'widget_form', $instance['form'] );
		$text_after = apply_filters( 'widget_text_after', isset($instance['text_after']) ? $instance['text_after'] : false);

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

			if ($text_before) echo '<div class="mymail-widget-text mymail-widget-text-before">'.$text_before.'</div>';

			mymail_form( $form_id, true, 'mymail-in-widget');

			if ($text_after) echo '<div class="mymail-widget-text mymail-widget-text-after">'.$text_after.'</div>';

		echo $after_widget;
	}

}

class MyMail_Newsletter_List_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'mymail_list_newsletter', // Base ID
			'(MyMail) '.__('Newsletter List', 'mymail'), // Name
			array( 'description' => __( 'Display the most recent newsletters', 'mymail' ), ) // Args
		);

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	public function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_newsletter', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);


		$title = apply_filters('widget_title', empty($instance['title']) ? __('Latest Newsletter', 'mymail') : $instance['title'], $instance, $this->id_base);
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
 			$number = 10;

		$r = new WP_Query( apply_filters( 'widget_newsletter_args', array( 'post_type' => 'newsletter', 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => array('finished', 'active'), 'ignore_sticky_posts' => true ) ) );
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php  while ($r->have_posts()) : $r->the_post(); ?>
		<li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_newsletter', $cache, 'widget');
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete('widget_recent_newsletter', 'widget');
	}

	public function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : __('Latest Newsletter', 'mymail');
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'mymail'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Newsletters', 'mymail'); ?>:</label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}

class MyMail_Newsletter_Subscribers_Count_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'mymail_subscribers_count', // Base ID
			'(MyMail) '.__('Number of Subscribers', 'mymail'), // Name
			array( 'description' => __( 'Display the number of your Subscribers', 'mymail' ), ) // Args
		);

		add_action( 'mymail_subscriber_change_status', array(&$this, 'flush_widget_cache') );
		add_action( 'mymail_unassign_lists', array(&$this, 'flush_widget_cache') );
		add_action( 'mymail_update_subscriber', array(&$this, 'flush_widget_cache') );
	}

	public function widget($args, $instance) {
		$cache = wp_cache_get('widget_subscribers_count', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);
?>
		<?php echo isset($before_widget) ? $before_widget : ''; ?>
		<?php echo isset($instance['prefix']) ? $instance['prefix'] : ''; ?>
		<?php echo do_shortcode( '[newsletter_subscribers formated="'.($instance['formated']).'" round="'.($instance['round']).'"]' ); ?>
		<?php echo isset($instance['postfix']) ? $instance['postfix'] : ''; ?>
		<?php echo isset($after_widget) ? $after_widget : ''; ?>
<?php

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_subscribers_count', $cache, 'widget');
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['prefix'] = ($new_instance['prefix']);
		$instance['postfix'] = ($new_instance['postfix']);
		$instance['formated'] = (bool) $new_instance['formated'];
		$instance['round'] = (int) $new_instance['round'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete('widget_subscribers_count', 'widget');
	}

	public function form( $instance ) {
		$prefix = isset($instance['prefix']) ? $instance['prefix'] : '';
		$postfix = isset($instance['postfix']) ? $instance['postfix'] : __('Subscribers', 'mymail');
		$formated = isset($instance['formated']) ? !!$instance['formated'] : true;
		$round = isset($instance['round']) ? absint($instance['round']) : 1;
?>
		<p><label for="<?php echo $this->get_field_id('prefix'); ?>"><?php _e( 'Prefix', 'mymail'); ?>:</label>
		<input id="<?php echo $this->get_field_id('prefix'); ?>" name="<?php echo $this->get_field_name('prefix'); ?>" type="text" value="<?php echo esc_attr($prefix); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('postfix'); ?>"><?php _e( 'Postfix', 'mymail'); ?>:</label>
		<input id="<?php echo $this->get_field_id('postfix'); ?>" name="<?php echo $this->get_field_name('postfix'); ?>" type="text" value="<?php echo esc_attr($postfix); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Round up to the next', 'mymail'); ?></label>
		<select name="<?php echo $this->get_field_name('round'); ?>" >
			<option value="1" <?php selected( $round, 1 ); ?>><?php _e('do not round', 'mymail' ); ?></option>
			<option value="10" <?php selected( $round, 10 ); ?>><?php echo number_format(10) ?></option>
			<option value="100" <?php selected( $round, 100 ); ?>><?php echo number_format(100) ?></option>
			<option value="1000" <?php selected( $round, 1000 ); ?>><?php echo number_format(1000) ?></option>
			<option value="10000" <?php selected( $round, 10000 ); ?>><?php echo number_format(10000) ?></option>
		</select></p>
		<p><label for="<?php echo $this->get_field_id('formated'); ?>"><input id="<?php echo $this->get_field_id('formated'); ?>" name="<?php echo $this->get_field_name('formated'); ?>" type="checkbox" value="1" <?php checked( $formated ); ?> /><?php _e( 'format number', 'mymail'); ?></label>
		</p>
		<?php if(!empty($instance)) : ?>
		<p><strong><?php _e('Preview', 'mymail' ); ?></strong></p>
		<p class="description"><?php $this->widget(array(), $instance) ?></p>
		<?php endif; ?>
<?php
	}
}
