<?php
/**
 * Review submit form block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review submit form block class.
 *
 * @class Review_Submit_Form
 */
class Review_Submit_Form extends Form {

	/**
	 * Class constructor.
	 *
	 * @param array $args Block arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'form' => 'review_submit',
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Bootstraps block properties.
	 */
	protected function bootstrap() {

		// Set listing ID.
		if ( is_singular( 'hp_listing' ) ) {
			$this->values['listing_id'] = get_the_ID();
		}

		parent::bootstrap();
	}
}
