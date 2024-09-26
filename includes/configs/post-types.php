<?php
/**
 * Post types configuration.
 *
 * @package HivePress\Configs
 */

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

return [
	'review_user_post' => [
		'delete_with_user' => true,
		'rewrite'          => [ 'slug' => 'user' ],

		'labels'           => [
			'name'               => esc_html__( 'Users', 'hivepress-reviews' ),
			'singular_name'      => esc_html__( 'User', 'hivepress-reviews' ),
			'add_new'            => esc_html__( 'Add New', 'hivepress-reviews' ),
			'add_new_item'       => esc_html__( 'Add User', 'hivepress-reviews' ),
			'edit_item'          => esc_html__( 'Edit User', 'hivepress-reviews' ),
			'new_item'           => esc_html__( 'Add User', 'hivepress-reviews' ),
			'view_item'          => esc_html__( 'View User', 'hivepress-reviews' ),
			'all_items'          => esc_html__( 'Users', 'hivepress-reviews' ),
			'view_items'         => esc_html__( 'View Users', 'hivepress-reviews' ),
			'search_items'       => esc_html__( 'Search Users', 'hivepress-reviews' ),
			'not_found'          => esc_html__( 'No users found', 'hivepress-reviews' ),
			'not_found_in_trash' => esc_html__( 'No users found', 'hivepress-reviews' ),
		],
	],
];
