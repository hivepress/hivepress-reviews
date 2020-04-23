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
final class Review extends Component {

	/**
	 * Class constructor.
	 *
	 * @param array $args Component arguments.
	 */
	public function __construct( $args = [] ) {

		// Add attributes.
		add_filter( 'hivepress/v1/models/listing/attributes', [ $this, 'add_attributes' ] );

		// Add model fields.
		add_filter( 'hivepress/v1/models/listing', [ $this, 'add_model_fields' ] );
		add_filter( 'hivepress/v1/models/vendor', [ $this, 'add_model_fields' ] );

		// Update rating.
		add_action( 'hivepress/v1/models/review/create', [ $this, 'update_rating' ] );
		add_action( 'hivepress/v1/models/review/update_status', [ $this, 'update_rating' ] );
		add_action( 'hivepress/v1/models/review/delete', [ $this, 'update_rating' ] );

		// Validate review.
		add_filter( 'hivepress/v1/models/review/errors', [ $this, 'validate_review' ], 10, 2 );

		// Delete reviews.
		add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_reviews' ] );

		if ( ! is_admin() ) {

			// Alter menus.
			add_filter( 'hivepress/v1/menus/listing_manage/items', [ $this, 'alter_listing_manage_menu' ], 100, 2 );

			// Alter templates.
			add_filter( 'hivepress/v1/templates/listing_view_block', [ $this, 'alter_listing_view_template' ] );
			add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_template' ] );
			add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_page' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_block', [ $this, 'alter_vendor_view_template' ] );
			add_filter( 'hivepress/v1/templates/vendor_view_page', [ $this, 'alter_vendor_view_template' ] );
		}

		parent::__construct( $args );
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
					'label'      => esc_html__( 'Rating', 'hivepress-reviews' ),
					'protected'  => true,
					'sortable'   => true,

					'edit_field' => [
						'label' => esc_html__( 'Rating', 'hivepress-reviews' ),
						'type'  => 'rating',
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
		$model['fields'] = array_merge(
			[
				'rating' => [
					'type'      => 'rating',
					'_external' => true,
				],
			],
			$model['fields'],
			[
				'rating_count' => [
					'type'      => 'number',
					'min_value' => 0,
					'_external' => true,
				],
			]
		);

		return $model;
	}

	/**
	 * Gets rating.
	 *
	 * @param array $listing_ids Listing IDs.
	 * @return array
	 */
	protected function get_rating( $listing_ids ) {
		global $wpdb;

		$rating = [ null, null ];

		if ( $listing_ids ) {

			// Get results.
			$placeholder = implode( ', ', array_fill( 0, count( (array) $listing_ids ), '%d' ) );

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT AVG( comment_karma ), COUNT( * ) FROM {$wpdb->comments}
					WHERE comment_type = %s AND comment_approved = %s AND comment_post_ID IN ( {$placeholder} );",
					array_merge( [ 'hp_review', '1' ], (array) $listing_ids )
				),
				ARRAY_A
			);

			// Get rating.
			$rating = array_map(
				function( $value ) {
					return empty( $value ) ? null : floatval( $value );
				},
				reset( $results )
			);
		}

		return $rating;
	}

	/**
	 * Updates rating.
	 *
	 * @param int $review_id Review ID.
	 */
	public function update_rating( $review_id ) {

		// Get review.
		$review = Models\Review::query()->get_by_id( $review_id );

		// Get listing.
		$listing = $review->get_listing();

		if ( empty( $listing ) ) {
			return;
		}

		// Get listing rating.
		$listing_rating = $this->get_rating( $listing->get_id() );

		// Update listing rating.
		$listing->fill(
			[
				'rating'       => reset( $listing_rating ),
				'rating_count' => end( $listing_rating ),
			]
		)->save();

		// Get vendor.
		$vendor = $listing->get_vendor();

		if ( empty( $vendor ) ) {
			return;
		}

		// Get vendor rating.
		$vendor_rating = $this->get_rating(
			Models\Listing::query()->filter(
				[
					'status' => 'publish',
					'vendor' => $vendor->get_id(),
				]
			)->get_ids()
		);

		// Update vendor rating.
		$vendor->fill(
			[
				'rating'       => reset( $vendor_rating ),
				'rating_count' => end( $vendor_rating ),
			]
		)->save();
	}

	/**
	 * Validates review.
	 *
	 * @param array  $errors Error messages.
	 * @param object $review Review object.
	 * @return array
	 */
	public function validate_review( $errors, $review ) {
		if ( ! $review->get_id() && empty( $errors ) && ! get_option( 'hp_review_allow_multiple' ) ) {

			// Get review ID.
			$review_id = Models\Review::query()->filter(
				[
					'author'  => $review->get_author__id(),
					'listing' => $review->get_listing__id(),
				]
			)->get_first_id();

			// Add error.
			if ( $review_id ) {
				$errors[] = esc_html__( 'You\'ve already submitted a review.', 'hivepress-reviews' );
			}
		}

		return $errors;
	}

	/**
	 * Deletes reviews.
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_reviews( $user_id ) {
		Models\Review::query()->filter(
			[
				'author' => $user_id,
			]
		)->delete();
	}

	/**
	 * Alters listing manage menu.
	 *
	 * @param array  $items Menu items.
	 * @param object $menu Menu object.
	 * @return array
	 */
	public function alter_listing_manage_menu( $items, $menu ) {
		if ( isset( $items['listing_view'] ) ) {

			// Get listing.
			$listing = $menu->get_context( 'listing' );

			if ( hp\is_class_instance( $listing, '\HivePress\Models\Listing' ) && $listing->get_rating_count() ) {
				$items['listing_reviews'] = [
					'label'  => hivepress()->translator->get_string( 'reviews' ),
					'url'    => $items['listing_view']['url'] . '#reviews',
					'_order' => 20,
				];
			}
		}

		return $items;
	}

	/**
	 * Alters listing view template.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_listing_view_template( $template ) {
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
		return hp\merge_trees(
			$template,
			[
				'blocks' => [
					'page_content'            => [
						'blocks' => [
							'reviews_container' => [
								'type'       => 'section',
								'title'      => hivepress()->translator->get_string( 'reviews' ),
								'_order'     => 100,

								'attributes' => [
									'id' => 'reviews',
								],

								'blocks'     => [
									'reviews' => [
										'type'   => 'related_reviews',
										'_order' => 10,
									],
								],
							],
						],
					],

					'listing_actions_primary' => [
						'blocks' => [
							'review_submit_modal' => [
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

							'review_submit_link'  => [
								'type'   => 'part',
								'path'   => 'listing/view/page/review-submit-link',
								'_order' => 30,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters vendor view template.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_vendor_view_template( $template ) {
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
