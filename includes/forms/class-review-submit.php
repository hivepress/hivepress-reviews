<?php
/**
 * Review submit form.
 *
 * @package HivePress\Forms
 */

namespace HivePress\Forms;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review submit form class.
 *
 * @class Review_Submit
 */
class Review_Submit extends Model_Form {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'   => esc_html__( 'Write a Review', 'hivepress-reviews' ),
				'captcha' => false,
				'model'   => 'review',
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Form arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'message' => esc_html__( 'Your review has been submitted.', 'hivepress-reviews' ),
				'action'  => hivepress()->router->get_url( 'review_submit_action' ),

				'fields'  => [
					'rating'  => [
						'required' => true,
						'_order'   => 10,
					],

					'text'    => [
						'_order' => 20,
					],

					'listing' => [
						'display_type' => 'hidden',
					],
				],

				'button'  => [
					'label' => esc_html__( 'Submit Review', 'hivepress-reviews' ),
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
