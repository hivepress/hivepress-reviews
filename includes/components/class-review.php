<?php
/**
 * Review component.
 *
 * @package HivePress\Components
 */

namespace HivePress\Components;

use HivePress\Helpers as hp;
use HivePress\Models;
use HivePress\Emails;

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
		add_filter( 'hivepress/v1/models/vendor/attributes', [ $this, 'add_attributes' ] );

		// Add model fields.
		add_filter( 'hivepress/v1/models/listing', [ $this, 'add_model_fields' ] );
		add_filter( 'hivepress/v1/models/vendor', [ $this, 'add_model_fields' ] );

		// Update rating.
		add_action( 'hivepress/v1/models/review/create', [ $this, 'update_rating' ], 10, 2 );
		add_action( 'hivepress/v1/models/review/update_status', [ $this, 'update_rating' ], 10, 2 );
		add_action( 'hivepress/v1/models/review/delete', [ $this, 'update_rating' ], 10, 2 );

		// Validate review.
		add_filter( 'hivepress/v1/models/review/errors', [ $this, 'validate_review' ], 10, 2 );

		// Delete reviews.
		add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_reviews' ] );

		// Alter menus.
		add_filter( 'hivepress/v1/menus/listing_manage/items', [ $this, 'alter_listing_manage_menu' ], 100, 2 );

		// Alter templates.
		add_filter( 'hivepress/v1/templates/listing_view_block', [ $this, 'alter_listing_view_template' ] );
		add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_template' ] );
		add_filter( 'hivepress/v1/templates/listing_view_page', [ $this, 'alter_listing_view_page' ] );

		add_filter( 'hivepress/v1/templates/vendor_view_block', [ $this, 'alter_vendor_view_template' ] );
		add_filter( 'hivepress/v1/templates/vendor_view_page', [ $this, 'alter_vendor_view_template' ] );

		add_filter( 'hivepress/v1/templates/review_view_block', [ $this, 'alter_review_view_block' ] );

		// Send request feedback email.
		add_action( 'hivepress/v1/models/booking/update_status', [ $this, 'send_request_feedback_email' ], 10, 4 );
		add_action( 'hivepress/v1/models/order/update_status', [ $this, 'send_request_feedback_email' ], 10, 4 );

		parent::__construct( $args );
	}

	/**
	 * Send request feedback email.
	 *
	 * @param int    $model_id Model ID.
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $model Model.
	 */
	public function send_request_feedback_email( $model_id, $new_status, $old_status, $model ) {

		// Get model type.
		$type = $model::_get_meta( 'name' );

		if ( ! $type || ! in_array( $new_status, [ 'wc-completed', 'publish' ] ) ) {
			return;
		}

		// Get listing url.
		$listing_url = null;

		// Get user.
		$user = null;

		// Get model id.
		$model_number = $model->get_id();

		if ( 'booking' === $type ) {
			if ( hivepress()->get_version( 'marketplace' ) ) {

				// Get order.
				$order = hp\get_first_array_value(
					wc_get_orders(
						[
							'limit'      => 1,
							'meta_key'   => 'hp_booking',
							'meta_value' => $model_number,
						]
					)
				);

				if ( $order && $order->get_status() !== 'completed' ) {
					return;
				}
			}

			// Set user.
			$user = $model->get_user();

			if ( ! $user ) {
				return;
			}

			// Get listing.
			$listing = $model->get_listing();

			if ( ! $listing ) {
				return;
			}

			// Set listing url.
			$listing_url = get_permalink( $listing->get_id() );

		} else {

			// Get order.
			$order = wc_get_order( $model->get_id() );

			if ( ! $order ) {
				return;
			}

			// Get item.
			$item = hp\get_first_array_value( $order->get_items() );

			if ( ! $item || ! $item->get_product_id() ) {
				return;
			}

			// Get listing.
			$listing = hivepress()->marketplace->get_product_listing( $item->get_product() );

			if ( ! $listing ) {
				return;
			}

			if ( $order->get_meta( 'hp_booking' ) ) {

				// Set model type.
				$type = 'booking';

				// Set model id.
				$model_number = absint( $order->get_meta( 'hp_booking' ) );
			}

			// Set listing url.
			$listing_url = get_permalink( $listing->get_id() );

			// Set user.
			$user = $model->get_buyer();
		}

		if ( ! $listing_url ) {
			return;
		}

		// Send email.
		( new Emails\Request_Feedback(
			[
				'recipient' => $user->get_email(),

				'tokens'    => [
					'listing_url'  => $listing_url,
					'user_name'    => $user->get_username(),
					'model_type'   => $type,
					'model_number' => $model_number,
					'model'        => $model,
					'user'         => $user,
				],
			]
		) )->send();
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
					WHERE comment_type = %s AND comment_approved = %s AND comment_parent = 0 AND comment_post_ID IN ( {$placeholder} );",
					array_merge( [ 'hp_review', '1' ], (array) $listing_ids )
				),
				ARRAY_A
			);

			// Get rating.
			$rating = array_map(
				function( $value ) {
					return empty( $value ) ? null : floatval( $value );
				},
				hp\get_first_array_value( $results )
			);
		}

		return $rating;
	}

	/**
	 * Adds attributes.
	 *
	 * @param array $attributes Attributes.
	 * @return array
	 */
	public function add_attributes( $attributes ) {
		$attributes['rating'] = [
			'protected'  => true,
			'sortable'   => true,

			'edit_field' => [
				'label' => esc_html__( 'Rating', 'hivepress-reviews' ),
				'type'  => 'rating',
			],
		];

		return $attributes;
	}

	/**
	 * Adds model fields.
	 *
	 * @param array $model Model arguments.
	 * @return array
	 */
	public function add_model_fields( $model ) {
		$model['fields'] = array_merge(
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
	 * Updates rating.
	 *
	 * @param int    $review_id Review ID.
	 * @param object $review Review object.
	 */
	public function update_rating( $review_id, $review ) {

		// Get review.
		if ( ! is_object( $review ) ) {
			$review = Models\Review::query()->get_by_id( $review_id );
		}

		// Get listing.
		$listing = $review->get_listing();

		if ( ! $listing ) {
			return;
		}

		// Get listing rating.
		$listing_rating = $this->get_rating( $listing->get_id() );

		// Update listing rating.
		$listing->fill(
			[
				'rating'       => hp\get_first_array_value( $listing_rating ),
				'rating_count' => hp\get_last_array_value( $listing_rating ),
			]
		)->save( [ 'rating', 'rating_count' ] );

		// Get vendor.
		$vendor = $listing->get_vendor();

		if ( ! $vendor ) {
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
				'rating'       => hp\get_first_array_value( $vendor_rating ),
				'rating_count' => hp\get_last_array_value( $vendor_rating ),
			]
		)->save( [ 'rating', 'rating_count' ] );
	}

	/**
	 * Validates review.
	 *
	 * @param array  $errors Error messages.
	 * @param object $review Review object.
	 * @return array
	 */
	public function validate_review( $errors, $review ) {
		if ( ! $review->get_id() && ! $review->get_parent__id() && empty( $errors ) && ! get_option( 'hp_review_allow_multiple' ) ) {

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

			if ( $listing && $listing->get_rating_count() ) {
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
								'_label' => esc_html__( 'Rating', 'hivepress-reviews' ),
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
								'type'   => 'section',
								'title'  => hivepress()->translator->get_string( 'reviews' ),
								'_order' => 100,

								'blocks' => [
									'listing_reviews' => [
										'type'      => 'related_reviews',
										'_label'    => hivepress()->translator->get_string( 'reviews' ) . ' (' . hivepress()->translator->get_string( 'related_plural' ) . ')',
										'_settings' => [ 'columns' ],
										'_order'    => 10,
									],
								],
							],
						],
					],

					'listing_actions_primary' => [
						'blocks' => [
							'review_submit_modal' => [
								'type'        => 'modal',
								'title'       => esc_html__( 'Write a Review', 'hivepress-reviews' ),
								'_capability' => 'read',
								'_order'      => 5,

								'blocks'      => [
									'review_submit_form' => [
										'type'   => 'review_submit_form',
										'_order' => 10,
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
								'_label' => esc_html__( 'Rating', 'hivepress-reviews' ),
								'_order' => 20,
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Alters review view block.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_review_view_block( $template ) {
		if ( get_option( 'hp_review_allow_replies' ) ) {
			$template = hp\merge_trees(
				$template,
				[
					'blocks' => [
						'review_content' => [
							'blocks' => [
								'review_reply_modal' => [
									'type'        => 'modal',
									'model'       => 'review',
									'title'       => esc_html__( 'Reply to Review', 'hivepress-reviews' ),
									'_capability' => 'edit_posts',
									'_order'      => 5,

									'blocks'      => [
										'review_reply_form' => [
											'type'   => 'review_reply_form',
											'_order' => 10,
										],
									],
								],

								'review_reply_link'  => [
									'type'   => 'part',
									'path'   => 'review/view/review-reply-link',
									'_order' => 30,
								],
							],
						],
					],
				]
			);
		}

		return $template;
	}
}
