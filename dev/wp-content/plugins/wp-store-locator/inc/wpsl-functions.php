<?php

/**
 * Collect all the parameters ( language, key, region ) 
 * we need before making a request to the Google Maps API.
 *
 * @since 1.0.0
 * @return string $api_params The api parameters
 */
function wpsl_get_gmap_api_params() {

    global $wpsl_settings;

    $api_params = '';
    $api_keys   = array( 'language', 'key', 'region' );

    foreach ( $api_keys as $api_key ) {
        if ( !empty( $wpsl_settings['api_'.$api_key] ) ) {
            $api_params .= $api_key . '=' . $wpsl_settings['api_'.$api_key] . '&';	
        }	
    }
    
    if ( $api_params ) {
        $api_params = '?' . rtrim( $api_params, '&' );
    }
    
    return apply_filters( 'wpsl_gmap_api_params', $api_params );
}

/**
 * Get the default plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function wpsl_get_default_settings() {

    $default_settings = array(
        'api_key'                 => '',
        'api_language'            => 'en',
        'api_region'              => '',
        'distance_unit'           => 'km',
        'max_results'             => '[25],50,75,100',
        'search_radius'           => '10,25,[50],100,200,500',
        'marker_effect'           => 'bounce',
        'address_format'          => 'city_state_zip',
        'hide_distance'           => 0,
        'auto_locate'             => 1,
        'autoload'                => 1,
        'autoload_limit'          => 50,
        'zoom_level'              => 3,
        'auto_zoom_level'         => 15,
        'zoom_name'               => '',
        'zoom_latlng'             => '',
        'height'                  => 350,
        'map_type'                => 'roadmap',
        'map_style'               => '',
        'type_control'            => 0,
        'streetview'              => 0,
        'results_dropdown'        => 1,
        'radius_dropdown'         => 1,
        'category_dropdown'       => 0,
        'infowindow_width'        => 225,
        'search_width'            => 179,
        'label_width'             => 95,
        'control_position'        => 'left',
        'scrollwheel'             => 1,
        'marker_clusters'         => 0,
        'cluster_zoom'            => 0,
        'cluster_size'            => 0,
        'new_window'              => 0,
        'reset_map'               => 0,
        'template_id'             => 'default',
        'listing_below_no_scroll' => 0,
        'direction_redirect'      => 0,
        'more_info'               => 0,
        'store_url'               => 0,
        'phone_url'               => 0,
        'marker_streetview'       => 0,
        'marker_zoom_to'          => 0,
        'more_info_location'      => 'info window',
        'mouse_focus'             => 1,
        'start_marker'            => 'red.png',
        'store_marker'            => 'blue.png',
        'editor_country'          => '',
        'editor_hours'            => wpsl_default_opening_hours(),
        'editor_hour_input'       => 'dropdown',
        'editor_hour_format'      => 12,
        'editor_map_type'         => 'roadmap',
        'hide_hours'              => 0,
        'permalinks'              => 0,
        'permalink_slug'          => __( 'stores', 'wpsl' ),
        'category_slug'           => __( 'store-category', 'wpsl' ),
        'infowindow_style'        => 'default',
        'show_credits'            => 0,
        'debug'                   => 0,
        'start_label'             => __( 'Start location', 'wpsl' ),
        'search_label'            => __( 'Your location', 'wpsl' ),
        'search_btn_label'        => __( 'Search', 'wpsl' ),
        'preloader_label'         => __( 'Searching...', 'wpsl' ),
        'radius_label'            => __( 'Search radius', 'wpsl' ),
        'no_results_label'        => __( 'No results found', 'wpsl' ),
        'results_label'           => __( 'Results', 'wpsl' ),
        'more_label'              => __( 'More info', 'wpsl' ),
        'directions_label'        => __( 'Directions', 'wpsl' ),
        'no_directions_label'     => __( 'No route could be found between the origin and destination', 'wpsl' ),
        'back_label'              => __( 'Back', 'wpsl' ),
        'street_view_label'       => __( 'Street view', 'wpsl' ),
        'zoom_here_label'         => __( 'Zoom here', 'wpsl' ),
        'error_label'             => __( 'Something went wrong, please try again!', 'wpsl' ),
        'limit_label'             => __( 'API usage limit reached', 'wpsl' ),
        'phone_label'             => __( 'Phone', 'wpsl' ),
        'fax_label'               => __( 'Fax', 'wpsl' ),
        'email_label'             => __( 'Email', 'wpsl' ),
        'url_label'               => __( 'Url', 'wpsl' ),
        'hours_label'             => __( 'Hours', 'wpsl' ),
        'category_label'          => __( 'Category filter', 'wpsl' )
    ); 

    return $default_settings;
}

/**
 * Get the current plugin settings.
 * 
 * @since 1.0.0
 * @return array $setting The current plugin settings
 */
