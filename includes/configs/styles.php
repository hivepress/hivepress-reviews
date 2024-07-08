<?php
/**
 * Styles configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'reviews_frontend' => [
		'handle'  => 'hivepress-reviews-frontend',
		'src'     => hivepress()->get_url( 'reviews' ) . '/assets/css/frontend.min.css',
		'version' => hivepress()->get_version( 'reviews' ),
		'scope'   => [ 'frontend', 'editor' ],
	],

	'reviews_backend'  => [
		'handle'  => 'hivepress-reviews-backend',
		'src'     => hivepress()->get_url( 'reviews' ) . '/assets/css/backend.min.css',
		'version' => hivepress()->get_version( 'reviews' ),
		'scope'   => [ 'backend' ],
	],
];
