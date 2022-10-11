<?php
/**
 * Request feedback email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Sent to users to ask for feedback
 */
class Request_Feedback extends Email {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Class meta values.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'       => esc_html__( 'Request Feedback', 'hivepress-reviews' ),
				'description' => esc_html__( 'This email is sent to users to ask for review.', 'hivepress-reviews' ),
				'recipient'   => hivepress()->translator->get_string( 'user' ),
				'tokens'      => [ 'listing_url', 'user_name', 'model_type', 'model_number', 'model', 'user' ],
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Email arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'subject' => esc_html__( 'Request Feedback', 'hivepress' ),
				'body'    => hp\sanitize_html( __( 'Hi, %user_name%! The %model_type% #%model_number% has been completed, click on the following link to write the review: %listing_url%', 'hivepress-reviews' ) ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
