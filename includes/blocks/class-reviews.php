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
	 * Columns number.
	 *
	 * @var int
	 */
	protected $columns;

	/**
	 * Reviews number.
	 *
	 * @var int
	 */
	protected $number;

	/**
	 * Reviews order.
	 *
	 * @var string
	 */
	protected $order;

	/**
	 * Class initializer.
	 *
	 * @param array $meta Block meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'    => esc_html__( 'Reviews', 'hivepress-reviews' ),

				'settings' => [
					'columns' => [
						'label'    => hivepress()->translator->get_string( 'columns_number' ),
						'type'     => 'select',
						'default'  => 2,
						'required' => true,
						'_order'   => 10,

						'options'  => [
							1 => '1',
							2 => '2',
							3 => '3',
						],
					],

					'number'  => [
						'label'     => hivepress()->translator->get_string( 'items_number' ),
						'type'      => 'number',
						'min_value' => 1,
						'default'   => 2,
						'required'  => true,
						'_order'    => 20,
					],

					'order'   => [
						'label'    => hivepress()->translator->get_string( 'sort_order' ),
						'type'     => 'select',
						'required' => true,
						'_order'   => 30,

						'options'  => [
							'created_date' => hivepress()->translator->get_string( 'by_date_added' ),
							'rating'       => esc_html_x( 'Rating', 'sort order', 'hivepress-reviews' ),
						],
					],
				],
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		if ( $this->number ) {

			// Get column width.
			$columns      = absint( $this->columns );
			$column_width = 12;

			if ( $columns > 0 && $columns <= 12 ) {
				$column_width = round( $column_width / $columns );
			}

			// Get review query.
			$query = $this->get_context( 'review_query' );

			// Get IDs.
			$review_ids = [];

			if ( empty( $query ) ) {
				$query = Models\Review::query()->filter(
					[
						'approved' => true,
					]
				)->limit( $this->number );

				// Set order.
				if ( 'rating' === $this->order ) {
					$query->order( [ 'rating' => 'desc' ] );
				} else {
					$query->order( [ 'created_date' => 'desc' ] );
				}

				// Get cached IDs.
				$review_ids = hivepress()->cache->get_cache( array_merge( $query->get_args(), [ 'fields' => 'ids' ] ), 'models/review' );

				if ( is_array( $review_ids ) ) {
					$query = Models\Review::query()->filter(
						[
							'approved' => true,
							'id__in'   => $review_ids,
						]
					)->order( 'id__in' )
					->limit( count( $review_ids ) );
				}
			}

			// Query reviews.
			$reviews = $query->get();

			// Cache IDs.
			if ( is_null( $review_ids ) && $reviews->count() <= 1000 ) {
				hivepress()->cache->set_cache( array_merge( $query->get_args(), [ 'fields' => 'ids' ] ), 'models/review', $reviews->get_ids() );
			}

			// Render reviews.
			if ( $reviews->count() ) {
				$output  = '<div class="hp-reviews hp-grid hp-block">';
				$output .= '<div class="hp-row">';

				foreach ( $reviews as $review ) {
					$output .= '<div class="hp-grid__item hp-col-sm-' . esc_attr( $column_width ) . ' hp-col-xs-12">';

					// Render review.
					$output .= ( new Template(
						[
							'template' => 'review_view_block',

							'context'  => [
								'review'  => $review,
								'listing' => $this->get_context( 'listing' ),
							],
						]
					) )->render();

					$output .= '</div>';
				}

				$output .= '</div>';
				$output .= '</div>';
			}
		}

		return $output;
	}
}
