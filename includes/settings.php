<?php
/**
 * Contains plugin settings.
 *
 * @package HivePress\Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings = [

	// Listing component.
	'listing' => [

		// Post types.
		'post_types' => [
			'listing' => [
				'supports' => [ 'comments' ],
			],
		],
	],

	// Review component.
	'review'  => [

		// Forms.
		'forms'     => [
			'submit' => [
				'name'            => esc_html__( 'Submit Review', 'hivepress-reviews' ),
				'capability'      => 'read',
				'captcha'         => false,
				'success_message' => esc_html__( 'Your review has been submitted.', 'hivepress-reviews' ),

				'fields'          => [
					'rating'  => [
						'name'     => esc_html__( 'Rating', 'hivepress-reviews' ),
						'type'     => 'rating',
						'required' => true,
						'order'    => 10,
					],

					'review'  => [
						'name'       => esc_html__( 'Review', 'hivepress-reviews' ),
						'type'       => 'textarea',
						'max_length' => 2048,
						'required'   => true,
						'order'      => 20,
					],

					'post_id' => [
						'type'     => 'hidden',
						'required' => true,
					],
				],

				'submit_button'   => [
					'name' => esc_html__( 'Submit Review', 'hivepress-reviews' ),
				],
			],
		],

		// Templates.
		'templates' => [
			'archive_listing' => [
				'areas' => [
					'properties' => [
						'rating' => [
							'path'  => 'listing/parts/rating',
							'order' => 20,
						],
					],
				],
			],

			'single_listing'  => [
				'areas' => [
					'properties' => [
						'rating' => [
							'path'  => 'listing/parts/rating',
							'order' => 20,
						],
					],

					'actions'    => [
						'review' => [
							'path'  => 'review/parts/submit-button',
							'order' => 15,
						],
					],

					'content'    => [
						'loop' => [
							'path'  => 'review/parts/loop',
							'order' => 40,
						],
					],
				],
			],
		],

		// Styles.
		'styles'    => [
			'frontend' => [
				'handle'  => 'hp-reviews',
				'src'     => HP_REVIEWS_URL . '/assets/css/frontend.min.css',
				'version' => HP_REVIEWS_VERSION,
			],
		],

		// Scripts.
		'scripts'   => [
			'raty'     => [
				'handle'  => 'raty',
				'src'     => HP_REVIEWS_URL . '/assets/js/jquery.raty.min.js',
				'version' => HP_REVIEWS_VERSION,
			],

			'frontend' => [
				'handle'  => 'hp-reviews',
				'src'     => HP_REVIEWS_URL . '/assets/js/frontend.min.js',
				'deps'    => [ 'hp-core', 'raty' ],
				'version' => HP_REVIEWS_VERSION,
			],
		],
	],
];
