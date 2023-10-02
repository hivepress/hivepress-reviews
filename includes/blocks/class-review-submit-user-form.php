<?php
/**
 * Review submit user form block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review submit user form block class.
 *
 * @class Review_Submit_User_Form
 */
class Review_Submit_User_Form extends Form {

	/**
	 * Class constructor.
	 *
	 * @param array $args Block arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'form' => 'review_submit_user',
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Bootstraps block properties.
	 */
	protected function boot() {

		// Get user.
		$user = $this->get_context( 'user' );

		if ( $user ) {

			// Get user post.
			$user_post = Models\Review_User_Post::query()->filter( [ 'user' => $user->get_id() ] )->get_first_id();

			if ( ! $user_post ) {
				$user_post = ( new Models\Review_User_Post() )->fill(
					[
						'title'  => $user->get_display_name(),
						'slug'   => $user->get_username(),
						'status' => 'publish',
						'user'   => $user->get_id(),
					]
				);

				if ( $user_post->save() ) {

					// Set user post value.
					$this->values['user'] = $user_post->get_id();
				}
			} else {

				// Set user post value.
				$this->values['user'] = $user_post;
			}
		}

		parent::boot();
	}
}
