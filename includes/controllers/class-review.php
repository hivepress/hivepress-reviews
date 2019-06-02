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
	 * Controller name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Controller routes.
	 *
	 * @var array
	 */
	protected static $routes = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Controller arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'routes' => [
					[
						'path'      => '/reviews',
						'rest'      => true,

						'endpoints' => [
							[
								'methods' => 'POST',
								'action'  => 'submit_review',
							],
						],
					],
				],
			],
			$args
		);

		parent::init( $args );
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

		// Get user.
		$user_id = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();
		$user    = get_userdata( $user_id );

		if ( false === $user ) {
			return hp\rest_error( 400 );
		}

		if ( get_current_user_id() !== $user->ID && ! current_user_can( 'moderate_comments' ) ) {
			return hp\rest_error( 403 );
		}

		// Get listing.
		$listing = Models\Listing::get( $form->get_value( 'listing_id' ) );

		if ( is_null( $listing ) || $listing->get_status() !== 'publish' ) {
			return hp\rest_error( 400 );
		}

		// Add review.
		$review = new Models\Review();

		$review->fill(
			array_merge(
				$form->get_values(),
				[
					'user_id'    => $user->ID,
					'user_name'  => $user->display_name,
					'user_email' => $user->user_email,
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