<?php
/**
 * Strings configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'reviews'                           => esc_html__( 'Reviews', 'hivepress-reviews' ),
	'you_cant_review_your_own_listings' => esc_html__( 'You can\'t review your own listings.', 'hivepress-reviews' ),
];
