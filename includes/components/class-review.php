<?php
/**
 * Review component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review component class.
 *
 * @class Review
 */
final class Review {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Add attributes.
		add_filter( 'hivepress/v1/models/listing/attributes', [ $this, 'add_attributes' ] );

		// Add model fields.
		add_filter( 'hivepress/v1/models/listing', [ $this, 'add_model_fields' ] );
		add_filter( 'hivepress/v1/models/vendor', [ $this, 'add_model_fields' ] );

		// Remove edit fields.
		add_filter( 'hivepress/v1/meta_boxes/listing_attributes', [ $this, 'remove_edit_fields' ] );

		// Set rating.
		add_action( 'hivepress/v1/models/listing/create', [ $this, 'set_rating' ] );
		add_action( 'hivepress/v1/models/vendor/create', [ $this, 'set_rating' ] );

		// Update rating.
		add_action( 'hivepress/v1/models/review/create', [ $this, 'update_rating' ] );
		add_action( 'hivepress/v1/models/review/update_status', [ $this, 'update_rating' ] );
		add_action( 'hivepress/v1/models/review/delete', [ $this, 'update_rating' ] );

		// Delete reviews.
		add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_reviews' ] );

		if ( ! is_admin() ) {

			// Alter templates.
			add_filter( 'hivepress/v1/templates/listing_view_block', [ $this, 'alter_listing_view_block' ] );
			add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_page' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_block', [ $this, 'alter_vendor_view_block' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_page', [ $this, 'alter_vendor_view_page' ] );
		}
	}

	/**
	 * Adds attributes.
	 *
	 * @param array $attributes Attributes.
	 * @return array
	 */
	public function add_attributes( $attributes ) {
		return array_merge(
			$attributes,
			[
				'rating' => [
					'sortable'     => true,

					'edit_field'   => [
						'type' => 'rating',
					],

					'search_field' => [
						'label' => esc_html__( 'Rating', 'hivepress-reviews' ),
					],
				],
			]
		);
	}

	/**
	 * Adds model fields.
	 *
	 * @param array $model Model arguments.
	 * @return array
	 */
	public function add_model_fields( $model ) {
		if ( ! isset( $model['fields']['rating'] ) ) {
			$model['fields']['rating'] = [
				'type' => 'rating',
			];
		}

		$model['fields']['rating_count'] = [
			'type'      => 'number',
			'min_value' => 0,
		];

		return $model;
	}

	/**
	 * Removes edit fields.
	 *
	 * @param array $form Form arguments.
	 * @return array
	 */
	public function remove_edit_fields( $form ) {
		unset( $form['fields']['rating'] );

		return $form;
	}

	/**
	 * Gets rating.
	 *
	 * @param array $args Query arguments.
	 * @return array
	 */
	protected function get_rating( $args ) {

		// Get review IDs.
		$review_ids = get_comments(
			array_merge(
				[
					'type'   => 'hp_review',
					'status' => 'approve',
					'fields' => 'ids',
				],
				$args
			)
		);

		// Get rating count.
		$rating_count = count( $review_ids );
		$rating_value = null;

		// Calculate rating.
		foreach ( $review_ids as $review_id ) {
			$rating = absint( get_comment_meta( $review_id, 'hp_rating', true ) );

			if ( $rating < 1 ) {
				$rating = 1;
			} elseif ( $rating > 5 ) {
				$rating = 5;
			}

			$rating_value += $rating;
		}

		if ( $rating_count > 0 ) {
			$rating_value = round( $rating_value / $rating_count, 1 );

			return [ $rating_value, $rating_count ];
		}
	}

	/**
	 * Sets rating.
	 *
	 * @param int $listing_id Listing ID.
	 */
	public function set_rating( $listing_id ) {
		add_post_meta( $listing_id, 'hp_rating', '', true );
	}

