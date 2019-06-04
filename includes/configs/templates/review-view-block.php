<?php
/**
 * Review view block template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
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
							'filepath' => 'review/view/image',
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
									'type'       => 'container',
									'order'      => 10,

									'attributes' => [
										'class' => [ 'hp-review__details' ],
									],

									'blocks'     => [
										'review_author' => [
											'type'     => 'element',
											'filepath' => 'review/view/author',
											'order'    => 10,
										],

										'review_date'   => [
											'type'     => 'element',
											'filepath' => 'review/view/date',
											'order'    => 20,
										],
									],
								],

								'review_rating'  => [
									'type'     => 'element',
									'filepath' => 'review/view/rating',
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
							'filepath' => 'review/view/text',
							'order'    => 10,
						],
					],
				],
			],
		],
	],
];
