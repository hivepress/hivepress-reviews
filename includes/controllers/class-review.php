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

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review controller class.
 *
 * @class Review
 */
class Review extends Controller {

	/**
	 * Class constructor.
	 *
	 * @param array $args Controller arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'routes' => [
					[
						'path'   => '/reviews',
						'rest'   => true,

						'routes' => [
							[
								'method' => 'POST',
								'action' => [ $this, 'submit_review' ],
							],
						],
					],
				],
			],
			$args
		);

		parent::__construct( $args );
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

		// Validate form.
		$form = new Forms\Review_Submit();

		$form->set_values( $request->get_params() );

		if ( ! $form->validate() ) {
			return hp\rest_error( 400, $form->get_errors() );
		}

		// Get author.
		$author_id = $request->get_param( 'author_id' ) ? $request->get_param( 'author_id' ) : get_current_user_id();
		$author    = get_userdata( $author_id );

		if ( false === $author ) {
			return hp\rest_error( 400 );
		}

		if ( get_current_user_id() !== $author->ID && ! current_user_can( 'moderate_comments' ) ) {
			return hp\rest_error( 403 );
		}

		// Get listing.
		$listing = Models\Listing::get_by_id( $form->get_value( 'listing_id' ) );

		if ( is_null( $listing ) || $listing->get_status() !== 'publish' ) {
			return hp\rest_error( 400 );
		}

		// Check reviews.
		$review_id = Models\Review::filter(
			[
				'user_id'    => $author->ID,
				'listing_id' => $listing->get_id(),
			]
		)->get_first_id();

		if ( ! empty( $review_id ) ) {
			return hp\rest_error( 403, esc_html__( "You've already submitted a review.", 'hivepress-reviews' ) );
		}

		// Add review.
		$review = new Models\Review();

		$review->fill(
			array_merge(
				$form->get_values(),
				[
					'approved'     => 0,
					'author_id'    => $author->ID,
					'author_name'  => $author->display_name,
					'author_email' => $author->user_email,
				]
			)
		);

		if ( ! $review->save() ) {
			return hp\rest_error( 400 );
		}

		return new \WP_Rest_Response(
			[
				'data' => [
					'id' => $review->get_id(),
				],
			],
			200
		);
	}
}
