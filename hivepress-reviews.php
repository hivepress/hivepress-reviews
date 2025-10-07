<?php
/**
 * Plugin Name: HivePress Reviews
 * Description: Allow users to rate and review listings.
 * Version: 1.3.2
 * Author: HivePress
 * Author URI: https://hivepress.io/
 * Text Domain: hivepress-reviews
 * Domain Path: /languages/
 *
 * @package HivePress
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Register extension directory.
add_filter(
	'hivepress/v1/extensions',
	function( $extensions ) {
		$extensions[] = __DIR__;

		return $extensions;
	}
);
