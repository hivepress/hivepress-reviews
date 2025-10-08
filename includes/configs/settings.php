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
		'title'    => hivepress()->translator->get_string( 'reviews' ),
		'_order'   => 40,

		'sections' => [
			'submission' => [
				'_order' => 10,

				'fields' => [
					'review_allow_multiple'    => [
						'label'   => hivepress()->translator->get_string( 'submission' ),
						'caption' => esc_html__( 'Allow submitting multiple reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 10,
					],

					'review_allow_anonymous'   => [
						'caption' => esc_html__( 'Allow making reviews anonymous', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 15,
					],

					'review_enable_moderation' => [
						'label'   => hivepress()->translator->get_string( 'moderation' ),
						'caption' => esc_html__( 'Manually approve new reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'default' => true,
						'_order'  => 20,
					],

					'review_allow_replies'     => [
						'label'   => esc_html__( 'Replies', 'hivepress-reviews' ),
						'caption' => esc_html__( 'Allow replying to reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 30,
					],

					'review_criteria'          => [
						'label'       => esc_html__( 'Criteria', 'hivepress-reviews' ),
						'description' => esc_html__( 'Allow multiple ratings per review by adding criteria and optional weights for calculating the average.', 'hivepress-reviews' ),
						'type'        => 'repeater',
						'_order'      => 40,

						'fields'      => [
							'name'   => [
								'placeholder' => hivepress()->translator->get_string( 'name' ),
								'type'        => 'text',
								'max_length'  => 256,
								'required'    => true,
								'_order'      => 10,
							],

							'weight' => [
								'placeholder' => esc_html__( 'Weight', 'hivepress-reviews' ) . ' (%)',
								'type'        => 'number',
								'min_value'   => 1,
								'max_value'   => 100,
								'_order'      => 20,
							],
						],
					],
				],
			],
		],
	],
];
