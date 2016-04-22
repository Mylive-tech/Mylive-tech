<?php
/**
 * Admin Notices
 *
 * @author Tijmen Smit
 * @since  2.0.0
*/

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_Notices' ) ) {
    
    /**
     * Handle the meta boxes.
     *
     * @since 2.0.0
     */
	class WPSL_Notices {
        
        /**
         * Holds the notices.
         * @since 2.0.0
         * @var array
         */
        private $notices = array();
                
        public function __construct() {
            
            $this->notices = get_option( 'wpsl_notices' ); 
            
            add_action( 'all_admin_notices', array( $this, 'show' ) );
        }
        
        /**
         * Show the notice.
         * 
         * @since 2.0.0
         * @return void
         */
        public function show() {
            
            if ( !empty( $this->notices ) ) {
                $class = ( 'update' == $this->notices['type'] ) ? 'updated' : 'error';
                $allowed_html = array(
                    'a' => array(
                        'href'       => array(),
                        'id'         => array(),
                        'class'      => array(),
                        'data-nonce' => array(),
                        'title'      => array(),
                        'target'     => array()
                    ),
                    'br' => array(),
                    'em' => array(),
                    'strong' => array( 
                        'class' => array() 
                    ),
                    'span' => array( 
                        'class' => array() 
                    )
                );

                echo '<div class="' . esc_attr( $class ) . '"><p>' . wp_kses( $this->notices['message'], $allowed_html ) . '</p></div>';

                // Empty the notices.
                $this->notices = array();
                update_option( 'wpsl_notices', $this->notices ); 
            }
        }
                
        /**
         * Save the notice.
         * 
         * @since 2.0.0
         * @param  string $type    The type of notice, either 'update' or 'error'
         * @param  string $message The user message
         * @return void
         */
        public function save( $type, $message ) {
 
            $this->notices = array(
                'type'	  => $type,
                'message' => $message
            );
                    
            update_option( 'wpsl_notices', $this->notices ); 
        }
    }
}