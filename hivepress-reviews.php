<?php
/**
 * Plugin Name: HivePress Reviews
 * Description: Reviews add-on for HivePress plugin.
 * Version: 1.0.1
 * Author: HivePress
 * Author URI: https://hivepress.co/
 * Text Domain: hivepress-reviews
 * Domain Path: /languages/
 *
 * @package HivePress\Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Register plugin path.
add_filter(
	'hivepress/core/plugin_paths',
	function( $paths ) {
		return array_merge( $paths, [ dirname( __FILE__ ) ] );
	}
);
