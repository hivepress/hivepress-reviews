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
		'forms'         => [
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
		'templates'     => [
			'archive_review'  => [
				'path'  => 'review/content-archive',

				'areas' => [
					'summary' => [
						'rating' => [
							'path'  => 'review/parts/rating',
							'order' => 10,
						],

						'date'   => [
							'path'  => 'review/parts/date',
							'order' => 20,
						],
					],

					'sidebar' => [
						'image' => [
							'path'  => 'review/parts/image',
							'order' => 10,
						],
					],

					'content' => [
						'author'  => [
							'path'  => 'review/parts/author',
							'order' => 10,
						],

						'summary' => [
							'path'  => 'review/content-archive/summary',
							'order' => 20,
						],

						'text'    => [
							'path'  => 'review/parts/text',
							'order' => 30,
						],
					],
				],
			],

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

					'reviews'    => [
						'title' => [
							'path'  => 'listing/content-single/parts/reviews-title',
							'order' => 10,
						],

						'loop'  => [
							'path'  => 'review/parts/loop',
							'order' => 20,
						],
					],

					'content'    => [
						'reviews' => [
							'path'  => 'listing/content-single/reviews',
							'order' => 40,
						],
					],
				],
			],
		],

		// Styles.
		'styles'        => [
			'frontend' => [
				'handle'  => 'hp-reviews',
				'src'     => HP_REVIEWS_URL . '/assets/css/frontend.min.css',
				'version' => HP_REVIEWS_VERSION,
				'editor'  => true,
			],
		],

		// Scripts.
		'scripts'       => [
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

		// Editor blocks.
		'editor_blocks' => [
			'listing_reviews' => [
				'title'    => esc_html__( 'Reviews', 'hivepress-reviews' ),
				'category' => 'widgets',
				'fields'   => [
					'number' => [
						'name'    => esc_html__( 'Number', 'hivepress-reviews' ),
						'type'    => 'number',
						'default' => 3,
						'order'   => 10,
					],

					'rating' => [
						'name'    => esc_html__( 'Rating', 'hivepress-reviews' ),
						'type'    => 'select',
						'options' => [
							'' => '—',
							1  => '1+',
							2  => '2+',
							3  => '3+',
							4  => '4+',
						],
						'order'   => 20,
					],
				],
			],
		],
	],
];
