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
	protected function boot() {
		if ( is_user_logged_in() ) {

			// Get listing.
			$listing = $this->get_context( 'listing' );

			if ( $listing ) {
				$this->values['listing'] = $listing->get_id();
			}

			// Set draft.
			if ( get_option( 'hp_review_allow_attachment' ) ) {
				$this->context['review'] = hivepress()->review->get_review_draft();

				$this->attributes['data-reset'] = 'true';
			}
		}

		parent::boot();
	}
}
