<?php
/**
 * Reviews block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Reviews block class.
 *
 * @class Reviews
 */
class Reviews extends Block {

	/**
	 * Block type.
	 *
	 * @var string
	 */
	protected static $type;

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Get review IDs.
		$review_ids = get_comments(
			[
				'type'    => 'hp_review',
				'status'  => 'approve',
				'post_id' => get_the_ID(),
				'fields'  => 'ids',
			]
		);

		// Render reviews.
		if ( ! empty( $review_ids ) ) {
			$output .= '<div class="hp-grid">';

			foreach ( $review_ids as $review_id ) {

				// Get review.
				$review = Models\Review::get( $review_id );

				if ( ! is_null( $review ) ) {
					$output .= '<div class="hp-grid__item">';

					// Render review.
					$output .= ( new Template(
						[
							'template' => 'review_view_block',

							'context'  => [
								'review' => $review,
							],
						]
					) )->render();

					$output .= '</div>';
				}
			}

			$output .= '</div>';
		}

		return $output;
	}
}
