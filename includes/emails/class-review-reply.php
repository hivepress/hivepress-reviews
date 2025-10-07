<?php
/**
 * Review reply email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review reply email class.
 *
 * @class Review_Reply
 */
class Review_Reply extends Email {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'       => esc_html__( 'Review Replied', 'hivepress-reviews' ),
				'description' => esc_html__( 'This email is sent to users when a new review reply is received.', 'hivepress-reviews' ),
				'recipient'   => hivepress()->translator->get_string( 'user' ),
				'tokens'      => [ 'user_name', 'listing_title', 'review_url', 'reply_text', 'user', 'listing', 'review', 'reply' ],
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
				'subject' => esc_html__( 'New reply to review', 'hivepress-reviews' ),
				'body'    => hp\sanitize_html( __( 'Hi, %user_name%! You\'ve received a new reply to your review of "%listing_title%", click on the following link to view it: %review_url%', 'hivepress-reviews' ) ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
