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
	 * Template name.
	 *
	 * @var string
	 */
	protected static $name;

	/**
	 * Template blocks.
	 *
	 * @var array
	 */
	protected static $blocks = [];

	/**
	 * Class initializer.
	 *
	 * @param array $args Template arguments.
	 */
	public static function init( $args = [] ) {
		$args = hp\merge_trees(
			[
				'blocks' => [
					'review_container' => [
						'type'       => 'container',
						'order'      => 10,

						'attributes' => [
							'class' => [ 'hp-review', 'hp-review--view-block' ],
						],

						'blocks'     => [
							'review_header'  => [
								'type'       => 'container',
								'order'      => 10,

								'attributes' => [
									'class' => [ 'hp-review__header' ],
								],

								'blocks'     => [
									'review_image'   => [
										'type'     => 'element',
										'filepath' => 'review/view/review-image',
										'order'    => 10,
									],

									'review_summary' => [
										'type'       => 'container',
										'order'      => 20,

										'attributes' => [
											'class' => [ 'hp-review__summary' ],
										],

										'blocks'     => [
											'review_details' => [
												'type'   => 'container',
												'order'  => 10,

												'attributes' => [
													'class' => [ 'hp-review__details' ],
												],

												'blocks' => [
													'review_author' => [
														'type'     => 'element',
														'filepath' => 'review/view/review-author',
														'order'    => 10,
													],

													'review_date'   => [
														'type'     => 'element',
														'filepath' => 'review/view/review-date',
														'order'    => 20,
													],
												],
											],

											'review_rating'  => [
												'type'     => 'element',
												'filepath' => 'review/view/review-rating',
												'order'    => 20,
											],
										],
									],
								],
							],

							'review_content' => [
								'type'       => 'container',
								'order'      => 20,

								'attributes' => [
									'class' => [ 'hp-review__content' ],
								],

								'blocks'     => [
									'review_text' => [
										'type'     => 'element',
										'filepath' => 'review/view/review-text',
										'order'    => 10,
									],
								],
							],
						],
					],
				],
			],
			$args,
			'blocks'
		);

		parent::init( $args );
	}
}
