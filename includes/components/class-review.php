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

		// Update review status.
		add_action( 'hivepress/v1/models/review/create', [ $this, 'update_review_status' ], 10, 2 );
		add_action( 'hivepress/v1/models/review/update_status', [ $this, 'update_review_status' ], 10, 4 );

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

		add_filter( 'hivepress/v1/templates/review_view_block/blocks', [ $this, 'alter_review_view_blocks' ], 10, 2 );

		parent::__construct( $args );
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
	 * Updates review status.
	 *
	 * @param int    $review_id Review ID.
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $review Review object.
	 */
	public function update_review_status( $review_id, $new_status, $old_status = null, $review = null ) {

		// Get review.
		if ( is_null( $review ) ) {
			$review = $new_status;
		}

		if ( ! $review->get_listing__id() ) {
			return;
		}

		// Get moderation flag.
		$moderate = get_option( 'hp_review_enable_moderation' );

		if ( ( $moderate && 'approve' === $new_status ) || ( ! $moderate && is_object( $new_status ) ) ) {

			// Get listing.
			$listing = $review->get_listing();

			// Set email arguments.
			$email_args = [
				'tokens' => [
					'review'        => $review,
					'listing'       => $listing,
					'listing_title' => $listing->get_title(),
					'review_url'    => hivepress()->router->get_url( 'listing_view_page', [ 'listing_id' => $listing->get_id() ] ) . '#review-' . $review->get_id(),
				],
			];

			// Get parent review.
			$parent_review = $review->get_parent();

			if ( $parent_review ) {

				// Get user.
				$user = $parent_review->get_author();

				if ( $user ) {

					// Send email.
					( new Emails\Review_Reply(
						hp\merge_arrays(
							$email_args,
							[
								'recipient' => $user->get_email(),

								'tokens'    => [
									'review'     => $parent_review,
									'reply'      => $review,
									'user'       => $user,
									'user_name'  => $user->get_display_name(),
									'reply_text' => $review->display_text(),
								],
							]
						)
					) )->send();
				}
			} else {

				// Get vendor.
				$vendor = $listing->get_user();

				if ( $vendor ) {

					// Send email.
					( new Emails\Review_Add(
						hp\merge_arrays(
							$email_args,
							[
								'recipient' => $vendor->get_email(),

								'tokens'    => [
									'user'      => $vendor,
									'user_name' => $vendor->get_display_name(),
								],
							]
						)
					) )->send();
				}

				if ( $moderate ) {

					// Get user.
					$user = $review->get_author();

					if ( $user ) {

						// Send email.
						( new Emails\Review_Approve(
							hp\merge_arrays(
								$email_args,
								[
									'recipient' => $user->get_email(),

									'tokens'    => [
										'user'      => $user,
										'user_name' => $user->get_display_name(),
									],
								]
							)
						) )->send();
					}
				}
			}
		}
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
		return hivepress()->template->merge_blocks(
			$template,
			[
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
		return hivepress()->template->merge_blocks(
			$template,
			[
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
		return hivepress()->template->merge_blocks(
			$template,
			[
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
			]
		);
	}

	/**
	 * Alters review view blocks.
	 *
	 * @param array  $blocks Block arguments.
	 * @param object $template Template object.
	 * @return array
	 */
	public function alter_review_view_blocks( $blocks, $template ) {

		// Get review.
		$review = $template->get_context( 'review' );

		if ( $review && ! $review->get_parent__id() ) {
			$blocks = hivepress()->template->merge_blocks(
				$blocks,
				[
					'review_container' => [
						'attributes' => [
							'id' => 'review-' . $review->get_id(),
						],
					],
				]
			);
		}

		if ( get_option( 'hp_review_allow_replies' ) ) {
			$blocks = hivepress()->template->merge_blocks(
				$blocks,
				[
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
				]
			);
		}

		return $blocks;
	}
}
