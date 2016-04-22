<?php
/**
 * Widget LTE Menu
 *

 */

class Doover_Menu_Widget extends WP_Widget {

	
	/* ---------------------------------------------------------------------------
	 * Constructor
	 * --------------------------------------------------------------------------- */
	function Doover_Menu_Widget() {
		$widget_ops = array( 'classname' => 'widget_doover_menu', 'description' => __( 'Use this widget on pages to display aside menu with children or siblings of the current page', 'doover' ) );
		$this->WP_Widget( 'widget_doover_menu', __( 'LTE Menu', 'doover' ), $widget_ops );
		$this->alt_option_name = 'widget_doover_menu';
	}
	
	
	/* ---------------------------------------------------------------------------
	 * Outputs the HTML for this widget.
	 * --------------------------------------------------------------------------- */
	function widget( $args, $instance ) {

		if ( ! isset( $args['widget_id'] ) ) $args['widget_id'] = null;
		extract( $args, EXTR_SKIP );

		$title = "";
		if( $instance['use_page_title'] ){
			$title = wp_title( '', false );
		} elseif( $instance['title'] ) {
			$title = $instance['title'];
		}

		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base);

		echo $before_widget;
		
		if( $title ){
			echo $before_title;
			echo $title;
			echo $after_title;
		}

		if( $instance['use_page_sibling']==1 ){
			// sibling
			$aPost = get_post( get_the_ID() );
			$parentID = $aPost->ancestors[0];
		} else {
			// children
			$parentID = get_the_ID();
		}
		
		$aPages_attr = array(
			'title_li' => '',	
			'depth' => 1,
			'child_of' => $parentID,
			'link_after' => '<em></em>',
			'echo' => 0,
		);

		$aPages = wp_list_pages( $aPages_attr );
		
		// if there is no children OIFOWAC TO zeby tylko przy dzieciach bylo
		if( ( $instance['use_page_sibling']==2 ) && ( ! $aPages ) ){
			$aPost = get_post( get_the_ID() );
			$parentID = $aPost->ancestors[0];
			
			$aPages_attr['child_of'] = $parentID;
			$aPages = wp_list_pages( $aPages_attr );
		}
					
		if( $aPages ): ?>
			<nav class="submenu">
				<ul>
					<?php echo $aPages; ?>
				</ul>
			</nav>
		<?php endif;
		
		echo $after_widget;
	}


	/* ---------------------------------------------------------------------------
	 * Deals with the settings when they are saved by the admin.
	 * --------------------------------------------------------------------------- */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['use_page_title'] = (int) $new_instance['use_page_title'];
		$instance['use_page_sibling'] = (int) $new_instance['use_page_sibling'];
		return $instance;
	}

	
	/* ---------------------------------------------------------------------------
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 * --------------------------------------------------------------------------- */
	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$use_page_title = isset( $instance['use_page_title'] ) ? absint( $instance['use_page_title'] ) : 0;
		$use_page_sibling = isset( $instance['use_page_sibling'] ) ? absint( $instance['use_page_sibling'] ) : 0;
?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'doover' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'use_page_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_page_title' ) ); ?>" type="checkbox" value="1" <?php if( esc_attr( $use_page_title ) ) echo "checked='checked'" ?>" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'use_page_title' ) ); ?>"><?php _e( 'Use current page title', 'doover' ); ?></label>	
			</p>
			
			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'use_page_sibling' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_page_sibling' ) ); ?>" type="radio" value="1" <?php if( $use_page_sibling==1  ) echo "checked='checked'" ?>" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'use_page_sibling' ) ); ?>"><?php _e( 'Show page siblings', 'doover' ); ?></label>	
				<br/>
				<input id="<?php echo esc_attr( $this->get_field_id( 'use_page_childrens' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_page_sibling' ) ); ?>" type="radio" value="0" <?php if( ! $use_page_sibling ) echo "checked='checked'" ?>" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'use_page_childrens' ) ); ?>"><?php _e( 'Show page children', 'doover' ); ?></label>	
				<br/>
				<input id="<?php echo esc_attr( $this->get_field_id( 'use_page_childrens2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_page_sibling' ) ); ?>" type="radio" value="2" <?php if( $use_page_sibling==2 ) echo "checked='checked'" ?>" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'use_page_childrens2' ) ); ?>"><?php _e( 'Show page children (if there is no children, show siblings)', 'doover' ); ?></label>	
			</p>
		<?php
	}
}
?>