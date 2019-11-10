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
		'handle'  => 'hp-reviews-frontend',
		'src'     => HP_REVIEWS_URL . '/assets/css/frontend.min.css',
		'version' => HP_REVIEWS_VERSION,
		'scope'   => [ 'frontend', 'editor' ],
	],
];
