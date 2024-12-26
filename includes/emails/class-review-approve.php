<?php
/**
 * Review approve email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review approve email class.
 *
 * @class Review_Approve
 */
class Review_Approve extends Email {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'       => esc_html__( 'Review Approved', 'hivepress-reviews' ),
				'description' => esc_html__( 'This email is sent to users when their review is approved.', 'hivepress-reviews' ),
				'recipient'   => hivepress()->translator->get_string( 'user' ),
				'tokens'      => [ 'user_name', 'listing_title', 'review_url', 'user', 'listing', 'review' ],
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
				'subject' => esc_html__( 'Review Approved', 'hivepress-reviews' ),
				'body'    => hp\sanitize_html( __( 'Hi, %user_name%! Your review of "%listing_title%" has been approved, click on the following link to view it: %review_url%', 'hivepress-reviews' ) ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
