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
		'src'     => HP_REVIEWS_URL . '/assets/js/jquery.raty.min.js',
		'version' => HP_REVIEWS_VERSION,
	],

	'reviews_frontend' => [
		'handle'  => 'hp-reviews-frontend',
		'src'     => HP_REVIEWS_URL . '/assets/js/frontend.min.js',
		'version' => HP_REVIEWS_VERSION,
		'deps'    => [ 'hp-core-frontend', 'raty' ],
	],
];
