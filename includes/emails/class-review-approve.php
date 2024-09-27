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
				'label'     => esc_html__( 'Review Approved', 'hivepress-reviews' ),
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
				'subject' => esc_html__( 'Review Approved', 'hivepress-reviews' ),
				'body'    => esc_html__( 'Hi, %user_name%! Your review for "%listing_title%" has been approved, click on the following link to view it: %listing_url%', 'hivepress-reviews' ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
