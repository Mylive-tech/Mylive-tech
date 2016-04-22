<?php
/**
 * Opening Hours Widget
 *
 * @class 	  WooCommerce_POS_Pro_Opening_Hours_Widget
 * @package   WooCommerce POS Pro
 * @author    Paul Kilmurray <paul@kilbot.com.au>
 * @link      http://www.woopos.com.au
 */

class WC_POS_Pro_Widgets_Hours extends WP_Widget {

	public function __construct() {
		// Instantiate the parent object
		parent::__construct(
			'store-opening-hours', // Base ID
			__('Store Opening Hours', 'woocommerce-pos-pro'), // Name
			array( 'description' => __( 'Display store hours set in WooCommerce POS', 'woocommerce-pos-pro' ) ) // Args
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// Widget output
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . apply_filters( 'widget_title', $title ). $after_title;
		}

		$days = array(
			/* translators: wordpress */__( 'Monday' ),
			/* translators: wordpress */__( 'Tuesday' ),
			/* translators: wordpress */__( 'Wednesday' ),
			/* translators: wordpress */__( 'Thursday' ),
			/* translators: wordpress */__( 'Friday' ),
			/* translators: wordpress */__( 'Saturday' ),
			/* translators: wordpress */__( 'Sunday' )
		);

		$hours = get_post_meta( $store, '_opening_hours', true );
		$note = get_post_meta( $store, '_opening_hours_notes', true );

		if( $hours ):
			echo '<dl class="opening-hours">';
			foreach( $days as $i => $day ) : if( isset($hours[$i]) ):
				echo '<dt>'. $day .'</dt>';
				for( $j=0; $j<count($hours[$i]); $j+=2 ):
					echo '<dd>'. $hours[$i][$j] .' - '. $hours[$i][$j+1] .'</dd>';
				endfor;
			endif; endforeach;
			echo '</dl>';
			if( $note ) echo '<p>'. $note .'</p>';
		endif;

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['store'] = ( ! empty( $new_instance['store'] ) ) ? $new_instance['store'] : '';
		return $instance;
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '' ;
		$store_id = isset( $instance[ 'store' ] ) ? $instance[ 'store' ] : '' ;
		$stores = get_pages( array( 'post_type' => 'stores' ) );
		?>
		<table style="width:100%;margin:10px 0;">
			<tr>
				<td><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php /* translators: wordpress */ _e( 'Title' ); ?>:</label></td>
				<td><input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width:100%"></td>
			</tr>
			<tr>
				<td><label for="<?php echo $this->get_field_id( 'store' ); ?>"><?php _e( 'Store', 'woocommerce-pos-pro' ); ?>:</label></td>
				<td>
					<?php
					if( $stores ) :
						wp_dropdown_pages( array( 'post_type' => 'stores', 'name' => $this->get_field_name( 'store' ), 'id' => $this->get_field_id( 'store' ), 'sort_column' => 'menu_order', 'selected' => $store_id ) );
					else:
						printf( __( 'No stores. <a href="%s">Create a new store</a>.', 'woocommerce-pos-pro' ), admin_url('edit.php?post_type=stores') );
					endif;
					?>
				</td>
			</tr>
		</table>
	<?php
	}
}
