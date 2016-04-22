<?php

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Social_Icons_Widget' ) ):

class G1_Social_Icons_Widget extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'g1_social_icons', // Base ID
            'G1 Social Icons', // Name
            array( 'description' => __( 'A Social Icons Widget', 'g1_social_icons' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        $elems = array('include', 'exclude', 'template', 'size', 'hide');
        $attrs = '';

        foreach ( $elems as $elem ) {
            if ( !empty( $instance[$elem] ) ) {
                $attrs .= sprintf(' %s="%s"', $elem, $instance[$elem]);
            }
        }

        echo do_shortcode('[g1_social_icons '. $attrs .']');

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $include = !empty( $instance['include'] ) ? $instance['include'] : '';
        $exclude = !empty( $instance['exclude'] ) ? $instance['exclude'] : '';
        $template = !empty( $instance['template'] ) ? $instance['template'] : '';
        $size = !empty( $instance['size'] ) ? $instance['size'] : '';
        $hide = !empty( $instance['hide'] ) ? $instance['hide'] : '';
        ?>
        <ul class="g1-form-controls">
            <li class="g1-option-view g1-option-view-string">
                <div class="g1-label">
                    <label for="<?php echo $this->get_field_id( 'include' ); ?>"><?php _e( 'Include', 'g1_social_icons' ); ?>:</label>
                </div>
                <div class="g1-field">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'include' ); ?>" name="<?php echo $this->get_field_name( 'include' ); ?>" type="text" value="<?php echo esc_attr( $include ); ?>" />
                    <div class="g1-hint"><?php _e( 'Include only specified icons (eg. facebook,twitter). Leave empty to include all.', 'g1_social_icons' ); ?></div>
                </div>
            </li>
            <li class="g1-option-view g1-option-view-string">
                <div class="g1-label">
                    <label for="<?php echo $this->get_field_id( 'exclude' ); ?>"><?php _e( 'Exclude', 'g1_social_icons' ); ?>:</label>
                </div>
                <div class="g1-field">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'exclude' ); ?>" name="<?php echo $this->get_field_name( 'exclude' ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>" />
                    <div class="g1-hint"><?php _e( 'Exclude specified icons (eg. facebook,twitter).', 'g1_social_icons' ); ?></div>
                </div>
            </li>
            <li class="g1-option-view g1-option-view-choice">
                <div class="g1-label">
                    <label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template', 'g1_social_icons' ); ?>:</label>
                </div>
                <div class="g1-field">
                    <select name="<?php echo $this->get_field_name( 'template' ); ?>">
                        <option value="list-vertical" <?php selected( $template, 'list-vertical' ); ?>><?php _e( 'list vertical', 'g1_social_icons' ); ?></option>
                        <option value="list-horizontal" <?php selected( $template, 'list-horizontal' ); ?>><?php _e( 'list horizontal', 'g1_social_icons' ); ?></option>
                    </select>
                </div>
            </li>
            <li class="g1-option-view g1-option-view-choice">
                <div class="g1-label">
                    <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size', 'g1_social_icons' ); ?>:</label>
                </div>
                <div class="g1-field">
                    <select name="<?php echo $this->get_field_name( 'size' ); ?>">
                        <option value="16" <?php selected( $size, '16' ); ?>>16px</option>
                        <option value="24" <?php selected( $size, '24' ); ?>>24px</option>
                        <option value="32" <?php selected( $size, '32' ); ?>>32px</option>
                        <option value="48" <?php selected( $size, '48' ); ?>>48px</option>
                    </select>
                </div>
            </li>
            <li class="g1-option-view g1-option-view-string">
                <div class="g1-label">
                    <label for="<?php echo $this->get_field_id( 'hide' ); ?>"><?php _e( 'Hide', 'g1_social_icons' ); ?>:</label>
                </div>
                <div class="g1-field">
                    <input class="widefat" id="<?php echo $this->get_field_id( 'hide' ); ?>" name="<?php echo $this->get_field_name( 'hide' ); ?>" type="text" value="<?php echo esc_attr( $hide ); ?>" />
                    <div class="g1-hint"><?php _e( 'Hide icon elements (eg. label,caption,icon).', 'g1_social_icons' ); ?></div>
                </div>
            </li>
        </ul>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['include'] = ( !empty( $new_instance['include'] ) ) ? strip_tags( $new_instance['include'] ) : '';
        $instance['exclude'] = ( !empty( $new_instance['exclude'] ) ) ? strip_tags( $new_instance['exclude'] ) : '';
        $instance['template'] = ( !empty( $new_instance['template'] ) ) ? strip_tags( $new_instance['template'] ) : '';
        $instance['size'] = ( !empty( $new_instance['size'] ) ) ? absint( $new_instance['size'] ) : '';
        $instance['hide'] = ( !empty( $new_instance['hide'] ) ) ? strip_tags( $new_instance['hide'] ) : '';

        return $instance;
    }
}

endif;