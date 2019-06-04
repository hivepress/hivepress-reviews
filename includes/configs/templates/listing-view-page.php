<?php
/**
 * Listing view page template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'blocks' => [
		'page_container' => [
			'blocks' => [
				'page_columns' => [
					'blocks' => [
						'page_content' => [
							'blocks' => [
								'listing_details_primary' => [
									'blocks' => [
										'listing_rating' => [
											'type'     => 'element',
											'filepath' => 'listing/view/rating',
											'order'    => 30,
										],
									],
								],

								'reviews'                 => [
									'type'  => 'reviews',
									'order' => 100,
								],
							],
						],

						'page_sidebar' => [
							'blocks' => [
								'listing_actions_primary' => [
									'blocks' => [
										'review_submit_modal' => [
											'type'    => 'modal',
											'caption' => esc_html__( 'Write a Review', 'hivepress-reviews' ),

											'blocks'  => [
												'review_submit_form' => [
													'type' => 'review_submit_form',
													'order' => 10,

													'attributes' => [
														'class' => [ 'hp-form--narrow' ],
													],
												],
											],
										],

										'review_submit_button' => [
											'type'     => 'element',
											'filepath' => 'review/submit/submit-link',
											'order'    => 15,
										],
									],
								],
							],
						],
					],
				],
			],
		],
	],
];
