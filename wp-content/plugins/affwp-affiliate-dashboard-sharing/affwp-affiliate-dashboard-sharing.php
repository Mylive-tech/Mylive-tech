<?php
/**
 * Plugin Name: AffiliateWP - Affiliate Dashboard Sharing
 * Plugin URI: http://affiliatewp.com/addons/affiliate-dashboard-sharing/
 * Description: Easily allow your affiliates to share referral URLs generated from the affiliate dashboard 
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.0.3
 * Text Domain: affwp-sharing
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package AffiliateWP Sharing
 * @category Core
 * @author Andrew Munro
 * @version 1.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Affiliate_Dashboard_Sharing' ) ) {

	final class AffiliateWP_Affiliate_Dashboard_Sharing {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of Affiliate Sharing exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		private static $plugin_dir;

		/**
		 * Plugin Version
		 */
		private $version = '1.0.3';

		/**
		 * Plugin Title
		 */
		public $title = 'Affiliate Dashboard Sharing';

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Affiliate_Dashboard_Sharing ) ) {
				self::$instance = new AffiliateWP_Affiliate_Dashboard_Sharing;

				self::$plugin_dir = plugin_dir_path( __FILE__ );

				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->hooks();

			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_AFFILIATE_SHARING_PLUGIN_DIR' ) ) {
				define( 'AFFWP_AFFILIATE_SHARING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			// activation
			add_action( 'admin_init', array( $this, 'activation' ) );
			
			// text domain
			add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );

			// css
			add_action( 'wp_head', array( $this, 'affiliate_dashboard_css' ) );

			// js
			add_action( 'wp_footer', array( $this, 'affiliate_dashboard_js' ) );

			// ajax
			add_action( 'wp_ajax_affiliate_share', array( $this, 'sharing_ajax' ) );
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			if( is_admin() ) {

				require_once self::$plugin_dir . 'includes/admin.php';

			}

		}

		/**
		 * Activation function fires when the plugin is activated.
		 *
		 * This function is fired when the activation hook is called by WordPress,
		 * it flushes the rewrite rules and disables the plugin if EDD isn't active
		 * and throws an error.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @return void
		 */
		public function activation() {
			global $wpdb;
			if ( ! class_exists( 'Affiliate_WP' ) ) {

				// is this plugin active?
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					// deactivate the plugin
			 		deactivate_plugins( plugin_basename( __FILE__ ) );
			 		// unset activation notice
			 		unset( $_GET[ 'activate' ] );
			 		// display notice
			 		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}

			}
		}

		/**
		 * Admin notices
		 *
		 * @since 1.0
		*/
		public function admin_notices() {
			if ( ! is_plugin_active( 'affiliatewp/affiliate-wp.php' ) ) {
				echo '<div class="error"><p>' . sprintf( __( 'You must install %sAffiliateWP%s to use %s', 'affwp-affiliate-dashboard-sharing' ), '<a href="https://affiliatewp.com/pricing" title="AffiliateWP" target="_blank">', '</a>', $this->title ) . '</p></div>';
			}
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( AFFWP_AFFILIATE_SHARING_PLUGIN_DIR ) ) . '/languages/';
			$lang_dir = apply_filters( 'affwp_affiliate_sharing_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'affwp_affiliate_dashboard_sharing' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'affwp_affiliate_dashboard_sharing', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affwp-affiliate-dashboard-sharing/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				load_textdomain( 'affwp_affiliate_dashboard_sharing', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				load_textdomain( 'affwp_affiliate_dashboard_sharing', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affwp_affiliate_dashboard_sharing', false, $lang_dir );
			}
		}

		/**
		 * Sharing
		 */
		public function sharing( $share_url = '' ) {

			$options 		= get_option( 'affwp_settings' );
			$options 		= $options['dashboard_sharing'];
			$sharing_text 	= isset( $options['sharing_text'] ) ? $options['sharing_text'] : '';

			ob_start();
		?>
		<h2><?php _e( 'Share this URL', 'affwp-affiliate-dashboard-sharing' ); ?></h2>
			<div class="affwp-sharing">

				<?php 
					$twitter_default_text 	=  $sharing_text ? $sharing_text : get_bloginfo('name');
					$twitter_count_box 		= 'vertical';
					$twitter_button_size 	= 'medium';
				?>
				<div class="share twitter">
					<a href="https://twitter.com/share" data-text="<?php echo $twitter_default_text; ?>" data-lang="en" class="twitter-share-button" data-count="<?php echo $twitter_count_box; ?>" data-size="<?php echo $twitter_button_size; ?>" data-counturl="<?php echo $share_url; ?>" data-url="<?php echo $share_url; ?>">
						Share
					</a>
				</div>

				<?php
					$data_share = 'false';
					$facebook_button_layout = 'box_count';
				?>
				
				<div class="share facebook">
					<div class="fb-like" data-href="<?php echo $share_url; ?>" data-send="true" data-action="like" data-layout="<?php echo $facebook_button_layout; ?>" data-share="<?php echo $data_share; ?>" data-width="" data-show-faces="false"></div>
				</div>
				
				<?php 
					$googleplus_button_size = 'tall';
					$google_button_annotation = 'bubble';
					$google_button_recommendations = 'false';
				?>
				<div class="share googleplus">
					<div class="g-plusone" data-recommendations="<?php echo $google_button_recommendations; ?>" data-annotation="<?php echo $google_button_annotation;?>" data-callback="plusOned" data-size="<?php echo $googleplus_button_size; ?>" data-href="<?php echo $share_url; ?>"></div>
				</div>

				<?php 
					$linkedin_counter = 'top';
				?>

				<div class="share linkedin">
					<script type="IN/Share" data-counter="<?php echo $linkedin_counter; ?>" data-onSuccess="share" data-url="<?php echo $share_url; ?>"></script>
				</div>

			</div>

		<?php 
			$html = ob_get_clean();
			return apply_filters( 'affwp_sharing_html', $html );
		}

		/**
		 * Is Affiliate Dashboard
		 * @return boolean true if on dashboard, false otherwise
		 */
		private function is_affiliate_dashboard() {
			global $post;

			if ( has_shortcode( $post->post_content, 'affiliate_area' ) )
				return true;

			return false;
		}

		public function affiliate_dashboard_js() {

			?>
			<script>
				var affwpds_ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
				jQuery(document).ready(function ($) {
				  	
				  $( '#affwp-generate-ref-url' ).append( '<div id="affwp-affiliate-dashboard-sharing"></div>' );

				   $( '#affwp-generate-ref-url' ).submit( function() {
				    
				      var url    = $('#affwp-referral-url').val();
				      console.log( url );    
				     
				      var data   = {
				          action: 'affiliate_share',
				          url : url
				      };

				      $.ajax({
				          type: "POST",
				          data: data,
				          dataType: "json",
				          url: affwpds_ajaxurl,
				          success: function (response) {

				            if ( response.sharing ) {

				               $( '#affwp-affiliate-dashboard-sharing' ).empty().append( response.sharing );

                				// LinkedIn
                				if ( typeof (IN) != 'undefined' ) {
                				    IN.parse();
                				} 
                				else {
                				   $.getScript("https://platform.linkedin.com/in.js");
                				}

								// Twitter
								if ( typeof (twttr) != 'undefined' ) {
									twttr.widgets.load();
								} 
								else {
									$.getScript('http://platform.twitter.com/widgets.js');
								}

								// Facebook
								if ( typeof (FB) != 'undefined' ) {
									FB.init({ status: true, cookie: true, xfbml: true });
								} 
								else {
									$.getScript("http://connect.facebook.net/en_US/all.js#xfbml=1", function () {
										FB.init({ 
											status: true, 
											cookie: true, 
											xfbml: true
										});
									});
								}

            				   // Google
        				       if ( typeof (gapi) != 'undefined' ) {
        				           $(".g-plusone").each(function () {
        				           		gapi.plusone.render( $(this).get(0), { "size": "tall" } );
        				           });
        				       } else {
        				           $.getScript('https://apis.google.com/js/plusone.js');
        				       }

				            }

				          }
				      }).fail(function (response) {
				          console.log(response);
				      }).done(function (response) {

				      });

				   });

				});

			</script>
			<?php
		}


		/**
		 * Sharing ajax
		 *
		 * @since  1.0
		 */
		public function sharing_ajax() {

			// get URL
			$url = isset( $_POST['url'] ) ? $_POST['url'] : null;

			$return = array(
				'sharing' 	=> html_entity_decode( $this->sharing( $url ), ENT_COMPAT, 'UTF-8' )
			);

			echo json_encode( $return );

			wp_die();
		}

		/**
		 * Load scripts
		 * 
		 * @return void
		 * @since  1.0
		 */
		public function affiliate_dashboard_css() {
			?>
			<style>
				#affwp-affiliate-dashboard-sharing { margin: 2em 0 4em 0; }
				.affwp-sharing .share { display: inline-block; vertical-align: top; padding: 0 0.5em 0 0; }
				.affwp-sharing .share iframe { max-width: none; }
			</style>
		<?php	
		}
		
	}
}

/**
 * Loads a single instance of Affiliate Sharing
 *
 * This follows the PHP singleton design pattern.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example <?php $affwp_affiliate_sharing = affwp_affiliate_sharing(); ?>
 *
 * @since 1.0
 *
 * @see AffiliateWP_Affiliate_Dashboard_Sharing::get_instance()
 *
 * @return object Returns an instance of the AffiliateWP_Affiliate_Dashboard_Sharing class
 */
function affwp_affiliate_dashboard_sharing() {
	return AffiliateWP_Affiliate_Dashboard_Sharing::get_instance();
}

/**
 * Loads plugin after all the others have loaded and have registered their hooks and filters
 *
 * @since 1.0
*/
add_action( 'plugins_loaded', 'affwp_affiliate_dashboard_sharing', apply_filters( 'affwp_affiliate_dashboard_sharing_action_priority', 10 ) );