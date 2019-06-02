<?php
/**
 * Review component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;

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

		// Delete reviews.
		add_action( 'delete_user', [ $this, 'delete_reviews' ] );

		if ( ! is_admin() ) {

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
