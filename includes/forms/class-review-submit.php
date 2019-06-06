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
	 * Form name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Form title.
	 *
	 * @var string
	 */
	protected static $title;

	/**
	 * Form message.
	 *
	 * @var string
	 */
	protected static $message;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected static $model;

	/**
	 * Form action.
	 *
	 * @var string
	 */
	protected static $action;

	/**
	 * Form method.
	 *
	 * @var string
	 */
	protected static $method = 'POST';

	/**
	 * Form captcha.
	 *
	 * @var bool
	 */
	protected static $captcha = false;

	/**
	 * Form fields.
	 *
	 * @var array
	 */
	protected static $fields = [];

	/**
	 * Form button.
	 *
	 * @var object
	 */
	protected static $button;

	/**
	 * Class initializer.
	 *
	 * @param array $args Form arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'title'   => esc_html__( 'Submit Review', 'hivepress-reviews' ),
				'message' => esc_html__( 'Your review has been submitted', 'hivepress-reviews' ),
				'model'   => 'review',
				'action'  => hp\get_rest_url( '/reviews' ),

				'fields'  => [
					'rating'     => [
						'order' => 10,
					],

					'text'       => [
						'order' => 20,
					],

					'listing_id' => [
						'type' => 'hidden',
					],
				],

				'button'  => [
					'label' => esc_html__( 'Submit Review', 'hivepress-reviews' ),
				],
			],
			$args
		);

		parent::init( $args );
	}
}
