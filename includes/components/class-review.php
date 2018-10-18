<?php
namespace HivePress\Reviews;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Manages reviews.
 *
 * @class Review
 */
class Review extends \HivePress\Component {

	/**
	 * Class constructor.
	 *
	 * @param array $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );

		// Update rating.
		add_action( 'comment_post', [ $this, 'update_rating' ] );
		add_action( 'wp_set_comment_status', [ $this, 'update_rating' ] );
		add_action( 'delete_comment', [ $this, 'update_rating' ] );
	}

	/**
	 * Updates rating.
	 *
	 * @param int $review_id
	 */
	public function update_rating( $review_id ) {

		// Get current review.
		$current_review = get_comment( $review_id );

		if ( ! is_null( $current_review ) && 'comment' === $current_review->comment_type && 'hp_listing' === $current_review->post_type ) {
			$rating_count = 0;
			$rating_value = 0;

			// Get all reviews.
			$reviews = get_comments(
				[
					'type'    => 'comment',
					'status'  => 'approve',
					'post_id' => $current_review->comment_post_ID,
				]
			);

			// Calculate rating.
			foreach ( $reviews as $review ) {
				$rating = absint( get_comment_meta( $review->comment_ID, 'hp_rating', true ) );

				if ( $rating < 1 ) {
					$rating = 1;
				} elseif ( $rating > 5 ) {
					$rating = 5;
				}

				$rating_count++;
				$rating_value += $rating;
			}

			if ( $rating_count > 0 ) {
				$rating_value = round( $rating_value / $rating_count, 1 );
			}

			// Update rating.
			update_post_meta( $current_review->comment_post_ID, 'hp_rating_count', $rating_count );
			update_post_meta( $current_review->comment_post_ID, 'hp_rating_value', $rating_value );
		}
	}
}
