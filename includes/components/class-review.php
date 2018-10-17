<?php
namespace HivePress\Reviews;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Manages reviews.
 *
 * @class Review
 */
class Review extends \HivePress\Component {

	/**
	 * Class constructor.
	 *
	 * @param array $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );
	}
}
