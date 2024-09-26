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
		add_filter( 'hivepress/v1/models/review_user/errors', [ $this, 'validate_review' ], 10, 2 );

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

		if ( get_option( 'hp_review_allow_users' ) ) {

			// Alter settings.
			add_filter( 'hivepress/v1/settings', [ $this, 'alter_settings' ] );

			// Add attribute.
			add_filter( 'hivepress/v1/models/user/attributes', [ $this, 'add_attributes' ] );

			// Add model fields.
			add_filter( 'hivepress/v1/models/user', [ $this, 'add_model_fields' ] );

			// Alter templates.
			add_filter( 'hivepress/v1/templates/user_view_block', [ $this, 'alter_user_view_template' ] );
			add_filter( 'hivepress/v1/templates/user_view_page', [ $this, 'alter_user_view_template' ] );
			add_filter( 'hivepress/v1/templates/user_view_page/blocks', [ $this, 'alter_user_view_page' ], 1000, 2 );

			// Update rating.
			add_action( 'hivepress/v1/models/review_user/create', [ $this, 'update_rating' ], 10, 2 );
			add_action( 'hivepress/v1/models/review_user/update_status', [ $this, 'update_rating' ], 10, 2 );
			add_action( 'hivepress/v1/models/review_user/delete', [ $this, 'update_rating' ], 10, 2 );

			// Delete user post.
			add_action( 'hivepress/v1/models/user/delete', [ $this, 'delete_user_post' ], 10, 2 );
		}

		parent::__construct( $args );
	}

	/**
	 * Gets rating.
	 *
	 * @param array $listing_ids Listing IDs.
	 * @return array
	 */
	protected function get_rating( $listing_ids, $review_type = 'hp_review' ) {
		global $wpdb;

		$rating = [ null, null ];

		if ( $listing_ids ) {

			// Get results.
			$placeholder = implode( ', ', array_fill( 0, count( (array) $listing_ids ), '%d' ) );

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT AVG( comment_karma ), COUNT( * ) FROM {$wpdb->comments}
					WHERE comment_type = %s AND comment_approved = %s AND comment_parent = 0 AND comment_post_ID IN ( {$placeholder} );",
					array_merge( [ $review_type, '1' ], (array) $listing_ids )
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

		// Is user review.
		$is_user_review = strpos( current_action(), 'review_user' ) !== false;

		// Get review.
		if ( ! is_object( $review ) ) {
			if ( $is_user_review ) {
				$review = Models\Review_User::query()->get_by_id( $review_id );
			} else {
				$review = Models\Review::query()->get_by_id( $review_id );
			}
		}

		// Get model.
		$model = null;

		if ( $is_user_review ) {

			// Get user.
			$model = $review->get_user();
		} else {

			// Get listing.
			$model = $review->get_listing();
		}

		if ( ! $model ) {
			return;
		}

		// Get model rating.
		$model_rating = [];

		if ( $is_user_review ) {
			$model_rating = $this->get_rating( $model->get_id(), 'hp_review_user' );

			// Change model from user post to user.
			$model = $model->get_user();
		} else {
			$model_rating = $this->get_rating( $model->get_id() );
		}

		// Update model rating.
		$model->fill(
			[
				'rating'       => hp\get_first_array_value( $model_rating ),
				'rating_count' => hp\get_last_array_value( $model_rating ),
			]
		)->save( [ 'rating', 'rating_count' ] );

		if ( $is_user_review ) {
			return;
		}

		// Get vendor.
		$vendor = $model->get_vendor();

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

			// Set review query arguments.
			$review_args = [
				'author'  => $review->get_author__id(),
				'listing' => $review->get_listing__id(),
			];

			// Get review ID.
			$review_id = null;

			if ( strpos( current_filter(), 'review_user' ) !== false ) {

				// Add error.
				// @todo move to marketplace.
				if ( get_option( 'hp_order_review_restriction_users' ) && ! Models\Order::query()->filter(
					[
						'buyer'      => $review->get_user()->get_user__id(),
						'seller'     => $review->get_author__id(),
						'status__in' => [ 'wc-processing', 'wc-completed', 'wc-refunded' ],
					]
				)->get_first_id() ) {
					$errors[] = esc_html__( 'Only sellers can send reviews.', 'hivepress-reviews' );
					return $errors;
				}

				// Set user argument.
				$review_args['user'] = $review->get_user__id();

				// Remove listing argument.
				unset( $review_args['listing'] );

				// Set review ID.
				$review_id = Models\Review_User::query()->filter( $review_args )->get_first_id();
			} else {

				// Set review ID.
				$review_id = Models\Review::query()->filter( $review_args )->get_first_id();
			}

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

		// Is user review.
		$is_user_review = 'user_view_page' === hivepress()->router->get_current_route_name();

		if ( get_option( 'hp_review_allow_replies' ) ) {

			// Set template arguments.
			$template_args = [
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
			];

			if ( $is_user_review ) {

				// Change template arguments.
				$template_args['review_content']['blocks']['review_reply_modal'] = array_merge(
					$template_args['review_content']['blocks']['review_reply_modal'],
					[
						'model'       => 'review_user',
						'_capability' => null,

						'blocks'      => [
							'review_reply_form' => [
								'type'   => 'review_reply_user_form',
								'_order' => 10,
							],
						],
					]
				);

				$template_args['review_content']['blocks']['review_reply_link'] = array_merge(
					$template_args['review_content']['blocks']['review_reply_link'],
					[
						'path' => 'user/review-reply-link',
					]
				);
			}

			// Update template.
			$template = hivepress()->template->merge_blocks( $template, $template_args );
		}

		if ( $is_user_review ) {

			// Remove block.
			hivepress()->template->fetch_block( $template, 'review_listing' );
		}

		return $template;
	}

	/**
	 * Alters settings.
	 *
	 * @param array $settings Settings configuration.
	 * @return array
	 */
	public function alter_settings( $settings ) {
		if ( ! hivepress()->get_version( 'marketplace' ) ) {
			return $settings;
		}

		// Add settings.
		// @todo move to marketplace.
		$settings['orders']['sections']['restrictions']['fields']['order_review_restriction_users'] = [
			'caption' => esc_html__( 'Restrict reviews to sellers', 'hivepress-reviews' ),
			'type'    => 'checkbox',
			'_order'  => 30,
		];

		return $settings;
	}

	/**
	 * Alters user view template.
	 *
	 * @param array $template Template arguments.
	 * @return array
	 */
	public function alter_user_view_template( $template ) {
		return hivepress()->template->merge_blocks(
			$template,
			[
				'user_details_primary' => [
					'blocks' => [
						'user_rating' => [
							'type'   => 'part',
							'path'   => 'user/user-rating',
							'_label' => esc_html__( 'Rating', 'hivepress-reviews' ),
							'_order' => 30,
						],
					],
				],
			]
		);
	}

	/**
	 * Alters user view page.
	 *
	 * @param array  $blocks Template arguments.
	 * @param object $template Template object.
	 * @return array
	 */
	public function alter_user_view_page( $blocks, $template ) {

		// Get user.
		$user = $template->get_context( 'user' );

		if ( ! $user ) {
			return $blocks;
		}

		return hivepress()->template->merge_blocks(
			$blocks,
			[
				'page_content'         => [
					'blocks' => [
						'reviews_container' => [
							'type'   => 'section',
							'title'  => hivepress()->translator->get_string( 'reviews' ),
							'_order' => 100,

							'blocks' => [
								'user_reviews' => [
									'type'      => 'related_reviews',
									'_label'    => hivepress()->translator->get_string( 'reviews' ) . ' (' . hivepress()->translator->get_string( 'related_plural' ) . ')',
									'_settings' => [ 'columns' ],
									'_order'    => 10,
								],
							],
						],
					],
				],

				'user_actions_primary' => [
					'blocks' => [
						'review_submit_modal' => [
							'type'        => 'modal',
							'title'       => esc_html__( 'Write a Review', 'hivepress-reviews' ),
							'_capability' => 'read',
							'_order'      => 5,

							'blocks'      => [
								'review_submit_user_form' => [
									'type'   => 'review_submit_user_form',
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
	 * Delete user post.
	 *
	 * @param int    $object_id Model ID.
	 * @param string $object Model object.
	 */
	public function delete_user_post( $object_id, $object ) {

		// Delete user post.
		Models\Review_User_Post::query()->filter(
			[
				'user' => $object_id,
			]
		)->delete();
	}
}
