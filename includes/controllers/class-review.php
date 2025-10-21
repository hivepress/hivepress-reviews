<?php
/**
 * Review controller.
 *
 * @package HivePress\Controllers
 */

namespace HivePress\Controllers;

use HivePress\Helpers as hp;
use HivePress\Models;
use HivePress\Forms;
use HivePress\Blocks;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review controller class.
 *
 * @class Review
 */
final class Review extends Controller {

	/**
	 * Class constructor.
	 *
	 * @param array $args Controller arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'routes' => [
					'reviews_resource'     => [
						'path'   => '/reviews',
						'method' => 'GET',
						'action' => [ $this, 'get_reviews' ],
						'rest'   => true,
					],

					'review_submit_action' => [
						'base'   => 'reviews_resource',
						'method' => 'POST',
						'action' => [ $this, 'submit_review' ],
						'rest'   => true,
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Gets reviews.
	 *
	 * @param WP_REST_Request $request API request.
	 * @return WP_Rest_Response
	 */
	public function get_reviews( $request ) {

		// Get listing.
		$listing_id = absint( $request->get_param( 'listing' ) );

		if ( ! $listing_id ) {
			return hp\rest_error( 400 );
		}

		$listing = Models\Listing::query()->get_by_id( $listing_id );

		if ( ! $listing || $listing->get_status() !== 'publish' ) {
			return hp\rest_error( 404 );
		}

		// Set query.
		$query = Models\Review::query()->filter(
			[
				'approved' => true,
				'parent'   => null,
				'listing'  => $listing->get_id(),
			]
		);

		// Set order.
		if ( 'rating' === get_option( 'hp_review_default_order' ) ) {
			$query->order( [ 'rating' => 'desc' ] );
		} else {
			$query->order( [ 'created_date' => 'desc' ] );
		}

		// Set page.
		$page   = absint( $request->get_param( '_page' ) );
		$number = get_option( 'hp_reviews_per_page', 3 );

		if ( $page > 1 ) {
			$query->offset( $number * ( $page - 1 ) );
		}

		$query->limit( $number );

		// Set response.
		$response = [
			'results' => [],
		];

		foreach ( $query->get_ids() as $review_id ) {
			$response['results'][] = [
				'id' => $review_id,
			];
		}

		if ( $request->get_param( '_render' ) ) {

			// Get columns.
			$columns = absint( $request->get_param( '_columns' ) );

			if ( $columns < 1 || $columns > 3 ) {
				$columns = 1;
			}

			// Render reviews.
			$response['html'] = ( new Blocks\Related_Reviews(
				[
					'columns' => $columns,
					'wrap'    => false,

					'context' => [
						'listing'      => $listing,
						'review_query' => $query,
					],
				]
			) )->render();
		}

		return hp\rest_response( 200, $response );
	}

	/**
	 * Submits review.
	 *
	 * @param WP_REST_Request $request API request.
	 * @return WP_Rest_Response
	 */
	public function submit_review( $request ) {

		// Check authentication.
		if ( ! is_user_logged_in() ) {
			return hp\rest_error( 401 );
		}

		// Create form.
		$form = null;

		if ( $request->get_param( 'parent' ) ) {

			// Check settings.
			if ( ! get_option( 'hp_review_allow_replies' ) ) {
				return hp\rest_error( 403 );
			}

			$form = new Forms\Review_Reply();
		} else {
			$form = new Forms\Review_Submit();
		}

		// Validate form.
		$form->set_values( $request->get_params() );

		if ( ! $form->validate() ) {
			return hp\rest_error( 400, $form->get_errors() );
		}

		// Get author.
		$author_id = $request->get_param( 'author' ) ? $request->get_param( 'author' ) : get_current_user_id();

		$author = Models\User::query()->get_by_id( $author_id );

		if ( ! $author ) {
			return hp\rest_error( 400 );
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_users' ) && get_current_user_id() !== $author->get_id() ) {
			return hp\rest_error( 403 );
		}

		// Get listing.
		$listing = null;

		if ( $form->get_value( 'parent' ) ) {

			// Get parent review.
			$parent_review = Models\Review::query()->get_by_id( $form->get_value( 'parent' ) );

			if ( ! $parent_review || ! $parent_review->is_approved() || $parent_review->get_parent__id() ) {
				return hp\rest_error( 400 );
			}

			// Get listing.
			$listing = $parent_review->get_listing();
		} else {
			$listing = Models\Listing::query()->get_by_id( $form->get_value( 'listing' ) );
		}

		// Check listing.
		if ( ! $listing || $listing->get_status() !== 'publish' ) {
			return hp\rest_error( 404 );
		}

		if ( $form->get_value( 'parent' ) && $listing->get_user__id() !== $author->get_id() ) {
			return hp\rest_error( 403 );
		} elseif ( ! $form->get_value( 'parent' ) && $listing->get_user__id() === $author->get_id() ) {
			return hp\rest_error( 403, hivepress()->translator->get_string( 'you_cant_review_your_own_listings' ) );
		}

		// Set defaults.
		$review_args = [
			'listing'              => $listing->get_id(),
			'author'               => $author->get_id(),
			'author__display_name' => $author->get_display_name(),
			'author__email'        => $author->get_email(),
			'approved'             => get_option( 'hp_review_enable_moderation' ) ? 0 : 1,
		];

		if ( get_option( 'hp_review_criteria' ) && ! $form->get_value( 'parent' ) ) {

			// Get criteria.
			$review_args['criteria'] = [];

			foreach ( (array) get_option( 'hp_review_criteria' ) as $criterion ) {
				$review_args['criteria'][] = array_merge(
					$criterion,
					[
						'rating' => $form->get_value( '_rating_' . hp\sanitize_key( $criterion['name'] ) ),
					]
				);
			}

			// Get rating.
			$review_args['rating'] = round( array_sum( array_column( $review_args['criteria'], 'rating' ) ) / count( $review_args['criteria'] ) );
		}

		// Add review.
		$review = ( new Models\Review() )->fill( array_merge( $form->get_values(), $review_args ) );

		if ( get_option( 'hp_review_allow_attachment' ) && ! $form->get_value( 'parent' ) ) {

			// Get review draft.
			$review_draft = hivepress()->review->get_review_draft();

			if ( $review_draft && $review_draft->get_attachment__id() ) {

				// Get attachment.
				$attachment = $review_draft->get_attachment();

				if ( $attachment ) {

					// Set attachment.
					$review->set_attachment( $attachment->get_id() );
				}
			}
		}

		if ( ! $review->save() ) {
			return hp\rest_error( 400, $review->_get_errors() );
		}

		// Set attachment.
		if ( isset( $attachment ) ) {
			$attachment->set_parent( $review->get_id() )->save_parent();

			$review_draft->set_attachment( null )->save_attachment();
		}

		return hp\rest_response(
			201,
			[
				'id' => $review->get_id(),
			]
		);
	}
}
