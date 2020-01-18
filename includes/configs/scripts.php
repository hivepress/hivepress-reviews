<?php
/**
 * Scripts configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'raty'             => [
		'handle'  => 'raty',
		'src'     => hivepress()->get_url( 'reviews' ) . '/assets/js/jquery.raty.min.js',
		'version' => hivepress()->get_version( 'reviews' ),
	],

	'reviews_frontend' => [
		'handle'  => 'hivepress-reviews-frontend',
		'src'     => hivepress()->get_url( 'reviews' ) . '/assets/js/frontend.min.js',
		'version' => hivepress()->get_version( 'reviews' ),
		'deps'    => [ 'hivepress-core', 'raty' ],
	],
];
