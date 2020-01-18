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
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Get reviews.
		$reviews = [];

		$listing = $this->get_context( 'listing' );

		if ( hp\is_class_instance( $listing, '\HivePress\Models\Listing' ) ) {
			$reviews = Models\Review::query()->filter(
				[
					'listing'  => $listing->get_id(),
					'approved' => true,
				]
			)
			->order( [ 'created_date' => 'desc' ] )
			->get()
			->serialize();
		}

		// Render reviews.
		if ( $reviews ) {
			$output .= '<div class="hp-grid">';

			foreach ( $reviews as $review ) {
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

			$output .= '</div>';
		}

		return $output;
	}
}
