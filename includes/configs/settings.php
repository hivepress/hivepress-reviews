<?php
/**
 * Settings configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'reviews' => [
		'title'    => esc_html__( 'Reviews', 'hivepress-reviews' ),
		'_order'   => 20,

		'sections' => [
			'submission' => [
				'title'  => hivepress()->translator->get_string( 'submission' ),
				'_order' => 10,

				'fields' => [
					'review_allow_multiple'    => [
						'label'   => hivepress()->translator->get_string( 'submission' ),
						'caption' => esc_html__( 'Allow submitting multiple reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 10,
					],

					'review_enable_moderation' => [
						'label'   => hivepress()->translator->get_string( 'moderation' ),
						'caption' => esc_html__( 'Manually approve new reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'default' => true,
						'_order'  => 20,
					],
				],
			],
		],
	],
];
