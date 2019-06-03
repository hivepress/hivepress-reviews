<?php
/**
 * Listing view block template.
 *
 * @package HivePress\Configs\Templates
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'blocks' => [
		'listing_container' => [
			'blocks' => [
				'listing_content' => [
					'blocks' => [
						'listing_details_primary' => [
							'blocks' => [
								'todo' => [
									'type'     => 'element',
									'filepath' => 'listing/view/rating',
								],
							],
						],
					],
				],
			],
		],
	],
];