function wpsl_get_settings() {

    $settings = get_option( 'wpsl_settings' );            

    if ( !$settings ) {
        update_option( 'wpsl_settings', wpsl_get_default_settings() );
        $settings = wpsl_get_default_settings();
    }

    return $settings;
} 

/**
 * Get a single value from the default settings.
 * 
 * @since 1.0.0
 * @param  string $setting               The value that should be restored
 * @return string $wpsl_default_settings The default setting value
 */
function wpsl_get_default_setting( $setting ) {

    global $wpsl_default_settings;

    return $wpsl_default_settings[$setting];
}

/**
 * Set the default plugin settings.
 * 
 * @since 1.0.0
 * @return void
 */
function wpsl_set_default_settings() {

    $settings = get_option( 'wpsl_settings' );

    if ( !$settings ) {
        update_option( 'wpsl_settings', wpsl_get_default_settings() );
    }
}

/**
 * Return a list of the store templates.
 * 
 * @since 1.2.20
 * @return array $templates The list of default store templates
 */
function wpsl_get_templates() {

    $templates = array(
        array(
            'id'   => 'default',
            'name' => __( 'Default', 'wpsl' ), 
            'path' => WPSL_PLUGIN_DIR . 'frontend/templates/default.php'
        ), 
        array(
            'id'   => 'below_map',
            'name' => __( 'Show the store list below the map', 'wpsl' ), 
            'path' => WPSL_PLUGIN_DIR . 'frontend/templates/store-listings-below.php'
        )
    );

    return apply_filters( 'wpsl_templates', $templates );
}

/**
 * Return the days of the week.
 *
 * @since 2.0.0
 * @return array $weekdays The days of the week
 */
function wpsl_get_weekdays() {

   $weekdays = array( 
       'monday'    => __( 'Monday', 'wpsl' ), 
       'tuesday'   => __( 'Tuesday', 'wpsl' ),  
       'wednesday' => __( 'Wednesday', 'wpsl' ),  
       'thursday'  => __( 'Thursday', 'wpsl' ),  
       'friday'    => __( 'Friday', 'wpsl' ),  
       'saturday'  => __( 'Saturday', 'wpsl' ),
       'sunday'    => __( 'Sunday' , 'wpsl' )
   );

   return $weekdays;
}

/** 
 * Get the default opening hours.
 *
 * @since 2.0.0
 * @return array $opening_hours The default opening hours
 */
