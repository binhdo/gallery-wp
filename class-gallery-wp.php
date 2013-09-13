<?php
/**
 * Gallery WP.
 *
 * @package   Gallery_WP
 * @author    binaryhideout <m@binaryhideout.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 binaryhideout
 */

/**
 * Plugin class.
 *
 * @package Gallery_WP
 * @author  binaryhideout <m@binaryhideout.com>
 */
class Gallery_WP {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @const   string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	public static $plugin_slug = 'gallery-wp';
	private $settings;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		// Load plugin text domain
		add_action( 'init', array($this, 'load_plugin_textdomain') );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array($this, 'activate_new_site') );

		// Plugin admin settings
		if ( is_admin() )
			$this->settings = new Gallery_WP_Settings();

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_styles') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		add_action( 'wp_footer', array($this, 'action_gallery_markup') );
		//add_filter( 'TODO', array($this, 'filter_method_name') );
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}
				restore_current_blog();
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}
				restore_current_blog();
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since 1.0.0
	 *
	 * @param int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) )
			return;

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since 1.0.0
	 *
	 * @return array|false The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;
		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 1.0.0
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = self::$plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		//wp_enqueue_style( self::$plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), self::VERSION );
		wp_enqueue_style( self::$plugin_slug, plugins_url( 'vendor/css/blueimp-gallery.min.css', __FILE__ ), array(), null, 'screen' );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		//wp_enqueue_script( self::$plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array('jquery'), self::VERSION );
		wp_enqueue_script( self::$plugin_slug, plugins_url( 'vendor/js/jquery.blueimp-gallery.min.js', __FILE__ ), array('jquery'), null, true );
	}

	public function action_gallery_markup() {
		?>
		<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
		<div id="blueimp-gallery" class="blueimp-gallery">
			<div class="slides"></div>
			<h3 class="title"></h3>
			<a class="prev">‹</a>
			<a class="next">›</a>
			<a class="close">×</a>
			<a class="play-pause"></a>
			<ol class="indicator"></ol>
		</div>
		<?php
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}
