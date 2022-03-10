<?php
/**
 * Review reply form.
 *
 * @package HivePress\Forms
 */

namespace HivePress\Forms;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review reply form class.
 *
 * @class Review_Reply
 */
class Review_Reply extends Model_Form {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'   => esc_html__( 'Submit Reply', 'hivepress-reviews' ),
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
				'message' => esc_html__( 'Your reply has been submitted.', 'hivepress-reviews' ),
				'action'  => hivepress()->router->get_url( 'review_reply_action' ),

				'fields'  => [

					'text'    => [
						'_order' => 20,
					],

					'parent'  => [
						'display_type' => 'hidden',
					],

					'listing' => [
						'display_type' => 'hidden',
					],
				],

				'button'  => [
					'label' => esc_html__( 'Submit Reply', 'hivepress-reviews' ),
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
