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
	 * Add container?
	 *
	 * @var bool
	 */
	protected $wrap = true;

	/**
	 * Container attributes.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Class initializer.
	 *
	 * @param array $meta Block meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'    => hivepress()->translator->get_string( 'reviews' ),

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
	 * Bootstraps block properties.
	 */
	protected function boot() {

		// Set attributes.
		$this->attributes = hp\merge_arrays(
			$this->attributes,
			[
				'class' => [ 'hp-reviews', 'hp-block', 'hp-grid' ],
			]
		);

		parent::boot();
	}

	/**
	 * Renders block HTML.
	 *
	 * @return string
	 */
	public function render() {
		$output = '';

		// Check number.
		if ( ! $this->number ) {
			return $output;
		}

		// Get column width.
		$column_width = hp\get_column_width( $this->columns );

		// Get listing.
		$listing = $this->get_context( 'listing' );

		// Get review query.
		$query = $this->get_context( 'review_query' );

		if ( ! $query ) {

			// Set query.
			$query = Models\Review::query()->filter(
				[
					'approved' => true,
					'parent'   => null,
				]
			);

			// Set order.
			if ( 'rating' === $this->order ) {
				$query->order( [ 'rating' => 'desc' ] );
			} else {
				$query->order( [ 'created_date' => 'desc' ] );
			}

			if ( isset( $this->context['reviews'] ) ) {

				// Set listing.
				$query->filter( [ 'listing' => $listing->get_id() ] );

				// Set max page.
				$max_page = ceil( $query->get_count() / $this->number );
			} else {
				$query->filter( [ 'listing__not_in' => [ 0 ] ] );
			}

			$query->limit( $this->number );
		}

		// Get review IDs.
		$review_ids = [];

		if ( ! isset( $this->context['reviews'] ) ) {
			$review_ids = hivepress()->cache->get_cache( array_merge( $query->get_args(), [ 'fields' => 'ids' ] ), 'models/review' );

			if ( is_array( $review_ids ) ) {

				// Set query.
				$query = Models\Review::query()->filter(
					[
						'approved' => true,
						'id__in'   => $review_ids,
					]
				)->order( 'id__in' )
				->limit( count( $review_ids ) );
			}
		}

		// Get reviews.
		$reviews = [];

		foreach ( $query->get() as $review ) {
			$reviews[] = $review;

			if ( isset( $this->context['reviews'] ) && get_option( 'hp_review_allow_replies' ) ) {
				$replies = Models\Review::query()->filter(
					[
						'approved' => true,
						'parent'   => $review->get_id(),
					]
				)->order( [ 'created_date' => 'asc' ] )
				->get()
				->serialize();

				foreach ( $replies as $reply ) {
					$reviews[] = $reply;
				}
			}
		}

		// Cache review IDs.
		if ( is_null( $review_ids ) && $query->count() <= 1000 ) {
			hivepress()->cache->set_cache( array_merge( $query->get_args(), [ 'fields' => 'ids' ] ), 'models/review', $query->get_ids() );
		}

		// Render reviews.
		foreach ( $reviews as $review_index => $review ) {

			// Get class.
			$class = 'hp-grid__item hp-col-sm-' . $column_width . ' hp-col-xs-12';

			if ( $review->get_parent__id() ) {
				$class = 'hp-review__reply';
			}

			$output .= '<div class="' . esc_attr( $class ) . '">';

			// Render review.
			$output .= ( new Template(
				[
					'template' => 'review_view_block',

					'context'  => [
						'review'  => $review,
						'listing' => $listing,
					],
				]
			) )->render();

			// Wrap review.
			$next_review = hp\get_array_value( $reviews, $review_index + 1 );

			if ( $review->get_parent__id() || ! $next_review || ! $next_review->get_parent__id() ) {
				$output .= '</div>';
			}

			if ( $review->get_parent__id() && ( ! $next_review || ! $next_review->get_parent__id() ) ) {
				$output .= '</div>';
			}
		}

		if ( $output && $this->wrap ) {

			// Add wrapper.
			$output = '<div ' . hp\html_attributes( $this->attributes ) . '><div class="hp-row" data-block="' . esc_attr( $this->name ) . '">' . $output . '</div></div>';

			if ( isset( $max_page ) && $max_page > 1 ) {

				// Add pagination.
				$output .= '<div class="hp-pagination"><button class="button" data-render="' . hp\esc_json(
					wp_json_encode(
						[
							'block' => $this->name,
							'type'  => 'append',
							'pages' => $max_page,

							'url'   => hivepress()->router->get_url(
								'reviews_resource',
								[
									'listing'  => $listing->get_id(),
									'_columns' => $this->columns,
								]
							),
						]
					)
				) . '">' . esc_html( hivepress()->translator->get_string( 'load_more' ) ) . '</button></div>';
			}
		}

		return $output;
	}
}