function wpsl_default_opening_hours() {

   $current_version = get_option( 'wpsl_version' );
     
   $opening_hours = array(
       'dropdown' => array(
           'monday'    => array( '9:00 AM,5:00 PM' ),
           'tuesday'   => array( '9:00 AM,5:00 PM' ),
           'wednesday' => array( '9:00 AM,5:00 PM' ),
           'thursday'  => array( '9:00 AM,5:00 PM' ),
           'friday'    => array( '9:00 AM,5:00 PM' ),
           'saturday'  => '',
           'sunday'    => ''
        )
    );
   
   /* Only add the textarea defaults for users that upgraded from 1.x */
   if ( version_compare( $current_version, '2.0', '<' ) ) {
       $opening_hours['textarea'] = sprintf( __( 'Mon %sTue %sWed %sThu %sFri %sSat Closed %sSun Closed', 'wpsl' ), '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", '9:00 AM - 5:00 PM' . "\n", "\n" ); //cleaner way without repeating it 5 times??
   }

   return $opening_hours;
}

/**
 * Get the available map types.
 * 
 * @since 2.0.0
 * @return array $map_types The available map types 
 */
function wpsl_get_map_types() {
    
    $map_types = array(
        'roadmap'   => __( 'Roadmap', 'wpsl' ), 
        'satellite' => __( 'Satellite', 'wpsl' ),  
        'hybrid'    => __( 'Hybrid', 'wpsl' ),  
        'terrain'   => __( 'Terrain', 'wpsl' )
    );
    
    return $map_types;
}

/**
 * Get the address formats.
 * 
 * @since 2.0.0
 * @return array $address_formats The address formats
 */
function wpsl_get_address_formats() {
    
    $address_formats = array(
        'city_state_zip'       => __( '(city) (state) (zip code)', 'wpsl' ),
        'city_comma_state_zip' => __( '(city), (state) (zip code)', 'wpsl' ),
        'city_zip'             => __( '(city) (zip code)', 'wpsl' ),
        'city_comma_zip'       => __( '(city), (zip code)', 'wpsl' ),
        'zip_city_state'       => __( '(zip code) (city) (state)', 'wpsl' ),
        'zip_city'             => __( '(zip code) (city)', 'wpsl' )
    );
   
    return apply_filters( 'wpsl_address_formats', $address_formats );
}

/**
 * Make sure the provided map type is valid.
 * 
 * If the map type is invalid the default is used ( roadmap ).
 * 
 * @since 2.0.0
 * @param  string $map_type The provided map type
 * @return string $map_type A valid map type
 */
function wpsl_valid_map_type( $map_type ) {
    
    $allowed_map_types = wpsl_get_map_types();
    
    if ( !array_key_exists( $map_type, $allowed_map_types ) ) {
        $map_type = wpsl_get_default_setting( 'map_type' );
    }
    
    return $map_type;
}

/**
 * Make sure the provided zoom level is valid.
 * 
 * If the zoom level is invalid the default is used ( 3 ).
 * 
 * @since 2.0.0
 * @param  string $zoom_level The provided zoom level 
 * @return string $zoom_level A valid zoom level
 */
function wpsl_valid_zoom_level( $zoom_level ) {
    
    $zoom_level = absint( $zoom_level );
    
    if ( ( $zoom_level < 1 ) || ( $zoom_level > 21 ) ) {
        $zoom_level = wpsl_get_default_setting( 'zoom_level' );	
    }

    return $zoom_level;
}

/**
 * Get the max auto zoom levels for the map.
 * 
 * @since 2.0.0
 * @return array $max_zoom_levels The array holding the min - max zoom levels
 */
function wpsl_get_max_zoom_levels() {
    
    $max_zoom_levels = array();
    $zoom_level = array(
        'min' => 10,
        'max' => 21
    );
    
    $i = $zoom_level['min'];

    while ( $i <= $zoom_level['max'] ) {
        $max_zoom_levels[$i] = $i;
        $i++;
    } 
    
    return $max_zoom_levels;
}

/**
 * The labels and the values that can be set through the settings page.
 * 
 * @since 2.0.0
 * @return array $labels The label names from the settings page.
 */
function wpsl_labels() {

    $labels = array( 
        'search',
        'search_btn',
        'preloader',
        'radius',
        'no_results',
        'results',
        'more',
        'directions',
        'no_directions',
        'back',
        'street_view',
        'zoom_here',
        'error',
        'phone',
        'fax',
        'email',
        'url',
        'hours',
        'start',
        'limit',
        'category'
    );

    return $labels;
}

/**
 * Callback for array_walk_recursive, sanitize items in a multidimensional array.
 *
 * @since 2.0.0
 * @param string  $item The value
 * @param integer $key  The key
 */
function wpsl_sanitize_multi_array( &$item, $key ) {
    $item = sanitize_text_field( $item );
}
        
/**
 * Check whether the array is multidimensional.
 *
 * @since 2.0.0
 * @param  array    $array The array to check
 * @return boolean
 */
function wpsl_is_multi_array( $array ) {

    foreach ( $array as $value ) {
        if ( is_array( $value ) ) return true;
    }

    return false;
}