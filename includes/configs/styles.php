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
];
