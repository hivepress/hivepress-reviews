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

		// Is user review.
		$is_user_review = absint( $request->get_param( 'user' ) );

		// Create form.
		$form = null;

		if ( $request->get_param( 'parent' ) ) {

			// Check settings.
			if ( ! get_option( 'hp_review_allow_replies' ) ) {
				return hp\rest_error( 403 );
			}

			$form = $is_user_review ? new Forms\Review_Reply_User() : new Forms\Review_Reply();
		} else {
			$form = $is_user_review ? new Forms\Review_Submit_User() : new Forms\Review_Submit();
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

		// Set review arguments.
		$review_args = array_merge(
			$form->get_values(),
			[
				'author'               => $author->get_id(),
				'author__display_name' => $author->get_display_name(),
				'author__email'        => $author->get_email(),
				'approved'             => get_option( 'hp_review_enable_moderation' ) ? 0 : 1,
			]
		);

		if ( $is_user_review ) {

			// Check option.
			if ( ! get_option( 'hp_review_allow_users' ) ) {
				return hp\rest_error( 403 );
			}

			// Get user object and user post.
			$user      = null;
			$user_post = null;

			if ( $form->get_value( 'parent' ) ) {

				// Get parent review.
				$parent_review = Models\Review_User::query()->get_by_id( $form->get_value( 'parent' ) );

				if ( ! $parent_review || ! $parent_review->is_approved() || $parent_review->get_parent__id() ) {
					return hp\rest_error( 400 );
				}

				// Get user post.
				$user_post = $parent_review->get_user();
			} else {

				// Get user post.
				$user_post = Models\Review_User_Post::query()->get_by_id( $form->get_value( 'user' ) );
			}

			// Check user post.
			if ( ! $user_post || ! $user_post->get_user() ) {
				return hp\rest_error( 400 );
			}

			// Get user object.
			$user = $user_post->get_user();

			if ( $form->get_value( 'parent' ) && $user->get_id() !== $author->get_id() ) {
				return hp\rest_error( 403 );
			} elseif ( ! $form->get_value( 'parent' ) && $user->get_id() === $author->get_id() ) {
				return hp\rest_error( 403, hivepress()->translator->get_string( 'you_cant_review_your_own_listings' ) );
			}

			// Set review user argument.
			$review_args['user'] = $user_post->get_id();
		} else {

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
				return hp\rest_error( 400 );
			}

			if ( $form->get_value( 'parent' ) && $listing->get_user__id() !== $author->get_id() ) {
				return hp\rest_error( 403 );
			} elseif ( ! $form->get_value( 'parent' ) && $listing->get_user__id() === $author->get_id() ) {
				return hp\rest_error( 403, hivepress()->translator->get_string( 'you_cant_review_your_own_listings' ) );
			}

			// Set review listing argument.
			$review_args['listing'] = $listing->get_id();
		}

		// Get review.
		$review = null;

		if ( $is_user_review ) {

			// Add review.
			$review = ( new Models\Review_User() )->fill( $review_args );
		} else {

			// Add review.
			$review = ( new Models\Review() )->fill( $review_args );
		}

		if ( ! $review->save() ) {
			return hp\rest_error( 400, $review->_get_errors() );
		}

		return hp\rest_response(
			201,
			[
				'id' => $review->get_id(),
			]
		);
	}
}
