<?php
/**
 * Review user post.
 *
 * @package HivePress\Models
 */

namespace HivePress\Models;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review user post class.
 *
 * @class Review_User_Post
 */
class Review_User_Post extends Post {

	/**
	 * Class constructor.
	 *
	 * @param array $args Model arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields' => [
					'title'  => [
						'label'      => hivepress()->translator->get_string( 'title' ),
						'type'       => 'text',
						'max_length' => 256,
						'required'   => true,
						'_alias'     => 'post_title',
					],

					'slug'   => [
						'type'       => 'text',
						'max_length' => 256,
						'_alias'     => 'post_name',
					],

					'status' => [
						'type'       => 'text',
						'max_length' => 128,
						'_alias'     => 'post_status',
					],

					'user'   => [
						'type'     => 'id',
						'required' => true,
						'_alias'   => 'post_parent',
						'_model'   => 'user',
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
