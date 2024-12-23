<?php
/**
 * Review add email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review add email class.
 *
 * @class Review_Add
 */
class Review_Add extends Email {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'       => esc_html__( 'Review Added', 'hivepress-reviews' ),
				'description' => esc_html__( 'This email is sent to vendors when a new review is added.', 'hivepress-reviews' ),
				'recipient'   => hivepress()->translator->get_string( 'vendor' ),
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
				'subject' => esc_html__( 'Review Added', 'hivepress-reviews' ),
				'body'    => hp\sanitize_html( __( 'Hi, %user_name%! A new review of "%listing_title%" has been added, click on the following link to view it: %review_url%', 'hivepress-reviews' ) ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
