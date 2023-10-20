<?php
/**
 * Review submit vendor email.
 *
 * @package HivePress\Emails
 */

namespace HivePress\Emails;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review submit vendor email class.
 *
 * @class Review_Submit_Vendor
 */
class Review_Submit_Vendor extends Email {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				/* translators: %s: recipient. */
				'label'     => sprintf( esc_html__( 'Review Added (%s)', 'hivepress-reviews' ), hivepress()->translator->get_string( 'vendor' ) ),
				'recipient' => hivepress()->translator->get_string( 'vendor' ),
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
				'subject' => esc_html__( 'Review Added', 'hivepress-reviews' ),
				'body'    => esc_html__( 'Hi, %user_name%! There is a new review for "%listing_title%". Click on the following link to view it: %listing_url%', 'hivepress-reviews' ),
			],
			$args
		);

		parent::__construct( $args );
	}
}
