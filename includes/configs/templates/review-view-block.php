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
				'review_image'  => [
					'type'     => 'element',
					'filepath' => 'review/view/image',
				],

				'review_date'   => [
					'type'     => 'element',
					'filepath' => 'review/view/date',
				],

				'review_user'   => [
					'type'     => 'element',
					'filepath' => 'review/view/user',
				],

				'review_rating' => [
					'type'     => 'element',
					'filepath' => 'review/view/rating',
				],

				'review_text'   => [
					'type'     => 'element',
					'filepath' => 'review/view/text',
				],
			],
		],
	],
];
