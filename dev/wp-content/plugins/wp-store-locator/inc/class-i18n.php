<?php
/**
 * i18n class
 *
 * @author Tijmen Smit
 * @since  2.0.0
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WPSL_i18n' ) ) {
    
    class WPSL_i18n {
        
        private $wpml_active = null;
        
        private $qtrans_active = null;        
                
        /**
         * Class constructor
         */          
        function __construct() {
            add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );    
        }
                
        /**
         * Load the translations from the language folder
         *
         * @since 2.0.0
         * @return void
         */
        public function load_plugin_textdomain() {
            
            $domain = 'wpsl';
            $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
            
            // Load the language file from the /wp-content/languages/wp-store-locator folder, custom + update proof translations.
            load_textdomain( $domain, WP_LANG_DIR . '/wp-store-locator/' . $domain . '-' . $locale . '.mo' );
            
            // Load the language file from the /wp-content/plugins/wp-store-locator/languages/ folder.
            load_plugin_textdomain( $domain, false, dirname( WPSL_BASENAME ) . '/languages/' ); 
        }

        /**
         * Check if WPML is active
         *
         * @since 2.0.0
         * @return boolean|null
         */
        public function wpml_exists() {
            
            if ( $this->wpml_active == null ) {
                $this->wpml_active = function_exists( 'icl_register_string' );
            }
            
            return $this->wpml_active;
        } 

        /**
         * Check if a qTranslate compatible plugin is active.
         *
         * @since 2.0.0
         * @return boolean|null
         */
        public function qtrans_exists() {
            
            if ( $this->qtrans_active == null ) {
                $this->qtrans_active = ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) || function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) );
            }
            
            return $this->qtrans_active;
        } 
        
        /**
         * See if there is a translated page available for the provided store ID.
         * 
         * @since 2.0.0
         * @see    https://wpml.org/documentation/support/creating-multilingual-wordpress-themes/language-dependent-ids/#2
         * @param  string $store_id
         * @return string empty or the id of the translated store
         */
        public function maybe_get_wpml_id( $store_id ) {
            
            $return_original_id = apply_filters( 'wpsl_return_original_wpml_id', true );
            $translated_id      = icl_object_id( $store_id, 'wpsl_stores', $return_original_id, ICL_LANGUAGE_CODE );

            // If '$return_original_id' is set to false, NULL is returned if no translation exists.
            if ( is_null( $translated_id ) ) {
                $translated_id = '';
            }
                        
            return $translated_id;
        }
                
        /**
         * Translate with a qTranslate compatible plugin.
         *
         * @since 2.0.0
         * @param  string $text        The original text
         * @return string $translation The translated text
         */
        public function qtranslation( $text ) {
            
            if ( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
                $translation = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $text );
            } else {
                $translation = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage( $text );
            }
            
            return $translation;
        }

        /**
         * Get the correct translation.
         * 
         * Return the translated text from either WPML or qTranslate 
         * or the translation that was set on the settings page.
         * 
         * @since 2.0.0
         * @param  string $name The name of the translated string
         * @param  string $text The text of the translated string
         * @return string The translation
         */
        public function get_translation( $name, $text ) {
            
            global $wpsl_settings;

            $translation = '';
            
            /* Check if we need to use WPML or qTranslate for the translation */
            if ( $this->wpml_exists() ) {
                $translation = icl_t( 'admin_texts_wpsl_settings', '[wpsl_settings]' . $name, $text );
            } else if ( $this->qtrans_exists() ) {
                $translation = $this->qtranslation( $text );
            }
            
            /* If we don't have a translation here, we use the value set on the settings page */
            if ( empty( $translation ) ) {
                $translation = stripslashes( $wpsl_settings[$name] );
            }
            
            return $translation;
        }  
        
        /**
         * If a multilingual plugin like WPML or qTranslate X is active
         * we return the active language code.
         * 
         * @since 2.0.0
         * @return string Empty or the current language code
         */
        public function check_multilingual_code() {
            
            $language_code = '';
            
            if ( $this->wpml_exists() ) {
                $language_code = '_' . ICL_LANGUAGE_CODE;
            } else if ( $this->qtrans_exists() ) {
                
                if ( function_exists( 'qtranxf_getLanguage' ) ) {
                    $language_code = '_' . qtranxf_getLanguage();
                } else if ( function_exists( 'qtrans_getLanguage' ) ) {
                    $language_code = '_' . qtrans_getLanguage();
                }                
            }    
            
            return $language_code;
        }
        
    }
    
    new WPSL_i18n();
}