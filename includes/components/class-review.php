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
		add_filter( 'hivepress/v1/attributes', [ $this, 'add_attributes' ] );

		// Add model fields.
		add_filter( 'hivepress/v1/models/listing', [ $this, 'add_model_fields' ] );

		// Remove edit fields.
		add_filter( 'hivepress/v1/meta_boxes/listing_attributes', [ $this, 'remove_edit_fields' ] );

		// Update rating.
		add_action( 'wp_insert_comment', [ $this, 'update_rating' ] );
		add_action( 'wp_set_comment_status', [ $this, 'update_rating' ] );
		add_action( 'delete_comment', [ $this, 'update_rating' ] );

		// Delete reviews.
		add_action( 'delete_user', [ $this, 'delete_reviews' ] );

		// todo
		add_filter( 'hivepress/v1/templates/listing_view_block', [ $this, 'todo1' ] );
		add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'todo2' ] );
	}

	// todo
	public function todo1( $template ) {
		return hp\merge_trees(
			$template,
			[
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
				],
			],
			'blocks'
		);
	}

	// todo
	public function todo2( $template ) {
		return hp\merge_trees(
			$template,
			[
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

					'listing_actions_primary' => [
						'blocks' => [
							'review_submit_modal'  => [
								'type'    => 'modal',
								'caption' => esc_html__( 'Write a Review', 'hivepress-reviews' ),

								'blocks'  => [
									'review_submit_form' => [
										'type'       => 'review_submit_form',
										'order'      => 10,

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

					'page_content'            => [
						'blocks' => [
							'reviews' => [
								'type'  => 'reviews',
								'order' => 100,
							],
						],
					],
				],
			],
			'blocks'
		);
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
					'model'        => 'listing',
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

		return null;
	}

	/**
	 * Updates rating.
	 *
	 * @param int $review_id Review ID.
	 */
	public function update_rating( $review_id ) {

		// Get review.
		$review = Models\Review::get( $review_id );

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
				} else {
					delete_post_meta( $vendor_id, 'hp_rating' );
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
}
