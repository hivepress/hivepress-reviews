<?php
/**
 * Review view block template.
 *
 * @package HivePress\Templates
 */

namespace HivePress\Templates;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review view block template class.
 *
 * @class Review_View_Block
 */
class Review_View_Block extends Template {

	/**
	 * Class constructor.
	 *
	 * @param array $args Template arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'review_container' => [
						'type'       => 'container',
						'_order'     => 10,

						'attributes' => [
							'class' => [ 'hp-review', 'hp-review--view-block' ],
						],

						'blocks'     => [
							'review_header'  => [
								'type'       => 'container',
								'tag'        => 'header',
								'_order'     => 10,

								'attributes' => [
									'class' => [ 'hp-review__header' ],
								],

								'blocks'     => [
									'review_image'   => [
										'type'   => 'part',
										'path'   => 'review/view/review-image',
										'_order' => 10,
									],

									'review_summary' => [
										'type'       => 'container',
										'_order'     => 20,

										'attributes' => [
											'class' => [ 'hp-review__summary' ],
										],

										'blocks'     => [
											'review_author' => [
												'type'   => 'container',
												'_order' => 10,

												'attributes' => [
													'class' => [ 'hp-review__author' ],
												],

												'blocks' => [
													'review_author_text'           => [
														'type'   => 'part',
														'path'   => 'review/view/review-author',
														'_order' => 10,
													],

													'review_status_badge' => [
														'type' => 'part',
														'path' => 'review/view/review-status-badge',
														'_order' => 20,
													],
												],
											],

											'review_details' => [
												'type'   => 'container',
												'_order' => 20,

												'attributes' => [
													'class' => [ 'hp-review__details' ],
												],

												'blocks' => [
													'review_rating'  => [
														'type'     => 'part',
														'path' => 'review/view/review-rating',
														'_order'    => 10,
													],

													'review_created_date'   => [
														'type'     => 'part',
														'path' => 'review/view/review-created-date',
														'_order'    => 20,
													],
												],
											],
										],
									],
								],
							],

							'review_content' => [
								'type'       => 'container',
								'_order'     => 20,

								'attributes' => [
									'class' => [ 'hp-review__content' ],
								],

								'blocks'     => [
									'review_text'    => [
										'type'   => 'part',
										'path'   => 'review/view/review-text',
										'_order' => 10,
									],

									'review_listing' => [
										'type'   => 'part',
										'path'   => 'review/view/review-listing',
										'_order' => 20,
									],
								],
							],
						],
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
