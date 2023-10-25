<?php
/**
 * Settings configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'reviews' => [
		'title'    => hivepress()->translator->get_string( 'reviews' ),
		'_order'   => 40,

		'sections' => [
			'submission' => [
				'title'  => hivepress()->translator->get_string( 'submission' ),
				'_order' => 10,

				'fields' => [
					'review_allow_multiple'    => [
						'label'   => hivepress()->translator->get_string( 'submission' ),
						'caption' => esc_html__( 'Allow submitting multiple reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 10,
					],

					'review_enable_moderation' => [
						'label'   => hivepress()->translator->get_string( 'moderation' ),
						'caption' => esc_html__( 'Manually approve new reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'default' => true,
						'_order'  => 20,
					],

					'review_allow_replies'     => [
						'label'   => esc_html__( 'Replies', 'hivepress-reviews' ),
						'caption' => esc_html__( 'Allow replying to reviews', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 30,
					],

					'review_allow_attachment'  => [
						'label'   => esc_html__( 'Attachments', 'hivepress-reviews' ),
						'caption' => esc_html__( 'Allow file attachments', 'hivepress-reviews' ),
						'type'    => 'checkbox',
						'_order'  => 40,
					],

					'review_attachment_types'  => [
						'label'    => esc_html__( 'Allowed File Types', 'hivepress-reviews' ),
						'type'     => 'select',
						'options'  => 'mime_types',
						'multiple' => true,
						'_parent'  => 'review_allow_attachment',
						'_order'   => 50,
					],
				],
			],
		],
	],
];
