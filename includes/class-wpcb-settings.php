<?php
/**
 * Creates the settings for WP Color Browser
 *
 * @package    WP Color Browser
 * @subpackage Settings
 * @author     Carlos Rios
 * @version    1.0
 */

/**
 * WPCB_Settings class
 * 
 * @since  1.0
 */
class WPCB_Settings {

	/**
	 * Hooks when the class is loaded
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		add_action( 'admin_init', array( $this, 'create_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'update_option_wpcb-theme-color', array( $this, 'save_previous_colors' ) );
	}

	/**
	 * Loads the styles and scripts only on wp_color_browser page
	 *
	 * @since   1.0
	 * @param   $page
	 * @return  void
	 */
	public function load_scripts( $page )
	{
		if( $page == 'appearance_page_wp_color_browser' ){
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-browser-styles', WPCB_URL . 'assets/css/wp-color-browser.css' );
			wp_enqueue_script( 'wp-color-browser', WPCB_URL . 'assets/js/wp-color-browser.js', array( 'wp-color-picker' ), false, true );
		}

		return;
	}

	/**
	 * Create the menu to be displayed in admin side
	 *
	 * @since  1.0
	 */
	public function register_menu()
	{
		add_theme_page(
			__( 'WP Color Browser Settings', 'wp-color-browser' ),
			__( 'Color Browser', 'wp-color-browser' ),
			'administrator',
			'wp_color_browser',
			array( $this, 'settings_html' )
		);
	}

	/**
	 * Creates the settings and renders them with the correct html
	 *
	 * @since  1.0
	 * @return void
	 */
	public function settings_html()
	{ 
		/* if ( isset( $_REQUEST['saved'] ) ){
			$saved_message = __( 'Browser settings saved.', 'wp-color-browser' );
			echo sprintf( '<div id="message" class="updated fade"><p><strong></strong></p></div>', $saved_message );
		}*/
		?>
		<div class="wrap">

			<h2><?php _e( 'WP Color Browser Settings', 'wp-color-browser' ); ?></h2>

			<p><?php _e( 'Most modern web browsers allow you to take advantage of elements within them. Use the settings below to add color to your favorite web browsers.', 'wp-color-browser' ); ?></p>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'wp_color_browser' );
				do_settings_sections( 'wp_color_browser' );
				submit_button();
				?>
			</form>

		</div>
	<?php
	}

	/**
	 * Creates the sections & fields for WordPress Settings API and registers the settings.
	 *
	 * @since  1.0
	 */
	public function create_settings()
	{
		// Sortable settings section
		add_settings_section(
			'settings',
			'',
			'',
			'wp_color_browser'
		);

		// Theme Color Setting
		add_settings_field(
			'wpcb-theme-color',
			__( 'Mobile Browser Color', 'wp-color-browser' ),
			array( $this, 'theme_color_html' ),
			'wp_color_browser',
			'settings'
		);
		register_setting( 'wp_color_browser', 'wpcb-theme-color', 'wpcb_sanitize_hex_color' );
	}

	/**
	 * Renders the field's html output
	 *
	 * @since  1.0
	 * @return void
	 */
	public function theme_color_html()
	{
		/**
		 * Get the options and set the default variables
		 * 
		 * @var string
		 */
		$theme_color = get_option( 'wpcb-theme-color', '#f0f0f0' );
		$previous_colors = get_option( 'wpcb-previous-colors', array() ); ?>

		<fieldset id="wpcb-theme-color-fieldset">

			<div id="wpcb-android-preview">
				<div id="wpcb-android-header" style="background-color: <?php echo $theme_color; ?>;">
					<div id="wpcb-android-top-bar"></div>
					<div id="wpcb-android-url-bar" style="color: <?php echo wpcb_get_contrast( $theme_color ); ?>"><?php bloginfo('url'); ?></div>
				</div>
			</div>

			<p class="description wpcb-paragraph"><?php _e( 'This setting currently only changes the window color in Google Chrome.', 'wp-color-browser' ); ?></p>

			<div id="wpcb-theme-color-container">
				<h4><?php _e( 'Choose a color.', 'wp-color-browser' ); ?></h4>

				<label for="wpcb-theme-color">
					<input id="wpcb-theme-color" type="text" name="wpcb-theme-color" value="<?php echo $theme_color; ?>"></input>
				</label>
			</div><!-- end #wpcb-theme-color-container -->

			<?php if( !empty( $previous_colors ) ) { ?>

			<div id="wpcb-previous-colors">
				<h4><?php _e( 'Or choose from a previously used color.', 'wp-color-browser' ); ?></h4>

				<?php foreach( $previous_colors as $color ) { ?>
				<span class="wpcb-color" style="background-color: <?php echo $color; ?>" data-color="<?php echo $color; ?>"></span>
				<?php } // endforeach ?>
			</div>

			<?php } // endif; ?>

		</fieldset>

		<?php
	}

	/**
	 * Saves the previous colors after the theme color is saved
	 *
	 * @since   1.0
	 * @version 1.0
	 * 
	 * @param  string $old_color the old color value
	 * @param  string $new_color the new color, optional as the color is not always changed
	 * @return void
	 */
	public function save_previous_colors( $old_color, $new_color = '' )
	{
		if( $new_color = '' ){
			return;
		}

		if( $old_color !== $new_color ){
			// Get the previous options
			$previous_colors = get_option( 'wpcb-previous-colors' );

			// Set previous_colors to an array and add the old color
			if( !is_array( $previous_colors ) ){
				$previous_colors = array( $old_color );
			}

			// Check if the old color is not a previous color
			// and add it, also ensure there are only 6 previous colors
			// allowed at any given time.
			if( !in_array( $old_color, $previous_colors ) ){
				array_unshift( $previous_colors, $old_color );

				if( count( $previous_colors ) > 6 ){
					array_pop( $previous_colors );
				}
			}

			// Save the option
			update_option( 'wpcb-previous-colors', $previous_colors );
		}

		return;
	}

}

return new WPCB_Settings();
