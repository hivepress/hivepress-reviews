<?php
/**
 * Related reviews block.
 *
 * @package HivePress\Blocks
 */

namespace HivePress\Blocks;

use HivePress\Helpers as hp;
use HivePress\Models;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Related reviews block class.
 *
 * @class Related_Reviews
 */
class Related_Reviews extends Reviews {

	/**
	 * Class initializer.
	 *
	 * @param array $meta Block meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label' => null,
			],
			$meta
		);

		parent::init( $meta );
	}

	/**
	 * Class constructor.
	 *
	 * @param array $args Block arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'number' => 1000,
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Bootstraps block properties.
	 */
	protected function boot() {

		// Set query.
		$review_query = Models\Review::query()->filter(
			[
				'approved' => true,
			]
		)->order( [ 'created_date' => 'desc' ] )
		->limit( $this->number );

		// Get listing.
		$listing = $this->get_context( 'listing' );

		if ( $listing ) {

			// Set listing ID.
			$review_query->filter( [ 'listing' => $listing->get_id() ] );
		}

		// Set context.
		$this->context['review_query'] = $review_query;

		parent::boot();
	}
}
