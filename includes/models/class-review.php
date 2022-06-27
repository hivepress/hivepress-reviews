<?php
/**
 * Review model.
 *
 * @package HivePress\Models
 */

namespace HivePress\Models;

use HivePress\Helpers as hp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Review model class.
 *
 * @class Review
 */
class Review extends Comment {

	/**
	 * Class constructor.
	 *
	 * @param array $args Model arguments.
	 */
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'fields' => [
					'text'                 => [
						'label'      => esc_html__( 'Review', 'hivepress-reviews' ),
						'type'       => 'textarea',
						'max_length' => 2048,
						'required'   => true,
						'_alias'     => 'comment_content',
					],

					'rating'               => [
						'label'  => esc_html__( 'Rating', 'hivepress-reviews' ),
						'type'   => 'rating',
						'_alias' => 'comment_karma',
					],

					'created_date'         => [
						'type'   => 'date',
						'format' => 'Y-m-d H:i:s',
						'_alias' => 'comment_date',
					],

					'approved'             => [
						'type'      => 'number',
						'min_value' => 0,
						'max_value' => 1,
						'_alias'    => 'comment_approved',
					],

					'author'               => [
						'type'     => 'id',
						'required' => true,
						'_alias'   => 'user_id',
						'_model'   => 'user',
					],

					'author__display_name' => [
						'type'       => 'text',
						'max_length' => 256,
						'required'   => true,
						'_alias'     => 'comment_author',
					],

					'author__email'        => [
						'type'     => 'email',
						'required' => true,
						'_alias'   => 'comment_author_email',
					],

					'parent'               => [
						'type'   => 'id',
						'_alias' => 'comment_parent',
						'_model' => 'review',
					],

					'listing'              => [
						'type'     => 'id',
						'required' => true,
						'_alias'   => 'comment_post_ID',
						'_model'   => 'listing',
					],
				],
			],
			$args
		);

		parent::__construct( $args );
	}
}
