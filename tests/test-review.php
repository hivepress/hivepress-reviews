<?php
namespace HivePress\Reviews;

/**
 * Tests reviews.
 *
 * @class Review_Test
 */
class Review_Test extends \WP_UnitTestCase {

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Setups test.
	 */
	public function setUp() {
		parent::setUp();

		// Create user and login.
		wp_set_current_user( $this->factory->user->create() );

		// Create post.
		$this->post_id = $this->factory->post->create( [ 'post_type' => 'hp_listing' ] );
	}

	/**
	 * Tests submission.
	 */
	public function test_submission() {

		// Set default arguments.
		$submit_args = [
			'post_id' => $this->post_id,
			'rating'  => 9999,
			'review'  => 'Lorem ipsum dolor sit amet consectetuer',
		];

		// Test if review is added.
		hivepress()->review->submit( $submit_args );

		$this->assertCount( 1, get_comments() );

		// Test if review is not duplicated.
		hivepress()->review->submit( $submit_args );

		$this->assertCount( 1, get_comments() );

		// Test if rating is set.
		$this->assertEquals( '', get_post_meta( $this->post_id, 'hp_rating', true ) );

		foreach ( get_comments() as $review ) {
			wp_update_comment(
				[
					'comment_ID'       => $review->comment_ID,
					'comment_approved' => 1,
				]
			);

			hivepress()->review->update_rating( $review->comment_ID );
		}

		$this->assertEquals( '5', get_post_meta( $this->post_id, 'hp_rating', true ) );

		foreach ( get_comments() as $review ) {
			wp_delete_comment( $review->comment_ID, true );
		}

		// Test invalid post types.
		wp_update_post(
			[
				'ID'        => $this->post_id,
				'post_type' => 'post',
			]
		);

		hivepress()->review->submit( $submit_args );

		$this->assertCount( 0, get_comments() );

		// Test invalid post statuses.
		wp_update_post(
			[
				'ID'          => $this->post_id,
				'post_type'   => 'hp_listing',
				'post_status' => 'draft',
			]
		);

		hivepress()->review->submit( $submit_args );

		$this->assertCount( 0, get_comments() );
	}
}
