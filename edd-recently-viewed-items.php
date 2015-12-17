<?php
/**
 * Plugin Name: Easy Digital Downloads - Recently Viewed Items
 * Plugin URI: http://www.johnparris.com/wordpress-plugins/edd-recently-viewed-items
 * Description: Show your visitors the items they've recently viewed with this extension for Easy Digital Downloads.
 * Version: 1.0.2
 * Author: John Parris
 * Author URI: http://www.johnparris.com
 * Text Domain: edd-rvi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Recently_Viewed_Items {

	/**
	 * @var $instance The EDD_Recently_Viewed_Items class instance
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Get active instance
	 *
	 * @access public
	 * @since 1.0
	 * @return object self::$instance The EDD_Recently_Viewed_Items class instance.
	 */
	public static function instance() {

		if ( ! self::$instance ) {
			self::$instance = new EDD_Recently_Viewed_Items();
			self::$instance->setup_constants();

			if ( ! defined( 'EDD_VERSION' ) ) {
				return false;
			}

			self::$instance->includes();
			self::$instance->load_textdomain();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Sets up the plugin constants.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		// Current version
		if ( ! defined( 'EDD_RVI_VERSION' ) ) {
			define( 'EDD_RVI_VERSION', '1.0.2' );
		}

		// Plugin dir path
		if ( ! defined( 'EDD_RVI_PLUGIN_DIR' ) ) {
			define( 'EDD_RVI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin folder URI
		if ( ! defined( 'EDD_RVI_PLUGIN_URI' ) ) {
			define( 'EDD_RVI_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
		}
	}

	/**
	 * Load the translation files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for language directory
		$lang_dir = EDD_RVI_PLUGIN_DIR . '/languages/';
		$lang_dir = apply_filters( 'edd_rvi_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'edd-rvi' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'edd-rvi', $locale );

		// Setup paths to current locale file
		$mofile_local   = $lang_dir . $mofile;
		$mofile_global  = WP_LANG_DIR . '/edd-recently-viewed-items/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/edd-recently-viewed-items/ folder
			load_textdomain( 'edd-rvi', $mofile_global );

		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/edd-recently-viewed-items/languages/ folder
			load_textdomain( 'edd-rvi', $mofile_local );

		} else {
			// Load the default language files
			load_plugin_textdomain( 'edd-rvi', false, $lang_dir );
		}
	}

	/**
	 * Sets up action and filter hooks
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function hooks() {

		add_action( 'get_header', array( $this, 'set_cookie_values' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );

		add_action( 'template_redirect', array( $this, 'setup' ), 100 );

		if ( class_exists( 'EDD_License' ) ) {
			$license = new EDD_License( __FILE__, 'Recently Viewed Items', EDD_RVI_VERSION, 'John Parris' );
		}
	}

	/**
	 * Loads required plugin files.
	 */
	private function includes() {

		if ( is_admin() ) {
			require_once EDD_RVI_PLUGIN_DIR . 'includes/settings.php';
		}

		require_once EDD_RVI_PLUGIN_DIR . 'includes/misc-functions.php';
		require_once EDD_RVI_PLUGIN_DIR . 'includes/shortcodes.php';
		require_once EDD_RVI_PLUGIN_DIR . 'includes/class-edd-rvi-widget.php';
	}

	/**
	 * Builds the array of recently viewed items.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function set_cookie_values() {

		global $wp_query;

		if ( ! is_singular( 'download' ) ) {
			return;
		}

		if ( ! $wp_query->is_main_query() ) {
			return;
		}

		$id = get_the_ID();

		if ( isset( $_COOKIE['edd-rvi'] ) ) {

			$items = (array) json_decode( stripslashes( $_COOKIE['edd-rvi'] ), true );

			if ( count( $items ) >= 7 ) {
				$items = array_slice( $items, 0, 7 );
			}

			if ( ! in_array( $id, $items ) ) {
				array_unshift( $items, $id );

			} elseif ( in_array( $id, $items ) ) {
				$key = array_search( $id, $items );

				if( $key || 0 === $key ) {
					unset( $items[$key] );
					array_unshift( $items, $id );
				}
			}

		} else {
			$items = array( $id );
		}

		$this->set_cookie( $items );
	}

	/**
	 * Sets the cookie that stores the recently viewed items.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function set_cookie( $data ) {

		if ( empty( $data ) ) {
			return;
		}

		if ( ! headers_sent() ) {
			setcookie( 'edd-rvi', json_encode( $data ), time()+(60*60*24*90), '/' );
		}
	}

	/**
	 * Sets up the plugin according to the settings.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function setup() {

		if ( is_singular( 'download' ) && ! edd_get_option( 'edd_rvi_disable_single' ) ) {
			add_action( 'edd_after_download_content', array( $this, 'single_download' ) );
		}

		if ( edd_is_checkout() && ! edd_get_option( 'edd_rvi_disable_checkout' ) ) {
			add_action( 'edd_after_checkout_cart', array( $this, 'checkout' ) );
		}
	}

	/**
	 * Queries the posts that were recently viewed.
	 *
	 * @access public
	 * @param int $number Number of downloads to query.
	 * @since 1.0
	 * @return object|void The WP_Query object containing recently viewed items.
	 */
	public function get_recently_viewed_downloads( $number = 3 ) {

		if ( isset( $_COOKIE['edd-rvi'] ) ) {
			$items = (array) json_decode( stripslashes( $_COOKIE['edd-rvi'] ), true );


			$current = array_search( get_the_ID(), $items );

			if( ! empty( $current ) || (int) 0 === $current || edd_item_in_cart( $current ) ) {
				// Don't include the current product
				unset( $items[$current] );
			}

			if ( ! empty( $items ) ) {
				$args = array(
					'posts_per_page' => $number,
					'post_type'      => 'download',
					'post__in'       => $items,
					'orderby'        => 'post__in',
				);

				return new WP_Query( $args );
			}
		}

		return false;
	}

	/**
	 * Loads the recently viewed items on single downloads.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function single_download( $post_id ) {
		edd_rvi_get_template_part( 'edd-rvi-single' );
	}

	/**
	 * Loads the recently viewed items on the checkout page.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function checkout() {
		edd_rvi_get_template_part( 'edd-rvi-checkout' );
	}

	/**
	 * Loads the plugin styles
	 */
	public function styles() {
		if ( ! edd_get_option( 'edd_rvi_disable_css' ) ) {
			wp_enqueue_style( 'edd-rvi', EDD_RVI_PLUGIN_URI . 'style.css', array(), EDD_RVI_VERSION, 'all' );
		}
	}

}

function EDD_RVI() {
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if ( ! class_exists( 'EDD_Extension_Activation' ) ) {
			require_once 'includes/class.extension-activation.php';
		}
		$activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();
		return EDD_Recently_Viewed_Items::instance();
	} else {
		return EDD_Recently_Viewed_Items::instance();
	}
}
add_action( 'plugins_loaded', 'EDD_RVI' );
