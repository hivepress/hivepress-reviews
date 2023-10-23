<?php
/**
 * Review reply approved user email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review reply approved user email class.
 *
 * @class Review_Reply_Approve_User
 */
class Review_Reply_Approve_User extends Email {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				/* translators: %s: recipient. */
				'label'     => sprintf( esc_html__( 'Review Reply Approved (%s)', 'hivepress-reviews' ), hivepress()->translator->get_string( 'user' ) ),
				'recipient' => hivepress()->translator->get_string( 'user' ),
				'tokens'    => [ 'user_name', 'listing_title', 'listing_url', 'user', 'listing', 'review' ],
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
				'subject' => esc_html__( 'New reply for your review', 'hivepress-reviews' ),
				'body'    => esc_html__( 'Hi, %user_name%! You have got a new reply for your review of "%listing_title%", click on the following link to view it: %listing_url%', 'hivepress-reviews' ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