	/**
	 * Updates rating.
	 *
	 * @param int $review_id Review ID.
	 */
	public function update_rating( $review_id ) {

		// Get review.
		$review = Models\Review::query()->get_by_id( $review_id );

		if ( ! is_null( $review ) ) {

			// Update listing rating.
			$listing_rating = $this->get_rating( [ 'post_id' => $review->get_listing_id() ] );

			if ( ! is_null( $listing_rating ) ) {
				update_post_meta( $review->get_listing_id(), 'hp_rating', reset( $listing_rating ) );
				update_post_meta( $review->get_listing_id(), 'hp_rating_count', end( $listing_rating ) );
			} else {
				delete_post_meta( $review->get_listing_id(), 'hp_rating' );
				delete_post_meta( $review->get_listing_id(), 'hp_rating_count' );
			}

			// Get vendor ID.
			$vendor_id = wp_get_post_parent_id( $review->get_listing_id() );

			if ( $vendor_id ) {

				// Update vendor rating.
				$vendor_rating = $this->get_rating( [ 'post_parent' => $vendor_id ] );

				if ( ! is_null( $vendor_rating ) ) {
					update_post_meta( $vendor_id, 'hp_rating', reset( $vendor_rating ) );
					update_post_meta( $vendor_id, 'hp_rating_count', end( $vendor_rating ) );
				} else {
					delete_post_meta( $vendor_id, 'hp_rating' );
					delete_post_meta( $vendor_id, 'hp_rating_count' );
				}
			}
		}
	}

	/**
	 * Deletes reviews.
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_reviews( $user_id ) {

		// Get review IDs.
		$review_ids = get_comments(
			[
				'type'    => 'hp_review',
				'user_id' => $user_id,
				'fields'  => 'ids',
			]
		);

		// Delete reviews.
		foreach ( $review_ids as $review_id ) {
			wp_delete_comment( $review_id, true );
		}
	}

	/**
	 * Alters listing view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'listing_details_primary' => [
						'blocks' => [
							'listing_rating' => [
								'type'   => 'part',
								'path'   => 'listing/view/listing-rating',
								'_order' => 30,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters listing view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_view_page( $template ) {
		$blocks = [
			'listing_details_primary' => [
				'blocks' => [
					'listing_rating' => [
						'type'   => 'part',
						'path'   => 'listing/view/listing-rating',
						'_order' => 30,
					],
				],
			],
		];

		// Get review ID.
		$review_id = null;

		if ( is_user_logged_in() ) {
			$review_id = get_comments(
				[
					'type'    => 'hp_review',
					'user_id' => get_current_user_id(),
					'post_id' => get_the_ID(),
					'number'  => 1,
					'fields'  => 'ids',
				]
			);
		}

		if ( empty( $review_id ) ) {
			$blocks = array_merge(
				$blocks,
				[
					'listing_actions_primary' => [
						'blocks' => [
							'review_submit_modal'  => [
								'type'   => 'modal',
								'title'  => esc_html__( 'Write a Review', 'hivepress-reviews' ),

								'blocks' => [
									'review_submit_form' => [
										'type'       => 'review_submit_form',
										'_order'     => 10,

										'attributes' => [
											'class' => [ 'hp-form--narrow' ],
										],
									],
								],
							],

							'review_submit_button' => [
								'type'   => 'part',
								'path'   => 'listing/view/page/review-submit-link',
								'_order' => 20,
							],
						],
					],
				]
			);
		}

		// Get review IDs.
		$review_ids = get_comments(
			[
				'type'    => 'hp_review',
				'status'  => 'approve',
				'post_id' => get_the_ID(),
				'number'  => 1,
				'fields'  => 'ids',
			]
		);

		if ( ! empty( $review_ids ) ) {
			$blocks = array_merge(
				$blocks,
				[
					'page_content' => [
						'blocks' => [
							'reviews_container' => [
								'type'       => 'container',
								'_order'     => 100,

								'attributes' => [
									'class' => [ 'hp-section' ],
								],

								'blocks'     => [
									'reviews_title' => [
										'type'    => 'part',
										'path'    => 'page/section-title',
										'_order'  => 10,

										'context' => [
											'title' => esc_html__( 'Reviews', 'hivepress-reviews' ),
										],
									],

									'reviews'       => [
										'type'   => 'reviews',
										'_order' => 20,
									],
								],
							],
						],
					],
				]
			);
		}

		return hp\merge_trees(
			$template,
			[
				'blocks' => $blocks,
			]
		);
	}

	/**
	 * Alters vendor view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_vendor_view_block( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'vendor_details_primary' => [
						'blocks' => [
							'vendor_rating' => [
								'type'   => 'part',
								'path'   => 'vendor/view/vendor-rating',
								'_order' => 20,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters vendor view page.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_vendor_view_page( $template ) {
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'vendor_details_primary' => [
						'blocks' => [
							'vendor_rating' => [
								'type'   => 'part',
								'path'   => 'vendor/view/vendor-rating',
								'_order' => 20,
							],
						],
					],
				],
			]
		);
	}
}
