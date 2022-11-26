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
 * Review Reply email class.
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
				'label'     => esc_html__( 'New Reply', 'hivepress-reviews' ),
				'recipient' => hivepress()->translator->get_string( 'user' ),
				'tokens'    => [ 'user_name', 'listing_title', 'listing_url', 'user', 'listing' ],
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
				'body'    => esc_html__( 'Hi, %user_name%! There is a new reply for your review for "%listing_title%". Click on the following link to view it: %listing_url%', 'hivepress-reviews' ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
