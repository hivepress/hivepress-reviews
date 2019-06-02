<?php
/**
 * Listing view page template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'parent' => 'page',

	'blocks' => [
		'page_container' => [
			'blocks' => [
				'page_columns' => [
					'blocks' => [
						'page_sidebar' => [
							'blocks' => [
								'review_submit_form' => [
									'type'  => 'review_submit_form',
									'order' => 10,
								],
							],
						],
					],
				],
			],
		],
	],
];
