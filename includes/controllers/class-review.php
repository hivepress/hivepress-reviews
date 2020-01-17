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
						'path' => '/reviews',
						'rest' => true,
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
		$form = ( new Forms\Review_Submit() )->set_values( $request->get_params() );

		if ( ! $form->validate() ) {
			return hp\rest_error( 400, $form->get_errors() );
		}

		// Get author.
		$author_id = $request->get_param( 'author' ) ? $request->get_param( 'author' ) : get_current_user_id();

		$author = Models\User::query()->get_by_id( $author_id );

		if ( empty( $author ) ) {
			return hp\rest_error( 400 );
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_users' ) && get_current_user_id() !== $author->get_id() ) {
			return hp\rest_error( 403 );
		}

		// Get listing.
		$listing = Models\Listing::query()->get_by_id( $form->get_value( 'listing' ) );

		if ( empty( $listing ) || $listing->get_status() !== 'publish' ) {
			return hp\rest_error( 400 );
		}

		// todo check if review is already submitted.
		// Add review.
		// todo make sure that its not approved by default.
		$review = ( new Models\Review() )->fill(
			array_merge(
				$form->get_values(),
				[
					'author'               => $author->get_id(),
					'author__display_name' => $author->get_display_name(),
					'author__email'        => $author->get_email(),
				]
			)
		);

		if ( ! $review->save() ) {
			return hp\rest_error( 400 );
		}

		return hp\rest_response(
			201,
			[
				'id' => $review->get_id(),
			]
		);
	}
}
