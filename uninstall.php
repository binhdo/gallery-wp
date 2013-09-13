<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Gallery_WP
 * @author    binaryhideout <m@binaryhideout.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 binaryhideout
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// TODO: Define uninstall functionality here