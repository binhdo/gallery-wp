<?php

/**
 * Gallery WP Seetings Class.
 *
 * @author binaryhideout
 */
class Gallery_WP_Settings {

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	private $plugin_slug;

	public function __construct() {
		$this->plugin_slug = Gallery_WP::$plugin_slug;

		add_action( 'admin_init', array($this, 'action_admin_init') );

		// Add the options page and menu item.
		add_action( 'admin_menu', array($this, 'add_plugin_admin_menu') );
		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'gallery-wp.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array($this, 'add_action_links') );
		// Load admin style sheet and JavaScript.
		//add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_styles') );
		//add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );
	}

	public function action_admin_init() {
		// Register plugin settings
		register_setting( $this->plugin_slug, $this->plugin_slug, array($this, 'validate_settings') );

		// Add settings sections
		add_settings_section( 'basic_settings', __( 'Basic Settings', 'gallery-wp' ), array($this, 'section_basic_callback'), $this->plugin_screen_hook_suffix );
		
		// Add settings fields
		add_settings_field( 'load_gallery_js', __( 'Load Gallery javascripts', $this->plugin_slug ), array($this, 'add_field_checkbox'), $this->plugin_screen_hook_suffix, 'basic_settings', array(
			'id' => 'load_gallery_js',
			'desc' => __( 'additional description', $this->plugin_slug )
		) );
		add_settings_field( 'load_gallery_css', __( 'Load Gallery css', $this->plugin_slug ), array($this, 'add_field_checkbox'), $this->plugin_screen_hook_suffix, 'basic_settings', array(
			'id' => 'load_gallery_css',
			'desc' => __( 'additional description', $this->plugin_slug )
		) );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_plugins_page( __( 'Gallery WP Settings', 'gallery-wp' ), __( 'Gallery WP', 'gallery-wp' ), 'manage_options', $this->plugin_slug, array($this, 'display_plugin_admin_page') );
	}

	public function section_basic_callback() {
		
	}

	public function add_field_checkbox( $args ) {
		extract( $args );

		$html = '<input type="checkbox" id="' . $id . '" name="' . $this->plugin_slug . '[' . $id . ']" value="1"' . checked( 1, $this->get_plugin_option( $id ), false ) . '>' . "\n";
		if ( $desc )
			$html .= '<label class="description" for="' . $id . '">' . $desc . '</label>' . "\n";

		echo $html;
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	public function validate_settings( $input ) {
		$settings = array();
		$checkbox_options = array('load_gallery_js', 'load_gallery_css', 'auto_lightbox_galleries', 'auto_lightbox_images');
		foreach ( $checkbox_options as $checkbox_option ) {
			$settings[$checkbox_option] = isset( $input[$checkbox_option] ) ? true : false;
		}

		return $settings;
	}

	public function get_plugin_option( $option ) {
		$settings = get_option( $this->plugin_slug );

		if ( !is_array( $settings ) || !array_key_exists( $option, $settings ) ) {
			return false;
		} else {
			return $settings[$option];
		}
	}

	/**
	 * Returns an array of default settings
	 * 
	 * @return array
	 */
	public function get_default_settings() {
		return array(
			'version' => Gallery_WP::$version,
			'load_gallery_js' => true,
			'load_gallery_css' => true,
		);
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge( array('settings' => '<a href="' . admin_url( 'plugins.php?page=gallery-wp' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'), $links );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), Gallery_WP::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery'), Gallery_WP::VERSION );
		}
	}

}
