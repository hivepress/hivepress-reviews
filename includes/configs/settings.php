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
			'display'    => [
				'title'  => hivepress()->translator->get_string( 'display_noun' ),
				'_order' => 10,

				'fields' => [
					'review_default_order' => [
						'label'       => hivepress()->translator->get_string( 'default_sorting' ),
						'placeholder' => hivepress()->translator->get_string( 'by_date_added' ),
						'type'        => 'select',
						'_order'      => 10,

						'options'     => [
							'rating' => esc_html_x( 'Rating', 'sort order', 'hivepress-reviews' ),
						],
					],

					'reviews_per_page'     => [
						'label'     => esc_html__( 'Reviews per Page', 'hivepress-reviews' ),
						'type'      => 'number',
						'min_value' => 1,
						'default'   => 3,
						'required'  => true,
						'_order'    => 20,
					],
				],
			],

			'submission' => [
				'title'  => hivepress()->translator->get_string( 'submission' ),
				'_order' => 20,

				'fields' => [
					'review_allow_multiple'    => [
						'label'       => hivepress()->translator->get_string( 'submission' ),
						'caption'     => esc_html__( 'Allow submitting multiple reviews', 'hivepress-reviews' ),
						'description' => esc_html__( 'Check this option to allow users to post more than one review for the same listing.', 'hivepress-reviews' ),
						'type'        => 'checkbox',
						'_order'      => 10,
					],

					'review_allow_anonymous'   => [
						'caption'     => esc_html__( 'Allow making reviews anonymous', 'hivepress-reviews' ),
						'description' => esc_html__( 'Check this option to allow users to hide their personal details in reviews.', 'hivepress-reviews' ),
						'type'        => 'checkbox',
						'_order'      => 20,
					],

					'review_allow_attachment'  => [
						'caption' => hivepress()->translator->get_string( 'allow_attaching_images' ),
						'type'    => 'checkbox',
						'_order'  => 30,
					],

					'review_enable_moderation' => [
						'label'   => hivepress()->translator->get_string( 'moderation' ),
						'caption' => esc_html__( 'Manually approve new reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'default' => true,
						'_order'  => 40,
					],

					'review_allow_replies'     => [
						'label'   => esc_html__( 'Replies', 'hivepress-reviews' ),
						'caption' => esc_html__( 'Allow replying to reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 50,
					],

					'review_criteria'          => [
						'label'       => esc_html__( 'Criteria', 'hivepress-reviews' ),
						'description' => esc_html__( 'Add review criteria to enable multiple ratings per review.', 'hivepress-reviews' ),
						'type'        => 'repeater',
						'_order'      => 60,

						'fields'      => [
							'name' => [
								'placeholder' => hivepress()->translator->get_string( 'name' ),
								'type'        => 'text',
								'max_length'  => 256,
								'required'    => true,
								'_order'      => 10,
							],
						],
					],
				],
			],
		],
	],
];
