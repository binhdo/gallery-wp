<?php

/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that also follow
 * WordPress coding standards and PHP best practices.
 *
 * @package   Gallery_WP
 * @author    binaryhideout <m@binaryhideout.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 binaryhideout
 *
 * @wordpress-plugin
 * Plugin Name: Gallery WP
 * Plugin URI:  https://github.com/binhdo/gallery-wp
 * Description: TODO
 * Version:     1.0.0
 * Author:      binaryhideout
 * Author URI:  http://binaryhideout.com
 * Text Domain: gallery-wp
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'class-gallery-wp.php';
require_once plugin_dir_path( __FILE__ ) . 'class-gallery-wp-settings.php';

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array('Gallery_WP', 'activate') );
register_deactivation_hook( __FILE__, array('Gallery_WP', 'deactivate') );

// Instantiate plugin class
$gallery_wp = new Gallery_WP();
