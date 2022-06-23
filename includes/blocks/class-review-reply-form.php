<?php
/**
 * Review reply form block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review reply form block class.
 *
 * @class Review_Reply_Form
 */
class Review_Reply_Form extends Form {

	/**
	 * Class constructor.
	 *
	 * @param array $args Block arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'form' => 'review_reply',
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Bootstraps block properties.
	 */
	protected function boot() {

		// Get review.
		$review = $this->get_context( 'review' );

		if ( $review ) {

			// Set parent ID.
			$this->values['parent'] = $review->get_id();

			// Clear context.
			unset( $this->context['review'] );
		}

		parent::boot();
	}
}
