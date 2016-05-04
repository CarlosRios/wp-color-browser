<?php
/**
 * Plugin Name: WP Color Browser
 * Description: Change the color of the browser on mobile devices.
 * Author: Carlos Rios
 * Author URI: http://crios.me
 * Version: 1.0
 * Plugin URI: https://github.com/CarlosRios/wp-color-browser
 * License: GPL2+
 *
 * @package  WP Color Browser
 * @category WordPress/Plugin
 * @author   Carlos Rios
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
	die;
}

if( ! class_exists( 'WP_Color_Browser' ) ) {

	/**
	 * WP_Color_Browser class
	 * 
	 * @since  1.0
	 */
	class WP_Color_Browser {

		/**
		 * Sortable Posts version
		 *
		 * @since  1.0
		 * @var string
		 */
		public $version = '1.0';

		/**
		 * Initiates Sortable Posts
		 *
		 * @since  1.0
		 */
		public function __construct()
		{
			if( !defined( 'WPCB_URL' ) ){
				define( 'WPCB_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
			}

			$this->includes();
			$this->register_hooks();
		}

		/**
		 * Includes all necessary files
		 *
		 * @since  1.0
		 */
		public function includes()
		{
			if ( is_admin() ) {
				require_once( 'includes/wpcb-functions.php' );
				require_once( 'includes/class-wpcb-settings.php' );
			}
		}

		/**
		 * Registers all of our hooks
		 *
		 * @since  1.0
		 */
		public function register_hooks()
		{
			add_action( 'wp_head', array( $this, 'output_metadata' ) );
		}

		/**
		 * Outputs the metadata that will be added to the head of the site
		 *
		 * @since  1.0
		 * @return void
		 */
		public function output_metadata()
		{
			// Get the options
			$theme_color = get_option( 'wpcb-theme-color', '#f0f0f0' ); ?>

			<!-- Browser colors provided by WP Color Browser -->
			<meta name="theme-color" content="<?php echo $theme_color; ?>">
			<meta name="msapplication-navbutton-color" content="<?php echo $theme_color; ?>">
			<?php
		}

		/**
		 * Things to fire during activation
		 *
		 * @since   1.0
		 * @return
		 */
		public static function activate()
		{
			flush_rewrite_rules();
		}

	}

	new WP_Color_Browser();

	// Flush rewrite rules so that WP_REST_API is available after
	register_activation_hook( __FILE__, array( 'WP_Color_Browser', 'activate' ) );
}
